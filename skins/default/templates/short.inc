<%select(template)%>
<%case(body)%>
<div class="item">
<h3 class="ititle">
<a href="<%data(link)%>"><%data(title)%></a>
<span class="small">
[<a href="<%data(clink)%>"><%data(cname)%></a>]
<%view.parse(editlink.inc,[,編集,])%></span>
</h3>
<p class="date"><%view.strftime(date)%></p>

<div class="ibody">
<%data.parseditem(body)%><%view.template(morelink)%>
</div>
</div>
<%case(morelink)%>
<%ifnot.data.isempty(more)%>
<p class="morelink"><a href="<%data(link)%>">&lt;<%data(title)%>&nbsp;のつづき&gt;</a></p>
<%endif%>
<%case(date)%>
%Y年%-m月%-d日
<%endselect%>
