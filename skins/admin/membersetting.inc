<%note(
 * Jeans CMS (GPL license)
 * $Id$
)%>
<%select(template)%>
<%case(init)%>
	<h2><%admin.memberinfo.data(name)%><%text(_ADMIN_MEMBERINFO_S_PROFILE)%></h2>

	<%error.note(templates/note.inc)%>

	<%admin.memberinfo.setting(membersetting.inc)%>

<%case(head)%>
	<form method="post" action=""><ins>
	<input type="hidden" name="action" value="admin.memberinfo.membersetting" />
	<%ticket.hidden(admin.memberinfo.membersetting)%>
	<h3><%admin.memberinfo.data(name)%><%text(_ADMIN_MEMBERINFO_S_PROFILE)%></h3>
	<table class="nogrid">
	<tr><td><%text(_ADMIN_MEMBERINFO_ID)%></td><td><%admin.memberinfo.data(id)%></td></tr>

<%case(body)%>
	<tr><td><%data(desc)%></td><td><%view.parse(templates/forminput.inc)%></td></tr>

<%case(foot)%>
	<tr><td><%text(_ADMIN_SUBMIT)%></td><td><input type="submit" class="submit button" value="<%text(_ADMIN_MEMBERINFO_SUBMIT_MEMBERSETTING)%>" /></td></tr>
	</table>
	</ins></form>

	<form method="post" action="<%text(_CONF_SELF)%>?page=loginsetting&amp;mid=<%admin.memberinfo.data(id)%>"><ins>
	<input type="hidden" name="action" value="admin.memberinfo.loginsetting" />
	<%ticket.hidden(admin.memberinfo.loginsetting)%>
	<h3><%admin.memberinfo.data(name)%><%text(_ADMIN_MEMBERINFO_S_LOGIN_INFORMATION)%></h3>
	<table class="nogrid">
	<tr><td><%text(_ADMIN_MEMBERINFO_LOGINNAME)%></td><td><%admin.memberinfo.data(loginname)%></td></tr>
	<tr><td><%text(_ADMIN_MEMBERINFO_LOGIN_EMAIL)%></td><td><input type="text" class="text" name="email" size="40" value="<%admin.memberinfo.data(loginemail)%>" /></td></tr>
	<tr><td><%text(_ADMIN_MEMBERINFO_NEW_PASSWORD)%></td><td><input class="text" type="password" name="password1_text" size="40" autocomplete="off" /></td></tr>
	<tr><td><%text(_ADMIN_MEMBERINFO_NEW_PASSWORD_AGAIN)%></td><td><input class="text" type="password" name="password2_text" size="40" autocomplete="off" /></td></tr>
	<%ifnot.admin.memberinfo.myself%>
		<tr><td><%text(_ADMIN_MEMBERINFO_ACCEPT_LOGIN)%></td><td><%admin.memberinfo.flagradio(member::MEMBER_CAN_LOGIN,enabled)%></td></tr>
		<tr><td><%text(_ADMIN_MEMBERINFO_CONFER_ADMINISTRATOR_PRIVILEGE)%></td><td><%admin.memberinfo.flagradio(member::MEMBER_IS_ADMIN,admin)%></td></tr>
	<%endif%>
	<tr><td><%text(_ADMIN_SUBMIT)%></td><td><input type="submit" class="submit button" value="<%text(_ADMIN_MEMBERINFO_SUBMIT_LOGINSETTING)%>" /> (<%text(_ADMIN_MEMBERINFO_CONFIRM_LOGINSETTING)%>)</td></tr>
	</table>
	</ins></form>


<%endselect%>

