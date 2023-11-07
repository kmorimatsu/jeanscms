<%note(
 * Jeans CMS (GPL license)
 * $Id$
)%>
<%select(template)%>
<%case(init)%>
	<h2>
	<%if.data.isempty(_GET,mid)%>
		<%text(_ADMIN_MEDIA_LIST_YOURS)%>
	<%else%>
		<%text(_ADMIN_MEDIA_LIST)%> (<%memberinfo(name)%>)
	<%endif%>
	</h2>

	<%error.note(templates/note.inc)%>

	<%if.data.isempty(_GET,mid)%>
		<form method="post" action="" enctype="multipart/form-data">
		<input type="hidden" name="action" value="admin.media.upload" />
		<%ticket.hidden(admin.media.upload)%>
		<input type="hidden" name="MAX_FILE_SIZE" value="<%text(_CONF_MAX_UPLOAD_SIZE)%>" />
		<%text(_ADMIN_MEDIA_UPLOAD)%> <input name="binfile" type="file" />&nbsp;&nbsp;&nbsp;
		<input type="submit" class="submit button" value="<%text(_ADMIN_SUBMIT)%>" />
		</form>
	<%endif%>

	<%admin.media.list%>

<%case(head)%>
	<div style="float:right;"><%page.init%>
	<%text(_ADMIN_PAGE)%> <%data(libs,page,this)%>/<%data(libs,page,pages)%>
	<%if.data.ismorethan(1,libs,page,pages)%><%page(templates/paging.inc)%><%endif%>
	</div>
	<table class="grid"><tr>
	<th><%text(_ADMIN_MEDIA_THUMBNAIL)%></th>
	<th><%text(_ADMIN_MEDIA_FILE)%></th>
	<th><%text(_ADMIN_MEDIA_SIZE)%></th>
	<th><%text(_ADMIN_ACTIONS)%></th>
	</tr>

<%case(body)%>
	<tr>
	<td>
		<%select(type)%>
		<%case(image)%>
			<img src="<%data(thumbnail)%>" alt="<%data(name)%>" width="<%data(twidth)%>" height="<%data(theight)%>" />
		<%case.else%>
			<a href="?aciton=media.thumbnail&amp;file=<%data(name)%>" onclick="window.open(this.href,'jeans_media_view');return false;"><%text(_ADMIN_MEDIA_VIEW_MEDIA)%></a>
		<%endselect%>
	</td>
	
	<td><a href="#Select" onclick="return jeans_edititem_insert_image_in_opener('?action=media.view&amp;file=<%data(file)%>','<%data(name)%>','<%data(width)%>','<%data(height)%>');">
	<%data(name)%><br /><%text(_ADMIN_MEDIA_UPLOAD_TIME)%>: <%data(time)%>
	</a></td>
	<%ifnot.data.isempty(width)%>
		<td><%data(width)%> X <%data(height)%><br />(<%data(size)%> bytes)</td>
	<%else%>
		<td>(<%data(size)%> bytes)</td>
	<%endif%>
	
	<td>
		<a href="#Delete" onclick="return libs_admin_batch_submit('admin.batch.deletemedia','<%data(id)%>');"><%text(_ADMIN_DELETE)%></a>
	</td>
	</tr>

<%case(foot)%>
	</table>
	<%view.parse(batchform.inc)%>
	
<%endselect%>