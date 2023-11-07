/*
 * Jeans CMS (GPL license)
 * $Id: edititem.js 338 2014-10-15 03:59:08Z kmorimatsu $
 */
var jeans_edititem_focus=function(element){
	if (element) this.element=element;
	return this.element;
}
var jeans_edititem_keypress=function(element,event){
	if (event.keyCode!=13) return true;
	if (event.shiftKey) return true;
	jeans_edititem_add_at_cursor(element,'<br />');
	return true;
};
var jeans_edititem_add_at_cursor=function(element,text){
	if (!element) element=jeans_edititem_focus(0);
	element.focus();
	if (element.createTextRange) {
		// IE
		var range = document.selection.createRange();
		range.text=text;
		range.setEndPoint('StartToEnd',range);
		range.select();
		return;
	} else {
		// FireFox
		var t=''+element.value;
		var start=element.selectionStart;
		var end=element.selectionEnd;
		element.value=t.substring(0,start)+text+t.substring(start);
		element.selectionStart=start+text.length;
		element.selectionEnd=end+text.length;
		return;
	}
};
var jeans_edititem_clear_around_cursor=function(element){
	if (!element) element=jeans_edititem_focus(0);
	element.focus();
	if (element.createTextRange) {
		// IE
		var range = document.selection.createRange();
		range.text='';
		range.select();
		return;
	} else {
		// FireFox
		var t=''+element.value;
		var start=element.selectionStart;
		var end=element.selectionEnd;
		element.value=t.substring(0,start)+t.substring(end);
		element.selectionStart=start;
		element.selectionEnd=start;
		return;
	}
}
var jeans_edititem_selection=function(element){
	if (!element) element=jeans_edititem_focus(0);
	if (element.createTextRange) {
		// IE
		var range = document.selection.createRange();
		return ''+range.text;
	} else {
		// FireFox
		var t=''+element.value;
		var start=element.selectionStart;
		var end=element.selectionEnd;
		return t.substring(start,end);
	}
};
var jeans_edititem_add_around_cursor=function(pre,post){
	if (!element) element=jeans_edititem_focus(0);
	var selection=pre+jeans_edititem_selection(element)+post;
	element.focus();
	if (element.createTextRange) {
		// IE
		var range = document.selection.createRange();
		range.text=selection;
		range.select();
		return;
	} else {
		// FireFox
		var t=''+element.value;
		var start=element.selectionStart;
		var end=element.selectionEnd;
		element.value=t.substring(0,start)+selection+t.substring(end);
		element.selectionStart=start;
		element.selectionEnd=start+selection.length;
		return;
	}
};
var jeans_edititem_add_media_button=function(){
	window.open('?page=media','jeans_media');
};
var jeans_edititem_insert_image=function(url,name,width,height,popup){
	var selection=jeans_edititem_selection(0);
	if (selection=='') selection=name;
	if (popup) {
		// URL must contain /skins/ if it is the link to files in media directory.
		// On the other hand, DB file name should not contain /skins/
		var skin=url.lastIndexOf('/skins/',url.length);
		if (skin<0) {
			url=url.substring(url.lastIndexOf('=')+1);
			url='?imagepopup=db&image_path='+url+'&alt_text='+jeans_htmlspecialchars(selection);
		} else {
			url=url.substring(skin+7);
			url='?imagepopup=skin&image_path='+url+'&alt_text='+jeans_htmlspecialchars(selection);
		}
		url=jeans_htmlspecialchars(url);
		var script="window.open(this.href,'imagepopup','status=no,toolbar=no,scrollbars=no,resizable=yes,width="+width+",height="+height+"');return false;";
		jeans_edititem_add_at_cursor(0,'<a href="'+url+'" onclick="'+script+'">'+selection+'</a>');
		jeans_edititem_clear_around_cursor();
	} else {
		url=jeans_htmlspecialchars(url);
		selection=jeans_htmlspecialchars(selection);
		jeans_edititem_add_at_cursor(0,'<img src="'+url+'" alt="'+selection+'" width="'+width+'" height="'+height+'" />');
	}
};
var jeans_edititem_insert_image_in_opener=function(url,name,width,height){
	if (!window.opener) return false;
	if (!window.opener.jeans_edititem_insert_image) return false;
	window.opener.jeans_insert_image(url,name,width,height);
	window.close();
	return false;
}
