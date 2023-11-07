<?php
/*
 * Jeans CMS (GPL license)
 * $Id: admin_mail.php 365 2017-10-25 20:06:22Z kmorimatsu $
 */


class admin_mail extends jeans {
	static public function init(){
		// Authority check for the action.
		$warning=self::translate('_ADMIN_NO_PERMISSION');
		if (!member::logged_in()) {
			switch(@$_POST['action']){
				case 'admin.mail.forgotpassword':
				case 'admin.mail.reactivate':
					break;
				default:
					jerror::quit($warning);
			}
		}
		// Reactivation keys are valid for 2 days.
		sql::select_pdo('member');
		sql::query('DELETE FROM jeans_reactivate WHERE timestamp <= <%0%>',time()-172800);
	}
	static private function setting($key,$id=false){
		static $cache;
		if ($id===false) $id=member::setting('id');
		if (!isset($cache)) {
			$cache=array();
			sql::select_pdo('member');
			$res=sql::query('SELECT * FROM jeans_login');
			while ($row=$res->fetch()) $cache[$row['id']]=$row;
			$res=sql::query('SELECT id,name FROM jeans_member');
			while ($row=$res->fetch()) $cache[$row['id']]['name']=$row['name'];
			// admin
			$cache[-1]=array('id'=>-1,'name'=>'Jeans Admin','email'=>_CONF_ADMIN_EMAIL);
		}
		if (!isset($cache[$id])) return false;
		return isset($cache[$id][$key])?$cache[$id][$key]:false;
	}
	static public function rfc2047($text){
		return '=?UTF-8?B?'.base64_encode($text).'?=';
	}
	static public function send($id,$subject,$text,$from_member=false){
		// To:
		$email=self::setting('email',$id);
		if (!$email) return false;
		$name=self::setting('name',$id);
		$to="$name <$email>";
		// Subject:
		$subject=self::rfc2047(self::shorten($subject,300,'...'));
		// From:
		$admin=_CONF_SITE_NAME.' <'._CONF_ADMIN_EMAIL.'>';
		if ($from_member) {
			$from=self::setting('email')? self::setting('name').' <'.self::setting('email').'>' : $admin;
			$reply=$from;
		} else {
			$from=$admin;
			$reply=$to;
		}
		// Prepare header and main text
		$headers=array("From: $from","X-Sender: Jeans CMS","Content-Type: text/plain; charset=UTF-8",
			"Reply-to: $reply");
		$text=preg_replace('/(\r\n|\r|\n)/',"\n",$text);
		// Send the e-mail
		return @mail($email,$subject,$text, implode("\r\n",$headers) );
	}
	static public function action_post_forgotpassword(){
		sql::select_pdo('member');
		$row=sql::query('SELECT * FROM jeans_login WHERE loginname=<%0%> OR email=<%0%> LIMIT 1',$_POST['loginname'])->fetch();
		if ($row) {
			// Prepare key
			$key=self::random_key();
			// Prepare e-mail
			$id=$row['id'];
			$subject=self::translate('_ADMIN_MAIL_REACTIVATE_ACCOUNT_SUBJECT');
			$text=self::translate('_ADMIN_MAIL_REACTIVATE_ACCOUNT_TEXT1')."\n";
			$text.=_CONF_SITE_NAME.' ( '._CONF_URL_INDEX." )\n";
			$text.='User-ID: '.$row['loginname']."\n\n";
			$text.=self::translate('_ADMIN_MAIL_REACTIVATE_ACCOUNT_TEXT2')."\n";
			$text.=_CONF_URL_ADMIN.'?mid='.$id.'&reactivate='.$key."\n";
			// Send e-mail
			if (!self::send($id,$subject,$text)) return jerror::note('_ADMIN_MAIL_FAILED');
			// Store reactivation key to DB
			$row=array('memberid'=>$id,'key'=>hash('sha512',_HASH_SALT.$key),'timestamp'=>time());
			sql::select_pdo('member');
			sql::query('INSERT INTO jeans_reactivate (<%key:row%>) VALUES (<%row%>)',array('row'=>$row));
		} else {
			// Prepare e-mail
			$id=-1;
			$subject=self::translate('_ADMIN_MAIL_REACTIVATE_BAD_ACCOUNT_SUBJECT');
			$text=self::translate('_ADMIN_MAIL_REACTIVATE_BAD_ACCOUNT_TEXT1')."\n";
			$text.=_CONF_SITE_NAME.' ( '._CONF_URL_INDEX." )\n\n";
			$text.=self::translate('_ADMIN_MAIL_REACTIVATE_BAD_ACCOUNT_TEXT2')."\n";
			$text.='IP: '.$_SERVER['REMOTE_ADDR']."\nID: ".$_POST['loginname']."\n";
			// Send e-mail
			if (!self::send($id,$subject,$text)) return jerror::note('_ADMIN_MAIL_FAILED');
		}
		core::set_cookie('note_text',self::translate('_ADMIN_MAIL_FORGOTPASSWORD_DONE'),0);
		core::redirect_local(_CONF_URL_ADMIN);
	}
	static public function action_post_reactivate(){
		// Check if reactivation key is valid
		$array=array('id'=>$_GET['mid'],'key'=>hash('sha512',_HASH_SALT.$_GET['reactivate']));
		sql::select_pdo('member');
		$row=sql::query('SELECT memberid as id FROM jeans_reactivate WHERE memberid=<%id%> AND key=<%key%> LIMIT 1',$array)->fetch();
		if (!$row) return jerror::note('_ADMIN_MAIL_REACTIVATE_INVALID_KEY');
		// Check if passwords matches and are good.
		if ($_POST['password1_text']!=$_POST['password2_text']) return jerror::note(self::translate('_ADMIN_MEMBERINFO_PASSWORD_MISMATCH'));
		if (strlen($_POST['password1_text'])<6) return jerror::note(self::translate('_ADMIN_MEMBERINFO_PASSWORD_TOO_SHORT'));
		// Update DB
		$array=array('id'=>$row['id'],'password'=>hash('sha512',_HASH_SALT.$_POST['password1_text']));
		sql::select_pdo('member');
		sql::query('UPDATE jeans_login SET password=<%password%> WHERE id=<%id%>',$array);
		// Remove all reactivation keys
		sql::select_pdo('member');
		sql::query('DELETE FROM jeans_reactivate WHERE memberid=<%id%>',$array);
		// Redirect to admin main
		core::set_cookie('note_text',self::translate('_ADMIN_MAIL_REACTIVATION_DONE'),0);
		core::redirect_local(_CONF_URL_ADMIN);
	}
	static public function action_post_sendmessage(){
		if (strlen($_POST['message_text'])==0) {
			jerror::note(self::translate('_ADMIN_MAIL_NO_EMPTY_MESSAGE'));
			return;
		}
		$from_member=!empty($_POST['frommember']);
		$subject=self::translate('_ADMIN_MAIL_PRIVATE_MESSAGE').': '._CONF_SITE_NAME;
		$message=self::setting('name').' '.self::translate('_ADMIN_MAIL_SENT_FOLLOWING_MESSAGE')."\n";
		if ($from_member) $message.=self::translate('_ADMIN_MAIL_YOU_CAN_REPLY')."\n";
		else $message.=self::translate('_ADMIN_MAIL_YOU_CANNOT_REPLY')."\n";
		$message.=_CONF_URL_INDEX.'?memberid='.self::setting('id')."\n";
		$message.="-----\n".$_POST['message_text'];
		if (self::send($_POST['mid'],$subject,$message,$from_member)) {
			jerror::note('_ADMIN_MAIL_PRIVATE_MESSAGE_SUCESS');
			unset($_POST['message_text']);
		} else {
			jerror::note('_ADMIN_MAIL_PRIVATE_MESSAGE_FAILED');
		}
	}
}