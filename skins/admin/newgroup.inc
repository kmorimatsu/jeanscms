<%note(
 * Jeans CMS (GPL license)
 * $Id$
)%>
<%if.data.isset(_GET,sgid)%>
	<h2><%text(_ADMIN_GROUP_NEW_SUBGROUP_IN_SUBGROUP)%> <a href=""><%group.sgsetting(name)%></a></h2>

	<%error.note(templates/note.inc)%>

	<%admin.group.newsubgroup(templates/newgroup.inc)%>

<%elseif.data.isset(_GET,gid)%>
	<h2><%text(_ADMIN_GROUP_NEW_SUBGROUP_IN_GROUP)%> <a href=""><%group.setting(name)%></a></h2>

	<%error.note(templates/note.inc)%>

	<%admin.group.newsubgroup(templates/newgroup.inc)%>

<%else%>
	<h2><%text(_ADMIN_GROUP_NEW_GROUP)%></h2>

	<%error.note(templates/note.inc)%>

	<%admin.group.newgroup(templates/newgroup.inc)%>

<%endif%>

