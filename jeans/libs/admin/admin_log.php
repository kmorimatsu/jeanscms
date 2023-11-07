<?php
/*
 * Jeans CMS (GPL license)
 * $Id: admin_log.php 365 2017-10-25 20:06:22Z kmorimatsu $
 */

class admin_log extends jeans {
	static public function init(){
		// Load the language file
		$warning=self::translate('_ADMIN_NO_PERMISSION');
		// Only superadmin can use this class
		if (!member::is_admin()) jerror::quit($warning);
	}
	static public function tag_list(&$data,$skin=false,$limit=100){
		$offset=isset($_GET['offset'])?(int)$_GET['offset']:0;
		$query='SELECT 
			l.id as id, 
			l.ip as ip, 
			l.referer as referer, 
			l.time as time, 
			l.uri as uri, 
			l.type as type, 
			l.desc as desc, 
			l.owner as owner, 
			m.name as member, 
			m.id as mid 
			FROM jeans_log as l 
			LEFT JOIN jeans_member as m ON l.mid=m.id 
			GROUP BY l.id ORDER BY l.id DESC 
			LIMIT <%limit%> OFFSET <%offset%>';
		$array=array('limit'=>$limit, 'offset'=>$offset);
		$items=sql::count_query($query,$array);
		$data['libs']['page']=array('items'=>$items,'offset'=>$offset,'limit'=>$limit);
		view::show_using_query($data,$query,$array,$skin);
	}
	static public function action_post_delete(){
		sql::query('DELETE FROM jeans_log');
		jerror::note('_ADMIN_LOG_DELETED');
		if (!empty($_POST['vacuum'])) {
			foreach (sql::pdo_objects() as $mode=>$pdo) {
				sql::select_pdo($mode);
				sql::query('VACUUM;');
			}
			jerror::note('_ADMIN_VACUUM_DONE');
		}
	}
}