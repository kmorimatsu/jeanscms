<%note(
 * Jeans CMS (GPL license)
 * $Id$
)%>
<%select(template)%>
<%case(head)%>
	<div style="float:right;"><%page.init%>
	<%text(_ADMIN_PAGE)%> <%data(libs,page,this)%>/<%data(libs,page,pages)%>
	<%if.data.ismorethan(1,libs,page,pages)%><%page(paging.inc)%><%endif%>
	</div>
	<table class="grid"><tr>
	<th><%text(_ADMIN_INFORMATION)%></th>
	<th style="width: 70%;"><%text(_ADMIN_ITEM_TITLE_AND_TEXT)%></th>
	<th><%text(_ADMIN_ACTIONS)%></th>
	</tr>

<%case(body)%>
	<tr>
	<td>itemid: <a href="<%text(_CONF_URL_INDEX)%>?itemid=<%data(id)%>"><%data(id)%></a><br />
		group: <a href="<%text(_CONF_SELF)%>?page=itemlist&amp;gid=<%data(gid)%>"><%data(gname)%></a><br />
		subgroup: <a href="<%text(_CONF_SELF)%>?page=itemlist&amp;sgid=<%data(sgid)%>"><%data(sname)%></a><br />
		author: <a href="<%text(_CONF_SELF)%>?page=itemlist&amp;mid=<%data(author)%>"><%data(aname)%></a><br />
		date: <%view.strftime(date)%><br />
		time: <%view.strftime(time)%></td>
	<td><b><%data(title)%></b><br />
		<%data.shorten(300,...,body)%><br />
		<%view.include(flagstatus.inc)%>
		</td>
	<td><a href="<%text(_CONF_SELF)%>?page=edititem&amp;itemid=<%data(id)%>"><%text(_ADMIN_ITEM_EDIT)%></a><br />
		<a href="<%text(_CONF_SELF)%>?page=commentlist&amp;itemid=<%data(id)%>"><%text(_ADMIN_ITEM_COMMENT_LIST)%></a><br />
		<%text(_ADMIN_ITEM_MOVE)%><br />
		<a href="#Delete" onclick="return libs_admin_batch_submit('admin.batch.deleteitems','<%data(id)%>');"><%text(_ADMIN_ITEM_DELETE)%></a></td>
	</tr>

<%case(foot)%>
	</table>
	<%view.parse(../batchform.inc)%>

<%case(date)%>
	%Y-%m-%d

<%case(time)%>
	%H:%M

<%case(none)%>
	<table class="grid"><tr><td><%text(_ADMIN_NOTHING_TO_SHOW)%></td></tr></table>

<%endselect%>

