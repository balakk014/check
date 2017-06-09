<?php

return array(

	/**
	 * Configuration Name
	 *
	 * You use this name when initializing a new MangoDB instance
	 *
	 * $db = MangoDB::instance('default');
	 */
	'default' => array(

		/**
		 * Connection Setup
		 * 
		 * See http://www.php.net/manual/en/mongo.construct.php for more information
		 *
		 * or just edit / uncomment the keys below to your requirements
		 */
		'connection' => array(

			/** hostnames, separate multiple hosts by commas **/            
            'hostnames' => '192.168.1.104:27017',//Update Your hostname here

			/** connection options (see http://www.php.net/manual/en/mongo.construct.php) **/
			'options'   => array(
				// database to connect to
				'db'         => 'owayrider',//Update your DB name here
				

				// default timeout of 60 seconds is too long
				'connectTimeoutMS'    => 60000,

				// Connect to DB on creation - how do you want to deal with connection errors
				// TRUE : MangoDB::instance fails and an exception is thrown. Next call to MangoDB::instance will try to connect again
				// FALSE: Exception is thrown when you run first DB action. Next call to MangoDB::instance will return same object
				'connect'    => FALSE,
				
				// authentication			
				'username'  => 'owayride',//update your db username here
				'password'  => 'owayride',//update your db password here				
				'authsource'  => '',//update your source db name here
				'authMechanism'=>'SCRAM-SHA-1', 

				// replication
				//'replicaSet' => 'someSet',
			)
		),

		// see http://php.net/manual/en/mongo.writeconcerns.php
		'writeConcern' => 1,

		/**
		 * Whether or not to use profiling
		 *
		 * If enabled, profiling data will be shown through Kohana's profiler library
		 */
		'profiling' => TRUE
	)
   
    
);
