<%note(
 * Jeans CMS (GPL license)
 * $Id$
)%>
<div>
<p>
	<%text(_JP_SKINFILES_DELETE1)%>
	<b><%jp.skinfiles.class.setting(real_file)%></b>
	<%text(_JP_SKINFILES_DELETE2)%>
</p>
<form method="post" action="">
	<input type="hidden" name="action" value="jp.skinfiles.class.delete" />
	<input type="hidden" name="order" value="<%data(_GET,order)%>" />
	<input type="hidden" name="thumbnail" value="<%data(_GET,thumbnail)%>" />
	<input type="hidden" name="offset" value="<%data(_GET,offset)%>" />
	<%ticket.hidden(jp.skinfiles.class.delete)%>
	<input type="submit" class="submit button" value="<%text(_JP_SKINFILES_EXECUTE)%>" />
	<input type="button" class="cancel button" value="<%text(_JEANS_CANCEL)%>" onclick="$('#jp_skinfiles_cancel_form').get(0).submit();" />
</form>
</div>
