<%note(
 * Jeans CMS (GPL license)
 * $Id$
)%>
<%select(template)%>
<%case(head)%>
	<table class="grid">
	<tr><th><%text(_ADMIN_GROUP_TREE)%></th><th><%text(_ADMIN_INFORMATION)%></th><th><%text(_ADMIN_ACTIONS)%></th></tr>

<%case(body)%>
	<tr>
	<%if.data.is(0,gid)%>
		<td><a href="<%text(_CONF_SELF)%>?page=groupsetting&amp;gid=<%data(id)%>"><%data(name)%></a></td>
	<%else%>
		<td><%group.treetab(tab)%><a href="<%text(_CONF_SELF)%>?page=groupsetting&amp;sgid=<%data(id)%>"><%data(name)%></a></td>
	<%endif%>
	<td>id: <%data(id)%>
	<%view.include(flagstatus.inc)%><br />
	<%text(_ADMIN_NAME)%>:<%data.hsc(name)%><br />
	<%text(_ADMIN_GROUP_DESCRIPTION)%>: <%data(desc)%><br />
	<a href="<%text(_CONF_SELF)%>?page=itemlist&amp;sgid=<%data(id)%>"><%text(_ADMIN_GROUP_SHOW_ITEMS)%></a></td>
	<%if.data.is(0,gid)%>
		<td><a href="<%text(_CONF_SELF)%>?page=groupsetting&amp;gid=<%data(id)%>"><%text(_ADMIN_GROUP_EDIT)%></a><br />
	<%else%>
		<td><a href="<%text(_CONF_SELF)%>?page=groupsetting&amp;sgid=<%data(id)%>"><%text(_ADMIN_GROUP_EDIT)%></a><br />
	<%endif%>
	<%text(_ADMIN_GROUP_MOVE)%><br />
	<a href="#Delete" onclick="return libs_admin_batch_submit('admin.batch.deletegroups','<%data(id)%>');"><%text(_ADMIN_GROUP_DELETE)%></a><br />
	<%text(_ADMIN_GROUP_CREATE)%>
	<a href="<%text(_CONF_SELF)%>?page=newgroup&amp;sgid=<%data(id)%>"><%text(_ADMIN_GROUP_A_SUBGROUP)%></a> / 
	<a href="<%text(_CONF_SELF)%>?page=edititem&amp;itemid=0&amp;sgid=<%data(id)%>"><%text(_ADMIN_GROUP_AN_ITEM)%></a></td>
	</tr>
	<%group.treesub%>

<%case(foot)%>
	</table>
	<%view.parse(../batchform.inc)%>

<%case(tab)%>
	&nbsp;&nbsp;&nbsp;
<%case(none)%>
	There is no group.
<%endselect%>