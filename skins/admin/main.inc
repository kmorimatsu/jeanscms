<%note(
 * Jeans CMS (GPL license)
 * $Id$
)%>
<h2><%text(_ADMIN_GROUP_YOURGROUPS)%></h2>

<%error.note(templates/note.inc)%>

<%group.list(templates/grouplist.inc,1)%>

<h2><%text(_ADMIN_DRAFT_LIST)%></h2>

<%admin.item.narrowby(draft)%>
<%admin.item.list(templates/itemlist.inc,3)%>

<%view.parse(menu.inc)%>
