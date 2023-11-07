<%note(
 * Jeans CMS (GPL license)
 * $Id$
)%>
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<%view.parse(head.inc)%>
<title><%text(_INSTALL_CONGRATULATIONS)%></title>
</head>
<body>
<div style="text-align: center;"><%text(_INSTALL_CONGRATULATIONS)%></div>

<%text(_INSTALL_DONE)%><br /><br />

<%text(_INSTALL_GOTO_SITE)%><br />
<a href="<%text(_CONF_URL_INDEX)%>"><%text(_CONF_URL_INDEX)%></a><br /><br />

<%text(_INSTALL_GOTO_ADMIN)%><br />
<a href="<%text(_CONF_URL_INDEX)%>jeans/"><%text(_CONF_URL_INDEX)%>jeans/</a><br /><br />

</body>
</html>
