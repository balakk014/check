<?php defined('SYSPATH') OR die('No direct script access.'); ?>

2017-06-09 10:46:19 --- ERROR: MongoDB\Driver\Exception\ConnectionTimeoutException [ 13053 ]: No suitable servers found (`serverselectiontryonce` set): [Failed connecting to '192.168.1.104:27017': Connection refused] ~ DOCROOT/mongo-php-driver/vendor/mongodb/mongodb/src/Collection.php [ 177 ]
2017-06-09 10:46:19 --- STRACE: MongoDB\Driver\Exception\ConnectionTimeoutException [ 13053 ]: No suitable servers found (`serverselectiontryonce` set): [Failed connecting to '192.168.1.104:27017': Connection refused] ~ DOCROOT/mongo-php-driver/vendor/mongodb/mongodb/src/Collection.php [ 177 ]
--
#0 /home/developer/workspace/oway_new/mongo-php-driver/vendor/mongodb/mongodb/src/Collection.php(177): MongoDB\Driver\Manager->selectServer(Object(MongoDB\Driver\ReadPreference))
#1 /home/developer/workspace/oway_new/modules/mangodb/classes/kohana/mangodb.php(520): MongoDB\Collection->aggregate(Array)
#2 /home/developer/workspace/oway_new/modules/mangodb/classes/kohana/mangodb.php(332): Kohana_MangoDB->_call('aggregate', Array)
#3 /home/developer/workspace/oway_new/modules/taximobility/classes/model/taximobilitycommonmodel.php(781): Kohana_MangoDB->aggregate('siteinfo', Array)
#4 /home/developer/workspace/oway_new/application/classes/common_config.php(18): Model_TaximobilityCommonmodel->common_site_info(Array)
#5 /home/developer/workspace/oway_new/modules/taximobility/classes/controller/taximobilitywebsite.php(51): require('/home/developer...')
#6 /home/developer/workspace/oway_new/modules/taximobility/classes/controller/taximobilityusers.php(15): Controller_TaximobilityWebsite->__construct(Object(Request), Object(Response))
#7 [internal function]: Controller_TaximobilityUsers->__construct(Object(Request), Object(Response))
#8 /home/developer/workspace/oway_new/system/classes/kohana/request/client/internal.php(101): ReflectionClass->newInstance(Object(Request), Object(Response))
#9 /home/developer/workspace/oway_new/system/classes/kohana/request/client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#10 /home/developer/workspace/oway_new/system/classes/kohana/request.php(1154): Kohana_Request_Client->execute(Object(Request))
#11 /home/developer/workspace/oway_new/index.php(129): Kohana_Request->execute()
#12 {main}
2017-06-09 10:46:19 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: favicon.ico ~ SYSPATH/classes/kohana/request.php [ 1142 ]
2017-06-09 10:46:19 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: favicon.ico ~ SYSPATH/classes/kohana/request.php [ 1142 ]
--
#0 /home/developer/workspace/oway_new/index.php(129): Kohana_Request->execute()
#1 {main}
2017-06-09 10:46:39 --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: public/uploads/favicon/5913fe190ab7afavicon.png ~ SYSPATH/classes/kohana/request.php [ 1142 ]
2017-06-09 10:46:39 --- STRACE: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: public/uploads/favicon/5913fe190ab7afavicon.png ~ SYSPATH/classes/kohana/request.php [ 1142 ]
--
#0 /home/developer/workspace/oway_new/index.php(129): Kohana_Request->execute()
#1 {main}