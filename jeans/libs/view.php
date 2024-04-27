<?php
/*
 * Jeans CMS (GPL license)
 * $Id: view.php 365 2017-10-25 20:06:22Z kmorimatsu $
 */

class view extends jeans {
	static private $current_skin='/default/skin.inc',$globals=array();
	static public function init(){
		if (defined('_CONF_DEFAULT_GROUP_SKIN')) self::set_skin(_CONF_DEFAULT_GROUP_SKIN);
		// Include data from user in the '$data'.
		// Note that view::$globals keeps these data.
		// TODO: Consider if it's good to use '&' in following line.
		foreach(array('_GET','_POST','_REQUEST','_COOKIE','_SERVER') as $key) self::$globals[$key]=&$GLOBALS[$key];
		foreach(array('libs','jp', 'history') as $key) self::$globals[$key]=array();
		self::$globals['template']='init';
		self::$globals['libs']['data']['constant']=function ($data,$key) { return constant($key); };
	}
	/* Force current skin to the input. */
	static public function set_skin($skin,$parent_skin=false){
		self::$current_skin=self::skin_path($skin,$parent_skin);
	}
	/* Determine the full path to skinfile from either absolute path or relative path */
	static private function skin_path($skin,$parent_skin=false){
		// Change '\' to '/'
		$skin=str_replace('\\','/',$skin);
		if ($parent_skin==false) $parent_skin=self::$current_skin;
		$parent_skin=str_replace('\\','/',$parent_skin);
		// If input is absolute path, directly return it.
		if (substr($skin,0,1)=='/') return $skin;
		// Remove file name from original skin
		$parent_skin=preg_replace('#/[^/]+$#','/',$parent_skin);
		// Remove '../'
		while(substr($skin,0,3)=='../'){
			$skin=substr($skin,3);
			$parent_skin=preg_replace('#/[^/]+/$#','/',$parent_skin);
		}
		return $parent_skin.$skin;
	}
	/* Main skin parse routine follows */
	static public function parse_skin($skin=false,$parent_skin=false,$data=false,$template=false) {
		static $parsed=false;
		// Initialize $data and set template
		if ($data===false) $data=self::$globals;
		if (is_array($template)) {
			$data=array_merge($data,$template);
		} else if ($template!==false) {
			$data['template']=$template;
		}
		// Refresh current skin
		// Note that $data also keeps information of used skin.
		if ($skin) {
			if (isset($data['libs']['view']['lambda'][$skin]) && is_callable($data['libs']['view']['lambda'][$skin])) {
				// <%skin%> tag implementation
				$data['libs']['view']['lambda'][$skin]($data);
				return;
			} else {
				$skin=self::skin_path($skin,$parent_skin);
				self::$current_skin=$skin;
			}
		} else $skin=self::$current_skin;
		$data['skin']=$skin;
		// init_skinfile_parse event.
		$cached=false;
		core::event('init_skinfile_parse',array('data'=>&$data,'skin'=>$skin,'cached'=>&$cached,'child'=>$parsed),'view');
		// If not cached, parse skin.
		if ($cached===false) $code=self::skin_lambda($skin);
		else $code=false;
		// Parse skin
		if (!$parsed) {
			$parsed=true;
			core::event('pre_skin_parse',array('data'=>&$data,'skin'=>$skin,'code'=>&$code,'cached'=>$cached),'view');
			if ($cached===false) $code($data);
			else self::echo_html($cached);
			core::event('post_skin_parse',array('skin'=>$skin),'view');	
		} else {
			if ($cached===false) $code($data);
			else self::echo_html($cached);
		}
	}
	static private function find_skin_file($skin){
		if (self::local_file_exists(_DIR_SKINS,$skin)) return $skin;
		$extension=self::set_extension();
		foreach($extension as $replace){
			$file=preg_replace('#^/[^/]+/#',$replace,$skin);
			if (self::local_file_exists(_DIR_SKINS,$file)) return $file;
		}
		if (preg_match('#^/jp/([a-zA-Z0-9_]+)/(.*)$#',$skin,$m)) {
			if (is_array(core::class_file("jp_$m[1]")) && self::local_file_exists(_DIR_PLUGINS,"$m[1]/$m[2]")) return $skin;
		}
		return false;
	}
	static public function set_extension($array=false){
		// The extension can be set once.
		static $extension;
		if (isset($extension)) return $extension;
		if (!is_array($array)) return array('/jeans/'); // Default extension
		$extension=array();
		foreach($array as $skin){
			$extension[]='/'.preg_replace('#(^/|/$)#','',$skin).'/';
		}
		$extension[]='/jeans/'; // All skins extend jeans skin.
		return $extension;
	}
	static public function tag_extends(){
		$args=func_get_args();
		array_shift($args); //&$data
		self::set_extension($args);
	}
	/**
	 * JIT routine follows.
	 */
	static public function skin_lambda($skin){
		static $cache=array();
		if (!isset($cache[$skin])) {
			// Determine the file name (note that skin may be inherited)
			$filename=self::find_skin_file($skin);
			if (!$filename) jerror::quit('Skin file not found: <%0%>',$skin);
			$file=self::local_file_contents(_DIR_SKINS,$filename);
			if ($file===false && preg_match('#^/jp/([a-zA-Z0-9_]+)/(.*)$#',$skin,$m)) {
				$file=self::local_file_contents(_DIR_PLUGINS,"$m[1]/$m[2]");
			}
			// Compile and store in cache.
			$cache[$skin]=self::compile($file);
		}
		return $cache[$skin];
	}
	static public function compile($source,$lambda=true) {
		static $search,$replace;
		if (!isset($search)) {
			$search=array(
				'/(\r\n|\r|\n)(?:\t+)([^<%]|<[^%]||%[^>])/',
				'/(\r\n|\r|\n?)(?:\t*)<%([a-zA-Z_][a-zA-Z0-9_]*(?:\.[a-zA-Z_][a-zA-Z0-9_]*)*)%>/',
				'/(\r\n|\r|\n?)(?:\t*)<%([a-zA-Z_][a-zA-Z0-9_]*(?:\.[a-zA-Z_][a-zA-Z0-9_]*)*)\(([\s\S]*?)\)%>/');
			if (ini_get('short_open_tag')) array_unshift($search,'/<\?xml/');
			$replace=self::class.'::compile_cb';
		}
		// Actually, JIT compiler is just a preg_replace_callback.
		//self::$compile_using=array();// Reset <%using%> (this tag must be used in every .inc file)
		$compiled=preg_replace_callback($search,$replace,$source);
		if (!$lambda) return $compiled;
		// Create lambda
		eval(‘$code=function(&$data){?>'.$compiled.'<?php return true;};' );
		if (!is_callable($code)) return jerror::compile_error($source,$compiled);
		return $code;
	}
	static private $compile_using=array();
	static private function compile_cb($matches){
		// Simple replacements
		if (count($matches)<=2) {
			if ($matches[0]=='<?xml') return "<?php echo '<?xml'; ?>";
			else return $matches[0];
		}
		// Remove left tags at each line
		if (substr($matches[0],-2)!='%>') return $matches[1].$matches[2];
		// $matches[1]: return code, $matches[2]: skin-var name, $matches[3]: arguments
		$cr=$matches[1];
		if (3<count($matches)) $args=explode(',',$matches[3]);
		else $args=array();
		// Quote the arguments.
		// Note that rawurlencoded arguments are given.
		static $replace=array("'"=>"\\'",'\\'=>'\\\\');
		foreach ($args as $key=>$value) {
			$args[$key]="'".strtr(rawurldecode($value),$replace)."'";
		}
		$argarray=$args;
		// First argument for skin-var is always $data.
		array_unshift($args,'$data');
		$args=implode(',',$args);
		if (preg_match('/^(if|ifnot|elseif|elseifnot)(?:\.([a-zA-Z0-9\.]+))?$/',$matches[2],$if)) {
			// If-tags.
			$tag=isset($if[2]) ? $if[2]:'';
			$method=self::compile_cb_method($tag,'if_');
			if ($method===false) jerror::quit('If-tag not found: <%0%>',$tag);
			switch($if[1]){
				case 'ifnot':
					return "<?php $cr if (!( $method($args) )) { ?>";
				case 'elseif':
					return "<?php $cr } elseif ( $method($args) ) { ?>";
				case 'elseifnot':
					return "<?php $cr } elseif (!( $method($args) )) { ?>";
				case 'if':
				default:
					return "<?php $cr if ( $method($args) ) { ?>";
			}
		} else switch($matches[2]){
			// The other skinvars.
			// Note that the position of $cr is important.  This makes the template (if/select) description simple.
			case 'using':
				if (isset($matches[3])) {
					foreach(explode(',',$matches[3]) as $namespace){
						self::$compile_using[]=$namespace;
					}
				} else {
					self::$compile_using=array();
				}
				return $cr;
			case 'return':
				return "$cr<?php return true; ?>";
			case 'exit':
				return "$cr<?php exit; ?>";
			case 'note':
				return "$cr<?php /* note */ ?>";
			case 'text':
				// Note that jeans::translate returns values defined or sanitized by htmlspecialchars
				$langs=explode(',',$matches[3]);
				foreach($langs as $key=>$value) $langs[$key]=self::translate($value);
				return $cr.self::fill_html($langs[0],$langs);
			case 'else':
				return "<?php $cr } else { ?>";
			case 'endif':
				return "<?php $cr } /* endif */ ?>";
			case 'select':
				$switch='';
				foreach($argarray as $value) $switch.="[{$value}]";
				return "<?php $cr switch (@\$data{$switch}) { /*";
			case 'case':
				$cases='';
				foreach($argarray as $value) $cases.=" case {$value}:";
				return "<?php $cr break;/**/ $cases ?>";
			case 'caseelse': case 'case.else':
				return "<?php $cr break;/**/ default: ?>";
			case 'endselect':
				return "<?php $cr } /* endselect */ ?>";
			case 'skin':
				// TODO: Following routine will be revised after shifting to PHP 5.3
				static $funcnum=0;
				$funcnum++;
				$funcname="lambda_{$funcnum}".preg_replace('/[^a-z0-9]/','_',self::$current_skin);
				return "<?php $cr \$data['libs']['view']['lambda'][$argarray[0]]='$funcname'; ".
					"if (!function_exists('$funcname')) { function $funcname(&\$data){ ?>";
			case 'endskin':
				return "<?php $cr }; } /* endskin */ ?>";
			default:
				$method=self::compile_cb_method($matches[2]);
				if ($method) return "$cr<?php $method($args); ?>"; 
				return self::hsc($matches[0]);
		}
		
	}
	static private function compile_cb_method($text,$prefix='tag_',$using=true){
		if (count(self::$compile_using) && strpos($text,'.')===false && $using) {
			// <%using%> tag implementation.
			foreach(self::$compile_using as $namespace){
				$tag= ($text=='') ? $namespace:$namespace.'.'.$text;
				$method=self::compile_cb_method($tag,$prefix,false);
				if ($method) return $method;
			}
		}
		if (preg_match('/^jp\.([a-z0-9]+)$/i',$text,$m)) {
			$class='jp_'.$m[1];
			$method=$prefix.$m[1];
		} elseif (preg_match('/^([a-zA-Z0-9\.]+)\.([a-zA-Z0-9]+)$/',$text,$m)) {
			$class=str_replace('.','_',$m[1]);
			$method=$prefix.$m[2];
		} elseif (preg_match('/^[a-zA-Z0-9]+$/',$text,$m)) {
			$class='globalvars';
			$method=$prefix.$m[0];
			if (!is_callable("$class::$method")) {
				$class=$m[0];
				$method=$prefix.$m[0];
			}
		} else jerror::quit('Syntax error: <%0%>',$text);
		if (core::method_exists($class,$method)) return "$class::$method";
		elseif (core::method_exists("j$class",$method)) return "j$class::$method"; // Support jerror class (etc)
		else return false;
	}
	/*
	 * The routine for compiling item follows
	 * Note that "eval" won't be used here, but the methods are directly called for tag implementations.
	 */
	static public function compile_item(&$data,$item){
		static $search=array('/<%([a-zA-Z0-9\.]+)%>/','/<%([a-zA-Z0-9\.]+)\(([\s\S]*?)\)%>/');
		static $replace=self::class.'::'compile_item_cb';
		self::compile_item_cb(array('data'=>&$data,'prefix'=>'itemtag_'),true);
		return preg_replace_callback($search,$replace,$item);
	}
	static private function compile_item_cb($m,$init=false){
		static $data,$prefix;
		if ($init) {
			$data=&$m['data'];
			$prefix=$m['prefix'];
			return;
		}
		$method=self::compile_cb_method($m[1],$prefix,false);
		if (!$method) return self::hsc($m[0]);
		if (2<count($m)) {
			$args=explode(',',$m[2]);
			foreach($args as $key=>$value) $args[$key]=rawurldecode($value);
		} else $args=array();
		array_unshift($args,false);
		$args[0]=&$data;
		return (string)call_user_func_array($method,$args);
	}
	static public function tag_include(&$data,$skin,$mode='parse'){
		static $depth=0;
		$skin_path=self::skin_path($skin,$data['skin']);
		switch($mode){
			case 'parse':
				if (50<$depth) return;
				$depth++;
				$code=self::skin_lambda($skin_path);
				$code($data);
				$depth--;
				return;
			case 'html':
			default:
				self::echo_html(self::local_file_contents(_DIR_SKINS,$skin_path));
				return;
		}
	}
	/**
	 * Nesting is restricted to less than 50 times.
	 * $data['history'] is managed here.
	 */
	static private function nested_parse(&$data,$skin,$template='init') {
		static $depth=0;
		if (50<$depth) return;
		$depth++;
		array_unshift($data['history'],false);
		$data['history'][0]=&$data;
		self::parse_skin($skin,$data['skin'],$data,$template);
		array_shift($data['history']);
		$depth--;
	}
	/**
	 * <%view.parse%> skinvar can have more than 1 argument.
	 * The second and third (also 4th, 5th etc) are taken as <%1%> and <%2%> in 
	 * the child skin file.
	 */
	static public function tag_parse(&$data,$skin){
		$args=func_get_args();
		array_shift($args);
		array_shift($args);
		$skin_path=self::skin_path($skin,$data['skin']);
		array_unshift($args,$skin_path);
		foreach($data as $key=>$value){
			if (is_integer($key)) unset($data[$key]);
		}
		foreach($args as $key=>$value) $data[$key]=$value;
		self::nested_parse($data,$skin);
	}
	static public function tag_template(&$data,$template,$skin=false){
		if (!$skin) $skin=$data['skin'];
		self::nested_parse($data,$skin,$template);
	}
	static public function tag_strftime(&$data,$template,$skin=false,$key='time'){
		$args=func_get_args();
		$time=data::get_data($args,2,'time');
		if (!is_numeric($time)) $time=strtotime($time.' GMT');
		ob_start();
		self::tag_template($data,$template,$skin);
		$format=ob_get_clean();
		self::echo_html(date::strftime($format,$time));
	}
	static public function tag_date(&$data,$template,$skin=false,$key='time'){
		$args=func_get_args();
		$time=data::get_data($args,2,'time');
		if (!is_numeric($time)) $time=strtotime($time.' GMT');
		ob_start();
		self::tag_template($data,$template,$skin);
		$format=ob_get_clean();
		self::echo_html(date($format,$time));
	}
	static public function skinfile(&$data,$file,$full_url=true){
		$org=self::skin_path($file,$data['skin']);
		$path=self::find_skin_file($org);
		if (!$path) $path=$org;
		$path=substr($path,1);
		if (!$full_url) return $path;
		$pf=self::is_plugin_file($path);
		if ($pf!==false) return _CONF_URL_PLUGINS.$pf;
		else return _CONF_URL_SKINS.$path;
	}
	static public function is_plugin_file($path){
		if (preg_match('#jp/([a-zA-Z0-9_]+)/(.*)$#',$path,$m)) {
			if (self::local_file_exists(_DIR_SKINS,$path)) return false;
			if (self::local_file_exists(_DIR_PLUGINS,"$m[1]/$m[2]")) return "$m[1]/$m[2]";
		}
		return false;
	}
	static public function tag_skinfile(&$data,$file){
		self::p(self::skinfile($data,$file));
	}
	/**
	 * General parse routine using SQLite query follows.
	 * Callback function may be used.
	 */
	static public function show_using_query($data,$query,$array,$skin,$pre_cb=false,$post_cb=false){
		$head_parsed=false;
		$res=sql::query($query,$array);
		$counter=1;
		while($row=$res->fetch()){
			if (!$head_parsed) {
				self::tag_template($data,'head',$skin);
				$head_parsed=true;
			}
			sql::convert_xml($row);
			$row['counter']=$counter++;
			if ($pre_cb) call_user_func_array($pre_cb,array(&$row));
			foreach($row as $key=>$value) $data[$key]=$value;
			self::tag_template($data,'body',$skin);
			foreach($row as $key=>$value) unset($data[$key]);
			if ($post_cb) call_user_func_array($post_cb,array(&$row));
		}
		if ($head_parsed) self::tag_template($data,'foot',$skin);
		else self::tag_template($data,'none',$skin);
	}
	static public function show_using_array($data,$array,$skin,$pre_cb=false,$post_cb=false){
		$head_parsed=false;
		if (!count($array)) {
			self::tag_template($data,'none',$skin);
			return;
		}
		self::tag_template($data,'head',$skin);
		$counter=1;
		foreach($array as $row) {
			sql::convert_xml($row);
			$row['counter']=$counter++;
			if ($pre_cb) call_user_func_array($pre_cb,array(&$row));
			foreach($row as $key=>$value) $data[$key]=$value;
			self::tag_template($data,'body',$skin);
			foreach($row as $key=>$value) unset($data[$key]);
			if ($post_cb) call_user_func_array($post_cb,array(&$row));
		}
		self::tag_template($data,'foot',$skin);
	}
	static public function tag_query(&$data,$skin){
		$query=func_get_args();
		array_shift($query);
		array_shift($query);
		$query=implode(',',$query);
		self::show_using_query($data,$query,$data,$skin);
	}
	static public function create_link($array){
		$url=false;
		core::event('generate_url',array('url'=>&$url,'info'=>&$array),'view');
		if ($url===false) $url=_CONF_SELF;
		if (0<count($array)) $url.='?'.implode('&',self::create_link_sub($array));
		return $url;
	}
	static private function create_link_sub($array,$prefix=array()){
		ksort($array);
		$url=array();
		foreach($array as $key=>$value){
			$prefix2=array_merge($prefix,array($key));
			if (is_array($value)) {
				$url=array_merge($url,self::create_link_sub($value,$prefix2));
			} else {
				$temp='';
				while (count($prefix2)) {
					if (strlen($temp)==0) $temp=urlencode(array_shift($prefix2));
					else $temp.='['.urlencode(array_shift($prefix2)).']';
				}
				$url[]=$temp.'='.urlencode($value);
			}
		}
		return $url;
	}
}

class data extends jeans {
	static public function get_data($args,$start,$default=false){
		$data=array_shift($args);
		$value=&$data;
		for($i=0;$i<$start;$i++) array_shift($args);
		if (count($args)==0) return $data[$default];
		while(count($args) && is_array($value)){
			$value=&$value[array_shift($args)];
		}
		// Using lambda by create_function() for <%data%> is EXPERIMENTAL.
		// After releasing PHP5.3 version, this feature will be guaranteed.
		// In such case, do not use "create_function", but use Closure objects.
		if (is_string($value) && substr($value,0,1)!="\0") return $value;
		if (is_array($value) || !is_callable($value)) return $value;
		array_unshift($args,$data);
		return call_user_func_array($value,$args);
	}
	static public function tag_hsc(){
		$args=func_get_args();
		self::p(self::get_data($args,0),'hsc');
	}
	static public function tag_escape_hsc(){
		$args=func_get_args();
		self::p(self::get_data($args,0),'escape_hsc');
	}
	static public function tag_data(){
		$args=func_get_args();
		self::p(self::get_data($args,0));
	}
	static public function tag_t(){
		$args=func_get_args();
		self::p(self::translate(self::get_data($args,0)));
	}
	static public function tag_raw(){
		$args=func_get_args();
		self::echo_html(self::get_data($args,0));
	}
	static public function tag_base64(){
		$args=func_get_args();
		self::p(chunk_split(base64_encode(self::get_data($args,0))));
	}
	static public function tag_parse(&$data){
		$args=func_get_args();
		$code=view::compile(self::get_data($args,0));
		$code($data);
	}
	static public function tag_parseditem(&$data){
		$args=func_get_args();
		$html=view::compile_item($data,self::get_data($args,0));
		self::echo_html($html);
	}
	static public function tag_shorten(){
		$args=func_get_args();
		self::p(self::shorten(self::get_data($args,2),$args[1],$args[2]));
	}
	static public function tag_after(){
		$args=func_get_args();
		$value=self::get_data($args,1);
		$value=substr($value,strpos($value,$args[1])+strlen($args[1]));
		$value=substr($value,0,strpos($value,substr($args[1],-1)));
		self::p($value);
	}
	static public function tag_url(){
		$args=func_get_args();
		self::p(self::get_data($args,0),'url');
	}
	static public function tag_set(&$data,$value){
		$args=func_get_args();
		array_shift($args);
		array_shift($args);
		while(count($args) && is_array($data)) $data=&$data[array_shift($args)];
		$data=$value;
	}
	static public function tag_copyto(&$data,$dest){
		$dest=&$data[$dest];
		$args=func_get_args();
		array_shift($args);
		array_shift($args);
		while(count($args) && is_array($data)) $data=&$data[array_shift($args)];
		$dest=$data;
	}
	static public function tag_uniqueid(&$data,$mode='next'){
		static $id=0;
		switch ($mode) {
			case 'prev':
				$id--;
			case 'same':
				break;
			case 'next':
			default:
				$id++;
		}
		self::p("jeans_id_{$id}_");
	}
	static public function if_is(&$data,$value){
		$args=func_get_args();
		$datavalue=self::get_data($args,1);
		return (string)$datavalue==(string)$value;
	}
	static public function if_isdata(&$data,$value){
		$args=func_get_args();
		$datavalue=self::get_data($args,1);
		return (string)$datavalue==(string)$data[$value];
	}
	static public function if_ismorethan(&$data,$value){
		$args=func_get_args();
		$datavalue=self::get_data($args,1);
		return (float)$datavalue > (float)$value;
	}
	static public function if_islessthan(&$data,$value){
		$args=func_get_args();
		$datavalue=self::get_data($args,1);
		return (float)$datavalue < (float)$value;
	}
	static public function if_contains(&$data,$value){
		$args=func_get_args();
		$datavalue=self::get_data($args,1);
		return strpos($datavalue,$value)!==false;
	}
	static public function if_startsfrom(&$data,$value){
		$args=func_get_args();
		$datavalue=self::get_data($args,1);
		return substr($datavalue,0,strlen($value))==(string)$value;
	}
	static public function if_endswith(&$data,$value){
		$args=func_get_args();
		$datavalue=self::get_data($args,1);
		return substr($datavalue,0-strlen($value))==(string)$value;
	}
	static public function if_match(&$data,$value){
		$args=func_get_args();
		$datavalue=self::get_data($args,1);
		return preg_match($value,$datavalue);
	}
	static public function if_flag(&$data,$test){
		$args=func_get_args();
		$datavalue=self::get_data($args,1);
		return (bool)($datavalue & self::translate($test));
	}
	static private function _isset(&$data,&$args,$isempty=false){
		array_shift($args);
		while (count($args)) {
			$key=array_shift($args);
			if (!isset($data[$key])) return $isempty;
			$data=&$data[$key];
		}
		if ($isempty) return empty($data);
		else return true;
	}
	static public function if_isset(&$data){
		$args=func_get_args();
		return self::_isset($data,$args);
	}
	static public function if_isempty(&$data){
		$args=func_get_args();
		return self::_isset($data,$args,true);
	}
}

class globalvars extends jeans {
	static public function if_classloaded(&$data,$class) {
		return class_exists($class,false);
	}
	static public function if_classavailable(&$data,$class) {
		if (class_exists($class,false)) return true;
		return is_array(core::class_file($class));
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
		core::event($event,$arg,'view');
	}
	static public function tag_header(){
		$args=func_get_args();
		array_shift($args);//&$data
		$args=implode(',',$args);
		header(trim($args));
	}
}