<%note(
 * Jeans CMS (GPL license)
 * $Id$
)%>
<div>
<form method="post" action="">
	<input type="hidden" name="action" value="jp.skinfiles.class.edit" />
	<%ticket.hidden(jp.skinfiles.class.edit)%>
	<textarea style="width: 100%; height: 480px;" name="save_text"><%if.data.isset(_POST,save_text)%><%data.hsc(_POST,save_text)%><%else%><%jp.skinfiles.class.showtext%><%endif%></textarea>
	<input type="submit" class="submit button" value="<%text(_JP_SKINFILES_SAVE)%>" />
	<input type="button" class="cancel button" value="<%text(_JEANS_CANCEL)%>" onclick="$('#jp_skinfiles_cancel_form').get(0).submit();" />
</form>
</div>
