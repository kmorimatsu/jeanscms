<?php
/*
 * Jeans CMS (GPL license)
 * $Id: plugin.php 224 2010-06-29 20:26:15Z kmorimatsu $
 */

class plugin extends jeans {
	static private $install=false;
	static public function set_install($class){
		if (!self::plugin_list($class)) self::$install=strtolower($class);
	}
	static public final function plugin_list($class=false){
		static $plugins;
		$class=strtolower($class);
		if (!isset($plugins)) {
			$plugins=array();
			$res=sql::query('SELECT id FROM jeans_plugin ORDER BY sequence ASC');
			while ($row=$res->fetch()) $plugins[]=$row['id'];
		}
		if ($class===false) return $plugins;
		else return in_array($class,$plugins) || $class==self::$install;
	}
	static public final function plugin_filename($class){
		static $cache;
		$class=strtolower($class);
		if (!isset($cache)) {
			$d=dir(_DIR_PLUGINS);
			while (false!==($file_name=$d->read())) {
				if (!preg_match('/^(jp_[a-zA-Z0-9]+)\.php$/',$file_name,$m)) continue;
				$cache[strtolower($m[1])]=$file_name;
			}
		}
		if (!self::plugin_list($class)) return false;
		if (!isset($cache[$class])) return false;
		return $cache[$class];
	}
	static private $option_objects=array();
	static public final function instance($class){
		// Only core::autoload can call this method.
		// Initialize some functions (these lines will be removed after shifting to PHP 5.3).
		foreach(array('id','name','short_name','url_admin','dir_admin','url_admin_page') as $method) {
			call_user_func(array($class,$method),$class);
		}
		// Initialize plugin option object
		$obj=new plugin_option($class);
		call_user_func(array($class,'option'),$obj);
		self::$option_objects[]=$obj;
		if ($class==self::$install) call_user_func(array($class,'install'));
		else call_user_func(array($class,'init'));
	}
	/**
	 * @return plugin_option
	 */
	static public final function option($mode='global',$id=0){
		static $cache;
		// Initialize $cache. See instance() method.
		if (is_object($mode) && !isset($cache)) $cache=$mode;
		switch($mode){
			case 'group': case 'subgroup': case 'item': case 'comment': case 'member': case 'guest':
				return $cache->$mode($id);
			case 'global':
			default:
				return $cache;
		}
	}
	static public function shutdown_extra(){
		// shutdown() method cannot be used here because it must be clean for jp_xxx classes.
		foreach(self::$option_objects as $key=>$obj) $obj->shutdown();	
	}
	static public function init(){}
	static public function install(){}
	static public function uninstall(){}
	static public function name(){
		// "static::" will be used after shifting to PHP 5.3
		static $cache;
		if (isset($cache)) return $cache;
		$cache=func_get_arg(0);
	}
	static public function author(){
		return 'Undefined';
	}
	static public function url(){
		return 'Undefined';
	}
	static public function desc(){
		return 'Undefined';
	}
	static public function version(){
		return '0.0';
	}
	static public function events(){
		return array();
	}
	static public final function id(){
		// "static::" will be used after shifting to PHP 5.3
		static $cache;
		if (isset($cache)) return $cache;
		$cache=strtolower(func_get_arg(0));
	}
	static protected function short_name(){
		// "static::" will be used after shifting to PHP 5.3
		static $cache;
		if (isset($cache)) return $cache;
		$cache=strtolower(substr(func_get_arg(0),3));
	}
	static public function url_admin(){
		// "static::" will be used after shifting to PHP 5.3
		static $cache;
		if (isset($cache)) return $cache;
		$cache=_CONF_URL_PLUGINS.call_user_func(array(func_get_arg(0),'short_name')).'/';
	}
	static public function dir_admin(){
		// "static::" will be used after shifting to PHP 5.3
		static $cache;
		if (isset($cache)) return $cache;
		$cache=_DIR_PLUGINS.call_user_func(array(func_get_arg(0),'short_name')).'/';
	}
	static public function url_admin_page(){
		// "static::" will be used after shifting to PHP 5.3
		static $cache;
		if (isset($cache)) return $cache;
		if (!member::logged_in()) $key='guest_padmin';
		elseif (!member::is_admin()) $key='member_padmin';
		else $key='padmin';
		$cache=_CONF_URL_ADMIN."?$key=jp_".call_user_func(array(func_get_arg(0),'short_name'));
	}
}

