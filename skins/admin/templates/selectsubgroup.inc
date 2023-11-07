<%note(
 * Jeans CMS (GPL license)
 * $Id$
)%>
<%select(template)%>
<%case(head)%>
	<select name="sgid">

<%case(body)%>
	<%if.data.is(0,groupid)%>
		<option value="<%data(id)%>" <%if.data.isset(selected)%>selected="true"<%endif%> style="color: #C00000;"><%data(name)%></option>

	<%else%>
		<option value="<%data(id)%>" <%if.data.isset(selected)%>selected="true"<%endif%> ><%group.treetab(tab)%><%data(name)%></option>

	<%endif%>
	<%group.treesub%>

<%case(foot)%>
	</select>

<%case(tab)%>
	&nbsp;&nbsp;&nbsp;
<%case(none)%>
	There is no group.

<%endselect%>