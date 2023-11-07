/*
 * Jeans CMS (GPL license)
 * $Id: map.js 216 2010-06-27 18:42:54Z kmorimatsu $
 */
var libs_admin_map_clickicon=function(img,id){
	var expand=libs_admin_map_text('expand',0)+'';
	var collapse=libs_admin_map_text('collapse',0)+'';
	var display;
	var src=img.src+'';
	if (img.title+''==expand) {
		img.title=collapse;
		img.src=src.replace(/right.gif$/,'down.gif');
		var open=function(id){
			var img=$('#libs_admin_map_triangle_'+id).get(0);
			var display=img.title+''==expand ?'none':'block';
			var childs=libs_admin_map_childs(id);
			for(var i=0;i<childs.length;i++) {
				$('#libs_admin_map_tr_'+childs[i]).get(0).style.display=display;
				arguments.callee(childs[i]);
			}
		};
		open(id);
	} else {
		img.title=expand;
		img.src=src.replace(/down.gif$/,'right.gif');
		var close=function(id){
			var childs=libs_admin_map_childs(id);
			for(var i=0;i<childs.length;i++) {
				$('#libs_admin_map_tr_'+childs[i]).get(0).style.display='none';
				arguments.callee(childs[i]);
			}
		};
		close(id);
	}
};
var libs_admin_map_text=function(key,t){
	if (!arguments.callee.text) arguments.callee.text=[];
	if (t) arguments.callee.text[key]=t;
	return arguments.callee.text[key];
};
var libs_admin_map_childs=function(id,parent){
	if (!arguments.callee.childs) arguments.callee.childs=[];
	if (!parent) {
		if (arguments.callee.childs[id]) return arguments.callee.childs[id];
		else return [];
	}
	if (!arguments.callee.childs[parent]) arguments.callee.childs[parent]=[];
	var i=arguments.callee.childs[parent].length;
	arguments.callee.childs[parent][i]=id;
};
var libs_admin_map_clickname=function(element,id){
	$('#libs_admin_map_rightwindow').get(0).innerHTML=$('#libs_admin_map_info_'+id).get(0).innerHTML;
	libs_admin_map_changelinks();
	return false;
};
var libs_admin_map_clickitem=function(element,id){
	$('#libs_admin_map_rightwindow').get(0).innerHTML=$('#libs_admin_map_iteminfo_'+id).get(0).innerHTML;
	libs_admin_map_changelinks();
	return false;
};
var libs_admin_map_changelinks=function(){
	var a=$('#libs_admin_map_rightwindow a').get();
	var i;
	for(i=0;i<a.length;i++) {
		if (!a[i].onclick)a[i].onclick=function(){ return libs_admin_map_loadtorightwindow(this.href); };
	}
}
var libs_admin_map_clickright=function(img,id){
	var show=libs_admin_map_text('showitems',0)+'';
	var hide=libs_admin_map_text('hideitems',0)+'';
	var tr=$('#libs_admin_map_itemtr_'+id).get(0);
	var td=$('#libs_admin_map_itemtd_'+id);
	var display;
	var src=img.src+'';
	if (img.title+''==show) {
		img.title=hide;
		img.src=src.replace(/left.gif$/,'down.gif');
		tr.style.display='block';
		var cb=function(){
			var content=$('#ajax_content').get(0);
			if (!content) return alert('Server error');
			while (content=$('#ajax_content').get(0)) content.setAttribute('id','ajax_content_loaded');
		};
		td.get(0).innerHTML=$('#libs_admin_map_busystatus').get(0).innerHTML;
		td.load('?custom=map&page=itemlist&sgid='+id+' #ajax_content','',cb);
	} else {
		img.title=show;
		img.src=src.replace(/down.gif$/,'left.gif');
		tr.style.display='none';
	}
};
var libs_admin_map_moreitem=function(element,url){
	var originalHTML=element.innerHTML;
	var cb=function(){
		var content=$('#ajax_content').get(0);
		if (!content) {
			element.innerHTML=originalHTML;
			return alert('Server error');
		}
		element.onclick=false;
		while (content=$('#ajax_content').get(0)) content.id='ajax_content_loaded';
	};
	element.innerHTML=$('#libs_admin_map_busystatus').get(0).innerHTML;
	url=jeans_local_url(url);
	$(element).load(url+' #ajax_content','',cb);
}
var libs_admin_map_submit=function(){};
var libs_admin_map_loadtorightwindow=function(url,data){
	var target=$('#libs_admin_map_rightwindow');
	var cb=function(responseText,status,XMLHttpRequest){
		var content=$('#ajax_content').get(0);
		if (!content) {
			if (!responseText) alert('Server error');
			return;
		};
		content.setAttribute('id','ajax_content_loaded');
		// Change the target of links to right window
		libs_admin_map_changelinks();
		// Execute included javascripts
		var scripts=$('#ajax_content script',responseText).get();
		for(var i=0;i<scripts.length;i++) {
			var script = document.createElement('script');
			script.setAttribute('type','text/javascript');
			if (scripts[i].src) script.src=jeans_local_url(scripts[i].src);
			else {
				try {
					script.appendChild(document.createTextNode(scripts[i].innerHTML));
				} catch(e) {
					script.text=scripts[i].innerHTML;
				}
			}
			target.get(0).appendChild(script);
		}
		// Hook submit event
		if ((content.getAttribute('method')+'').toLowerCase()=='post') {
			content.setAttribute('action','javascript:libs_admin_map_submit();');
			libs_admin_map_submit=function(){
				var data=$(content).serializeArray();
				target.get(0).innerHTML=$('#libs_admin_map_busystatus').get(0).innerHTML;
				libs_admin_map_loadtorightwindow(url,data);
			};
		}
	}
	target.get(0).innerHTML=$('#libs_admin_map_busystatus').get(0).innerHTML;
	url=jeans_local_url(url);
	target.load(url+' #ajax_content',data,cb);
	return false;
};
