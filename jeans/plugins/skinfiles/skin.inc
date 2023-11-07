<%note(
 * Jeans CMS (GPL license)
 * $Id$
)%>
<link rel="stylesheet" type="text/css" href="<%view.skinfile(styles.css)%>" />
<script type="text/javascript">
	document.title='jp_SkinFiles: <%jp.skinfiles.class.setting(dir)%><%jp.skinfiles.class.setting(file)%>';
</script>

<h2><%text(_JP_SKINFILES_SKINFILE_MANAGEMENT)%></h2>

<form id="jp_skinfiles_cancel_form" method="get" action="">
	<input type="hidden" name="padmin" value="jp_skinfiles" />
	<input type="hidden" name="dir_path" value="<%jp.skinfiles.class.setting(dir)%>" />
</form>

<%error.note(/admin/templates/note.inc)%>

<%view.parse(breadcrumbs.inc)%>

<%if.jp.skinfiles.class.virtualfile%>
	<div>
		<b><%text(_JP_SKINFILES_SHOWING_VIRTUAL_PATH)%></b>
		(<%text(_JP_SKINFILES_REAL_PATH)%>:
		<a href="<%text(_CONF_SELF)%>?padmin=jp_skinfiles&amp;dir_path=<%jp.skinfiles.class.setting(real_dir_for_file)%>&amp;file=<%jp.skinfiles.class.setting(file)%>"><%jp.skinfiles.class.setting(real_file)%></a>)
		
	</div>
<%endif%>

<%if.jp.skinfiles.class.skinname%><%view.parse(skinextension.inc)%><%endif%>

<%select(_GET,preaction)%>
<%case(delete)%>
	<%view.include(delete.inc)%>
<%case(rename)%>
	<%view.include(rename.inc)%>
<%case(edit)%>
	<%view.include(edit.inc)%>
<%caseelse%>
	<%if.jp.skinfiles.class.text%>
		<pre class="code"><%jp.skinfiles.class.showtext%></pre>
	<%elseif.jp.skinfiles.class.image%>
		<p><img src="<%text(_CONF_URL_SKINS)%><%jp.skinfiles.class.setting(real_file)%>" alt="<%jp.skinfiles.class.setting(file)%>"/></p>
	<%elseif.jp.skinfiles.class.file%>
		<p><%text(_ADMIN_NOTHING_TO_SHOW)%></p>
	<%else%>
		<%view.parse(dir.inc)%>
	<%endif%>
<%endif%>