<?php
/*
 * Jeans CMS (GPL license)
 * $Id: admin_plugin2.php 365 2017-10-25 20:06:22Z kmorimatsu $
 */

class admin_plugin2 extends jeans {
	static public function init(){
	}
	static public function tag_parse(&$data){
		if (isset($_GET['guest_padmin'])) {
			$plugin=$_GET['guest_padmin'];
			$skin='guest_skin.inc';
		} elseif (isset($_GET['member_padmin'])) {
			if (!member::if_loggedin()) jerror::quit('_ADMIN_NO_PERMISSION');
			$plugin=$_GET['member_padmin'];
			$skin='member_skin.inc';
		} elseif (isset($_GET['padmin']) && member::is_admin()) {
			if (!member::is_admin()) jerror::quit('_ADMIN_NO_PERMISSION');
			$plugin=$_GET['padmin'];
			$skin='skin.inc';
		} else return;
		if (substr($plugin,0,3)!='jp_' || !plugin::plugin_list($plugin)) jerror::quit('_ADMIN_PLUGIN_NOT_FOUND');
		view::tag_parse($data,'/jp/'.substr($plugin,3)."/$skin");
	}
}