<%note(
 * Jeans CMS (GPL license)
 * $Id$
)%>
<h2><%text(_ADMIN_BATCH_CONFIRM)%></h2>

<%if.data.isempty(_POST,batch_action)%>
	<%error.note(templates/note.inc)%>
<%else%>
	<form id="ajax_content" method="post" action="<%admin.batch.actionurl%>"><ins>
	<input type="hidden" name="action" value="<%data(_POST,batch_action)%>" />
	<%admin.batch.tickethidden(_POST,batch_action)%>
	<input type="hidden" name="ids" value="<%data(_POST,ids)%>" />
	
	<%error.note(templates/note.inc)%>
	<table class="grid">
	<tr><th colspan="2"><%text(_ADMIN_BATCH_CONFIRM)%></th></tr>
	<tr><td style="color:red;"><%admin.batch.todo%></td><td style="color:red;"><%admin.batch.information%></td></tr>
	<%admin.batch.extra(templates/batchextra.inc)%>
	<tr><td><%text(_ADMIN_BATCH_CONTINUE)%></td>
	<td>
		<input type="submit" class="submit button" value="<%text(_JEANS_YES)%>" />&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="button" class="cancel button" value="<%text(_JEANS_CANCEL)%>" onclick="document.location='<%admin.batch.actionurl%>';" />
	</td></tr>
	</table>
	
	</ins></form>
<%endif%>

