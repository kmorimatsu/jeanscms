<%note(
 * Jeans CMS (GPL license)
 * $Id$
)%>
<%select(template)%>
<%case(init)%>
	<?xml version="1.0" encoding="UTF-8"?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<%view.parse(head.inc)%>
	<title><%text(_INSTALL_WELCOME)%></title>
	</head>
	<body>
	<div style="text-align: center;">
	<%text(_INSTALL_SELECT_LANGUAGE)%><br /><br />
	<form method="get" action="<%text(_CONF_SELF)%>"><ins>
	<%install.langlist(language.inc)%><br /><br />
	<input type="submit" class="submit button" value="<%text(_INSTALL_CONTINUE)%>" />
	</ins></form>
	</div>
	</body>
	</html>

<%case(head)%>
	<select name="language">

<%case(body)%>
	<%if.data.isempty(selected)%>
		<option value="<%data(language)%>"><%data(language)%></option>
	<%else%>
		<option value="<%data(language)%>" selected="ture"><%data(language)%></option>
	<%endif%>

<%case(foot)%>
	</select>

<%endselect%>
