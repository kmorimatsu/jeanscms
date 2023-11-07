<%note(
 * Jeans CMS (GPL license)
 * $Id$
)%>
<div>
<p>
	<%text(_JP_SKINFILES_RENAME_TO1)%>
	<b><%jp.skinfiles.class.setting(real_file)%></b>
	<%text(_JP_SKINFILES_RENAME_TO2)%>
</p>
<form method="post" action="">
	<input type="hidden" name="action" value="jp.skinfiles.class.rename" />
	<input type="hidden" name="order" value="<%data(_GET,order)%>" />
	<input type="hidden" name="thumbnail" value="<%data(_GET,thumbnail)%>" />
	<input type="hidden" name="offset" value="<%data(_GET,offset)%>" />
	<%ticket.hidden(jp.skinfiles.class.rename)%>
	<input type="text" name="new_path" size="40" value="<%jp.skinfiles.class.setting(basename)%>" />
	<input type="submit" class="submit button" value="<%text(_JP_SKINFILES_EXECUTE)%>" />
	<input type="button" class="cancel button" value="<%text(_JEANS_CANCEL)%>" onclick="$('#jp_skinfiles_cancel_form').get(0).submit();" />
</form>
</div>
