<%note(
 * Jeans CMS (GPL license)
 * $Id$
)%>
<%select(template)%>
<%case(init)%>
	<h2><%text(_ADMIN_EDITCOMMENT)%></h2>

	<%admin.comments.list(editcomment.inc)%>

<%case(head)%>
	<form id="ajax_content" method="post" action=""><ins>
	<input type="hidden" name="action" value="admin.comment.edit" />
	<%ticket.hidden(admin.comment.edit)%>

	<%error.note(templates/note.inc)%>

	<input type="hidden" name="redirect_url" value="<%data(_SERVER,HTTP_REFERER)%>" />
	<table class="nogrid"><tr><th colspan="2"><%text(_ADMIN_EDITCOMMENT)%></th></tr>

<%case(body)%>
	<tr><td><%text(_ADMIN_INFORMATION)%></td>
	<td>
		id:<%data(id)%> 
		(<a href="<%text(_CONF_SELF)%>?page=commentlist&amp;itemid=<%data(itemid)%>">itemid:<%data(itemid)%></a>)
		ip:<%data(ip)%><br />
		author: <%if.data.isempty(author)%><%data(user)%>
		<%else%><a href="<%text(_CONF_SELF)%>?page=commentlist&amp;mid=<%data(author)%>"><%data(user)%> (member)</a><%endif%><br />
		<%view.strftime(date)%>
	</td>
	<tr><td><%text(_ADMIN_COMMENT)%></td><td><textarea name="body_text" id="inputbody" cols="60" rows="15"><%data.hsc(body)%></textarea></td></tr>
	<%view.parse(templates/flags.inc,comment)%>
	<tr><td><%text(_ADMIN_SUBMIT)%></td><td><input type="submit" class="submit button" value="<%text(_ADMIN_EDITCOMMENT_SUBMIT_BUTTON)%>" /></td></tr>

<%case(foot)%>
	</table>
	</ins></form>

<%case(none)%>
	<%error.note(templates/note.inc)%>
	<%text(_ADMIN_NOTHING_TO_SHOW)%>

<%case(date)%>
	%Y-%m-%d %H:%M
<%endselect%>