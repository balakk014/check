<?php

/*
 * ----------------------------------------------------------------------------
 * "THE BEER-WARE LICENSE" (Revision 42):
 * Wouter Wiegmans wrote this file. As long as you retain this notice you
 * can do whatever you want with this stuff. If we meet some day, and you think
 * this stuff is worth it, you can buy me a beer in return - Wouter Wiegmans
 * ----------------------------------------------------------------------------
 */

class Kohana_MangoDB {

	/**
	 * @var  string  default instance name
	 */
	public static $default = 'default';

	/**
	 * @var  array  Database instances
	 */
	public static $instances = array();

	/**
	 * Get a singleton MangoDB instance. If configuration is not specified,
	 * it will be loaded from the MangoDB configuration file using the same
	 * group as the name.
	 *
	 *     // Load the default database
	 *     $db = MangoDB::instance();
	 *
	 *     // Create a custom configured instance
	 *     $db = MangoDB::instance('custom', $config);
	 *
	 * @param   string   instance name
	 * @param   array    configuration parameters
	 * @return  Database
	 */
	public static function instance($name = NULL, array $config = NULL)
	{
		if ($name === NULL)
		{
			// Use the default instance name
			$name = MangoDB::$default;
		}
		if ( ! isset(MangoDB::$instances[$name]))
		{
			if ($config === NULL)
			{
				// Load the configuration for this database
				$config = Kohana::$config->load('mangoDB.' . $name);
			}
			// Store the database instance
			MangoDB::$instances[$name] = new MangoDB($name,$config);
		}
		return self::$instances[$name];
	}

	// Instance name
	protected $_name;

	// Connected
	protected $_connected;

	// Mongo object
	protected $_connection;

	// MongoDB object
	protected $_db;

	// Configuration
	protected $_config;

	protected function __construct($name, array $config)
	{
		$this->_name   = $name;
		$this->_config = $config;

		$server  = $this->_config['connection']['hostnames'];
		$options = Arr::get($this->_config['connection'], 'options', array());

		if ( strpos($server, 'mongodb://') !== 0)
		{
			// Add 'mongodb://'
			$server = 'mongodb://' . $server;
			//$server = 'mongodb://' . $options['username'] . ':' . $options['password'] . '@' . $server . '/' . $options['db'] . '?authMechanism=' . $options['authMechanism'] . '&connectTimeoutMS=' . $options['connectTimeoutMS'];
			//$server = 'mongodb://' . $options['username'] . ':' . $options['password'] . '@' . $server . '/' . $options['db'] . '?authMechanism=' . $options['authMechanism'] . '&connectTimeoutMS=' . $options['connectTimeoutMS'];
		}
		##LAMP 7.0 and MongoDB 3.4 Conversion
		include 'mongo-php-driver/vendor/autoload.php';
		include 'mongo-php-driver/vendor/mongodb/mongodb/src/Client.php';

		// create Mongo object (but don't connect just yet)
		//$this->_connection = new MongoClient($server, array('connect' => FALSE) + $options);
		$this->_connection = new MongoDB\Client($server, array('connect' => FALSE) + $options);

		// connect
		if ( Arr::get($options, 'connect', TRUE))
		{
			$this->connect();
		}
	}

	final public function __toString()
	{
		return $this->_name;
	}

	/**
	 * Connect to database
	 */
	public function connect()
	{
		if ( $this->_connected)
		{
			return TRUE;
		}
                ##LAMP 7.0 and MongoDB 3.4 Conversion

		//$this->_connected = $this->_connection->connect();
                $this->_connected = $this->_connection->__get(Arr::path($this->_config, 'connection.options.db'));
		/*$this->_db        = $this->_connected
			? $this->_connection->selectDB(Arr::path($this->_config, 'connection.options.db'))
			: NULL;*/
                $this->_db        = $this->_connected
			? $this->_connection->selectDatabase(Arr::path($this->_config, 'connection.options.db'))
			: NULL;

		return $this->_connected;
	}

