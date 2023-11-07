<%note(
 * Jeans CMS (GPL license)
 * $Id$
)%>
<%select(libs,admin,custom)%>
<%case(map)%>
	<%select(libs,admin,page)%>
	<%case(itemlist)%>
		<%view.parse(map/itemlist.inc)%>
	<%case.else%>
		<div id="ajax_content">Error</div>
	<%endselect%>
<%case.else%>
	<div id="ajax_content">Error</div>
<%endselect%>

