<%note(
 * Jeans CMS (GPL license)
 * $Id$
)%>
<%select(template)%>
<%case(init)%>
	<%ifnot.data.isempty(_GET,itemid)%>
		<h2><%text(_ADMIN_COMMENTS_FOR_ITEM)%> #<%item.data(id)%></h2>
	<%elseifnot.data.isempty(_GET,mid)%>
		<h2><%text(_ADMIN_COMMENTS_BY_MEMBER)%> <%memberinfo(name)%></h2>
	<%elseifnot.data.isempty(_GET,gid)%>
		<h2><%text(_ADMIN_COMMENTS_FOR_GROUP)%> <%group.setting(name)%></h2>
	<%endif%>

	<div id="ajax_content">
		<%error.note(templates/note.inc)%>
		<%admin.comments.list(commentlist.inc)%>
	</div>

<%case(head)%>
	<div style="float:right;"><%page.init%>
	<%text(_ADMIN_PAGE)%> <%data(libs,page,this)%>/<%data(libs,page,pages)%>
	<%if.data.ismorethan(1,libs,page,pages)%><%page(templates/paging.inc)%><%endif%>
	</div>
	<table class="grid"><tr><th><%text(_ADMIN_INFORMATION)%></th><th><%text(_ADMIN_COMMENT)%></th><th><%text(_ADMIN_ACTIONS)%></th></tr>

<%case(body)%>
	<tr><td>
		id:<%data(id)%> 
		(<a href="<%text(_CONF_SELF)%>?page=commentlist&amp;itemid=<%data(itemid)%>">itemid:<%data(itemid)%></a>)
		ip:<%data(ip)%><br />
		author: <%if.data.isempty(author)%><%data(user)%>
		<%else%><a href="<%text(_CONF_SELF)%>?page=commentlist&amp;mid=<%data(author)%>"><%data(user)%> (member)</a><%endif%><br />
		<%view.strftime(date)%>
	</td>
	<td><%data.shorten(80,...,body)%><br /><%view.include(templates/flagstatus.inc)%></td>
	<td>
		<a href="<%text(_CONF_SELF)%>?page=editcomment&amp;cid=<%data(id)%>"><%text(_ADMIN_EDIT)%></a><br />
		<a href="#Delete" onclick="return libs_admin_batch_submit('admin.batch.deletecomments','<%data(id)%>');"><%text(_ADMIN_DELETE)%></a>
	</td></tr>

<%case(foot)%>
	</table>
	<%view.parse(batchform.inc)%>

<%case(none)%>
	<%text(_ADMIN_NOTHING_TO_SHOW)%>

<%case(date)%>
	%Y-%m-%d %H:%M
<%endselect%>