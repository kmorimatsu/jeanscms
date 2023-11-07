<%note(
 * Jeans CMS (GPL license)
 * $Id$
)%>
<%select(template)%>
<%case(head)%>
	<form method="post" action=""><ins>
	<input type="hidden" name="action" value="admin.group.edit" />
	<%ticket.hidden(admin.group.edit)%>

	<%if.data.isset(_GET,gid)%>
		<h3><%text(_ADMIN_GROUP_NEW_SUBGROUP)%></h3>
	<%else%>
		<h3><%text(_ADMIN_GROUP_NEW_GROUP)%></h3>
	<%endif%>

	<table class="nogrid">
<%case(body)%>
	<%if.data.is(text,type)%>
		<%if.data.is(hidden,extra)%><tr style="display:none;"><%else%><tr><%endif%>
	<%else%>
		<tr>
	<%endif%>
	<%if.data.is(separator,type)%>
		<th><%data(desc)%></th><th></th>
	<%else%>
		<td><%data(desc)%><%admin.help.link(name,group_)%></td><td><%view.parse(forminput.inc)%></td>
	<%endif%>
	</tr>
<%case(foot)%>
	<tr><td><%text(_ADMIN_SUBMIT)%></td><td><input type="submit" class="submit button" value="<%text(_ADMIN_GROUP_CREATE)%>" /></td></tr>
	</table>
	</ins></form>
<%case(none)%>
	<%text(_ADMIN_NOTHING_TO_SHOW)%>
<%endselect%>