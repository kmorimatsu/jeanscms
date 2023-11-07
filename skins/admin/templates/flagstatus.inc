<%note(
 * Jeans CMS (GPL license)
 * $Id$
)%>
<%if.data.flag(sql::FLAG_HIDDEN,flags)%> <span style="color: red;">(<%text(_ADMIN_FLAG_HIDDEN)%>)</span><%endif%>
<%if.data.flag(sql::FLAG_DRAFT,flags)%> <span style="color: red;">(<%text(_ADMIN_FLAG_DRAFT)%>)</span><%endif%>
<%if.data.flag(sql::FLAG_TEMP,flags)%> <span style="color: red;">(<%text(_ADMIN_FLAG_TEMP)%>)</span><%endif%>
<%if.data.flag(sql::FLAG_INVALID,flags)%> <span style="color: red;">(<%text(_ADMIN_FLAG_INVALID)%>)</span><%endif%>
