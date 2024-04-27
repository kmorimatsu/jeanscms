<?php
/*
 * Jeans CMS (GPL license)
 * $Id: jeans.php 375 2023-08-03 00:20:18Z kmorimatsu $
 */

/**
 * There is only one global function here.
 */
function __autoload($class) {
	return core::autoload($class);
}

class jeans {
	/**
	 * Jeans class provides several routines for security.
	 * All classes must inherit this class except for few exceptions.
	 */
	static public final function utf8($text){
		// Check UTF-8 encode.  Return zero string if broken UTF-8 is given.
		static $replace=array('&amp;'=>'&','&lt;'=>'<','&gt;'=>'>');
		return strtr(htmlspecialchars((string)$text,ENT_NOQUOTES,'UTF-8'),$replace);
	}
	static public final function hsc($text){
		// Quote string by htmlspecialchars()
		return htmlspecialchars((string)$text,ENT_QUOTES,'UTF-8');
	}
	static public final function hsc_but_not_jeans_tags($text){
		$text=self::hsc($text);
		$text=preg_replace('/&lt;%([a-zA-Z0-9_]+)%&gt;/','<%$1%>',$text);
		return $text;
	}
	static public final function shorten($text, $maxlength, $toadd='...') {
		if (strlen($text) <= $maxlength) return $text;
		for ($result='';strlen($result)==0 && 0<$maxlength;$maxlength--) $result=self::utf8(substr($text,0,$maxlength));
		return $result.$toadd;
	}
	static public final function quote_html($text,$mode='notag'){
		switch($mode){
			case 'escape_hsc':
				$text=strtr($text,array('"'=>'\\"', "'"=>"\\'", '\\'=>'\\\\'));
				break;
			case 'hsc':
				break;
			case 'urlencode':
				$text=urlencode($text);
				break;
			case 'rawurlencode':
				$text=rawurlencode($text);
				break;
			case 'url':
				if (!self::is_valid_url($text)) $text='';
				break;
			case 'notag':
			default:
				$text=strtr($text,array('"'=>'`',"'"=>'`'));
				$text=strip_tags($text);
				break;
		}
		return self::hsc($text);
	}
	static public final function p($text,$mode='notag'){
		// Print string.
		echo self::quote_html($text,$mode);
	}
	static public final function translate($text){
		// Translate string.
		// The language file in skin/plugin directory will be dinamically included.
		if (defined($text)) return constant($text);
		if (!preg_match('/^_([A-Z][A-Z0-9]*)_([A-Z0-9]*)(?:_[A-Z0-9]*)*$/',$text,$m)) {
			return self::hsc_but_not_jeans_tags($text);
		}
		switch ($m[1]) {
			case 'CONF': case 'DIR':
				return $text;
			case 'JP':
				$skinphp=strtolower($m[2]).'/language/'._LANGUAGE.'.php';
				$skinphpen=strtolower($m[2]).'/language/english.php';
				$dir=_DIR_PLUGINS;
				if (_DIR_PLUGINS!==false) break;
			default:
				$skinphp=strtolower($m[1]).'/language/'._LANGUAGE.'.php';
				$skinphpen=strtolower($m[1]).'/language/english.php';
				$dir=_DIR_SKINS;
		}
		if (self::local_file_exists($dir,$skinphp)) {
			$er=error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
			self::include_local($dir,$skinphp);
			error_reporting($er);
		} elseif (self::local_file_exists($dir,$skinphpen)) {
			$er=error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
			self::include_local($dir,$skinphpen);
			error_reporting($er);
		}
		if (!defined($text)) define($text,$text);
		return constant($text);
	}
	static public final function t($text){
		// Print translated string.
		echo self::utf8(self::translate($text));
	}
	static public final function fill_html($text,$array=array(),$mode='notag'){
		if (!is_array($array)) $array=array($array);
		$search=$replace=array();
		foreach($array as $key=>$value){
			if (is_array($value)) continue;
			$search[]='<%'.preg_replace('/[^a-zA-Z0-9_]+/','',$key).'%>';
			$replace[]=self::quote_html($value,$mode);
		}
		return str_replace($search,$replace,self::utf8($text));
	}
	static public final function echo_html($text,$array=array(),$mode='notag'){
		echo self::fill_html($text,$array,$mode);
	}
	static public final function local_file_exists($dir,$file){
		// Check null byte
		if (preg_match('/[\x00]/',$dir) || preg_match('/[\x00]/',$file)) jerror::quit('Null byte attack attempt for file function.');
		// Support Windows
		$dir=str_replace('\\','/',$dir);
		$file=str_replace('\\','/',$file);
		// Avoid double slash
		$dir_r = substr($dir,-1)=='/';
		$file_l= substr($file,0,1)=='/';
		if ($dir_r && $file_l) $fullpath=$dir.substr($file,1);
		elseif (!$dir_r && !$file_l) $fullpath=$dir.'/'.$file;
		else $fullpath=$dir.$file;
		// Check local file
		if (file_exists($fullpath)) {
			if (strpos(realpath($fullpath),realpath($dir))===0) return realpath($fullpath);
		} else {
			while(!file_exists($fullpath)) {
				if (substr($fullpath,-1)=='.') break;
				$fullpath=preg_replace('#/[^/]*$#','',$fullpath);
			}
			if (is_dir($fullpath) && substr($fullpath,-1)!='/') $fullpath.='/';
			if (strpos(realpath($fullpath),realpath($dir))===0) return false;
		}
		jerror::quit('Directory traversal attempt by file: <%1%><br /> in directory: <%0%>',array($dir,$file));
		exit;
	}
	static public final function local_file_contents($dir,$file){
		$file=self::local_file_exists($dir,$file);
		if ($file===false) return false;
		if (!is_file($file)) return false;
		return file_get_contents($file);
	}
	static public final function include_local($dir,$file,$require=true,$once=true){
		$file=self::local_file_exists($dir,$file);
		if (!$file) return false;
		if ($require) {
			if ($once) require_once($file);
			else require($file);
		} else {
			if ($once) include_once($file);
			else include($file);
		}
		return true;
	}
	static public final function random_key(){
		mt_srand( (double) microtime() * 1000000);
		return sha1(_HASH_SALT.uniqid(mt_rand()));
	}
	static public final function is_valid_url($url){
		static $search;
		if (!isset($search)) $search=
			'#^'.
				'(?:/|http://|https://|ftp://)'.
				'[a-zA-Z0-9\x80-\xff/\.\-]+'.
				'(?:[:][0-9]+)?'.
				'(?:'.
					'/(?:[a-zA-Z0-9\x80-\xff/_+\.\-]|%[0-9a-fA-F][0-9a-fA-F])*'.
					'\??(?:[a-zA-Z0-9\x80-\xff&=_+\.\-]|%[0-9a-fA-F][0-9a-fA-F])*'.
				')?'.
			'$#';
		return (preg_match($search,$url) && (string)$url===self::utf8($url)) || filter_var($url,FILTER_VALIDATE_URL) ;
	}
	static public function is_local_url($url){
		if (preg_match('#^[^/\?]*//([^/]+)/#',$url,$m)) {
			if ($m[1]!=$_SERVER['HTTP_HOST']) return false;
		}
		return self::is_valid_url($url);
	}
	static public function init(){
		// Default: do nothing
	}
	static public function shutdown(){
		// Default: do nothing
	}
}

