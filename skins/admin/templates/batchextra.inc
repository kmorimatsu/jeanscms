<%note(
 * Jeans CMS (GPL license)
 * $Id$
)%>
<%select(template)%>
<%case(body)%>
	<tr><td><%data(desc)%></td><td><%view.parse(forminput.inc)%></td></tr>
<%endselect%>