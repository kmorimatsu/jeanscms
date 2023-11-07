<%select(template)%>
<%case(body)%>
<dd><a href="<%data(link)%>"><%view.strftime(date)%></a></dd>
<%case(date)%>
%Y.%m
<%endselect%>