class core extends jeans {
	/**
	 * The core class provides basic functions including:
	 * 1) auto-loading PHP scripts accrding to the classes that are requested
	 * 2) throughing login request to member class
	 * 3) definition of _CONF_*
	 * 4) controling the order of objects that are closed when shutdown
	 * 5) access the cookies from libs/plugins
	 *
	 */
	static private $class_objects=array('sql');
	static public function init(){
		register_shutdown_function(array('core','shutdown'));
		define('_JEANS_VERSION','1.1.0 beta');
		header('Content-Type: text/html; charset=UTF-8');
		header('Last-Modified: '. gmdate('D, d M Y H:i:s'). ' GMT');
		sql::init();
		self::find_emulator();
		self::read_conf();
		self::post_read_conf();
		self::login();
		self::parse_url();
		self::action();
	}
	static private function find_emulator(){
		if (!function_exists('hash')){
			if (!class_exists('misc_hash')) jerror::quit('Available hash() function was not found.');
		} 
	}
	static private function check_input(&$data,$stop_mode=false,$orgkey=''){
		if (is_array($data)) {
			foreach ($data as $key=>$value) {
				if ($stop_mode) {
					if ((string)$key!==self::utf8($key) || !preg_match(_PREG_SAFE_TEXT,$key)) {
						jerror::quit('Invalid input!');
					}
				}
				if (!is_numeric($key)) $orgkey=$key;
				self::check_input($data[$key],$stop_mode,$orgkey);
			}
		} elseif ($stop_mode) {
			if (strlen($data)==0 || defined('_CONF_DEBUG_MODE') && substr($orgkey,0,5)=='debug') return;
			if (preg_match('/_[a-z]+$/',$orgkey,$m)) {
				switch($m[0]){
					case '_bin':
						return;
					case '_url':
						if (self::is_valid_url($data)) return;
						break;
					case '_text':
						if ((string)$data===self::utf8($data)) return;exit($data);
						break;
					case '_path':
						if (preg_match(_PREG_PATH,$data)) return;
						break;
					default:
						if (preg_match(_PREG_SAFE_TEXT,$data) && (string)$data===self::utf8($data)) return;
						break;
				}
			} elseif (preg_match(_PREG_SAFE_TEXT,$data) && (string)$data===self::utf8($data)) return;
			jerror::quit('Invalid input(<%key%>)!',array('key'=>$orgkey));
		} else {
			$data=self::utf8($data);
		}
	}
	static private function read_conf(){
		$er=error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
		// Clear DB configrations if not defined
		@define('_CONF_DB_TYPE', '');
		@define('_CONF_DB_MAIN', '');
		@define('_CONF_DB_LOGIN','');
		// Read configure from DB
		// ' type="global" AND contextid=0 ' is used here.
		// The other contextid is currently reserved. 
		$res=sql::query('SELECT * FROM jeans_config WHERE type="global" AND owner="jeans" AND contextid=0');
		if ($res) {
			while ($row=$res->fetch()) define ('_CONF_'.strtoupper($row['name']),$row['value']);
		}
		// Default settings follow
		error_reporting(E_ALL ^ E_NOTICE);
		// URL definitions
		if (!defined('_CONF_URL_INDEX')) {
			$url=substr(_DIR_ROOT,strlen(realpath($_SERVER['SCRIPT_FILENAME']))-strlen($_SERVER['SCRIPT_NAME']));
			$url=strtr($url,'\\','/');
			$url='/'.preg_replace('#(^/+|/+$)#','',$url).'/';
			if ($url=='//') $url='/';
			$url='http://'.$_SERVER['HTTP_HOST'].$url;
			define('_CONF_URL_INDEX',self::hsc($url));
			define('_CONF_URL_SKINS',_CONF_URL_INDEX.'skins/');
			define('_CONF_URL_MEDIA',_CONF_URL_INDEX.'media/');
			define('_CONF_URL_ADMIN',_CONF_URL_INDEX.'jeans/');
			define('_CONF_URL_PLUGINS',_CONF_URL_ADMIN.'plugins/');
			$init_config=true;
		} else $init_config=false;
		// Cookie definitions
		if (!defined('_CONF_COOKIE_PREFIX')) {
			define('_CONF_COOKIE_PREFIX','jeans_'.preg_replace('/[0-9]/','',sha1(__FILE__).'_'));
		}
		@define('_CONF_COOKIE_PATH','/');
		@define('_CONF_COOKIE_DOMAIN','');
		@define('_CONF_COOKIE_SECURE',0);
		@define('_CONF_SECURE_COOKIE_KEY',24);
		@define('_CONF_COOKIE_LIFETIME',1); //Month
		@define('_CONF_LASTVISIT',0);
		// Others
		@define('_DIR_PLUGINS', false); // For housekeeping jeans not using plugins (for example: installer).
		if (!defined('_CONF_TIMEZONE')) {
			define('_CONF_TIMEZONE',@date_default_timezone_get());
		}
		@define('_CONF_DEFAULT_LANGUAGE','english');
		@define('_CONF_DEFAULT_GROUP',1);
		@define('_CONF_DEFAULT_GROUP_SKIN','/default/skin.inc');
		@define('_NOW_TIMESTAMP',time());
		@define('_NOW',gmdate('Y-m-d H:i:s', _NOW_TIMESTAMP));
		@define('_XML_BLANC','<?xml version="1.0"?><xml></xml>');
		@define('_SAFE_CHAR',           '[0-9a-zA-Z\x20!#$&\(\)\-\.@\[\]\^_`{}~\x80-\xff]');
		@define('_PREG_SAFE_TEXT',    '/^[0-9a-zA-Z\x20!#$&\(\)\-\.@\[\]\^_`{}~\x80-\xff]*$/D');
		@define('_PREG_PATH',         '/^[0-9a-zA-Z\x20!#$&\(\)\-\.@\[\]\^_{}~\/\\\\]*$/D');
		@define('_NON_SAFE_CHAR',      '[^0-9a-zA-Z\x20!#$&\(\)\-\.@\[\]\^_`{}~\x80-\xff]');
		@define('_PREG_NON_SAFE_CHAR','/[^0-9a-zA-Z\x20!#$&\(\)\-\.@\[\]\^_`{}~\x80-\xff]/');
		error_reporting($er);
	}
	static private function post_read_conf(){
		// Remove $_REQUEST.
		unset($_REQUEST);
		// Remove cookies not for Jeans
		$new_cookie=array();
		foreach ($_COOKIE as $key=>$value) {
			if (strlen(_CONF_COOKIE_PREFIX)==0 || strpos($key,_CONF_COOKIE_PREFIX)===0) $new_cookie[substr($key,strlen(_CONF_COOKIE_PREFIX))]=$_COOKIE[$key];
		}
		$_COOKIE=$new_cookie;
		// Check the input values.
		self::check_input($_GET,true);
		self::check_input($_POST,true);
		self::check_input($_COOKIE);
		self::check_input($_SERVER);
		// Timezone setting
		date_default_timezone_set(_CONF_TIMEZONE);
		// Set note from previous session
		if (!empty($_COOKIE['note_text'])) {
			jerror::note('<%note_text%>',$_COOKIE);
			self::set_cookie('note_text','',0);
		}
		// Cancel magic_quotes_gpc (this routine will be removed after shifting to PHP6)
		/*if (get_magic_quotes_gpc()) {
			self::undo_magic($_GET);
			self::undo_magic($_POST);
			self::undo_magic($_COOKIE);
		}*/
	}
	static private function undo_magic(&$array){
		if (is_array($array)) {
			foreach ($array as $key=>$value) self::undo_magic($array[$key]);
		} elseif (ini_get('magic_quotes_sybase')) {
			$array=strtr($array,"''","'");
		} else {
			$array=strtr($array,array('\\\\'=>'\\','\\"'=>'"',"\\'"=>"'","\\\x00"=>"\x00"));
		}
	}
	static private function login(){
		// member login
		if ( _CONF_DB_TYPE && _CONF_DB_LOGIN && (isset($_POST['login']) || isset($_COOKIE['login'])) && self::class_exists('member') ) {
			member::login();
			// language setting
			if (preg_match('/^[a-z\-]+$/',member::setting('language'),$m) && !defined('_LANGUAGE')) define('_LANGUAGE',$m[0]);
		} else {
			eval("class member extends nonmember {}");
		}
		core::event('post_authentication',array('loggedin'=>member::logged_in()),'jeans');
		// language setting
		if (!defined('_LANGUAGE')) define('_LANGUAGE',_CONF_DEFAULT_LANGUAGE);
	}
	static private function parse_url(){
		// event_parse_url
		if (isset($_GET['virtualpath_text'])) {
			$get=$_GET;
			$_GET=array();
			core::event('parse_url',array('path'=>$get['virtualpath_text']),'view');
			// Check the input values.
			self::check_input($_GET,true);
			$_GET=array_merge($get,$_GET);
		}
	}
	/**
	 * Posted action always require valid ticket.
	 * Actions are provided by classes as 
	 * class::action_post_xxx() or class::action_get_xxx().
	 */
	static private function action(){
		if (isset($_POST['action'])) {
			$mode='post';
			$action=$_POST['action'];
			if (!_CONF_DB_TYPE || !_CONF_DB_LOGIN || !self::class_exists('ticket')) {
				jerror::note('_ADMIN_ACTION_NOT_FOUND');
				return;
			} elseif (!ticket::check()) {
				jerror::note('_ADMIN_INVALID_TICKET');
				return;
			}
		} elseif (isset($_GET['action'])) {
			$mode='get';
			$action=$_GET['action'];
		} else return;
		if (preg_match('/^([a-zA-Z0-9\._]+)\.([a-zA-Z0-9]+)$/',$action,$m)) {
			$class=str_replace('.','_',$m[1]);
			$method='action_'.$mode.'_'.$m[2];
		} else if (preg_match('/^([a-zA-Z0-9]+)$/',$action,$m)) {
			$class=$m[1];
			$method='action_'.$mode.'_'.$m[1];
		} else return;
		// Take action
		if (self::method_callable($class,$method)) {
			if ($mode=='post') self::event('pre_action',array('action'=>$action,'class'=>$class,'method'=>$method));
			$err=call_user_func(array($class,$method));
		} elseif (self::method_callable($class,'__callstatic')) {
			// Support PHP 5.2 for __callstatic
			if ($mode=='post') self::event('pre_action',array('action'=>$action,'class'=>$class,'method'=>$method));
			$args=array();
			$err=call_user_func_array(array($class,'__callstatic'),array($method,$args));
		} else {
			$err='_ADMIN_ACTION_NOT_FOUND';
		}
		// post_action event or registration of error
		if (is_string($err) && strlen($err)) {
			jerror::note($err);
		} else {
			if ($mode=='post') self::event('post_action',array('action'=>$action,'class'=>$class,'method'=>$method));
		}
}
	static public function autoload($class,$check_mode=false){
		static $method_exists=false;
		if ($check_mode) return $method_exists=(bool)$class;
		$class=strtolower($class);
		if (!preg_match('/^[a-zA-Z0-9_]+$/',$class)) jerror::quit('Class name error: <%0%>',$class);
		// Include PHP file
		$array=self::class_file($class);
		if (!$array) {
			if ($method_exists) return $method_exists=false;
			else jerror::quit('Class file does not exist or is disabled: <%0%>',$class);
		} else $method_exists=false;
		list($dir,$file,$is_plugin)=$array;
		if ($is_plugin && !$file) return false;
		self::include_local($dir,$file);
		// Initialize class
		if ($is_plugin) plugin::instance($class);
		else call_user_func(array($class,'init'));
		self::$class_objects[]=$class;
	}
	/*
	 * Following method can be also used to check if a class is available to use.
	 */
	static public function class_file($class){
		static $cache;
		if (isset($cache[$class])) return $cache[$class];
		// Check if the class is disabled
		$conf_enable_class='_CONF_ENABLE_CLASS_'.strtoupper($class);
		if (defined($conf_enable_class) && !constant($conf_enable_class)) return $cache[$class]=false;
		// Determine the file name to be included.
		if (substr($class,0,3)=='jp_' && _DIR_PLUGINS!==false) {
			// plugin
			$plugin_name=substr($class,3);
			$pos=strpos($plugin_name,'_');
			$dir=_DIR_PLUGINS;
			if ($pos===false) {// plugin class
				if (!plugin::plugin_list($class)) return $cache[$class]=false;
				$is_plugin=true;
				$file=plugin::plugin_filename($class);
			} else {// plugin sub class
				$plugin_class='jp_'.substr($plugin_name,0,$pos);
				if (!plugin::plugin_list($plugin_class)) return $cache[$class]=false;
				$is_plugin=false;
				$file=substr(plugin::plugin_filename($plugin_class),0,-4).substr($plugin_name,$pos).'.php';
				$pdir=strtolower(str_replace('_','/',$plugin_name));
				$file=preg_replace('#/[^/]+$#',"/$file",$pdir);
			}
		} else {
			// library
			$is_plugin=false;
			$dir=_DIR_LIBS;
			$file=str_replace('_','/',$class);
			$file=preg_replace('#/[^/]+$#',"/$class",$file).'.php';
		}
		// Return dir and file names
		if (self::local_file_exists($dir,$file)) return $cache[$class]=array($dir,$file,$is_plugin);
		// The file name with lower case characters is also acceptable.
		$file=strtolower($file);
		if (self::local_file_exists($dir,$file)) return $cache[$class]=array($dir,$file,$is_plugin);
		// Class file cannot be found.
		return $cache[$class]=false;
	}
	/*
	 * To check if a class has been loaded, use class_exists($class,false).
	 * Following method is used to check if a class is available,
	 * and the class will be loaded if not yet loaded.
	 */
	static public function class_exists($class) {
		self::autoload(true,true);// Set "method_exists" mode.
		$ret=class_exists($class,true);
		self::autoload(false,true);// Clear "method_exists" mode.
		return $ret;
	}
	static public function method_exists($class,$method){
		if (!self::class_exists($class)) return false;
		return method_exists($class,$method);
	}
	static public function method_callable($class,$method){
		if (!self::class_exists($class)) return false;
		return is_callable("$class::$method");
	}
	static public function error_exists(){
		if (!class_exists('error',false)) return false;
		return count(jerror::get_note());
	}
	/**
	 * Following method will be called when shutting down.
	 */
	static public function shutdown(){
		static $started=false;
		if ($started) return;
		$started=true;
		$plugins=$libs=$libsex=$sql=array();
		foreach (self::$class_objects as $class) {
			if ($class=='plugin') $libsex[]=$class;
			elseif ($class=='sql' || $class=='member') $sql[]=$class;
			elseif (substr($class,0,3)=='jp_') $plugins[]=$class;
			else $libs[]=$class;
		}
		// Shutdown plugins
		foreach ($plugins as $class) call_user_func(array($class,'shutdown'));
		// Shutdown libs by calling shutdown_extra() method (see plugin class).
		foreach ($libsex as $class) call_user_func(array($class,'shutdown_extra'));
		// Shutdown libs
		foreach ($libs as $class) call_user_func(array($class,'shutdown'));
		// Shutdown sql and member classes
		foreach ($sql as $class) call_user_func(array($class,'shutdown'));
	}
	static public function set_cookie($key,$value,$lifetime=2592000){
		if (0<$lifetime) $lifetime += time();
		elseif ($lifetime<0) $lifetime = time()-2592000;
		else $lifetime=0;
		setcookie(_CONF_COOKIE_PREFIX .$key,$value,$lifetime,_CONF_COOKIE_PATH,_CONF_COOKIE_DOMAIN,_CONF_COOKIE_SECURE, true);
	}
	static public function redirect($url){
		if (!self::is_valid_url($url)) {
			if (!preg_match('/^[a-zA-Z0-9\-\.]*\??[a-zA-Z0-9\-\.=&]*/',$url)) {
				jerror::quit('Invalied URL: <%0%>',$url);
			}
		}
		header('Location: ' . $url);
		exit;
	}
	static public function redirect_local($url){
		if (preg_match('#^[^/\?]*//([^/]+)/#',$url,$m)) {
			if ($m[1]!=$_SERVER['HTTP_HOST']) jerror::quit('Invalied local URL: <%0%>',$url);
		}
		self::redirect($url);
	}
	/*
	 * Method calling event follow.
	 */
	static public function event($event,$arg,$group='action'){
		static $cache=array();
		if (!isset($cache[$group])) {
			if (_DIR_PLUGINS===false) return;
			$cache[$group]=array();
			if ($group!='action') {
				// Get event information from DB.
				$query='SELECT g.event as event, e.class as plugin 
					FROM jeans_event_group as g, jeans_plugin as p 
					LEFT JOIN jeans_event as e ON g.event=e.event AND e.class=p.id 
					WHERE g.eventgroup=<%0%>  ORDER BY p.sequence ASC';
				$res=sql::query($query,$group);
				while ($row=$res->fetch()) {
					if (!isset($cache[$group][$row['event']])) $cache[$group][$row['event']]=array();
					if (!empty($row['plugin'])) $cache[$group][$row['event']][]=$row['plugin'];
				}
			}
		}
		if (!isset($cache[$group][$event])) {
			// If information was not got, get it again and register event/group to jeans_event_groups table.
			if ($group!='action') {
				$query='INSERT OR REPLACE INTO jeans_event_group (event,eventgroup) VALUES (<%event%>,<%group%>)';
				sql::register_shutdown_query($query,array('event'=>$event,'group'=>$group));
			}
			$cache[$group][$event]=array();
			$query='SELECT p.id as plugin 
				FROM jeans_event as e, jeans_plugin as p 
				WHERE e.class=p.id AND e.event=<%0%>
				ORDER BY p.sequence ASC';
			$res=sql::query($query,$event);
			while ($row=$res->fetch()) $cache[$group][$event][]=$row['plugin'];
		}
		if ($arg===false) {
			// export cache
			return $cache[$group][$event];
			// Otherwise $arg must be an array.
		}
		foreach ($cache[$group][$event] as $plugin) {
			call_user_func_array(array($plugin,"event_$event"),array(&$arg));
		}
	}
	/*
	 * Logging routine
	 */
	static public function log($desc,$by,$type='general'){
		$row=array(
			'ip'=>$_SERVER['REMOTE_ADDR'],
			'referer'=>@$_SERVER['HTTP_REFERER'],
			'mid'=>(int)member::setting('id'),
			'time'=>_NOW,
			'uri'=>$_SERVER['REQUEST_URI'],
			'type'=>self::quote_html($type),
			'desc'=>self::quote_html($desc),
			'owner'=>$by);
		sql::register_shutdown_query('INSERT INTO jeans_log (<%key:row%>) VALUES (<%row%>)',array('row'=>$row));
	}
}

