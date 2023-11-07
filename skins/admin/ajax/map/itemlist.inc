<%note(
 * Jeans CMS (GPL license)
 * $Id$
)%>
<%select(template)%>
<%case(init)%>
	<%admin.item.list(itemlist.inc)%>
<%case(head)%>
	<div id="ajax_content">
	<table class="nogrid">	

<%case(body)%>
	<tr><td>
		<img class="sitemap" src="<%view.skinfile(../../images/page.gif)%>" alt="item" width="16" height="16" />
		<a <%if.data.flag(sql::FLAG_HIDDEN,flags)%>style="color: grey;"<%endif%> href="<%text(_CONF_SELF)%>?page=edititem&itemid=23" title="<%data.shorten(300,...,body)%>" onclick="return libs_admin_map_clickitem(this,'<%data(id)%>');"><%data(title)%></a>
	</td><td style="display:none;" id="libs_admin_map_iteminfo_<%data(id)%>">
		itemid: <%data(id)%>
		<%view.include(../../templates/flagstatus.inc)%>
		<br />
		title: <%data(title)%><br />
		author: <%data(aname)%><br />
		date: <%date.strftime(%Y-%m-%d)%><br />
		time: <%date.strftime(%H:%M)%><br />
		<a href="<%text(_CONF_SELF)%>?page=edititem&amp;itemid=<%data(id)%>" onclick="return libs_admin_map_loadtorightwindow(this);"><%text(_ADMIN_ITEM_EDIT)%></a><br />
		<a href="<%text(_CONF_SELF)%>?page=commentlist&amp;itemid=<%data(id)%>"><%text(_ADMIN_ITEM_COMMENT_LIST)%></a><br />
		<%text(_ADMIN_ITEM_MOVE)%><br />
		<a href="#Delete" onclick="return libs_admin_batch_submit('admin.batch.deleteitems','<%data(id)%>');"><%text(_ADMIN_ITEM_DELETE)%></a>
	</td></tr>
	
<%case(foot)%>
	<%page.init%>
	</table>
	<%if.page.next%>
		<div style="cursor: pointer;" onclick="libs_admin_map_moreitem(this,'<%data(libs,page,link,next)%>');"><%text(_ADMIN_SITEMAP_SHOW_MORE_ITEMS)%></div>
	<%endif%>
	</div>

<%case(none)%>
	<div id="ajax_content"><%text(_ADMIN_NOTHING_TO_SHOW)%></div>
<%endselect%>