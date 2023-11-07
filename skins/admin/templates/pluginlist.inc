<%note(
 * Jeans CMS (GPL license)
 * $Id$
)%>
<%select(template)%>
<%case(head)%>
	<table class="grid">
	<tr><th><%text(_ADMIN_PLUGIN_INSTALL_PLUGIN)%></th></tr>
	<tr><td>
		<form method="post" action="">
		<input type="hidden" name="action" value="admin.plugin.install" />
		<%ticket.hidden(admin.plugin.install)%>
		<select name="plugin">

<%case(body)%>
		<option name="<%data(name)%>"><%data(name)%></option>

<%case(foot)%>
		</select>
		<input type="submit" class="submit button" value="<%text(_ADMIN_PLUGIN_INSTALL)%>" />
		</form>
	</td></tr>
	</table>

<%endselect%>