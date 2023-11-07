<%note(
 * Jeans CMS (GPL license)
 * $Id$
)%>
<h2><%text(_ADMIN_GROUP_SUBGROUPS_IN_GROUP)%> <a href="<%group.setting(url)%>"><%group.setting(name)%></a></h2>

<%error.note(templates/note.inc)%>

<%group.tree(templates/subgrouplist.inc,1,1)%>
