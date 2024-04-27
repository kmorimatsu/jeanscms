<?php
/*
 * Jeans CMS (GPL license)
 * $Id: config.php 288 2010-10-11 07:06:21Z kmorimatsu $
 */

ini_set('display_errors', 1);
error_reporting(E_ALL ^ E_NOTICE);

@define('_DIR_ROOT', dirname(__FILE__).'/');

@define('_CONF_DEBUG_MODE','admin');

// main jeans directory
@define('_DIR_JEANS', _DIR_ROOT . 'jeans/');
// skin file dir
@define('_DIR_SKINS', _DIR_ROOT . 'skins/');

// these dirs are normally sub dirs of the jeans dir, but 
// you can redefine them if you wish
@define('_DIR_LANG',        _DIR_JEANS . 'language/');
@define('_DIR_LIBS',        _DIR_JEANS . 'libs/'    );
@define('_DIR_PLUGINS',     _DIR_JEANS . 'plugins/' );

// database settings
// The different DBs are used for login and main.
@define('_CONF_DB_TYPE', 'sqlite');
@define('_CONF_DB_MAIN', _DIR_ROOT . 'sqlite/.htdbsqlite');
@define('_CONF_DB_LOGIN', _DIR_ROOT . 'sqlite/.htdblogin');

// Hash Salt
@define('_HASH_SALT','');

// Disable some classes
//define('_CONF_ENABLE_CLASS_ADMIN_SQL',false);

// Define error reporting
error_reporting(E_ALL|E_STRICT);

// Include core and initialize
require(_DIR_LIBS.'jeans.php');
core::init();
