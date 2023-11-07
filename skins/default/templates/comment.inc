<%select(template)%>
<%case(body)%>
<h4>
<a href="<%data(link)%>"><%data(user)%></a>
<span class="date">(<%view.strftime(date)%>)</span>
</h4>

<div class="cbody">
<%comments.body%>
</div>

<%case(date)%>
%Y年%-m月%-d日&nbsp;%H:%M:%S
<%case(none)%>
<div class="cbody">コメントはありません</div>
<%endselect%>
