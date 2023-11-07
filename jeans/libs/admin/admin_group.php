<?php
/*
 * Jeans CMS (GPL license)
 * $Id: admin_group.php 365 2017-10-25 20:06:22Z kmorimatsu $
 */

class admin_group extends jeans {
	static public function init(){
		// Load the language file
		$warning=self::translate('_ADMIN_NO_PERMISSION');
		// Only superadmin can use this class
		if (!member::is_admin()) jerror::quit($warning);
	}
	static private function _groupsetting($data,$skin,$type){
		static $cache=array();
		$data=array_merge($data,self::_groupsetting_sub(false,$type));
		if (!isset($cache[$type])) {
			$cache[$type]=array();
			foreach (array('id','gid','sgid') as $name) {
				$row=array('name'=>$name,'desc'=>'','type'=>'text','extra'=>'hidden',
					'value'=>self::_groupsetting_sub($name,$type));
				$cache[$type][]=$row;
			}
			$cache[$type][]=array('name'=>'name_text','desc'=>self::translate(_ADMIN_NAME),'type'=>'text',
				'extra'=>'','value'=>self::_groupsetting_sub('name',$type));
			$cache[$type][]=array('name'=>'desc_text','desc'=>self::translate(_ADMIN_DESC),'type'=>'text',
				'extra'=>'','value'=>self::_groupsetting_sub('desc',$type));
			$query='SELECT * FROM jeans_config_desc WHERE configtype=<%0%> ORDER BY sequence ASC';
			$res=sql::query($query,array(preg_replace('/^new/','',$type)));
			while ($row=$res->fetch()) {
				$value=self::_groupsetting_sub($row['name'],$type);
				if ($value===false) $value=$row['defvalue'];
				$row['value']=$value;
				$row['name'].='_text';
				$row['desc']=self::translate($row['desc']);
				$cache[$type][]=$row;
			}
			// Refill posted values (this happens when ticket is expired).
			foreach($_POST as $key=>$value){
				if (isset($cache[$type][$key])) $cache[$type][$key]=$value;
			}
		}
		view::show_using_array($data,$cache[$type],$skin);
	}
	static private function _groupsetting_sub($name,$type){
		switch($type){
			case 'newsubgroup':
				switch($name){
					case false:
						return array();
					case 'id': case 'flag':
						return 0;
					case 'gid':
						return group::setting('id');
					case 'sgid':
						$sgid=group::sgsetting('id');
						if ($sgid) return $sgid;
						else return group::setting('id');
					default:
						$default=sql::xml_default('subgroup');
						if (isset($default[$name])) return $default[$name];
						else return '';
				}
			case 'newgroup':
				switch($name){
					case false:
						return array();
					case 'id': case 'gid': case 'sgid': case 'flag':
						return 0;
					default:
						$default=sql::xml_default('group');
						if (isset($default[$name])) return $default[$name];
						else return '';
				}
			case 'subgroup':
				return group::sgsetting($name);
			case 'group':
			default:
				return group::setting($name);
		}
	}
	static public function tag_groupsetting(&$data,$skin){
		return self::_groupsetting($data,$skin,'group');
	}
	static public function tag_subgroupsetting(&$data,$skin){
		return self::_groupsetting($data,$skin,'subgroup');
	}
	static public function tag_newgroup(&$data,$skin){
		return self::_groupsetting($data,$skin,'newgroup');
	}
	static public function tag_newsubgroup(&$data,$skin){
		return self::_groupsetting($data,$skin,'newsubgroup');
	}
	static public function action_post_edit(){
		$post=admin::item_from_post('jeans_group');
		if (!$post) return;
		// Unset id column if value is zero (for creating a row).
		if (isset($post['id']) && $post['id']==0) unset($post['id']);
		// gid must not be zero if gid is set (for creating a subgroup in a group)
		if (empty($post['gid']) && !empty($post['sgid'])) $post['gid']=$post['sgid'];
		// Everything is prepared.  Let's insert/replace.
		$query='INSERT OR REPLACE INTO jeans_group (<%key:row%>) VALUES (<%row%>)';
		sql::query($query,array('row'=>$post));
		if (sql::pdo()->errorCode()!=='00000') {
			// error
		} elseif (isset($post['id'])) {
			// Replace
			$id=$post['gid']?$post['gid']:$post['id'];
			core::set_cookie('note_text',_ADMIN_GROUP_SAVED,0);
			core::redirect_local(_CONF_SELF.'?page=subgrouplist&gid='.(int)$id);
		} elseif ($post['gid']) {
			// new subgroup
			core::set_cookie('note_text',_ADMIN_GROUP_SUBGROUP_CREATED,0);
			core::redirect_local(_CONF_SELF.'?page=subgrouplist&gid='.(int)$post['gid']);
		} else {
			// new group
			core::set_cookie('note_text',_ADMIN_GROUP_CREATED,0);
			core::redirect_local(_CONF_SELF);
		}
	}
}