	/**
	 * Returns connection status
	 */
	public function connected()
	{
		return $this->_connected;
	}

	/**
	 * Disconnect from database
	 */
	public function disconnect()
	{
		if ( $this->_connected)
		{
			$this->_connection->close();
		}

		$this->_db = $this->_connected = NULL;
	}

	/** Database Management */

	public function last_error()
	{
		return $this->_connected
			? $this->_db->lastError() 
			: NULL;
	}

	public function prev_error()
	{
		return $this->_connected
			? $this->_db->prevError()
			: NULL;
	}

	public function reset_error()
	{
		return $this->_connected
			? $this->_db->resetError()
			: NULL;
	}

	public function command( array $data)
	{
		return $this->_call('command', array(), $data);
	}

	public function execute( $code, array $args = array() )
	{
		return $this->_call('execute', array(
			'code' => $code,
			'args' => $args
		));
	}

	public function db()
	{
		return $this->_connected
			? $this->_db
			: FALSE;
	}

	/** Collection management */

	public function create_collection ( $name, $capped= FALSE, $size= 0, $max= 0 )
	{
		return $this->_call('create_collection', array(
			'name'    => $name,
			'capped'  => $capped,
			'size'    => $size,
			'max'     => $max
		));
	}

	public function drop_collection( $name )
	{
		return $this->_call('drop_collection', array(
			'name' => $name
		));
	}

	public function ensure_index ( $collection_name, $keys, $options = array())
	{
		return $this->_call('ensure_index', array(
			'collection_name' => $collection_name,
			'keys'            => $keys,
			'options'         => $options
		));
	}

	/** Data Management */

	public function batch_insert ( $collection_name, array $a, array $options = array() )
	{
		return $this->_call('batch_insert', array(
			'collection_name' => $collection_name,
			'options'         => $options
		), $a);
	}

	public function count( $collection_name, array $query = array() )
	{
		return $this->_call('count', array(
			'collection_name' => $collection_name,
			'query'           => $query
		));
	}

	public function findOne($collection_name, array $query = array(), array $fields = array())
	{
		# fill_keys function added to support LAMP 7.0 and its mongo version
		$fields = $this->fill_keys($fields);
		if(!empty($fields)){
			$fields = ['projection'=>$fields];
		}
		
		/*return $this->_call('findOne', array(
			'collection_name' => $collection_name,
			'query'           => $query,
			'fields'          => $fields
		));*/
		
		##LAMP 7.0 And Mongodb 3.4
		$result= $this->_call('findOne', array(
			'collection_name' => $collection_name,
			'query'           => $query,
			'fields'          => $fields
		));                
		// $result = iterator_to_array($result) ; 
		return $result;
	}

	public function find($collection_name, array $query = array(), array $fields = array())
	{
		/*return $this->_call('find', array(
			'collection_name' => $collection_name,
			'query'           => $query,
			'fields'          => $fields
		));*/
             ##LAMP 7.0 And Mongodb 3.4
            $result= $this->_call('find', array(
			'collection_name' => $collection_name,
			'query'           => $query,
			'fields'          => $fields
                    ));            
            $result = iterator_to_array($result) ;
            // $result=json_decode(json_encode($result),true);
                return $result;
	}
	
	public function deleteOne($collection_name, array $criteria, $options = array())
	{
		return $this->_call('deleteOne', array(
			'collection_name' => $collection_name,
			'criteria'        => $criteria,
			'options'         => $options
		));
	}
	
	public function deleteMany($collection_name, array $criteria, $options = array())
	{
		return $this->_call('deleteMany', array(
			'collection_name' => $collection_name,
			'criteria'        => $criteria,
			'options'         => $options
		));
	}
        # Function created to support LAMP 7.0 and its mongo drive
	public function fill_keys($fields = array()){
	
		if(!empty($fields)){
			$fields = array_fill_keys($fields,1);			
		}
		return $fields;
	}


