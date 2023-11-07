<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Cache-Control" content="no-cache, must-revalidate" />
<meta http-equiv="Content-Script-Type" content="text/javascript" />
<link rel="alternate" type="application/rss+xml" title="RSS: <%blog.setting(name)%>" href="<%text(_CONF_URL_INDEX)%>feed.php?gid=<%blog.setting(id)%>" />
<%if.blog.categoryis(selected)%>
	<link rel="alternate" type="application/rss+xml" title="RSS: <%blog.setting(name)%>/<%blog.sgsetting(name)%>" href="<%text(_CONF_URL_INDEX)%>feed.php?sgid=<%blog.sgsetting(id)%>" />
<%endif%>
<link rel="stylesheet" type="text/css" href="<%view.skinfile(styles/base.css)%>" />
<!--[if IE 5.5000 | IE 6]><link rel="stylesheet" type="text/css" href="<%view.skinfile(styles/ie6.css)%>" /><![endif]-->

<!--
<link rel="stylesheet" type="text/css" href="<%view.skinfile(styles/red01.css)%>" />
<link rel="stylesheet" type="text/css" href="<%view.skinfile(styles/blue01.css)%>" />
<link rel="stylesheet" type="text/css" href="<%view.skinfile(styles/yellow01.css)%>" />
-->