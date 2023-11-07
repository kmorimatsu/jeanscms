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
		<a href="javascript:libs_admin_sql_changeoffset(<%data(count)%>,<%data(libs,page,limit)%>);">&nbsp;<%data(count)%>&nbsp;</a>
	<%else%>
		<b style="color:red;">&nbsp;<%data(count)%>&nbsp;</b>
	<%endif%>
<%endif%>
<%case(foot)%>
] 
<%endselect%>