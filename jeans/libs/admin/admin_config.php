<?php
/*
 * Jeans CMS (GPL license)
 * $Id: admin_config.php 365 2017-10-25 20:06:22Z kmorimatsu $
 */

class admin_config extends jeans {
	static public function init(){
		// Load the language file
		$warning=self::translate('_ADMIN_NO_PERMISSION');
		// Only superadmin can use this class
		if (!member::is_admin()) jerror::quit($warning);
	}
	static public function tag_conflist(&$data,$skin=false){
		static $cache;
		if (isset($data['id']) && plugin::plugin_list($data['id'])) {
			// plugin
			$owner=$data['id'];
			$option=call_user_func(array($owner,'option'));
		} else $owner='jeans';
		if (!isset($cache)) {
			$post=admin::item_from_post();
			$cache=array();
			$query='SELECT * FROM jeans_config_desc WHERE configtype="global" AND owner=<%0%> ORDER BY sequence ASC';
			$res=sql::query($query,$owner);
			while ($row=$res->fetch()) {
				if ($owner=='jeans'){
					if (defined('_CONF_'.$row['name'])) $value=constant('_CONF_'.$row['name']);
					else $value=$row['defvalue'];
				} else {
					$key=$row['name'];
					$value=$option->$key;
				}
				if (isset($post[$row['name']])) $row['value']=$post[$row['name']];
				else $row['value']=$value;
				$row['desc']=self::translate($row['desc']);
				$row['name'].='_text';
				$cache[]=$row;
			}
		}
		view::show_using_array($data,$cache,$skin);
	}
	static public function action_post_edit(){
		$post=admin::item_from_post();
		if (!$post) return;
		if (isset($_GET['poption'])){
			if (plugin::plugin_list($_GET['poption'])) $owner=$_GET['poption'];
			else return;
		} else $owner='jeans';
		sql::begin();
		$query='INSERT OR REPLACE INTO jeans_config (<%key:row%>) VALUES (<%row%>)';
		foreach($post as $key=>$value){
			$row=array('type'=>'global','owner'=>$owner,'name'=>$key,'contextid'=>0,'value'=>$value);
			sql::query($query,array('row'=>$row));
		}
		sql::commit();
		if (isset($_GET['poption'])) {
			core::set_cookie('note_text',_ADMIN_PLUGIN_OPTIONS_SAVED,0);
			core::redirect_local(_CONF_SELF.'?page=plugin');	
		} else {
			core::set_cookie('note_text',_ADMIN_CONF_SAVED,0);
			core::redirect_local(_CONF_SELF);	
		}
	}
}