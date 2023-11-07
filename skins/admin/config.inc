<%note(
 * Jeans CMS (GPL license)
 * $Id$
)%>
<%select(template)%>
<%case(init)%>
	<h2><%text(_ADMIN_CONFIGURATION)%></h2>

	<%error.note(templates/note.inc)%>

	<%admin.config.conflist%>
<%case(head)%>
	<form method="post" action=""><ins>
	<input type="hidden" name="action" value="admin.config.edit" />
	<%ticket.hidden(admin.config.edit)%>
	<h3><%text(_ADMIN_GENERAL_SETTINGS)%></h3>
	<table class="nogrid config">
<%case(body)%>
	<tr>
	<%if.data.is(separator,type)%>
		<th><%data(desc)%><%admin.help.link(name,conf_)%></th><th></th>
	<%else%>
		<td class="col1"><%data(desc)%><%admin.help.link(name,conf_)%></td><td class="col2"><%view.parse(templates/forminput.inc)%></td>
	<%endif%>
	</tr>
<%case(foot)%>
	<tr><th><%text(_ADMIN_UPDATE_SETTINGS)%></th><th></th></tr>
	<tr><td><%text(_ADMIN_UPDATE_SETTINGS)%></td><td><input type="submit" class="submit button" value="<%text(_ADMIN_UPDATE)%>"/></td></tr>
	</table>
	</ins></form>
<%case(none)%>
	<%text(_ADMIN_NOTHING_TO_SHOW)%>
<%endselect%>
