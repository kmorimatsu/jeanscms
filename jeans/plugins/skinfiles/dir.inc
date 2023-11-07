<%note(
 * Jeans CMS (GPL license)
 * $Id$
)%>
<%select(template)%>
<%case(init)%>
	<div class="jp_skinfiles_filelist">
		<%jp.skinfiles.class.dir%>
	</div>
	<div>
		<table class="nogrid">
			<tr><th colspan="2"><%text(_JP_SKINFILES_OTHER_ACTIONS)%></th></tr>
			<tr><td><%text(_JP_SKINFILES_UPLOAD_FILE)%></td><td>
				<form method="post" action="" enctype="multipart/form-data">
					<input type="hidden" name="action" value="jp.skinfiles.class.upload" />
					<%ticket.hidden(jp.skinfiles.class.upload)%>
					<input type="hidden" name="MAX_FILE_SIZE" value="<%text(_CONF_MAX_UPLOAD_SIZE)%>" />
					<input name="binfile" type="file" size="40" />&nbsp;&nbsp;&nbsp;
					<input type="submit" class="submit button" value="<%text(_ADMIN_SUBMIT)%>" />
				</form>
			</td></tr>
			<tr><td><%text(_JP_SKINFILES_CREATE_NEW_DIRECTORY)%></td><td>
				<form method="post" action="">
					<input type="hidden" name="action" value="jp.skinfiles.class.newdir" />
					<%ticket.hidden(jp.skinfiles.class.newdir)%>
					<input type="text" name="newdir" size="40" value="" />&nbsp;&nbsp;&nbsp;
					<input type="submit" class="submit button" value="<%text(_JP_SKINFILES_CREATE)%>" />
				</form>
			</td></tr>
			<tr><td><%text(_JP_SKINFILES_CREATE_NEW_FILE)%></td><td>
				<form method="post" action="">
					<input type="hidden" name="action" value="jp.skinfiles.class.newfile" />
					<%ticket.hidden(jp.skinfiles.class.newfile)%>
					<input type="text" name="newfile" size="40" value="" />&nbsp;&nbsp;&nbsp;
					<input type="submit" class="submit button" value="<%text(_JP_SKINFILES_CREATE)%>" />
				</form>
			</td></tr>
		</table>
	</div>

<%case(head)%>
	<%if.data.isempty(jp,skinfiles,image_exists)%>
	<%elseif.jp.skinfiles.class.thumbnail%>
		<dir>
			<a href="<%text(_CONF_SELF)%>?padmin=jp_skinfiles&amp;dir_path=<%jp.skinfiles.class.setting(dir)%>"><%text(_JP_SKINFILES_DO_NOT_SHOW_THUMBNAILS)%></a>
		</dir>
	<%else%>
		<dir>
			<a href="<%text(_CONF_SELF)%>?padmin=jp_skinfiles&amp;dir_path=<%jp.skinfiles.class.setting(dir)%>&amp;thumbnail=1"><%text(_JP_SKINFILES_SHOW_THUMBNAILS)%></a>
		</dir>
	<%endif%>
	<%page.init%>
	<%if.data.ismorethan(1,libs,page,pages)%>
		<%text(_ADMIN_PAGE)%> <%data(libs,page,this)%>/<%data(libs,page,pages)%>
		<%page(/admin/templates/paging.inc)%>
	<%endif%>
	<table class="grid">
		<tr>
			<th><a href="<%jp.skinfiles.class.link(orderbyname)%>"><%text(JP_SKINFILES_NAME)%></a></th>
			<th><%text(JP_SKINFILES_SIZE)%></th>
			<th><a href="<%jp.skinfiles.class.link(orderbydate)%>"><%text(JP_SKINFILES_LAST_MODIFIED)%></a></th>
			<th colspan="4"><%text(JP_SKINFILES_ACTIONS)%></th>
		</tr>

