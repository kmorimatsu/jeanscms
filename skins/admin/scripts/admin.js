/*
 * Jeans CMS (GPL license)
 * $Id: admin.js 216 2010-06-27 18:42:54Z kmorimatsu $
 */
var jeans_vdivider=function(window,width){
	this.enabled=false;
	this.X=false;
	var style=$(window).get(0).style;
	style.width=width;
	this.enable=function(e){
		this.enabled=true;
	};
	this.disable=function(e){
		this.enabled=false;
	};
	this.move=function(e){
		if (!this.enabled) return;
		if (this.X) {
			var move=e.pageX-this.X;
			if (-6<move && move<6) style.width=(parseInt(style.width)+move).toString()+'px';
		}
		this.X=e.pageX;
	};
};
var jeans_vdivider_register=function(divider,window,width){
	var obj=new jeans_vdivider(window,width);
	var element=$(divider);
	var style=element.get(0).style;
	element.mousedown(obj.enable);
	element.mouseup  (obj.disable);
	element.mouseout (obj.disable);
	element.mousemove(obj.move);
	style.width='16px';
	style.cursor='e-resize';
	style.backgroundRepeat='repeat-y';
};
var jeans_local_url=function(url){
	url=url+'';
	var lc=url.toLowerCase();
	var dl=(document.location+'').toLowerCase();
	if (url.indexOf(':')<0) return url;
	if (url.indexOf('//')<0) return url;
	var re=new RegExp('^[a-z]+://[^/]+/');
	var m=lc.match(re);
	if (m) {
		m=m[0];
		if (m==dl.substring(0,m.length)) return url;
	}
	alert('Non-local URL:\n' + url);
	return 'javascript:';
};
var jeans_htmlspecialchars=function(text){
	text = text.replace(/&/g, '&amp;') ;
	text = text.replace(/</g, '&gt;') ;
	text = text.replace(/>/g, '&lt;') ;
	text = text.replace(/\"/g, '&quot;') ;
	return text.replace(/\'/g, '&#039;') ;
}