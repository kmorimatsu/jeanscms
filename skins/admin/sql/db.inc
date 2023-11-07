<%note(
 * Jeans CMS (GPL license)
 * $Id$
)%>
<h2><%text(_ADMIN_DATABASE_MAINTENANCE)%></h2>

<%error.note(../templates/note.inc)%>

<table class="grid"><tr><th style="width: 30%;"><%text(_ADMIN_SQL_ACTIONS)%></th><th><%text(_ADMIN_SQL_QUERY)%></th></tr>
<tr><td>
<%admin.sql.dbfilelist(dbfilelist.inc)%>
<form method="post" action=""><ins>
<input type="hidden" name="action" value="admin.sql.vacuum" />
<%ticket.hidden(admin.sql.vacuum)%>
<input type="submit" class="submit button" value="<%text(_ADMIN_SQL_VACUUM)%>" />
</ins></form>
<form method="post" action=""><ins>
<input type="hidden" name="action" value="admin.sql.query" />
<%ticket.hidden(admin.sql.query)%>
<input type="hidden" name="query_text" value="SELECT 'tablelist' as libs_admin_sql_mode, tbl_name as name, sql as table_sql, get_table_index(tbl_name) as index_sql FROM sqlite_master WHERE type='table'" />
<input type="submit" class="submit button" value="<%text(_ADMIN_SQL_TABLE_LIST)%>" />
</ins></form>

</td><td>

<form method="post" action="" id="libs_admin_sql_queryform"><ins>
<input type="hidden" name="action" value="admin.sql.query" />
<%ticket.hidden(admin.sql.query)%>
<textarea name="query_text" style="width: 95%; height: 150px;"><%data.hsc(_POST,query_text)%></textarea>
<input type="submit" class="submit button" value="<%text(_ADMIN_SQL_EXEC_QUERY)%>" />
</ins></form>

</td></tr></table>

<%select(_POST,action)%>
<%case(admin.sql.query,admin.sql.edit)%>
	<%admin.sql.queryresult(queryresult.inc)%>
<%case(admin.sql.editform)%>
	<%admin.sql.editform(editform.inc)%>
<%endselect%>
