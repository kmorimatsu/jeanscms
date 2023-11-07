<?php
/*
 * Jeans CMS (GPL license)
 * $Id: admin_item.php 365 2017-10-25 20:06:22Z kmorimatsu $
 */

class admin_item extends jeans{
	static public function init(){
		// Load the language file
		$warning=self::translate('_ADMIN_NO_PERMISSION');
		// Only superadmin can use this class
		if (!member::is_admin()) jerror::quit($warning);
	}
	static public function tag_edititemform(&$data,$skin=false){
		// TODO: support using extended values defined by plugin as XML values.
		if ($skin===false) $skin=$data['skin'];
		$itemid=(int)@$_GET['itemid'];
		if ($itemid==0) {
			$row=array('id'=>0);
			if (isset($_GET['sgid'])) $row['sgid']=$_GET['sgid'];
			$row['time']=gmdate('Y-m-d H:i:s', time());
			$row['author']=member::setting('id');
		} else {
			$query='SELECT * FROM jeans_item WHERE id=<%itemid%> LIMIT 1';
			$array=array('itemid'=>$itemid);
			$row=sql::query($query,$array)->fetch();
		}
		$post=self::item_from_post('item');
		if (isset($post['time'])) $post['time']=gmdate('Y-m-d H:i:s', strtotime($post['time']));
		if ($post) $row=array_merge($row,$post);
		view::show_using_array($data,array($row),$skin);
	}
	static public function tag_edititemformextra(&$data,$skin){
		// TODO: here.
		// TODO: get data from XML in table using $_GET['itemid']
		if (!empty($_GET['itemid'])) {
			$org=sql::query('SELECT xml FROM jeans_item WHERE id=<%0%>',$_GET['itemid'])->fetch();
			if ($org) sql::convert_xml($org,'item');
			else $org=array();
		}
		$res=sql::query('SELECT * FROM jeans_config_desc WHERE configtype="item" ORDER BY owner,sequence ASC');
		$rows=array();
		while($row=$res->fetch()){
			if (isset($data[$row['name']])) $row['value']=$data[$row['name']];
			elseif (isset($org[$row['name']])) $row['value']=$org[$row['name']];
			$rows[]=$row;
		}
		view::show_using_array($data,$rows,$skin);
	}
	static private $narrowby=array('draft'=>false,'mydraft'=>false);
	static public function tag_narrowby() {
		$args=func_get_args();
		array_shift($args); //&$data
		foreach($args as $key) self::$narrowby[$key]=true;
	}
	static public function tag_list(&$data,$skin,$limit=10){
		$gid=isset($_GET['gid'])?(int)$_GET['gid']:0;
		$offset=isset($_GET['offset'])?(int)$_GET['offset']:0;
		$sgid=isset($_GET['sgid'])?(int)$_GET['sgid']:0;
		$mid=isset($_GET['mid'])?(int)$_GET['mid']:0;
		// Narrowing methods follow
		$draft=self::$narrowby['draft']|self::$narrowby['mydraft'];
		if (self::$narrowby['mydraft']) $mid=member::setting('id');
		foreach(self::$narrowby as $key=>$value) self::$narrowby[$key]=false;
		$query='SELECT '.
			'i.id as id, '.
			'i.time as time, '.
			'i.author as author, '.
			'i.title as title, '.
			'i.body as body, '.
			'i.more as more, '.
			'i.gid as gid, '.
			'i.sgid as sgid, '.
			'i.gid as blogid, '.
			'i.sgid as catid, '.
			'i.flags as flags, '.
			'g.name as gname, '.
			'g.desc as gdesc, '.
			's.name as sname, '.
			's.desc as sdesc, '.
			'm.name as aname '.
			'FROM jeans_item as i, jeans_group as g, jeans_group as s, jeans_member as m '.
			'WHERE i.author=m.id '.
			($mid ? 'AND i.author=<%mid%> ':'').
			($gid ? 'AND i.gid=<%gid%> ':'').
			($sgid ? 'AND i.sgid=<%sgid%> ':'').
			($draft ? 'AND (i.flags & <%const:sql::FLAG_DRAFT%>) ':'').
			'AND i.gid=g.id '.
			'AND i.sgid=s.id '.
			'ORDER by time DESC '.
			'LIMIT <%limit%> OFFSET <%offset%>';
		$array=array('gid'=>$gid, 'sgid'=>$sgid, 'mid'=>$mid, 'limit'=>$limit, 'offset'=>$offset);
		$items=sql::count_query($query,$array);
		$data['libs']['page']=array('items'=>$items,'offset'=>$offset,'limit'=>$limit);
		view::show_using_query($data,$query,$array,$skin,array('blog','_blog_cb'));
	}
	static private $ribbon=array();
	static public function tag_addtoribbon(&$data,$name=false,$desc=false,$script=false,$type='text',$text='',$width=16,$height=16){
		if (!$name) {
			self::$ribbon=array();
			return;
		}
		if ($type=='img') $text=view::skinfile($data,$text);
		self::$ribbon[$name]=array(
			'name'=>$name,
			'desc'=>self::translate($desc),
			'script'=>$script,
			'type'=>$type,
			'text'=>$text,
			'width'=>$width,
			'height'=>$height);
	}
	static public function tag_ribbon(&$data,$skin){
		$ribbon=self::$ribbon;
		core::event('pre_parse_ribbon',array('data'=>&$data,'ribbon'=>&$ribbon),'admin');
		view::show_using_array($data,$ribbon,$skin);
		core::event('post_parse_ribbon',array('data'=>&$data),'admin');
	}
	static private function item_from_post() {
		if (empty($_POST['sgid'])) return false;
		$res=sql::query('SELECT gid FROM jeans_group WHERE id=<%sgid%>',$_POST);
		if (!$res) return false;
		$row=$res->fetch();
		if ($row['gid']==0) $row['gid']=$_POST['sgid'];
		$post=admin::item_from_post('jeans_item');
		return array_merge($post,$row);
	}
	static public function action_post_edititem(){
		$post=self::item_from_post();
		if (!$post) {
			//sgid error
			return;
		}
		if (!empty($_GET['itemid'])) $post['id']=$_GET['itemid'];
		if (isset($post['id'])) core::event('pre_update_item',array('post'=>&$post));
		else core::event('pre_add_item',array('post'=>&$post));
		$post['time']=gmdate('Y-m-d H:i:s', strtotime($post['time']));
		$query='INSERT OR REPLACE INTO jeans_item (<%key:row%>) VALUES (<%row%>)';
		sql::query($query,array('row'=>$post));
		if (sql::pdo()->errorCode()=='00000') {
			if (isset($post['id'])) core::event('post_update_item',array('post'=>$post));
			else {
				$rowid=sql::pdo()->lastInsertId();
				$row=sql::query('SELECT id FROM jeans_item WHERE ROWID=<%0%>',$rowid)->fetch();
				core::event('post_add_item',array('post'=>$post,'id'=>$row['id']));
			}
			core::set_cookie('note_text',_ADMIN_ITEM_SAVED,0);
			core::redirect_local(_CONF_SELF.'?page=itemlist&gid='.(int)$post['gid']);
		} else {
			// Insert error
		}
	}
	static public function active_wysiwyg($mode='member'){
		switch($mode){
			case 'global':
				$class=_CONF_DEFAULT_EDITOR;
				break;
			case 'member':
			default:
				$class=member::setting('editor');
				break;
		}
		if ($class=='default' || !$class) return false;
		if (!core::class_file($class)) return false;
		return $class;
	}
	static public function tag_textarea($data,$key,$template='textarea',$skin=false){
		$wysiwyg=self::active_wysiwyg();
		if ($wysiwyg && method_exists($wysiwyg,'event_wysiwyg_textarea')) {
			$array=array('data'=>&$data,'skin'=>&$skin,'template'=>&$template,'key'=>&$key);
			call_user_func_array(array($wysiwyg,'event_wysiwyg_textarea'),array(&$array));
		}
		$data['key']=$key;
		$data['value']=&$data[$key];
		view::tag_template($data,$template,$skin);
	}
	static public function active_media_manager($mode='member'){
		switch($mode){
			case 'global':
				$class=_CONF_DEFAULT_EDITOR;
				break;
			case 'member':
			default:
				$class=member::setting('media_manager');
				break;
		}
		if ($class=='default' || !$class) return false;
		if (!core::class_file($class)) return false;
		return $class;
	}
	static public function tag_mediamanager(&$data,$template='mediamanager',$skin=false){
		$media=self::active_media_manager();
		if ($media && method_exists($media,'event_media_manager')){
			$array=array('data'=>&$data,'skin'=>&$skin,'template'=>&$template);
			call_user_func_array(array($media,'event_media_manager'),array(&$array));
		}
		view::tag_template($data,$template,$skin);
	}
}