	public function group( $collection_name, $keys , array $initial , $reduce, array $condition= array() )
	{
		return $this->_call('group', array(
			'collection_name' => $collection_name,
			'keys'            => $keys,
			'initial'         => $initial,
			'reduce'          => $reduce,
			'condition'       => $condition
		));
	}
	
	public function aggregate($collection_name)
	{
        // support both type of arguments as specified on
        // http://php.net/manual/en/mongocollection.aggregate.php

        $arguments = func_get_args();
        $collection_name = array_shift($arguments);

		$result=$this->_call('aggregate', array(
			'collection_name' => $collection_name,
            'arguments' => $arguments
		));
                ##LAMP 7.0 and Mongo DB 3.4 Conversion
                // $result= iterator_to_array($result,FALSE);                 
                $result=$result->toArray();
                //echo "<pre>";print_r($result);
                 //$result= json_decode(json_encode($result),true);             
                $bson = MongoDB\BSON\fromPHP($result);
                $result= MongoDB\BSON\toJSON($bson);
                $result=json_decode($result,true);
                //echo "<pre>";print_r($result);
                $result=array("result"=>$result);
                 
                 return $result;
	}

	public function updateOne($collection_name, array $criteria, array $newObj, $options = array())
	{
		return $this->_call('updateOne', array(
			'collection_name' => $collection_name,
			'criteria'        => $criteria,
			'options'         => $options
		), $newObj);
	}
        # folllowing code added  for  update many LAMP 7.0 and its supporting mongo driver 3.4
	public function updateMany($collection_name, array $criteria, array $newObj, $options = array())
	{
		return $this->_call('updateMany', array(
			'collection_name' => $collection_name,
			'criteria'        => $criteria,
			'options'         => $options
		), $newObj);
	}

	public function insertOne($collection_name, array $a, $options = array())
	{
		return $this->_call('insertOne', array(
			'collection_name' => $collection_name,
			'options'         => $options
		), $a);
	}
        # folllowing code added  for  insertMany LAMP 7.0 and its supporting mongo driver 3.4
	public function insertMany($collection_name, array $a, $options = array())
	{
		return $this->_call('insertMany', array(
			'collection_name' => $collection_name,
			'options'         => $options
		), $a);
	}


	public function remove($collection_name, array $criteria, $options = array())
	{
		return $this->_call('remove', array(
			'collection_name' => $collection_name,
			'criteria'        => $criteria,
			'options'         => $options
		));
	}

	public function save($collection_name, array $a, $options = array())
	{
		return $this->_call('save', array(
			'collection_name' => $collection_name,
			'options'         => $options
		), $a);
	}

	/** File management */

	public function gridFS()
	{
		$this->_connected OR $this->connect();

		$prefix = Arr::path($this->_config, 'gridFS.prefix', 'fs');

		return $this->_db->getGridFS($prefix);
	}

	public function get_file(array $criteria = array())
	{
		return $this->_call('get_file', array(
			'criteria' => $criteria
		));
	}

	public function get_files(array $query = array(), array $fields = array())
	{
		return $this->_call('get_files', array(
			'query'  => $query,
			'fields' => $fields
		));
	}

	public function set_file_bytes($bytes, array $extra = array(), array $options = array())
	{
		return $this->_call('set_file_bytes', array(
			'bytes'   => $bytes,
			'extra'   => $extra,
			'options' => $options
		));
	}

	public function set_file($filename, array $extra = array(), array $options = array())
	{
		return $this->_call('set_file', array(
			'filename' => $filename,
			'extra'    => $extra,
			'options'  => $options
		));
	}

	public function remove_file( array $criteria = array(), array $options = array())
	{
		return $this->_call('remove_file', array(
			'criteria' => $criteria,
			'options'  => $options
		));
	}

