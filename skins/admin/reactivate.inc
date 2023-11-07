<%note(
 * Jeans CMS (GPL license)
 * $Id$
)%>
<h2><%text(_ADMIN_MAIL_REACTIVATE)%></h2>
<div id="login">
<form method="post" action="">
	<div class="loginform">
		<%if.classloaded(error)%><%view.parse(templates/errors.inc)%><%endif%>
		<input type="hidden" name="action" value="admin.mail.reactivate" />
		<%ticket.hidden(admin.mail.reactivate)%>

		<label for="jeans_lf_pwd"><%text(_ADMIN_MEMBERINFO_NEW_PASSWORD)%></label>
		<input id="jeans_lf_pwd" name="password1_text" size="10" type="password" value="" class="formfield" />

		<label for="jeans_lf_pwd"><%text(_ADMIN_MEMBERINFO_NEW_PASSWORD_AGAIN)%></label>
		<input id="jeans_lf_pwd" name="password2_text" size="10" type="password" value="" class="formfield" />

		<input type="submit" class="submit button" alt="<%text(_ADMIN_SUBMIT)%>" value="<%text(_ADMIN_SUBMIT)%>" class="formbutton" />
	</div>
</form>
</div>
