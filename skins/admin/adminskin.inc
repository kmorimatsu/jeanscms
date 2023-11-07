<%note(
 * Jeans CMS (GPL license)
 * $Id$
)%>
<%view.include(templates/nocache.inc)%>
<!DOCTYPE html>
<html lang="<%text(_JEANS_LANG)%>">
<%view.parse(head.inc)%>
<body><div id="body">

<div class="header">
<h1>
<a href="<%text(_CONF_URL_ADMIN)%>"><%text(_CONF_SITE_NAME)%></a>
</h1>
<%view.parse(homelink.inc)%>
</div>

<div id="container">

<div id="content">

<%if.data.isempty(libs,admin,custom)%>
	<%view.parse(selector.inc)%>
<%else%>
	<%view.parse(ajax/selector.inc)%>
<%endif%>

<div class="foot">
Powered by Jeans CMS
</div>
</div>
</div>

<div id="quickmenu">
	<%view.parse(menutool.inc)%>
<div id="quickmenu_main">
	<%view.parse(menu.inc)%>
</div>
</div>

</div>
</body>
</html>
