<%select(template)%>
<%case(head)%>
	<tr><td colspan="2"><ul>

<%case(body)%>
	<li style="color:red;"><%data(error)%></li>

<%case(foot)%>
	</ul></td></tr>

<%endselect%>