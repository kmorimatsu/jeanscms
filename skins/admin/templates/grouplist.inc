<%note(
 * Jeans CMS (GPL license)
 * $Id$
)%>
<%select(template)%>
<%case(head)%>
	<table class="grid"><tr>
	<th><%text(_ADMIN_NAME)%></th>
	<th colspan="5"><%text(_ADMIN_ACTIONS)%></th>
	</tr>

<%case(body)%>
	<tr>
	<td title="groupid: <%data(id)%>, <%data(desc)%>">
		<a href="<%data(url)%>"><img src="<%view.skinfile(../images/globe.gif)%>" alt="globe"/></a> <%data(name)%>
		<%view.include(flagstatus.inc)%>
	</td>
	<td title="<%text(_ADMIN_GROUP_ADD_ITEM_DESC)%>"><a href="<%text(_CONF_SELF)%>?page=edititem&amp;itemid=0&amp;sgid=<%data(id)%>"><%text(_ADMIN_GROUP_ADD_ITEM)%></a></td>
	<td title="<%text(_ADMIN_GROUP_SUBGROUP_LIST_DESC)%>"><a href="<%text(_CONF_SELF)%>?page=subgrouplist&amp;gid=<%data(id)%>"><%text(_ADMIN_GROUP_SUBGROUP_LIST)%></a></td>
	<td title="<%text(_ADMIN_GROUP_ITEM_LIST_DESC)%>"><a href="<%text(_CONF_SELF)%>?page=itemlist&amp;gid=<%data(id)%>"><%text(_ADMIN_GROUP_ITEM_LIST)%></a></td>
	<td title="<%text(_ADMIN_GROUP_COMMENT_LIST_DESC)%>"><a href="<%text(_CONF_SELF)%>?page=commentlist&amp;gid=<%data(id)%>"><%text(_ADMIN_GROUP_COMMENT_LIST)%></a></td>
	<td title="<%text(_ADMIN_GROUP_SETTINGS_DESC)%>"><a href="<%text(_CONF_SELF)%>?page=groupsetting&amp;gid=<%data(id)%>"><%text(_ADMIN_GROUP_SETTINGS)%></a></td>
	</tr>

<%case(foot)%>
	</table>

<%case(none)%>
	<table class="grid"><tr><td><%text(_ADMIN_NOTHING_TO_SHOW)%></td></tr></table>
<%endselect%>