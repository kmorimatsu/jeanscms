<%select(template)%>
<%case(init)%>
	<%error.note(errors.inc)%>
<%case(head)%>
	<div class="error">
<%case(body)%>
	<%data(error)%><br />
<%case(foot)%>
	</div>
<%endselect%>