class plugin_option {
	/*
	 * Construction methods follow.
	 * All values must be private.
	 * See __get, __set, and iteration methods.
	 */
	private $class,$mode,$id;
	private $modified=false;
	private $objects=array();
	public function __construct($class,$mode='global',$id=0){
		$this->class=strtolower($class);
		$this->mode=$mode;
		$this->id=$id;
	}
	private function option($mode,$id){
		if (!isset($this->objects[$mode])) {
			$this->objects[$mode]=new self($this->class,$mode,$id);
		}
		return $this->objects[$mode];
	}
	public function group($id){
		return $this->option('group',$id);
	}
	public function subgroup($id){
		return $this->option('subgroup',$id);
	}
	public function item($id){
		return $this->option('item',$id);
	}
	public function comment($id){
		return $this->option('comment',$id);
	}
	public function member($id){
		return $this->option('member',$id);
	}
	public function guest($id){
		return $this->option('guest',$id);
	}
	/*
	 * Option-creating and deleting methods follow.
	 */
	public function create($name,$desc=false,$type='text',$defvalue='',$extra='',$sequence=0){
		$name=strtolower($name);
		if ($desc===false) $desc=$name;
		$sqlname=$this->mode=='global' ? $name:$this->class.'_'.$name;
		// Update DB
		$query='INSERT OR REPLACE INTO jeans_config_desc (<%key:row%>) VALUES (<%row%>);';
		$row=array('name'=>$sqlname,'desc'=>$desc,'type'=>$type,
			'defvalue'=>$defvalue,'extra'=>$extra,
			'configtype'=>$this->mode,'owner'=>$this->class,'sequence'=>$sequence);
		sql::register_shutdown_query($query,array('row'=>$row));
		// Update values in this process.
		$this->initiate();
		if (!isset($this->values[$name])) $this->values[$name]=$defvalue;
	}
	public function delete($name){
		$name=strtolower($name);
		$sqlname=$this->mode=='global' ? $name:$this->class.'_'.$name;
		$query='DELETE FROM jeans_config_desc WHERE name=<%name%> AND configtype=<%mode%> AND owner=<%class%>';
		$array=array('name'=>$sqlname,'mode'=>$this->mode,'class'=>$this->class);
		sql::register_shutdown_query($query,$array);
		switch($this->mode){
			case 'global':
				$query='DELETE FROM jeans_config WHERE name=<%name%> AND type=<%mode%> AND owner=<%class%>';
				break;
			default: 
				$query=sql::fill_query('UPDATE <%0%> SET xml=UpdateXML(xml,<%name%>,NULL)',$this->table_name());
				break;
		}
		sql::register_shutdown_query($query,$array);
	}
	/*
	 * Option-loading and saving methods follow.
	 */
	private $values,$orgvalues;
	private function initiate(){
		if (!isset($this->values)) {
			$this->values=$this->default_values();
			$this->load();	
			$this->orgvalues=$this->values;
		}
	}
	public function shutdown(){
		if ($this->modified) {
			foreach($this->orgvalues as $key=>&$value){
				if ($value==$this->values[$key]) unset($this->values[$key]);
			}
			if (count($this->values)) $this->save();
			$this->modified=false;
		}
		// Shutdown child objects
		foreach($this->objects as $obj) $obj->shutdown();
	}
	private function load_global(){
		$query='SELECT name, value FROM jeans_config WHERE type="global" AND owner=<%0%> AND contextid=0';
		$res=sql::query($query,$this->class);
		while ($row=$res->fetch()) {
			$this->values[$row['name']]=$row['value'];
		}
	}
	private function save_global(){
		$class=$this->class;
		foreach($this->values as $key=>&$value){
			$query='INSERT OR REPLACE INTO jeans_config (<%key:row%>) VALUES (<%row%>);';
			$row=array('type'=>'global','owner'=>$class,'name'=>$key,'contextid'=>'0','value'=>$value);
			sql::register_shutdown_query($query,array('row'=>$row));
		}
	}
	private function load(){
		if ($this->mode=='global') return $this->load_global();
		$query=sql::fill_query('SELECT xml FROM <%0%> WHERE id=<%id%> LIMIT 1',$this->table_name());
		$row=sql::query($query,array('id'=>$this->id))->fetch();
		if ($row) {
			// Receive values from XML.
			sql::convert_xml($row,$this->mode,$this->class);
			// Remove prefix.
			$values=array();
			$start=strlen($this->class)+1;
			foreach ($row as $key=>$value) $values[substr($key,$start)]=$value;
			// Set values.
			$this->values=array_merge($this->values,$values);
		}
	}
	private function save(){
		if ($this->mode=='global') return $this->save_global();
		$class=$this->class;
		$row=array();
		foreach ($this->values as $key=>&$value) {
			$row[]=$class.'_'.$key;
			$row[]=$value;
		}
		$query=sql::fill_query('UPDATE <%0%> SET xml=UpdateXML(xml,<%row%>) WHERE id=<%id%>',$this->table_name());
		$array=array('id'=>$this->id,'row'=>$row);
		sql::register_shutdown_query($query,$array);
	}
	/*
	 * Option-getting and setting methods follow.
	 */
	public function __get($name){
		if (!$this->__isset($name)) return false;
		return $this->values[$name];
	}
	public function __set($name,$value){
		if (!$this->__isset($name)) return false;
		$this->values[$name]=$value;
		return $this->modified=true;
	}
	public function __isset($name){
		$this->initiate();
		return isset($this->values[$name]);
	}
	public function __unset($name){
		// Do nothing
	}
	/* 
	 * Iterator methods follow.
	 */
	private $iterator=array();
	private $remaining;
	public function rewind() {
		$this->initiate();
		$this->remaining=count($this->iterator=$this->values);
		return reset($this->iterator);
	}
	public function current() {
		return current($this->iterator);
	}
	public function key() {
		return key($this->iterator);
	}
	public function next() {
		$this->remaining--;
		return next($this->iterator);
	}
	public function valid() {
		return 0<$this->remaining;
	}
	/*
	 * Other private methods follow
	 */
	private function default_values(){
		static $cache=array();
		$class=$this->class;
		$type=$this->mode;
		if (!isset($cache[$class])) {
			$cache[$class]=array();
			$query='SELECT configtype,name,defvalue FROM jeans_config_desc WHERE owner=<%0%>';
			$res=sql::query($query,$class);
			while ($row=$res->fetch()) {
				if (!isset($cache[$class][$row['configtype']])) $cache[$class][$row['configtype']]=array();
				$cache[$class][$row['configtype']][$row['name']]=$row['defvalue'];
			}
		}
		if (isset($cache[$class][$type])) return $cache[$class][$type];
		else return false;
	}
	private function table_name($mode=false){
		if (!$mode) $mode=$this->mode;
		switch($mode){
			case 'member': case 'guest':
				return 'jeans_member';
			case 'group': case 'subgroup': 
				return 'jeans_group';
			case 'item': case 'comment':
				return 'jeans_'.$mode;
			case 'grobal':
			default:
				return 'jeans_config';
		}
	}
}