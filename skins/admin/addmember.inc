<%note(
 * Jeans CMS (GPL license)
 * $Id$
)%>

<form method="post" action="<%text(_CONF_SELF)%>?page=loginsetting"><ins>

<%error.note(templates/note.inc)%>

<input type="hidden" name="action" value="admin.memberinfo.loginsetting" />
<%ticket.hidden(admin.memberinfo.loginsetting)%>
<h3><%text(_ADMIN_MEMBERINFO_REGISTER_MEMBER)%></h3>
<table class="nogrid">
<tr><td><%text(_ADMIN_MEMBERINFO_LOGINNAME)%></td><td><input type="text" class="text" name="loginname" size="40" value="" /></td></tr>
<tr><td><%text(_ADMIN_MEMBERINFO_NAME)%></td><td><input type="text" class="text" name="name" size="40" value="" /></td></tr>
<tr><td><%text(_ADMIN_MEMBERINFO_LOGIN_EMAIL)%></td><td><input type="text" class="text" name="email" size="40" value="" /></td></tr>
<tr><td><%text(_ADMIN_MEMBERINFO_NEW_PASSWORD)%></td><td><input class="text" type="password" name="password1_text" size="40" /></td></tr>
<tr><td><%text(_ADMIN_MEMBERINFO_NEW_PASSWORD_AGAIN)%></td><td><input class="text" type="password" name="password2_text" size="40" /></td></tr>
<tr><td><%text(_ADMIN_MEMBERINFO_ACCEPT_LOGIN)%></td><td><input name="enabled" value="1" checked="true" type="radio"><label for="enabled"><%text(_JEANS_YES)%></label><input name="enabled" value="0" type="radio"><label for="enabled"><%text(_JEANS_NO)%></label></td></tr>
<tr><td><%text(_ADMIN_MEMBERINFO_CONFER_ADMINISTRATOR_PRIVILEGE)%></td><td><input name="admin" value="1" type="radio"><label for="admin"><%text(_JEANS_YES)%></label><input name="admin" value="0" checked="true" type="radio"><label for="admin"><%text(_JEANS_NO)%></label></td></tr>
<tr><td><%text(_ADMIN_SUBMIT)%></td><td><input type="submit" class="submit button" value="<%text(_ADMIN_MEMBERINFO_SUBMIT_LOGINSETTING)%>" /> (<%text(_ADMIN_MEMBERINFO_CONFIRM_LOGINSETTING)%>)</td></tr>
</table>
</ins></form>
