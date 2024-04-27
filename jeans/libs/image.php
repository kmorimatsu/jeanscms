<?php
/*
 * Jeans CMS (GPL license)
 * $Id: image.php 287 2010-10-11 04:06:34Z kmorimatsu $
 */

class image extends jeans{
	static public function itemtag_image(&$data,$file,$alt=false,$width=false,$height=false){
		// Usage:
		// <%image(1/20100101-test.jpg,test image,100,10)%>
		// <%image(1/20100101-test.jpg,test image,100,10,class=xxx,title=yyy)%>
		$args=func_get_args();
		$args[0]=&$data;
		if (self::local_file_exists(_DIR_SKINS,'/media/'.$file)) $args[1]='/media/'.$file;
		ob_start();
		call_user_func_array(self::class.'::tag_image',$args);
		return ob_get_clean();
	}
	static private function image_props(&$data,$file=false,$alt=false,$width=false,$height=false){
		if (!$file) $file=$data['file'];
		if (!$alt) $alt=isset($data['alt']) ? $data['alt']:$file;
		$fullpath=view::skinfile($data,$file);
		if (strpos($fullpath,_CONF_URL_PLUGINS)===0) {
			$dir=_DIR_PLUGINS;
			$path=substr($fullpath,strlen(_CONF_URL_PLUGINS));
		} else {
			$dir=_DIR_SKINS;
			$path=substr($fullpath,strlen(_CONF_URL_SKINS));
		}
		if (!self::local_file_exists($dir,$path)) {
			// Check if the image is stored in DB
			if (!preg_match('/^([0-9]+)\-(.*)$/',$file,$m)) return self::p($alt);
			$fullpath='?action=media.view&file='.rawurlencode($file);
			if ($height==false || !is_numeric($height)) {
				$query='SELECT xml FROM jeans_binary WHERE 
					owner="jeans" AND type="media" 
					AND name=<%2%> AND contextid=<%1%>';
				$row=sql::query($query,$m)->fetch();
				if (!$row) return self::p($alt);
				$xml=new SimpleXMLElement($row['xml']);
				$width=(int)$xml->width;
				$height=(int)$xml->height;
			}
		} elseif ($height==false || !is_numeric($height)) {
			$size=getimagesize($dir.$path);
			$width=$size[0];
			$height=$size[1];
		}
		return array('src'=>$fullpath,'alt'=>$alt,'width'=>$width,'height'=>$height);
	}
	static public function tag_image(&$data,$file=false,$alt=false,$width=false,$height=false){
		// Usage:
		// <%image(/media/1/20100101-test.jpg,test image,100,10)%>
		// <%image(/media/1/20100101-test.jpg,test image,100,10,class=xxx,title=yyy)%>
		// <%image(image/test.jpg)%>
		// property=value implementation
		$args=func_get_args();
		$args[0]=&$data;
		$props=call_user_func_array(self::class.'::image_props',$args);
		array_shift($args);
		foreach($args as $arg){
			if (preg_match('/^([a-z]+)=([\s\S]*)$/',$arg,$m)) $props[$m[1]]=$m[2];
		}
		// Construct img tag
		$html='<img';
		foreach($props as $key=>$value){
			$html.=self::fill_html(' <%0%>="<%1%>"',array($key,$value),'hsc');
		}
		$html.=' />';
		self::echo_html($html);
	}
	static public function tag_popup(&$data,$file=false,$alt=false,$width=false,$height=false){
		//TODO: here
		$args=func_get_args();
		$args[0]=&$data;
		$data=call_user_func_array(self::class.'::image_props',$args);
		array_shift($args);
		$props=array('href'=>$data['src']);
		foreach($args as $arg){
			if (preg_match('/^([a-z]+)=([\s\S]*)$/',$arg,$m)) $props[$m[1]]=$m[2];
		}
		// Construct a tag
		// Construct img tag
		$html='<a';
		foreach($props as $key=>$value){
			$html.=self::fill_html(' <%0%>="<%1%>"',array($key,$value),'hsc');
		}
		$html.=' onclick="window.open(this.href,\'imagepopup\',\'status=no,toolbar=no,scrollbars=no,resizable=yes,width=<%width%>,height=<%height%>\');return false;"';
		$html.='><%alt%></a>';
		self::echo_html($html);
		
	}
}