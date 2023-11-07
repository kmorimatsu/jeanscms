<%note(
 * Jeans CMS (GPL license)
 * $Id$
)%>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Cache-Control" content="no-cache, must-revalidate" />
<meta http-equiv="Content-Script-Type" content="text/javascript" />
<link rel="stylesheet" type="text/css" href="<%view.skinfile(styles/forms.css)%>" />
<link rel="stylesheet" type="text/css" href="<%view.skinfile(styles/admin.css)%>" />
<link rel="stylesheet" type="text/css" href="<%view.skinfile(styles/edititem.css)%>" />
<%view.include(scripts/jquery.inc)%>
<script type="text/javascript" src="<%view.skinfile(scripts/admin.js)%>"></script>
<script type="text/javascript" src="<%view.skinfile(scripts/edititem.js)%>"></script>
<%admin.help.javascript%><%admin.help.setfile(help/)%><%admin.help.seticon(images/help.gif,15,15)%>
<title><%text(_ADMIN_ADMIN_PAGE)%>(<%data(libs,admin,page)%>): <%text(_CONF_SITE_NAME)%></title>
</head>
