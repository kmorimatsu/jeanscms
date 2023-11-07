<%note(
 * Jeans CMS (GPL license)
 * $Id$
)%>
<div id="quickmenu_hide" style="text-align: right; cursor: pointer;" title="<%text(_ADMIN_MENU_HIDEMENU)%>" onclick="
	$('#quickmenu_main').get(0).style.display='none';
	$('#content').get(0).style.marginLeft='35px';
	$('#quickmenu').get(0).style.width='15px';
	$('#quickmenu_show').get(0).style.display='block';
	this.style.display='none';
">
	<img src="<%view.skinfile(images/triangle_left.gif)%>" alt="hide" width="16" height="16" />
</div>
<div id="quickmenu_show" style="display:none; cursor: pointer;" title="<%text(_ADMIN_MENU_SHOWMENU)%>" onclick="
	$('#quickmenu_main').get(0).style.display='block';
	$('#content').get(0).style.marginLeft='175px';
	$('#quickmenu').get(0).style.width='150px';
	$('#quickmenu_hide').get(0).style.display='block';
	this.style.display='none';
">
	<img src="<%view.skinfile(images/quickmenu.gif)%>" alt="hide" width="15" height="79" />
</div>
