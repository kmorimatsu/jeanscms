<?php
/*
 * Jeans CMS (GPL license)
 * $Id: admin_help.php 240 2010-08-01 00:54:14Z kmorimatsu $
 */

class admin_help extends jeans{
	static public function init(){
	}
	static private $file='admin/help.html';
	static $contents;
	static public function tag_setfile(&$data,$file){
		if (substr($file,-1)=='/') $file.=_LANGUAGE.'.html';
		self::$file=view::skinfile($data,$file,false);
	}
	static private function content_exists($key){
		if (!isset(self::$contents)) {
			$pf=view::is_plugin_file(self::$file);
			if ($pf===false) $help=self::local_file_contents(_DIR_SKINS,self::$file);
			else $help=self::local_file_contents(_DIR_PLUGINS,$pf);
			self::$contents=array();
			if (preg_match_all('/<a[\s]+name[\s]*=[\s]*("[^"]+"|\'[^\']+\')/i',$help,$m,PREG_SET_ORDER)) {
				foreach ($m as $name) {
					self::$contents[]=strtolower(substr($name[1],1,-1));
				}
			}
		}
		return in_array($key,self::$contents);
	}
	static private $html='help';
	static public function tag_settext(&$data,$text){
		self::$html=$text;
	}
	static public function tag_seticon(&$data,$file,$width=false,$height=false){
		$file=view::skinfile($data,$file);
		$html='<img src="<%file%>" alt="help"'.
			($width?' width="<%width%>"':'').
			($height?' height="<%height%>"':'').
			'/>';
		self::$html=self::fill_html($html,array(
			'file'=>$file,
			'width'=>$width,
			'height'=>$height));
	}
	static public function tag_javascript(){
?><script type="text/javascript">
/*<[!CDATA[*/
var libs_admin_help=function(element){
  var popup = window.open(element.href,'jeans_helpwindow','status=no,toolbar=yes,scrollbars=yes,resizable=yes,width=500,height=500,top=0,left=0');
  if (popup.focus) popup.focus();
  if (popup.GetAttention) popup.GetAttention();
  return false;
}
/*]]>*/
</script><?php
	}
	static public function tag_link(&$data,$key,$prefix=''){
		if ($key) $name=isset($data[$key])?$data[$key]:'';
		else $name='';
		$name=strtolower($prefix.$name);
		if (!self::content_exists($name)) return;
		$html=' <a href="<%file%>#<%name%>" onClick="if (libs_admin_help) return libs_admin_help(this);">';
		self::echo_html($html,array(
			'file'=>_CONF_URL_SKINS.self::$file,
			'name'=>$name));
		self::echo_html(self::$html);
		self::echo_html('</a>');
	}
}