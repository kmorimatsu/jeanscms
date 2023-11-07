<%note(
 * Jeans CMS (GPL license)
 * $Id$
)%>
<%select(template)%>
<%case(head)%>
<table class="grid">
<tr><td colspan="4" style="text-align: center;"><%text(_ADMIN_SQL_DATAVASE_FILES)%></td></tr>
<%case(body)%>
<tr>
<td><%data(mode)%></td>
<td><%data(file)%></td>
<td><%data(size)%>&nbsp;bytes</td>
<td>
	<form method="post" action=""><ins>
	<input type="hidden" name="action" value="admin.sql.backup" />
	<%ticket.hidden(admin.sql.backup)%>
	<input type="hidden" name="file" value="<%data(file)%>" />
	<input type="submit" class="submit button" value="backup" />
	</ins></form>
</td>
</tr>
<%case(foot)%>
</table>
<%endselect%>
