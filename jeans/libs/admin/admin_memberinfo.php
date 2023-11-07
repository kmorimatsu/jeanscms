<?php
/*
 * Jeans CMS (GPL license)
 * $Id: admin_memberinfo.php 365 2017-10-25 20:06:22Z kmorimatsu $
 */

class admin_memberinfo extends jeans {
	static private $mid;
	static public function init(){
		// Load the language file
		$warning=self::translate('_ADMIN_NO_PERMISSION');
		// Superadmin can use this class
		// The member also can use this class, but only for own information.
		self::$mid=empty($_GET['mid'])? false:(int)$_GET['mid'];
		if (!member::is_admin()) {
			if (!member::logged_in()) jerror::quit($warning);
			if (self::$mid!=member::setting('id')) jerror::quit($warning);
		}
	}
	static public function data($key,$mid=false){
		static $cache;
		if (!isset($cache)) {
			$cache=array();
			$query='SELECT * FROM jeans_member';
			$res=sql::query($query);
			while($row=$res->fetch()){
				$cache[$row['id']]=$row;
				sql::convert_xml($cache[$row['id']],'member');
			}
			$query='SELECT id, loginname, authority, email as loginemail FROM jeans_login';
			sql::select_pdo('member');
			$res=sql::query($query);
			while($row=$res->fetch()){
				$row['admin']=($row['authority'] & member::MEMBER_IS_ADMIN) ? 1:0;
				$row['canlogin']=($row['authority'] & member::MEMBER_CAN_LOGIN) ? 1:0;
				if (!isset($cache[$row['id']])) $cache[$row['id']]=array();
				$cache[$row['id']]=array_merge($cache[$row['id']],$row);
			}
		}
		if ($key==false && $mid==false) return $cache;
		if ($mid==false) $mid=self::$mid;
		if (!isset($cache[$mid])) return false;
		if (!isset($cache[$mid][$key])) return false;
		return $cache[$mid][$key];
	}
	static public function tag_data(&$data,$key){
		self::p(self::data($key));
	}
	static public function tag_setting(&$data,$skin){
		$array=array();
		$array[]=array(
			'name'=>'name',
			'desc'=>self::translate('_ADMIN_MEMBERINFO_NAME'),
			'value'=>self::data('name'),
			'type'=>'text',
			'extra'=>'');
		$array[]=array(
			'name'=>'language',
			'desc'=>self::translate('_ADMIN_MEMBERINFO_LANGUAGE'),
			'value'=>self::data('language'),
			'type'=>'select',
			'extra'=>'languagelist');
		$res=sql::query('SELECT * FROM jeans_config_desc WHERE configtype IN ("member_guest","member") ORDER BY owner,sequence ASC');
		while ($row=$res->fetch()) {
			if ($row['configtype']!='member_guest' && !self::data('admin')) continue;
			$row['value']=self::data($row['name']);
			$row['name'].='_text';
			$row['desc']=self::translate($row['desc']);
			$array[]=$row;
		}
		view::show_using_array($data,$array,$skin);
	}
	static public function tag_memberlist(&$data,$skin){
		$array=self::data(false);
		view::show_using_array($data,$array,$skin);
	}
	static public function if_myself(){
		return self::$mid==member::setting('id');
	}
	static public function tag_flagradio(&$data,$flag,$name){
		$flags=self::data('authority');
		$flag=constant($flag);
		if ($flags & $flag) {
			$html='<input name="<%name%>" value="1" checked="true" type="radio"><label for="<%name%>"><%yes%></label><input name="<%name%>" value="0" type="radio"><label for="<%name%>"><%no%></label>';
		} else {
			$html='<input name="<%name%>" value="1" type="radio"><label for="<%name%>"><%yes%></label><input name="<%name%>" value="0" checked="true" type="radio"><label for="<%name%>"><%no%></label>';
		}
		$array=array('name'=>$name,'yes'=>_JEANS_YES,'no'=>_JEANS_NO);
		self::echo_html($html,$array);
	}
	static public function action_post_membersetting(){
		$post=admin::item_from_post();
		$query='SELECT id FROM jeans_member WHERE name=<%name%> AND NOT id=<%id%> LIMIT 1';
		$array=array('name'=>$post['name'],'id'=>$_GET['mid']);
		if (sql::query($query,$array)->fetch()) {
			// The name is already used.
			jerror::note(_ADMIN_MEMBERINFO_NAME_USED);
			return;
		}
		$xml=new SimpleXMLElement(_XML_BLANC);
		foreach($post as $key=>$value){
			if ($key=='name' || $key=='language') continue;
			if (self::check_available_key($_GET['mid'],$key)) $xml->$key=$value;
		}
		$query='UPDATE jeans_member SET name=<%name%>, language=<%language%>, xml=<%xml%> WHERE id=<%id%>';
		$array=array_merge($post,array('xml'=>$xml->asXML(),'id'=>$_GET['mid']));
		sql::query($query,$array);
		core::set_cookie('note_text',self::translate('_ADMIN_MEMBERINFO_MEMBERSETTING_SAVED'),0);
		if (member::is_admin()) {
			core::redirect_local(_CONF_SELF.'?page=memberlist');
		} else {
			core::redirect_local(_CONF_SELF);
		}
	}
	static private function check_available_key($mid,$key){
		static $admin,$guest;
		if (!isset($admin)) {
			$admin=$guest=array();
			$res=sql::query('SELECT name,configtype FROM jeans_config_desc WHERE configtype IN ("member_guest","member")');
			while($row=$res->fetch()){
				$admin[]=$row['name'];
				if ($row['configtype']=='member_guest') $guest[]=$row['name'];
			}
		}
		if (self::data('admin',$mid)) {
			return in_array($key,$admin);	
		} else {
			return in_array($key,$guest);	
		}
	}
	static public function action_post_loginsetting(){
		// Return URL when faileur
		$return=self::$mid ? '?page=addmember':'?page=membersetting&mid='.self::$mid;
		// Prepare data
		$post=admin::item_from_post();
		if ($post['password1']!=$post['password2']) {
			core::set_cookie('note_text',self::translate('_ADMIN_MEMBERINFO_PASSWORD_MISMATCH'),0);
			core::redirect_local(_CONF_SELF.$return);
		}
		if ((0<strlen($post['password1']) || self::$mid==false) && strlen($post['password1'])<6) {
			core::set_cookie('note_text',self::translate('_ADMIN_MEMBERINFO_PASSWORD_TOO_SHORT'),0);
			core::redirect_local(_CONF_SELF.$return);
		}
		// If password isn't set, show the confirm page.
		if (!isset($post['password'])) return;
		// Check if password is correct
		if (!member::try_login(member::setting('loginname'),$post['password'])){
			jerror::note('_ADMIN_MEMBERINFO_WRONG_PASSWORD');
			return;
		}
		// Set authority flags
		$flags=self::data('authority') & (~(member::MEMBER_CAN_LOGIN+member::MEMBER_IS_ADMIN));
		if ($post['enabled']) $flags|=member::MEMBER_CAN_LOGIN;
		if ($post['admin']) $flags|=member::MEMBER_IS_ADMIN;
		$post['flags']=$flags;
		// Change setting or add new member
		if (self::$mid) self::change_setting($post);
		else self::add_member($post);
	}
	static private function change_setting($post){
		// Prepare query and array
		// Note that loginname and authority columns should not be changed here.
		$ownsetting=self::$mid==member::setting('id');
		if (0<strlen($post['password1'])) {
			if ($ownsetting) $query='UPDATE jeans_login SET password=<%password%>, email=<%email%> WHERE id=<%id%>';
			else $query='UPDATE jeans_login SET password=<%password%>, email=<%email%>, authority=<%flags%> WHERE id=<%id%>';
		} else {
			if ($ownsetting) $query='UPDATE jeans_login SET email=<%email%> WHERE id=<%id%>';
			else $query='UPDATE jeans_login SET email=<%email%>, authority=<%flags%> WHERE id=<%id%>';
		}
		$array=array(
			'id'=>self::$mid,
			'email'=>$post['email'],
			'password'=>hash('sha512',_HASH_SALT.$post['password1']),
			'flags'=>$post['flags']);
		// Let's check the authority here again for security though it's checked in init() method.
		if (!member::logged_in()) jerror::quit(_ADMIN_NO_PERMISSION);
		if (member::setting('id')!=self::$mid && !member::is_admin()) jerror::quit(_ADMIN_NO_PERMISSION);
		sql::select_pdo('member');
		$res=sql::query($query,$array);
		// Everything is done. Show the information.
		if ($res->rowCount()) $note=self::translate('_ADMIN_MEMBERINFO_LOGINSETTING_SAVED');
		else $note=self::translate('_ADMIN_MEMBERINFO_LOGINSETTING_NOT_SAVED');
		core::set_cookie('note_text',$note,0);
		if (member::is_admin()) {
			core::redirect_local(_CONF_SELF.'?page=memberlist');
		} else {
			core::redirect_local(_CONF_SELF);
		}
	}
	static private function add_member($post){
		// Let's check the authority here again for security though it's checked in init() method.
		if (!member::is_admin()) jerror::quit(_ADMIN_NO_PERMISSION);
		// Check if loginname, e-mail, and name are OK.
		$query='SELECT id FROM jeans_login WHERE email=<%email%> OR loginname=<%loginname%>';
		sql::select_pdo('member');
		$row=sql::query($query,$post)->fetch();
		if ($row) {
			core::set_cookie('note_text',self::translate('_ADMIN_MEMBERINFO_ACCOUNT_USED'),0);
			core::redirect_local(_CONF_SELF.'?page=addmember');
		}
		$query='SELECT id FROM jeans_member WHERE name=<%name%>';
		$row=sql::query($query,$post)->fetch();
		if ($row) {
			core::set_cookie('note_text',self::translate('_ADMIN_MEMBERINFO_NAME_USED'),0);
			core::redirect_local(_CONF_SELF.'?page=addmember');
		}
		// Insert a row
		$post['password']=hash('sha512',_HASH_SALT.$post['password1']);
		$query='INSERT INTO jeans_login (loginname,authority,email,password) VALUES (<%loginname%>,<%flags%>,<%email%>,<%password%>);';
		sql::select_pdo('member');
		sql::query($query,$post);
		$query='SELECT id FROM jeans_login WHERE loginname=<%loginname%>';
		sql::select_pdo('member');
		$row=sql::query($query,$post)->fetch();
		if (!$row) {
			core::set_cookie('note_text','Addition of member failed by unknown reason.',0);
			core::redirect_local(_CONF_SELF.'?page=addmember');
		}
		// Create the member information
		$row['name']=$post['name'];
		$row['language']=_CONF_DEFAULT_LANGUAGE;
		$query='INSERT INTO jeans_member (id,name,language) VALUES (<%id%>,<%name%>,<%language%>);';
		sql::query($query,$row);
		// All done.  Let's inform
		core::set_cookie('note_text',_ADMIN_MEMBERINFO_MEMBER_REGISTERED,0);
		core::redirect_local(_CONF_SELF.'?page=memberlist');
	}
}