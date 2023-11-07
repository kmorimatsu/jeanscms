<%select(template)%>
<%case(head)%>
<div id="archive">
<%case(body)%>
<h3><a href="<%data(link)%>"><%view.strftime(date)%></a></h3>
<%case(foot)%>
</div>
<%case(date)%>
%Y年%-m月
<%endselect%>
