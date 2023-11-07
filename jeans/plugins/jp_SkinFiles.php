<?php
/*
 * Jeans CMS (GPL license)
 * $Id: jp_SkinFiles.php 365 2017-10-25 20:06:22Z kmorimatsu $
 */

class jp_SkinFiles extends plugin{
	static public function name(){
		return '_JP_SKINFILES_SKINFILES_PLUGIN';
	}
	static public function author(){
		return 'Katsumi';
	}
	static public function url(){
		return 'http://jeanscms.sourceforge.jp/';
	}
	static public function desc(){
		return '_JP_SKINFILES_SKINFILES_DESC';
	}
	static public function version(){
		return '0.2.0';
	}
	static public function install(){
		self::option()->create('text_ext','_JP_SKINFILES_TEXT_FILE_EXTENSIONS','text','txt,text,htm,html,inc,php,css,js');
		self::option()->create('image_ext','_JP_SKINFILES_IMAGE_FILE_EXTENSIONS','text','jpeg,gif,jpg,png');
		self::option()->create('date_prefix','_JP_SKINFILES_USE_DATE_PREFIX','yesno',1);
	}
	static public function events(){
		return array('quick_menu','media_manager');
	}
	static public function event_quick_menu(&$array){
		array_push($array['options'],array(
				'title'=>'_JP_SKINFILES_SKINFILE_MANAGEMENT',
				'url'=>self::url_admin_page(),
				'tooltip'=>self::name()
			));
	}
	static public function event_media_manager(&$array){
		$array['skin']='/jp/skinfiles/mediamanager.inc';
	}
}