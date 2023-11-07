<%note(
 * Jeans CMS (GPL license)
 * $Id$
)%>
<h2><%text(_ADMIN_MAIL_FORGOTPASSWORD_FORM)%></h2>
<div id="login">
<form method="post" action="">
	<div class="loginform">
		<%if.classloaded(error)%><%view.parse(templates/errors.inc)%><%endif%>
		<input type="hidden" name="action" value="admin.mail.forgotpassword" />
		<%ticket.hidden(admin.mail.forgotpassword)%>

		<label for="jeans_lf_name" accesskey="l"><%text(_JEANS_MAIL_FORGOTPASSWORD_ID_OR_EMAIL)%></label>
		<input id="jeans_lf_name" name="loginname" size="10" value="" class="formfield" />
		<input type="submit" class="submit button" alt="<%text(_ADMIN_SUBMIT)%>" value="<%text(_ADMIN_SUBMIT)%>" class="formbutton" />
	</div>
</form>
</div>

