<?php
/*
 * Jeans CMS (GPL license)
 * $Id: admin_event.php 323 2011-01-19 07:46:51Z kmorimatsu $
 */

class admin_event extends jeans{
	static public function init(){
	}
	static public function tag_quickmenu(&$data,$skin){
		// quick_menu event
		// Each plugin must push an array containing title, url and tooltip values
		$array=array();
		if (member::is_admin()) core::event('quick_menu',array('options'=>&$array),'admin');
		elseif (member::logged_in()) core::event('member_quick_menu',array('options'=>&$array),'admin');
		view::show_using_array($data,$array,$skin);
	}
	static public function tag_postparseplugindesc(&$data){
		static $cache;
		if (!isset($cache)) $cache=core::event('post_parse_plugin_desc',false,'action');
		if (in_array($data['id'],$cache)) {
			$args=array();
			call_user_func_array(array($data['id'],'event_post_parse_plugin_desc'),$args);
		}
	}
}