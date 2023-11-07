<%note(
 * Jeans CMS (GPL license)
 * $Id$
)%>
<%select(template)%>
<%case(head)%>
	<ul id="ajax_content">
<%case(body)%>
	<li style="color:red;"><%data.hsc(error)%></li>
<%case(foot)%>
	</ul>
<%endselect%>