	/**
	 * All commands for which benchmarking could be useful
	 * are executed by this method
	 *
	 * This allows for easy benchmarking
	 */
	protected function _call($command, array $args = array(), array $values = NULL)
	{
		$this->_connected OR $this->connect();

		extract($args);

		if ( ! empty($this->_config['profiling']))
		{
			$_bm_name = isset($collection_name)
			 ? $collection_name . '.' . $command
			 : $command;

			if ( isset($query))    $_bm_name .= ' (' . JSON_encode($query) . ')';
			if ( isset($criteria)) $_bm_name .= ' (' . JSON_encode($criteria) . ')';
			if ( isset($values))   $_bm_name .= ' (' . JSON_encode($values) . ')';

			$_bm = Profiler::start("MangoDB {$this->_name}",$_bm_name);
		}

		if ( isset($collection_name))
		{
			$c = $this->_db->selectCollection($collection_name);
		}

		if ( isset($options) && ! array_key_exists('w', $options))
		{
			$options['w'] = Arr::get($this->_config, 'writeConcern', 1);
		}

		switch ($command)
		{
			case 'ensure_index':
				$r = $c->ensureIndex($keys, $options);
			break;
			case 'create_collection':
				$r = $this->_db->createCollection($name,$capped,$size,$max);
			break;
			case 'drop_collection':
				$r = $this->_db->dropCollection($name);
			break;
			case 'command':
				$r = $this->_db->command($values);
			break;
			case 'execute':
				$r = $this->_db->execute($code,$args);
			break;
			case 'batch_insert':
				$r = $c->batchInsert($values, $options);
			break;
			case 'count':
				$r = $c->count($query);
			break;
			case 'findOne':
				$r = $c->findOne($query,$fields);
			break;
			case 'find':
				$r = $c->find($query,$fields);
			break;
			case 'group':
				$r = $c->group($keys,$initial,$reduce,$condition);
			break;
			case 'aggregate':
				$r = call_user_func_array(array($c, 'aggregate'), $arguments);
			break;
                        # following code 'update' keyword has been changed to 'updateOne' for LAMP 7.0 and its supporting mongo driver 3.4
			case 'updateOne':
				$r = $c->updateOne($criteria, $values, $options);
			break;
                        # following code added for 'updateMany' for LAMP 7.0 and its supporting mongo driver 3.4
                        case 'updateMany':
                                $r = $c->updateMany($criteria, $values, $options); //php 7.0												
			break;
                     # following code 'insert' keyword has been changed to 'insertOne' for LAMP 7.0 and its supporting mongo driver 3.4
			case 'insertOne':
				$r = $c->insertOne($values, $options);
			break;
                    # following code added for 'insertMany' for LAMP 7.0 and its supporting mongo driver 3.4
                        case 'insertMany':
                            $r = $c->insertMany($values, $options); //php 7.0												
			
			break;
			/*case 'remove':
				$r = $c->remove($criteria,$options);
			break;*/
                     # following code added for 'insertMany' for LAMP 7.0 and its supporting mongo driver 3.4
                        case 'deleteOne':
				$r = $c->deleteOne($criteria,$options);
			break;
                        case 'deleteMany':
				$r = $c->deleteMany($criteria,$options);
			break;
			case 'save':
				$r = $c->save($values, $options);
			break;
			case 'get_file':
				$r = $this->gridFS()->findOne($criteria);
			break;
			case 'get_files':
				$r = $this->gridFS()->find($query, $fields);
			break;
			case 'set_file_bytes':
				$r = $this->gridFS()->storeBytes($bytes, $extra, $options);
			break;
			case 'set_file':
				$r = $this->gridFS()->storeFile($filename, $extra, $options);
			break;
			case 'remove_file':
				$r = $this->gridFS()->remove($criteria, $options);
			break;
		}

		if ( isset($_bm))
		{
			Profiler::stop($_bm);
		}

		return $r;
	}  
}
