<%note(
 * Jeans CMS (GPL license)
 * $Id$
)%>
<%if.member.loggedin%>
	<div class="loginname">
	<%view.parse(forms/loginform.inc)%>
	<a href="<%text(_CONF_URL_ADMIN)%>"><%text(_ADMIN_ADMIN_HOME)%></a> - 
	<a href="<%text(_CONF_URL_INDEX)%>"><%text(_ADMIN_YOUR_SITE)%></a><br />
	(Jeans CMS <%text(_JEANS_VERSION)%>)
	</div>
<%else%>
	<div class="loginname">
	<a href="<%text(_CONF_URL_ADMIN)%>"><%text(_ADMIN_ADMIN_HOME)%></a> - 
	<a href="<%text(_CONF_URL_INDEX)%>"><%text(_ADMIN_THE_SITE)%></a><br />
	(Jeans CMS)
	</div>
<%endif%>
