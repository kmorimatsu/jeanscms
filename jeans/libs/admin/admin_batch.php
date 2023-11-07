<?php
/*
 * Jeans CMS (GPL license)
 * $Id: admin_batch.php 365 2017-10-25 20:06:22Z kmorimatsu $
 */

class admin_batch extends jeans{
	static private $ids=array(),$target='unknown';
	static public function init(){
		foreach(explode(',',$_POST['ids']) as $id){
			$id=preg_replace('/[^0-9a-z_]/','',trim($id));
			if (strlen($id)) self::$ids[]=$id;
		}
		switch(@$_POST['batch_action']){
			case 'admin.batch.deletecomments':
				self::$target='comment';
				break;
			case 'admin.batch.deletegroups':
				self::$target='subgroup';
				break;
			case 'admin.batch.deleteitems':
				self::$target='item';
				break;
			case 'admin.batch.deleteplugins':
				self::$target='plugin';
				break;
			case 'admin.batch.deletemedia':
				self::$target='media';
				break;
			default:
				self::$target='unknown';
		}
		// Authority check for the action.
		$warning=self::translate('_ADMIN_NO_PERMISSION');
		if (isset($_POST['action'])) {
			switch($_POST['action']){
				case 'admin.batch.deletecomments':
				case 'admin.batch.deletemedia':
					if (!member::logged_in()) jerror::quit($warning);
					break;
				default:
					if (!member::is_admin()) jerror::quit($warning);
					break;
			}
		}
	}
	static public function tag_actionurl(&$data){
		if (self::is_local_url($_POST['action_url'])) self::p($_POST['action_url']);
		else self::p(_CONF_SELF);
	}
	static public function tag_tickethidden(&$data){
		$args=func_get_args();
		array_shift($args);//&$data
		$value=&$data;
		while(count($args) && is_array($value)) $value=&$value[array_shift($args)];
		ticket::tag_hidden($data,$value);
	}
	static public function tag_todo(&$data){
		switch($_POST['batch_action']){
			case 'admin.batch.deletecomments':
			case 'admin.batch.deletegroups':
			case 'admin.batch.deleteitems':
			case 'admin.batch.deleteplugins':
			case 'admin.batch.deletemedia':
				// _ADMIN_MATCH_DELETECOMMENTS etc.
				self::t('_'.str_replace('.','_',strtoupper($_POST['batch_action'])));
				break;
			default:
				self::p($_POST['batch_action']);
		}
	}
	static public function tag_information(&$data){
		if (count(self::$ids)==1) {
			switch(self::$target){
				case 'comment':
					$query='SELECT body as info FROM jeans_comment WHERE id=<%0%>';
					break;
				case 'subgroup': case 'group':
					$query='SELECT name as info, desc as desc FROM jeans_group WHERE id=<%0%>';
					break;
				case 'item':
					$query='SELECT title as info FROM jeans_item WHERE id=<%0%>';
					break;
				case 'plugin':
					$query='SELECT name as info FROM jeans_plugin WHERE id=<%0%>';
					break;
				case 'media':
					$query='SELECT name as info FROM jeans_binary WHERE id=<%0%>';
					break;
			}
			if (isset($query)){
				$row=sql::query($query,self::$ids[0])->fetch();
				if ($row) self::t($row['info']);
				else self::p(self::$target.' #'.self::$ids);
			}
		} else {
			self::p(self::$target.' #'.implode(',#',self::$ids));
		}
	}
	static public function tag_extra(&$data,$skin){
		$array=array();
		switch(@$_POST['batch_action']){
			case 'admin.batch.deleteitems':
				$array[]=array(
					'name'=>'deletecomments',
					'desc'=>self::translate('_ADMIN_BATCH_DELETEITEMS_DELETECOMMENTS'),
					'type'=>'yesno',
					'value'=>0,
					'extra'=>'');
				break;
			default:
				return;
		}
		view::show_using_array($data,$array,$skin);
	}
	static public function action_post_deleteitems(){
		$checkquery='SELECT COUNT(*) as result FROM jeans_comment WHERE itemid=<%0%>';
		$itemquery='DELETE FROM jeans_item WHERE id=<%0%>';
		$commentquery='DELETE FROM jeans_comment WHERE itemid=<%0%>';
		foreach (self::$ids as $id) {
			if (empty($_POST['deletecomments'])) {
				$row=sql::query($checkquery,$id)->fetch();
				if ($row['result']) {
					$note=self::translate('_ADMIN_BATCH_ITEM_COMMENTS_EXIST');
					jerror::note($note.': #<%0%>',$id);
					continue;
				}
			}
			sql::begin();
			$res=sql::query($itemquery,$id);
			if (0<$res->rowCount()) {
				$note=self::translate('_ADMIN_BATCH_DELETEITEM');
				sql::query($commentquery,$id);
			} else $note=self::translate('_ADMIN_BATCH_DELETEITEM_FALIED');
			jerror::note($note.': #<%0%>',$id);
			sql::commit();
		}
	}
	static public function action_post_deletecomments(){
		// This method is placed here because this cannot be placed in admin_comments class.
		if (member::is_admin()) $query='DELETE FROM jeans_comment WHERE id=<%id%>';
		elseif (member::logged_in()) $query='DELETE FROM jeans_comment WHERE id=<%id%> AND author=<%author%>';
		else jerror::quit(_ADMIN_NO_PERMISSION);
		sql::begin();
		foreach (self::$ids as $id) {
			$res=sql::query($query,array('id'=>$id,'author'=>member::setting('id')));
			if (0<$res->rowCount()) $note=self::translate('_ADMIN_BATCH_DELETECOMMENT');
			else $note=self::translate('_ADMIN_BATCH_DELETECOMMENT_FALIED');
			jerror::note($note.': #<%0%>',$id);
		}
		sql::commit();
	}
	static public function action_post_deletegroups(){
		$countquery='SELECT COUNT(*) as result FROM jeans_group as g, jeans_item as i WHERE g.gid=<%0%> OR i.gid=<%0%>';
		$groupquery='DELETE FROM jeans_group WHERE id=<%0%>';
		$itemquery='UPDATE jeans_item SET sgid=gid WHERE sgid=<%0%>';
		$sgquery='UPDATE jeans_group SET sgid=gid WHERE sgid=<%0%>';
		foreach (self::$ids as $id) {
			$res=sql::query($countquery,$id)->fetch();
			if ($res['result']) {
				$note=self::translate('_ADMIN_BATCH_GROUP_NOT_EMPTY');
				jerror::note($note.': #<%0%>',$id);
				continue;
			}
			sql::begin();
			$res=sql::query($groupquery,$id);
			if (0<$res->rowCount()) {
				sql::query($itemquery,$id);
				sql::query($sgquery,$id);
				$note=self::translate('_ADMIN_BATCH_DELETEGROUP');
			} else $note=self::translate('_ADMIN_BATCH_DELETEGROUP_FALIED');
			sql::commit();
			jerror::note($note.': #<%0%>',$id);
		}
	}
	static public function action_post_deleteplugins(){
		foreach (self::$ids as $id) {
			if (plugin::plugin_list($id)) call_user_func(array($id,'uninstall'));
		}
		$pluginquery='DELETE FROM jeans_plugin WHERE id=<%0%>';
		$configquery='DELETE FROM jeans_config_desc WHERE owner=<%0%>';
		$eventquery ='DELETE FROM jeans_event WHERE class=<%0%>';
		sql::begin();
		foreach (self::$ids as $id) {
			$note=self::translate('_ADMIN_BATCH_DELETEPLUGIN_FAILED');
			if (plugin::plugin_list($id)) {
				$res=sql::query($pluginquery,$id);
				if (0<$res->rowCount()) {
					sql::query($configquery,$id);
					sql::query($eventquery,$id);
					$note=self::translate('_ADMIN_BATCH_DELETEPLUGIN');
				}
			}
			jerror::note($note.': <%0%>',$id);
		}
		sql::commit();
		$res=sql::query('SELECT id FROM jeans_plugin ORDER BY sequence ASC');
		sql::begin();
		for ($i=0;$row=$res->fetch();$i++) {
			sql::query('UPDATE jeans_plugin SET sequence=<%i%> WHERE id=<%id%>',array('id'=>$row['id'],'i'=>$i));
		}
		sql::commit();
	}
	static public function action_post_moveplugins(){
		$plugins=array();
		$upper=$middle=$lower=array();
		$res=sql::query('SELECT id FROM jeans_plugin ORDER BY sequence ASC');
		for ($i=0;$row=$res->fetch();$i++) {
			if (in_array($row['id'],self::$ids)) $middle[]=$row['id'];
			elseif (count($middle)==0) $upper[]=$row['id'];
			else $lower[]=$row['id'];
		}
		if ($_POST['direction']=='up' && count($upper)) {
			array_unshift($lower,array_pop($upper));
			jerror::note(self::translate('_ADMIN_BATCH_MOVEPLUGINS_UP'));
		} elseif ($_POST['direction']=='down' && count($lower)) {
			array_push($upper,array_shift($lower));
			jerror::note(self::translate('_ADMIN_BATCH_MOVEPLUGINS_DOWN'));
		}
		$query='UPDATE jeans_plugin SET sequence=<%i%> WHERE id=<%id%>';
		$i=0;
		sql::begin();
		foreach ($upper as $id) sql::query($query,array('id'=>$id,'i'=>$i++));
		foreach ($middle as $id) sql::query($query,array('id'=>$id,'i'=>$i++));
		foreach ($lower as $id) sql::query($query,array('id'=>$id,'i'=>$i++));
		sql::commit();
	}
	static public function action_post_deletemedia(){
		if (member::is_admin()) {
			$query='SELECT m.id as id FROM jeans_binary as m, jeans_binary as mm 
				WHERE mm.id IN (<%ids%>) AND mm.type="media" 
				AND mm.name=m.name AND (m.type="media" OR m.type="thumbnail")';
		} else {
			$query='SELECT m.id as id FROM jeans_binary as m, jeans_binary as mm 
				WHERE mm.id IN (<%ids%>) AND mm.type="media" 
				AND mm.name=m.name AND (m.type="media" OR m.type="thumbnail")
				AND m.contextid=<%mid%>';
		}
		$res=sql::query($query,array('ids'=>self::$ids, 'mid'=>member::setting('id')));
		$ids=array();
		while ($row=$res->fetch()) $ids[]=$row['id'];
		$res=sql::query('DELETE FROM jeans_binary WHERE id IN (<%ids%>)',array('ids'=>$ids));
		if (0<$res->rowCount()) jerror::note('_ADMIN_BATCH_DELETEMEDIA_DONE');
		else jerror::note('_ADMIN_BATCH_DELETEMEDIA_FAILED');
	}
}