<%case(body)%>
	<%if.data.isempty(virtual)%><tr><%else%><tr class="jp_skinfiles_virtual"><%endif%>
		<td><%view.template(link)%></td>
		<td><%data(size)%></td>
		<td><%view.strftime(time)%></td>
		<td><%ifnot.data.isempty(writable)%>
				<a href="<%text(_CONF_SELF)%>?padmin=jp_skinfiles&amp;preaction=rename&amp;dir_path=<%jp.skinfiles.class.setting(dir)%>&amp;file=<%data(name)%><%view.include(link.inc)%>"><%text(_JP_SKINFILES_RENAME)%></a>
			<%endif%></td>
		<td><%ifnot.data.isempty(writable)%>
				<a href="<%text(_CONF_SELF)%>?padmin=jp_skinfiles&amp;preaction=delete&amp;dir_path=<%jp.skinfiles.class.setting(dir)%>&amp;file=<%data(name)%><%view.include(link.inc)%>"><%text(_JP_SKINFILES_DELETE)%></a>
			<%endif%></td>
		<td><%select(type)%>
				<%case(dir)%>
				<%case(image)%>
					<a href="<%text(_CONF_SELF)%>?padmin=jp_skinfiles&amp;dir_path=<%jp.skinfiles.class.setting(dir)%>&amp;file=<%data(name)%>"><%text(_JP_SKINFILES_SHOW)%></a>
				<%caseelse%>
					<%ifnot.data.isempty(writable)%>
						<a href="<%text(_CONF_SELF)%>?padmin=jp_skinfiles&amp;preaction=edit&amp;dir_path=<%jp.skinfiles.class.setting(dir)%>&amp;file=<%data(name)%>"><%text(_JP_SKINFILES_EDIT)%></a>
					<%endif%>
			<%endselect%></td>
		<td><%ifnot.data.is(dir,type)%>
				<a href="<%text(_CONF_SELF)%>?padmin=jp_skinfiles&amp;&amp;action=jp.skinfiles.class.download&amp;dir_path=<%jp.skinfiles.class.setting(dir)%>&amp;file=<%data(name)%>"><%text(_JP_SKINFILES_DOWNLOAD)%></a>
			<%endif%></td>
	</tr>

<%case(foot)%>
	</table>
	<script type="text/javascript">
		if (window.opener && window.opener.jeans_edititem_insert_image) {
			$('.jp_skinfiles_image_link').click(function(){
				var re=new RegExp('^(.*)\\(([0-9]*)x([0-9]*)\\)$');
				re.exec(this.title+'');
				var name=RegExp.$1;
				var width=RegExp.$2;
				var height=RegExp.$3;
				var url='<%text(_CONF_URL_SKINS)%><%jp.skinfiles.class.setting(real_dir)%>'+name;
				window.opener.jeans_insert_image(url,name,width,height);
				window.close();
				return false;
			});
		}
	</script>

<%case(link)%>
	<%if.data.is(dir,type)%>
		<a href="<%text(_CONF_SELF)%>?padmin=jp_skinfiles&amp;dir_path=<%jp.skinfiles.class.setting(dir)%><%data(name)%>/"><%view.template(img)%><%data(name)%></a>
	<%elseif.data.is(other,type)%>
		<%view.template(img)%><%data(name)%>
	<%elseif.data.is(image,type)%>
		<a title="<%data(name)%>(<%data(width)%>x<%data(height)%>)" class="jp_skinfiles_image_link" href="<%text(_CONF_SELF)%>?padmin=jp_skinfiles&amp;dir_path=<%jp.skinfiles.class.setting(dir)%>&amp;file=<%data(name)%>"><%view.template(img)%><%data(name)%></a>
	<%else%>
		<a href="<%text(_CONF_SELF)%>?padmin=jp_skinfiles&amp;dir_path=<%jp.skinfiles.class.setting(dir)%>&amp;file=<%data(name)%>"><%view.template(img)%><%data(name)%></a>
	<%endif%>

<%case(img)%>
	<%select(type)%>
	<%case(dir)%>
		<img src="<%view.skinfile(images/dir.gif)%>" width="16" height="16" alt="dir" />
	<%case(html)%>
		<img src="<%view.skinfile(images/html.gif)%>" width="16" height="16" alt="html" />
	<%case(php)%>
		<img src="<%view.skinfile(images/php.gif)%>" width="16" height="16" alt="php" />
	<%case(image)%>
		<%if.jp.skinfiles.class.thumbnail%>
			<%jp.skinfiles.class.thumbnail%>
		<%else%>
			<img src="<%view.skinfile(images/image.gif)%>" width="16" height="16" alt="image" />
		<%endif%>
	<%case(other)%>
		<img src="<%view.skinfile(images/generic.gif)%>" width="16" height="16" alt="generic" />
	<%caseelse%>
		<img src="<%view.skinfile(images/text.gif)%>" width="16" height="16" alt="text" />
	<%endselect%>
	
<%case(time)%>
	%Y/%m/%d %H:%M

<%case(none)%>
	<table class="grid"><tr><td colspan="7"><%text(_ADMIN_NOTHING_TO_SHOW)%></td></tr></table>

<%endselect%>