<%ifnot.comments.enabled%>
	<div class="commentform"><%text(_JEANS_COMMENT_ITEMCLOSED)%></div>
<%elseif.member.loggedin%>
	<a id="jeans_cf"></a>
	<form method="post" action="#jeans_cf"><div class="commentform">
		<input type="hidden" name="action" value="admin.comment.add" />
		<%ticket.hidden(admin.comment.add)%>
		<%if.data.isset(id)%><input type="hidden" name="itemid" value="<%data(id)%>" /><%endif%>
		<%if.classloaded(error)%><%view.parse(../templates/errors.inc)%><%endif%>
		<label for="jeans_cf_body"><%text(_JEANS_COMMENTFORM_COMMENT)%></label>
		<textarea name="body_text" class="formfield" cols="40" rows="10" id="jeans_cf_body"><%data(_POST,body_text)%></textarea>
		<%text(_JEANS_COMMENTFORM_YOUARE)%> <%member.setting(loginname)%> 
		(<a href="?action=member.logout" title="<%text(_JEANS_LOGOUT)%>"><%text(_JEANS_LOGOUT)%></a>)
		<input type="submit" class="submit button" alt="<%text(_JEANS_COMMENTFORM_SUBMIT)%>" value="<%text(_JEANS_COMMENTFORM_SUBMIT)%>" class="formbutton" />
		<%callback(form_extra,type,commentform)%>
	</div></form>
<%else%>
	<a id="jeans_cf"></a>
	<form method="post" action="#jeans_cf"><div class="commentform">
		<input type="hidden" name="action" value="admin.comment.add" />
		<%ticket.hidden(admin.comment.add)%>
		<%if.data.isset(id)%><input type="hidden" name="itemid" value="<%data(id)%>" /><%endif%>
		<%if.classloaded(error)%><%view.parse(../templates/errors.inc)%><%endif%>
		<label for="jeans_cf_body"><%text(_JEANS_COMMENTFORM_COMMENT)%></label>
		<textarea name="body_text" class="formfield" cols="40" rows="10" id="jeans_cf_body"><%data(_POST,body_text)%></textarea>
		<label for="jeans_cf_name"><%text(_JEANS_COMMENTFORM_NAME)%></label>
		<input name="user_text" size="40" maxlength="40" value="<%ifnot.data.isempty(_POST,user_text)%><%data(_POST,user_text)%><%else%><%data(_COOKIE,commentform_user_text)%><%endif%>" class="formfield" id="jeans_cf_name" />
		<label for="jeans_cf_mail"><%text(_JEANS_COMMENTFORM_WEB)%></label>
		<input name="web_url" size="40" maxlength="60" value="<%ifnot.data.isempty(_POST,web_url)%><%data(_POST,web_url)%><%else%><%data(_COOKIE,commentform_web_url)%><%endif%>" class="formfield" id="jeans_cf_mail" />
		<label for="jeans_cf_email"><%text(_JEANS_COMMENTFORM_EMAIL)%></label>
		<input name="email" size="40" maxlength="100" value="<%ifnot.data.isempty(_POST,email)%><%data(_POST,email)%><%else%><%data(_COOKIE,commentform_email)%><%endif%>" class="formfield" id="jeans_cf_email" />
		<%callback(form_extra,type,commentform)%>
		<input type="checkbox" value="1" name="remember" id="jeans_cf_remember" <%ifnot.data.isempty(_POST,remember)%>checked="true"<%endif%> />
		<label for="jeans_cf_remember"><%text(_JEANS_COMMENTFORM_REMEMBER)%></label>
		<input type="submit" class="submit button" alt="<%text(_JEANS_COMMENTFORM_SUBMIT)%>" value="<%text(_JEANS_COMMENTFORM_SUBMIT)%>" class="formbutton" />
	</div></form>
<%endif%>