<%note(
 * Jeans CMS (GPL license)
 * $Id$
)%>
<%select(template)%>
<%case(init)%>
	<h2><%text(_ADMIN_LOG_LIST)%></h2>

	<%error.note(templates/note.inc)%>
	
	<form method="post" action="">
	<input name="action" type="hidden" value="admin.log.delete" />
	<%ticket.hidden(admin.log.delete)%>
	<input type="submit" class="submit button" value="<%text(_ADMIN_LOG_DELETE)%>" />&nbsp;&nbsp;&nbsp;
	<input type="checkbox" name="vacuum" /><%text(_ADMIN_LOG_VACUUM)%>
	</form>
	
	<%admin.log.list%>

<%case(head)%>
	<div style="float:right;"><%page.init%>
	<%text(_ADMIN_PAGE)%> <%data(libs,page,this)%>/<%data(libs,page,pages)%>
	<%if.data.ismorethan(1,libs,page,pages)%><%page(templates/paging.inc)%><%endif%>
	</div>
	<table class="grid">
	<tr>
		<th><%text(_ADMIN_LOG_IP)%></th>
		<th><%text(_ADMIN_LOG_TIME)%></th>
		<th><%text(_ADMIN_LOG_MEMBER)%></th>
		<th><%text(_ADMIN_LOG_CLASS)%></th>
		<th><%text(_ADMIN_LOG_TYPE)%></th>
		<th><%text(_ADMIN_LOG_DESC)%></th>
		<th>URI</th>
		<th><%text(_ADMIN_LOG_REFERER)%></th>
	</tr>

<%case(body)%>
	<tr>
		<td><%data(ip)%></td>
		<td><%date(Y-m-d H:i:s)%></td>
		<td><%ifnot.data.isempty(mid)%><%data(member)%> (<%data(mid)%>)<%else%>-<%endif%></td>
		<td><%data(owner)%></td>
		<td><%data(type)%></td>
		<td title="<%data.hsc(desc)%>"><%data.shorten(30,...,desc)%></td>
		<td title="<%data.hsc(uri)%>"><%data.shorten(30,...,uri)%></td>
		<td title="<%data.hsc(referer)%>"><%data.shorten(30,...,referer)%></td>
	</tr>

<%case(foot)%>
	</table>

<%case(none)%>
	<%text(_ADMIN_NOTHING_TO_SHOW)%>

<%endselect%>