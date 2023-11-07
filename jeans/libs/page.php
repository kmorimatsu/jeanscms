<?php
/*
 * Jeans CMS GPL license)
 * $Id: page.php 216 2010-06-27 18:42:54Z kmorimatsu $
 */

class page extends jeans {
	static public function init(){

	}
	static public function tag_init(&$data,$limit=false,$offset=false,$items=false){
		if (!isset($data['libs'])) $data['libs']=array();
		if (!isset($data['libs']['page'])) $data['libs']['page']=array();
		$page=&$data['libs']['page'];
		if ($limit===false) $limit=@$page['limit'];
		if ($offset===false) $offset=@$page['offset'];
		if ($items===false) $items=@$page['items'];
		$pages=$limit ? intval(1+($items-1)/$limit):1;
		$thispage=$limit ? intval(1+$offset/$limit):1;
		$links=array(
			'first'=>self::create_link($data,1),
			'second'=>self::create_link($data,2),
			'prev'=>self::create_link($data,$thispage-1),
			'this'=>self::create_link($data,$thispage),
			'next'=>self::create_link($data,$thispage+1),
			'penultimate'=>self::create_link($data,$pages-1),
			'last'=>self::create_link($data,$pages));
		$data['libs']['page']=array(
			'limit'=>$limit,
			'offset'=>$offset,
			'pages'=>$pages,
			'items'=>$items,
			'prev'=>$thispage-1,
			'this'=>$thispage,
			'next'=>$thispage+1,
			'penultimate'=>$pages-1,
			'last'=>$pages,
			'link'=>$links);
	}
	static public function create_link(&$data,$page){
		$array=$_GET;
		if (isset($data['libs']['page']['limit'])) $array['offset']=($page-1) * $data['libs']['page']['limit'];
		else $array['offset']=0;
		return view::create_link($array);
	}
	static public function if_prev(&$data){
		return 1<$data['libs']['page']['this'];
	}
	static public function if_next(&$data){
		$page=&$data['libs']['page'];
		return $page['this']<$page['pages'];
	}
	static public function tag_page(&$data,$skin,$max=7){
		$page=&$data['libs']['page'];
		$pages=$page['pages'];
		$thispage=$page['this'];
		if (!$skin) $skin=$data['skin'];
		if (!$max) $max=$pages;
		$array=$row=array();
		$skipped=false;
		for ($i=1;$i<=$pages;$i++) {
			if ($pages<=$max || $i<=2 || $pages-1<=$i 
					|| ($thispage-1<=$i && $i<=$thispage+1)) {
				$row=array('count'=>$i,'link'=>self::create_link($data,$i));
				if ($i==$page['this']) $row['this']=1;
				else $row['this']=0;
				$array[]=$row;
				$skipped=false;
			} else {
				if ($skipped) continue;
				$row=array('count'=>0, 'link'=>'');
				$array[]=$row;
				$skipped=true;
			}
		}
		view::show_using_array($data,$array,$skin);
	}
}