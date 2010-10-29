<?php
error_reporting(E_ALL|E_STRICT);
ini_set('display_errors', 1);
date_default_timezone_set('Europe/London');

// directory setup and class loading
set_include_path('.' . PATH_SEPARATOR . '../lib/' . PATH_SEPARATOR . '../application/models' . PATH_SEPARATOR . get_include_path());
include "Zend/Loader.php";
Zend_Loader::registerAutoload();

// load configuration
$config = new Zend_Config_Ini('../application/config/config.ini', 'general');
$registry = Zend_Registry::getInstance();
$registry->set('config', $config);

$writer = new Zend_Log_Writer_Stream($config->log->get('file'));
$logger = new Zend_Log($writer);
Zend_Registry::set('logger', $logger);

// setup database
$db = Zend_Db::factory($config->db);
Zend_Db_Table::setDefaultAdapter($db);
Zend_Registry::set('db', $db);
$db->getProfiler()->setEnabled(true);

// setup controller
$frontController = Zend_Controller_Front::getInstance();
$frontController->throwExceptions(true);
$frontController->setControllerDirectory('../application/controllers');
Zend_Layout::startMvc(array('layoutPath'=>'../application/layouts'));

// set up routes
/*$router = $frontController->getRouter(); 
$route = new Zend_Controller_Router_Route_Static(
    '/index',
    array('controller' => 'album', 'action' => 'list')
);
$router->addRoute('default', $route);
$frontController->setRouter($router);*/

// run!
$frontController->dispatch();
