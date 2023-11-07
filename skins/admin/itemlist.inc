<%note(
 * Jeans CMS (GPL license)
 * $Id$
)%>
<%if.data.isset(_GET,mid)%>
	<h2>
		<%text(_ADMIN_ITEM_ITEMS_BY)%> <a href=""><%admin.memberinfo.data(loginname)%></a>
		<%ifnot.data.isempty(_GET,option,draft)%> (<%text(_ADMIN_FLAG_DRAFT)%>)<%endif%>
	</h2>
<%elseif.data.isset(_GET,sgid)%>
	<h2><%text(_ADMIN_ITEM_ITEMS_IN_SUBGROUP)%> <a href=""><%group.sgsetting(name)%></a></h2>
<%elseif.data.isset(_GET,gid)%>
	<h2><%text(_ADMIN_ITEM_ITEMS_IN_GROUP)%> <a href="<%group.setting(url)%>"><%group.setting(name)%></a></h2>
<%else%>
	<h2><%text(_ADMIN_ITEM_ITEMS)%></h2>
<%endif%>

<%error.note(templates/note.inc)%>

<%ifnot.data.isempty(_GET,option,draft)%><%admin.item.narrowby(draft)%><%endif%>
<%admin.item.list(templates/itemlist.inc)%>
