<%note(
 * Jeans CMS (GPL license)
 * $Id$
)%>
<h2><%text(_ADMIN_MEMBERINFO_PASSWORD_REQURED)%></h2>

<%error.note(templates/note.inc)%>

<form method="post" action=""><ins>
<input type="hidden" name="action" value="admin.memberinfo.loginsetting" />
<%ticket.hidden(admin.memberinfo.loginsetting)%>

<input type="hidden" name="email" value="<%data.hsc(_POST,email)%>" />
<input type="hidden" name="password1_text" value="<%data.hsc(_POST,password1_text)%>" />
<input type="hidden" name="password2_text" value="<%data.hsc(_POST,password2_text)%>" />
<input type="hidden" name="admin" value="<%data.hsc(_POST,admin)%>" />
<input type="hidden" name="enabled" value="<%data.hsc(_POST,enabled)%>" />
<%if.data.isempty(_GET,mid)%>
	<%note(Add member settings follow)%>
	<input type="hidden" name="name" value="<%data.hsc(_POST,name)%>" />
	<input type="hidden" name="loginname" value="<%data.hsc(_POST,loginname)%>" />
<%endif%>

<table class="nogrid">
<tr><th style="width:30%;"><%text(_ADMIN_MEMBERINFO_PASSWORD_REQURED)%></th><th></th><tr>
<tr><td><%text(_ADMIN_MEMBERINFO_YOUR_PASSWORD)%></td><td><input type="password" name="password_text" size="40" /></td></tr>

<tr><td><%text(_ADMIN_SUBMIT)%></td><td><input type="submit" class="submit button" value="<%text(_ADMIN_MEMBERINFO_SUBMIT_LOGINSETTING)%>" /></td></tr>
</table>
</ins></form>

