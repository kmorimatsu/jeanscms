<?php
/*
 * Jeans CMS (GPL license)
 * $Id: admin.php 365 2017-10-25 20:06:22Z kmorimatsu $
 */

class admin extends jeans{
	static public function init(){
		// Load the language file
		// It's not required to load the language file again in each admin classes
		self::translate('_ADMIN_NAME');
	}
	static private $custom_admin_page=false;
	static public function register_page($array){
		// Format: $_GET['page'] => page name
		// The page name must start from member_ or guest_ if accepted by non-admin user.
		if (is_array($array) && !self::$custom_admin_page) self::$custom_admin_page=$array;
	}
	static public function selector($skin=false){
		// Determine admin skin
		if (!$skin) $skin=member::setting('admin_skin');
		if ( !($skin && self::local_file_exists(_DIR_SKINS,$skin)) ) {
			$skin=_CONF_DEFAULT_ADMIN_SKIN;
			if (!self::local_file_exists(_DIR_SKINS,$skin)) $skin='/admin/adminskin.inc';
		}
		$parent_skin=false;
		$data=false;
		// Decide which page to show
		foreach(array('reactivate','poption','padmin','guest_padmin', 'member_padmin') as $key){
			if (!isset($_GET[$key])) continue;
			$page=$key;
			break;
		}
		if (!isset($page)) $page=isset($_GET['page'])?$_GET['page']:'main';
		// TODO: check the authority here.
		switch($page){
			case 'main': case 'edititem': case 'itemlist': case 'subgrouplist': case 'groupsetting':
			case 'config': case 'db': case 'commentlist': case 'editcomment': case 'batch':
			case 'newgroup': case 'membersetting': case 'memberlist': case 'loginsetting':
			case 'plugin': case 'poption': case 'reactivate': case 'forgotpassword': case 'media':
			case 'log': case 'map': case 'addmember': case 'padmin': case 'guest_padmin': case 'member_padmin':
				break;
			default:
				if (isset(self::$custom_admin_page[$page])) {
					$page=self::$custom_admin_page[$page];
					break;
				}
				$page='main';
				jerror::note(_ADMIN_FEATURE_NOT_IMPLEMENTED);
		}
		switch($page){
			case 'main': case 'reactivate': case 'forgotpassword':
				// Accept everyone
				break;
			case 'commentlist': case 'batch': case 'membersetting': case 'loginsetting': case 'media':
			case 'editcomment':
				// Accept admin and member 
				if (!member::logged_in()) jerror::quit(_ADMIN_NO_PERMISSION);
				break;
			default:
				if (substr($page,0,6)=='guest_') break;
				elseif (substr($page,0,7)=='member_' && member::logged_in()) break;
				// Only superadmin can go ahead
				if (!member::is_admin()) jerror::quit(_ADMIN_NO_PERMISSION);
		}
		$template=array('libs'=>array('admin'=>array(
				'page'=>$page, 
				'custom'=>isset($_GET['custom'])?$_GET['custom']:''
			)));
		view::parse_skin($skin,$parent_skin,$data,$template);
	}
	static public function item_from_post($table=false){
		// The argument, $table is used to construct XML data from POST.
		if (!count($_POST)) return false;
		$post=$_POST;
		unset($post['action']);
		unset($post['ticket']);
		$row=array();
		foreach($post as $key=>$value){
			if (substr($key,-5)=='_text') {
				$row[substr($key,0,-5)]=$value;
				unset($post[$key]);
			}
		}
		$post=array_merge($post,$row);
		if (!$table) return $post;
		// Fetch XML and flags from DB if available
		if (!empty($post['id'])) {
			$query=sql::fill_query('SELECT xml,flags FROM <%0%> WHERE id=<%id%>',$table);
			$row=sql::query($query,array('id'=>$post['id']))->fetch();
			if ($row) {
				$xml=new SimpleXMLElement($row['xml']);
				$flags=$row['flags'];
			}
		}
		if (!isset($xml)) $xml=new SimpleXMLElement(_XML_BLANC);
		if (!isset($flags)) $flags=0;
		// Merge all flags
		if (isset($post['flags']['update']) && is_array($post['flags']['update'])) {
			foreach($post['flags']['update'] as $key=>$value){
				$mask=(int)$key;
				$flags=($flags & (~$mask) ) | (empty($post['flags']['value'][$key])?0:$mask); 
			}
			$post['flags']=$flags;
		} else unset($post['flags']);
		// Create XML from posts
		$query=sql::fill_query('PRAGMA table_info(<%0%>)',$table);
		$res=sql::query($query);
		$columns=array();
		while($row=$res->fetch()){
			if (!isset($post[$row['name']])) continue;
			$columns[$row['name']]=$post[$row['name']];
			unset($post[$row['name']]);
		}
		foreach ($post as $key=>$value) $xml->$key=$value;
		$columns['xml']=$xml->asXML();
		// All done. Return constructed data.
		return $columns;
	}
	static public function tag_callback(&$data,$event){
		$args=func_get_args();
		array_shift($args); //&$data
		array_shift($args); //$event
		$arg=array('data'=>&$data);
		while (1<count($args)) {
			$key=array_shift($args);
			$value=array_shift($args);
			$arg[$key]=$value;
		}
		core::event($event,$arg,'admin');
	}
}