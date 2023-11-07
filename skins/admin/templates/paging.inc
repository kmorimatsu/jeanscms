<%note(
 * Jeans CMS (GPL license)
 * $Id$
)%>
<%select(template)%>
<%case(head)%>
	 [
<%case(body)%>
	<%if.data.isempty(count)%>
		,&nbsp;...&nbsp;
	<%else%>
		<%if.data.ismorethan(1,count)%>,<%endif%>
		<%if.data.isempty(this)%>
			<a href="<%data(link)%>">&nbsp;<%data(count)%>&nbsp;</a>
		<%else%>
			<b style="color:red;">&nbsp;<%data(count)%>&nbsp;</b>
		<%endif%>
	<%endif%>
<%case(foot)%>
	] 
<%endselect%>