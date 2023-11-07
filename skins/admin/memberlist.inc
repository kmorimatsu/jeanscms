<%note(
 * Jeans CMS (GPL license)
 * $Id$
)%>
<%select(template)%>
<%case(init)%>
	<h2><%text(_ADMIN_MEMBERINFO_MEMBERLIST)%></h2>

	<%error.note(templates/note.inc)%>

	<%admin.memberinfo.memberlist(memberlist.inc)%>

<%case(head)%>
	<table class="grid">
	<tr>
	<th>id</th>
	<th><%text(_ADMIN_MEMBERINFO_NAME)%></th>
	<th><%text(_ADMIN_MEMBERINFO_LOGINNAME)%></th>
	<th><%text(_ADMIN_MEMBERINFO_LOGIN_EMAIL)%></th>
	<th><%text(_ADMIN_MEMBERINFO_CAN_LOGIN)%></th>
	<th><%text(_ADMIN_MEMBERINFO_ADMIN)%></th>
	</tr>

<%case(body)%>
	<tr>
	<td><%data(id)%></td>
	<td><a href="<%text(_CONF_SELF)%>?page=membersetting&amp;mid=<%data(id)%>"><%data(name)%></a></td>
	<td><%data(loginname)%></td>
	<td><a href="mailto:<%data(loginemail)%>"><%data(loginemail)%></a></td>
	<td><%ifnot.data.isempty(canlogin)%><%text(_JEANS_YES)%><%else%><%text(_JEANS_NO)%><%endif%></td>
	<td><%ifnot.data.isempty(admin)%><%text(_JEANS_YES)%><%else%><%text(_JEANS_NO)%><%endif%></td>
	</tr>

<%case(foot)%>
	</table>
	<a href="<%text(_CONF_SELF)%>?page=addmember"><%text(_ADMIN_MEMBERINFO_REGISTER_MEMBER)%></a>

<%endselect%>

