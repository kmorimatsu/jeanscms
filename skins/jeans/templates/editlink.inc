<%if.member.loggedin%>
	<%data.raw(1)%><a href="<%text(_CONF_URL_ADMIN)%>?page=edititem&amp;itemid=<%data(id)%>" onclick="window.open(this.href); return false;"><%data.raw(2)%></a><%data.raw(3)%>
<%endif%>
