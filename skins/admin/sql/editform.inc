<%note(
 * Jeans CMS (GPL license)
 * $Id$
)%>
<%select(template)%>
<%case(head)%>
	<form method="post" action=""><ins>
	<input type="hidden" name="action" value="admin.sql.edit" />
	<%ticket.hidden(admin.sql.edit)%>
	<input type="hidden" name="table" value="<%data(libs,admin_sql,tablename)%>" />
	<input type="hidden" name="query_text" value="<%data.hsc(_POST,query_text)%>" />
	<table class="nogrid">
	<tr><th>key</th><th>value</th></tr>

<%case(body)%>
	<tr><td><%data(key)%></td>
	<td><%if.data.is(hidden,extra)%><%data(value)%><%endif%><%view.parse(../templates/forminput.inc)%></td></tr>
<%case(foot)%>
	<tr>
	<td><input type="submit" class="submit button" value="<%text(_ADMIN_SQL_SAVE)%>" /></td>
	<td style="text-align: right;"><%ifnot.data.isempty(_POST,itemid)%>
		<input type="checkbox" name="delete" value="true" /><%text(_ADMIN_SQL_CHECK_TO_DELETE)%>
	<%endif%></td>
	</tr>
	</table>
	</ins></form>
<%endselect%>
