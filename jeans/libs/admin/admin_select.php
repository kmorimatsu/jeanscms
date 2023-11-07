<?php
/*
 * Jeans CMS (GPL license)
 * $Id: admin_select.php 345 2014-10-17 02:50:02Z kmorimatsu $
 */

class admin_select extends jeans {
	static public function init(){
		self::translate('_ADMIN_NAME');
	}
	static public function tag_custom(&$data){
		self::echo_html('<select name="<%name%>">',$data);
		$temp=explode('|',$data['extra']);
		for ($i=0;$i<count($temp)-1;$i+=2) {
			if ($data['value']==$temp[$i+1]) {
				core::echo_html('<option selected="true" value="<%0%>"><%1%></option>',array($temp[$i+1],$temp[$i]));
			} else {
				core::echo_html('<option value="<%0%>"><%1%></option>',array($temp[$i+1],$temp[$i]));
			}
		}
		self::echo_html('</select>');
	}
	static public function tag_grouplist($data){
		static $cache;
		if (!isset($cache)) {
			$cache='';
			$query='SELECT * FROM jeans_group WHERE gid=0 AND sgid=0';
			$res=sql::query($query);
			while ($row=$res->fetch()) {
				$cache.=str_replace('|','_',$row['name']).'|'.(int)$row['id'].'|';
			}
		}
		$data['extra']=$cache;
		self::tag_custom($data);
	}
	static public function tag_skinlist($data,$incfile='skin.inc'){
		static $cache=array();
		if (!isset($cache[$incfile])) {
			$cache[$incfile]='';
			$d=dir(_DIR_SKINS);
			while (false !== ($entry = $d->read())) {
				if ($entry=='.' || $entry=='..') continue;
				if (!@is_dir(_DIR_SKINS.$entry.'/')) continue;
				if (!file_exists(_DIR_SKINS."$entry/$incfile")) continue;
				$cache[$incfile].="$entry|/$entry/$incfile|";
			}
			$d->close();  
		}
		$data['extra']=$cache[$incfile];
		self::tag_custom($data);
	
	}
	static public function tag_languagelist($data){
		static $cache;
		if (!isset($cache)) {
			$cache='';
			$d=dir(_DIR_SKINS.'jeans/language/');
			while (false !== ($entry = $d->read())) {
				if (substr($entry,-4)!='.php') continue;
				$entry=substr($entry,0,-4);
				$cache.="$entry|$entry|";
			}
			$d->close();  
		}
		$data['extra']=$cache;
		self::tag_custom($data);
	}
	static public function tag_editorlist($data){
		static $cache;
		if (!isset($cache)) {
			$cache=_ADMIN_SELECT_PLAIN_TEXT.'|default';
			$query='SELECT p.name as name, e.class as class 
				FROM jeans_plugin as p, jeans_event as e 
				WHERE e.event="wysiwyg_textarea" AND e.class=p.id 
				ORDER BY p.sequence ASC';
			$res=sql::query($query);
			while($row=$res->fetch()) $cache.="|$row[name]|$row[class]";
		}
		$data['extra']=$cache;
		self::tag_custom($data);
	}
	static public function tag_mediamanagerlist($data){
		static $cache;
		if (!isset($cache)) {
			$cache=_ADMIN_DEFAULT.'|default';
			$query='SELECT p.name as name, e.class as class 
				FROM jeans_plugin as p, jeans_event as e 
				WHERE e.event="media_manager" AND e.class=p.id 
				ORDER BY p.sequence ASC';
			$res=sql::query($query);
			while($row=$res->fetch()) {
				$name=self::translate($row['name']);
				$cache.="|$name|$row[class]";
			}
		}
		$data['extra']=$cache;
		self::tag_custom($data);
	}
}