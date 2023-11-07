<?php
/*
 * Jeans CMS (GPL license)
 * $Id: admin_plugin.php 365 2017-10-25 20:06:22Z kmorimatsu $
 */

class admin_plugin extends jeans {
	static public function init(){
		// Load the language file
		$warning=self::translate('_ADMIN_NO_PERMISSION');
		// Only superadmin can use this class
		if (!member::is_admin()) jerror::quit($warning);
		// Register SQL function
		sql::pdo()->sqliteCreateFunction('libs_admin_plugin_deleteunusedplugins',array('admin_plugin','cb_action_post_deleteunusedoptions'),1);
	}
	static private function info($class=false){
		static $cache;
		if (!isset($cache)){
			$cache=array();
			$res=sql::query('SELECT * FROM jeans_plugin ORDER BY sequence ASC');
			while ($row=$res->fetch()) $cache[$row['id']]=$row;
		}
		if ($class===false) return $cache;
		$class=strtolower($class);
		if (isset($cache[$class])) return $cache[$class];
		else return false;
	}
	static public function tag_list(&$data,$skin=false){
		$query='SELECT 
			p.id as id, 
			p.name as name, 
			p.desc as desc, 
			p.author as author, 
			p.version as version, 
			p.url as url, 
			p.filemtime as filemtime, 
			d.configtype as options 
			FROM jeans_plugin as p 
			LEFT JOIN jeans_config_desc as d 
			ON p.id=d.owner AND d.configtype="global" AND NOT d.type="hidden"
			GROUP BY p.id ORDER BY p.sequence ASC';
		$cb=array('admin_plugin','cb_tag_list');
		view::show_using_query($data,$query,array(),$skin,$cb);
	}
	static public function cb_tag_list(&$row){
		static $events,$files;
		if (!isset($events)) {
			$events=$files=array();
			// Check events
			$query='SELECT event,class FROM jeans_event';
			$res=sql::query($query);
			while ($row2=$res->fetch()) {
				if (!isset($events[$row2['class']])) $events[$row2['class']]=array();
				$events[$row2['class']][]=$row2['event'];
			}
			// Check files
			$dir=dir(_DIR_PLUGINS);
			while ($file=$dir->read()) {
				if (preg_match('/^(jp_[a-zA-Z0-9]+)\.php$/',$file,$m)) $files[]=strtolower($m[1]);
			}
		}
		// Check the file
		if (!in_array($row['id'],$files)) {
			$row['notfound']=1;
			return;	
		}
		if (filemtime(_DIR_PLUGINS.plugin::plugin_filename($row['id']))!=$row['filemtime']) {
			self::refresh_plugin_info($row['id']);
			$info=self::get_plugin_info($row['id']);
			foreach (array('name','desc','author','version','url') as $method) {
				$row[$method]=$info[$method];
			}
			$events[$row['id']]=$info['events'];
		}
		// Check events
		if (isset($events[$row['id']])) $row['events']=implode(', ',$events[$row['id']]);
		// Check admin area
		if (self::local_file_exists(_DIR_PLUGINS,substr(strtolower($row['id']),3).'/skin.inc')) $row['admin']=1;
	}
	static public function tag_install(&$data,$skin){
		$array=array();
		$dir=dir(_DIR_PLUGINS);
		while ($file=$dir->read()) {
			if (!preg_match('/^(jp_[a-zA-Z0-9]+)\.php$/',$file,$m)) continue;
			if (self::info($m[1])) continue;
			$array[]=array('name'=>$m[1]);
		}
		view::show_using_array($data,$array,$skin);
	}
	static public function action_post_install(){
		$plugin=$_POST['plugin'];
		if (!preg_match('/^[jp_[a-zA-Z0-9]+$/',$plugin)) return jerror::note('Invalid plugin name');
		if (!self::local_file_exists(_DIR_PLUGINS,"$plugin.php")) return jerror::note('Plugin file not found.');
		plugin::set_install($plugin);
		if (class_exists($plugin)) {
			self::refresh_plugin_info($plugin);
			core::set_cookie('note_text',_ADMIN_PLUGIN_INSTALL_SUCESS,0);
			core::redirect_local(_CONF_SELF.'?page=plugin');
		}
		jerror::note(_ADMIN_PLUGIN_INSTALL_FAILED);
		
	}
	static public function action_post_deleteunusedoptions(){
		if (!isset($_POST['sure']) || !$_POST['sure']) return;
		// Initialize $plugins data
		$plugins=array();
		foreach (self::info() as $row) $plugins[]=$row['id'];
		$plugins[]='jeans';
		self::cb_action_post_deleteunusedoptions($plugins);
		// Update DB
		sql::begin();
		sql::query('DELETE FROM jeans_event_group');
		$query='DELETE FROM jeans_config WHERE owner LIKE "jp_%" AND NOT owner IN (<%plugins%>)';
		sql::query($query,array('plugins'=>$plugins));
		foreach (array('group','item','comment','member') as $table) {
			$query=sql::fill_query('UPDATE <%table%> SET xml=libs_admin_plugin_deleteunusedplugins(xml)',array('table'=>"jeans_$table"));
			sql::query($query);
		}
		sql::commit();
		jerror::note('_ADMIN_PLUGIN_DELETEUNUSEDOPTIONS_DONE');
	}
	static public function cb_action_post_deleteunusedoptions(&$xmltext){
		static $plugins;
		if (is_array($xmltext) && !isset($plugins)) {
			$plugins=$xmltext;
			return;
		}
		$xml=new SimpleXMLElement($xmltext);
		foreach ($xml as $key=>$value) {
			if (substr($key,0,3)=='jp_' && !in_array($key,$plugins)) unset($xml->$key);
		}
		return $xml->asXML();
	}
	static private function refresh_plugin_info($plugin){
		// Add plugin to jeans_plugin table
		$info=self::get_plugin_info($plugin);
		$sequence=count(plugin::plugin_list());
		$query='INSERT OR REPLACE INTO jeans_plugin (<%key:row%>) VALUES (<%row%>)';
		$row=array(
			'id'=>$info['id'],
			'name'=>$info['name'],
			'desc'=>$info['desc'],
			'author'=>$info['author'],
			'version'=>$info['version'],
			'url'=>$info['url'],
			'filemtime'=>filemtime(_DIR_PLUGINS.plugin::plugin_filename($plugin)),
			'sequence'=>$sequence);
		sql::register_shutdown_query($query,array('row'=>$row));
		// Add events to jeans_event table
		$plugin=strtolower($plugin);
		sql::register_shutdown_query('DELETE FROM jeans_event WHERE class=<%0%>',$plugin,'A');
		$query='INSERT OR REPLACE INTO jeans_event (event,class) VALUES (<%event%>,<%plugin%>)';
		foreach ($info['events'] as $event) {
			$array=array('event'=>$event,'plugin'=>$plugin);
			sql::register_shutdown_query($query,$array,'B');
		}
	}
	static private function get_plugin_info($plugin){
		static $cache=array();
		if (!isset($cache[$plugin])) {
			$cache[$plugin]=array();
			foreach (array('id','name','desc','author','version','url','events') as $method) {
				$cache[$plugin][$method]=call_user_func(array($plugin,$method));
			}
		}
		return $cache[$plugin];
	}
	static public function tag_options(&$data,$skin=false){
		$info=self::info($_GET['poption']);
		if (!$info) return;
		$data=array_merge($data,$info);
		admin_config::tag_conflist($data,$skin);
	}
}