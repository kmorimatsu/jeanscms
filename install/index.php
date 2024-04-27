<?php
/*
 * Jeans CMS (GPL license)
 * $Id: index.php 300 2010-10-22 00:30:42Z kmorimatsu $
 */

// Check the conditions of server
if (!class_exists('PDO')){
	exit('PHP >5.2 is required for installing Jeans CMS.');
}

$pdo=PDO::getAvailableDrivers();
if (!in_array('sqlite',$pdo)){
	exit('pdo_sqlite is requred for installing Jeans CMS.');
}

if (file_exists(dirname(__FILE__).'/../sqlite/.htdblogin')){
	if (0<filesize(dirname(__FILE__).'/../sqlite/.htdblogin')) exit('Jeans CMS is already installed.');
}

// Install script specific settings follow
@define('_CONF_DEBUG_MODE',true);
@define('_DIR_PLUGINS',false);
@define('_CONF_SELF',basename(__FILE__));
@define('_DIR_SKINS', dirname(__FILE__) . '/');
@define('_CONF_URL_SKINS',
	'http://'.$_SERVER['HTTP_HOST'].
	substr($_SERVER['SCRIPT_NAME'],0,0-strlen(basename(__FILE__))));

// Remove all cookie settings.
// This is required to avoid including member.php
$_COOKIE=array();

// Remove actions
unset($_GET['action']);
unset($_POST['action']);

require(dirname(__FILE__).'/../config.php');
if (file_exists(_CONF_DB_LOGIN)){
	if (0<filesize(_CONF_DB_LOGIN)) exit('Jeans CMS is already installed.');
}

require(dirname(__FILE__).'/install.php');
install::init();
install::selector();