class sql extends jeans{
	const FLAG_HIDDEN=1,FLAG_DRAFT=2,FLAG_TEMP=4,FLAG_INVALID=8;
	const FLAG_COPY=16,FLAG_BRANCH=32,FLAG_FOR_MODERATION=64;
	static protected $pdo,$pdo_array=array();
	static public function init($mode='main',$db=_CONF_DB_MAIN){
		if (!(_CONF_DB_TYPE && $db) ) {
			self::$pdo=self::$pdo_array[$mode]=new dummy_pdo;
			return false;
		}
		if (isset(self::$pdo_array[$mode])) return self::$pdo_array[$mode];
		// construct PDO object
		$dsn=_CONF_DB_TYPE.':'.$db;
		$user=defined('_CONF_DB_MAIN_USER')?_CONF_DB_MAIN_USER:null;
		$passwd=defined('_CONF_DB_MAIN_PASSWD')?_CONF_DB_MAIN_PASSWD:null;
		try {
			$pdo=new PDO($dsn,$user,$passwd);
		} catch (PDOException $e) {
			//jerror::quit('DB-connection falied.');
			$pdo=new dummy_pdo;
		}
		// register user functions
		sqlfunc::register($pdo);
		// return object
		self::$pdo_array[$mode]=$pdo;
		if ($mode=='main') self::$pdo=$pdo;
	}
	/**
	 * @return PDO
	 */
	static public function pdo($mode=false) {
		if ($mode==false || !isset(self::$pdo_array[$mode])) return self::$pdo;
		return self::$pdo_array[$mode];
	}
	static public function select_pdo($mode='main'){
		if (isset(self::$pdo_array[$mode])) self::$pdo=self::$pdo_array[$mode];
		else self::$pdo=self::$pdo_array['main'];
	}
	/**
	 * @return array
	 */
	static public function pdo_objects(){
		return self::$pdo_array;
	}
	static public function sqlfunc_ExtractValue($xml_frag, $xpath_expr){
		// Note that only relative path for $xpath_expr is allowed.
		if (!preg_match("/<$xpath_expr>([\s\S]*)</$xpath_expr>/",$xml_frag,$m)) return '';
		return strtr($m[1],array('&amp;'=>'&','&lt;'=>'<','&gt;'=>'>'));
		/* $xml=new SimpleXMLElement($xml_frag);
		return $xml->$xpath_expr; //*/
	}
	static public function sqlfunc_UpdateXML($xml_target, $xpath_expr, $new_text){
		// Note that only relative path for $xpath_expr is allowed.
		$xml=new SimpleXMLElement($xml_target);
		$xml->$xpath_expr=$new_text;
		return $xml->asXML();
	}
	static public function quote($text){
		return self::$pdo->quote($text);
	}
	/**
	 * sql::fill_query privides the way to fill SQL query string with values.
	 * This method mainly used to change the key directly, 
	 * for example: "SELECT * FROM <%table%> WHERE id=<%id%>", 
	 * with: array('table'=>$table_name) // note <%id%> will be used in sql::query
	 * @param string $text
	 * @param array $data
	 */
	static public function fill_query($text,$data){
		if (!is_array($data)) $data=array($data);
		return self::fill_sql($text,$data);
	}
	static private function fill_sql($text,&$data,$prepared=false){
		static $search=array(
			'/(?:\'[^\']*?\'|"["]*?")/',
			'/(?:<%([a-zA-Z0-9_]+)%>|<%key:[a-zA-Z0-9_]+%>)/',
			'/<%(const):([a-zA-Z0-9_]+)%>/',
			'/<%(const):([a-zA-Z0-9_]+::[a-zA-Z0-9_]+)%>/');
		static $replace=self::class.'::fill_sql_cb';
		self::fill_sql_cb(false,$data,$prepared); // Initialize callback function.
		$query=preg_replace_callback($search,$replace,$text);
		if ($prepared) {
			$used=self::fill_sql_cb(false,'used');
			$newdata=array();
			foreach ($used as $key) {
				if (is_array($data[$key])) {
					foreach ($data[$key] as $key2=>$value) {
						$newdata[":data_{$key}_{$key2}"]=$value;
					}
				} else {
					$newdata[":data_{$key}"]=$data[$key];
				}
			}
			$data=$newdata;
		}
		return $query;
	}
	static private function fill_sql_cb($m,$data=false,$prepared=false){
		static $sdata,$used;
		if (!is_array($m)) {
			switch ($data) {
				case 'used':
					return $used;
				default:
					// Initialize
					$sdata=$used=array();
					foreach ($data as $key=>$value) {
						if (is_array($value)) {
							$key_array=$value_array=array();
							foreach($value as $key2=>$each) {
								$key_array[]=preg_replace('/[^a-zA-Z0-9_]/','',$key2);
								if ($prepared) $value_array[]=":data_{$key}_{$key2}";
								else $value_array[]=self::quote($each);
							}
							$sdata["<%{$key}%>"]=implode(',',$value_array);
							$sdata["<%key:{$key}%>"]=implode(',',$key_array);
						} else {
							if ($prepared) $sdata["<%{$key}%>"]=":data_{$key}";
							else $sdata["<%{$key}%>"]=self::quote($value);
						}
					}
					return;
			}
		}
		switch (count($m)) {
			case 3: // <%const:xxx%>, <%const:xxx::xxx%>
				return defined($m[2]) ? self::quote(constant($m[2])) : $m[0];
			default:// 'xxx', "xxx", <%xxx%>, <%key:xxx%>
				if (!isset($sdata[$m[0]])) return $m[0];
				if (isset($m[1])) $used[]=$m[1];
				return $sdata[$m[0]];
		}
	}
		/**
	 * sql::query provides general method for executing SQLite query.
	 * $query must be like "SELECT * FROM jeans_item WHERE id=<%id%>".
	 * $array must be like array('id'=>$id).
	 * @param string $query
	 * @param array $data
	 * @return PDOStatement
	 */
	static public function query($query,$data=false,$mode=PDO::FETCH_ASSOC){
		if ($data===false) $data=array();
		elseif (!is_array($data)) $data=array($data);
		$query=self::fill_sql($query,$data,true);
		$obj=self::$pdo->prepare($query);
		if (!is_object($obj)) {
			$e=self::$pdo->errorInfo();
			$e['query']=$query;
			if (error_reporting() & E_WARNING) jerror::show('<%query%><br /><%2%> (<%0%> <%1%>)<br />',$e);
		} else {
			$obj->setFetchMode($mode);
			$obj->execute($data);
		}
		self::select_pdo('main');
		return $obj;
	}
	static public function count_query($query,$data=false){
		$query=preg_replace('/^([^\'^"`]|\'[^\']*\'|"[^"]*"|`[^`]*`)+?\sFROM\s/i','',$query);
		$query=preg_replace('/^([^\'^"`]*?)\s(NATURAL\s+)?(LEFT\s+|LEFT\s+OUTER\s+|LEFT\s+|LEFT\s+)?JOIN\s([^\'^"`]*?)\sWHERE\s/i','$1 WHERE ',$query);
		$query=preg_replace('/\sLIMIT\s([^\'^"`]|\'[^\']*\'|"[^"]*"|`[^`]*`)+?$/i','',$query);
		$res=sql::query('SELECT COUNT(*) as result FROM '.$query,$data);
		if (!$res) return false;
		$result=0;
		while ($row=$res->fetch()) $result+=$row['result'];
		return $result;
	}
	static public function begin($mode='main'){
		self::pdo($mode)->beginTransaction();
	}
	static public function commit($mode='main'){
		self::pdo($mode)->commit();
	}
	static public function convert_xml(&$row,$type=false,$prefix=false){
		$regex=($prefix===false)? false : '/^'.preg_quote($prefix.'_','/').'/';
		if (!empty($row['xml'])) {
			if ($type) $default=self::xml_default($type);
			elseif (isset($row['xtable'])) $default=self::xml_default($row['xtable']);
			else $default=array();
			$xml=new SimpleXMLElement($row['xml']);
			foreach ($xml as $key=>$value) {
				unset($default[$key]);
				if ( (!$regex || preg_match($regex,$key)) && !isset($row[$key]) ) $row[$key]=(string)$value;
			}
			foreach ($default as $key=>$value) {
				if ( (!$regex || preg_match($regex,$key)) && !isset($row[$key]) ) $row[$key]=$value;
			}
		}
		unset($row['xml']);
		return $row;
	}
	static public function xml_default($type){
		static $cache;
		if (!isset($cache)) {
			$cache=array();
			$query='SELECT name,defvalue,configtype FROM jeans_config_desc WHERE NOT configtype="global"';
			$res=sql::query($query);
			while($row=$res->fetch()){
				if (preg_match('/^(.*)_/',$row['configtype'],$m)) $row['configtype']=$m[1];
				$defvalue=$row['defvalue'];
				if (0<strlen($defvalue) && preg_match('/^_[A-Z0-9_]+$/',$defvalue)) $defvalue=self::translate($defvalue);
				if (!isset($cache[$row['configtype']])) $cache[$row['configtype']]=array();
				$cache[$row['configtype']][$row['name']]=$defvalue;
			}
		}
		if (isset($cache[$type])) return $cache[$type];
		else return array();
	}
	/*
	 * shutdown() method is assinged to execute shutdown queries.
	 */
	static private $shutdown_queries=array();
	static public function register_shutdown_query($query,$data,$priority='A'){
		static $i=0;
		if (preg_match('/\sjeans_([a-z]+)\s/i',$query,$m)) $key=$m[1];
		else $key='';
		self::$shutdown_queries[strtoupper($priority.$key).($i++)]=array($query,$data);
	}
	static public function shutdown(){
		// Execute shutdown queries.
		ksort(self::$shutdown_queries);
		sql::begin();
		foreach(self::$shutdown_queries as &$each){
			list($query,$data)=$each;
			self::query($query,$data);
		}
		sql::commit();
		self::$shutdown_queries=array();
	}
	/*
	 * Skin tags follow.
	 */
	static public function tag_quote(&$data,$key){
		if (isset($data[$key])) self::echo_html(self::quote($data[$key]));
	}
}
class sqlfunc {
	static public function register($pdo){
		$pdo->sqliteCreateFunction('ExtractValue',array('sqlfunc','ExtractValue'),2);
		$pdo->sqliteCreateFunction('UpdateXML',array('sqlfunc','UpdateXML'));
		$pdo->sqliteCreateFunction('base64decode','base64_decode');
	}
	static public function ExtractValue($xml_frag, $xpath_expr){
		// Note that only relative path for $xpath_expr is allowed.
		/* $xml=new SimpleXMLElement($xml_frag);
		return $xml->$xpath_expr; //*/
		if (!preg_match("#<$xpath_expr>([\s\S]*)</$xpath_expr>#",$xml_frag,$m)) return '';
		return strtr($m[1],array('&amp;'=>'&','&lt;'=>'<','&gt;'=>'>'));
	}
	static public function UpdateXML(&$xml_target, $xpath_expr, $new_text){
		// Note that only relative path for $xpath_expr is allowed.
		$args=func_get_args();
		array_shift($args); //$xml_target
		if (strlen($xml_target)==0) $xml_target=_XML_BLANC;
		$xml=new SimpleXMLElement($xml_target);
		while(2<=count($args)){
			$xpath_expr=array_shift($args);
			$new_text=array_shift($args);
			if ($new_text===null) unset($xml->$xpath_expr);
			else $xml->$xpath_expr=$new_text;
		}
		return $xml->asXML();
	}
}

class nonmember extends jeans {
	static public function action_get_logout(){
		// Do nothing.
	}
	// Following methods will be implemented by __callstatic when PHP 5.3 will be used.
	static public function logged_in(){
		return self::debug();
	}
	static public function is_admin(){
		return self::debug();
	}
	static public function if_loggedin(){
		return self::debug();
	}
	static public function if_isadmin(){
		return self::debug();
	}
	static public function tag_setting(){
		self::debug();
	}
	static public function setting(){
		return self::debug();
	}
	static private function debug(){
		/*static $cache;
		if (isset($cache)) return $cache;
		if (!defined('_CONF_DEBUG_MODE')) $cache=false;
		elseif(!isset($_GET['start_debug'])) $cache=false;
		elseif($_SERVER['HTTP_HOST']!='localhost') $cache=false;
		else $cache=true;
		return $cache;//*/
		return false;
	}
}
class dummy_pdo {
	public function __call($name,$args){
		switch($name){
			case 'prepare':
				return new self;
			default:
			return false;
		}
	}
}
