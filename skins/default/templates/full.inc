<%select(template)%>
<%case(body)%>
<p class="cname"><a href="<%data(clink)%>"><%data(cname)%></a></p>
<div class="item">
<h2 class="ititle"><%data(title)%><%view.parse(editlink.inc,<span class="small">[,編集,]</span>)%></h2>
<p class="date"><%view.strftime(date)%></p>

<div class="ibody">
<%data.parseditem(body)%>
<%data.parseditem(more)%>
</div>
</div>
<%case(date)%>
%Y年%-m月%-d日
<%case(date)%>
%H:%M:%S
<%endselect%>
