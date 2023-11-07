<%select(template)%>
<%case(body)%>
<dd><a href="<%data(link)%>"><%data(title)%> <small>(<%view.strftime(date)%>)</small></a></dd>
<%case(date)%>
%y.%m.%d
<%case(time)%>
%H:%M:%S
<%endselect%>
