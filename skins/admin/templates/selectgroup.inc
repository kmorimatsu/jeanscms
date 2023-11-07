<%note(
 * Jeans CMS (GPL license)
 * $Id$
)%>
<%select(template)%>
<%case(head)%>
	<select name="gid">
<%case(body)%>
	<%if.data.isset(selected)%>
		<option selected="true" value="<%data(id)%>"><%data(name)%></option>
	<%else%>
		<option value="<%data(id)%>"><%data(name)%></option>
	<%endif%>
<%case(foot)%>
	</select>

<%case(none)%>
	There is no group.

<%endselect%>