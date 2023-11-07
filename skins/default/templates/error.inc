<%select(template)%>
<%case(body)%>
<dt><%data(counter)%>: <span style="color: red; font-weight: bold;"><%data(error)%></span></dt>
<%case(none)%>
エラーはありません。
<%endselect%>
