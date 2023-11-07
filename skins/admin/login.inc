<%note(
 * Jeans CMS (GPL license)
 * $Id$
)%>
<h2><%text(_ADMIN_PLEASE_LOGIN)%></h2>
<div id="login">
<%view.parse(forms/loginform.inc)%>
</div>

<div><a href="<%text(_CONF_SELF)%>?page=forgotpassword"><%text(_ADMIN_MAIL_FORGOTPASSWORD)%></a></div>
