<%note(
 * Jeans CMS (GPL license)
 * $Id$
)%>
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<%view.parse(head.inc)%>
<title><%text(_INSTALL_WELCOME)%></title>
<style type="text/css">
body {font-size:75%;text-align:center;background-color:#f5f5f5;line-height:1.6;}
ins { text-decoration:none; }
div.wrap {text-align:left;width:500px;border:1px solid #ccc;margin:20px auto;padding:30px;background-color:#fff;}
input.text {font:inherit;width:220px;}
input.submit {padding:10px;background-color:#ddd;cursor:pointer;cursor:hand;border:1px solid #ccc;;margin:20px;letter-spacing:2px;}
input:hover.submit {background-color:#f0f0f0;}
</style>
</head>
<body>
<div class="wrap">
<div style="text-align: left;margin-bottom:20px;">
	<%text(_INSTALL_SET_PERMISSION)%>&nbsp;
	<%if.install.dbok%>
		(<%text(_INSTALL_PERMISSION_OK)%>)
	<%else%>
		(<span style="color:red;"><%text(_INSTALL_PERMISSION_NG)%></span>)
	<%endif%>
</div>
<form method="post" action=""><ins>
<%install.setdata%>
<div style="font-weight:bold;margin-bottom:15px;">
<%text(_INSTALL_INPUT_INFORMATION)%>
</div>
<table style="margin:0 auto;width:100%;">
<%error.note(templates/note.inc)%>
<tr><td style="text-align: right;"><%text(_INSTALL_SITE_NAME)%></td>
	<td><input type="text" class="text" name="sitename_text" value="<%data(sitename)%>" /></td></tr>
<tr><td style="text-align: right;"><%text(_INSTALL_TIME_ZONE)%></td>
	<td><input type="text" class="text" name="timezone_text" value="<%data(timezone)%>" /></td></tr>
<tr><td style="text-align: right;"><%text(_INSTALL_YOUR_EMAIL)%></td>
	<td><input type="text" class="text" name="email" value="<%data(email)%>" /></td></tr>
<tr><td style="text-align: right;"><%text(_INSTALL_YOUR_LOGINNAME)%></td>
	<td><input type="text" class="text" name="loginname" value="<%data(loginname)%>" /></td></tr>
<tr><td style="text-align: right;"><%text(_INSTALL_YOUR_NAME)%></td>
	<td><input type="text" class="text" name="name" value="<%data(name)%>" /></td></tr>
<tr><td style="text-align: right;"><%text(_INSTALL_PASSWORD)%></td>
	<td><input type="password" name="password1_text" value="<%data(password1)%>" /></td></tr>
<tr><td style="text-align: right;"><%text(_INSTALL_PASSWORD_AGAIN)%></td>
	<td><input type="password" name="password2_text" value="<%data(password2)%>" /></td></tr>

<tr><td colspan="2" style="text-align: center;">
<input type="submit" class="submit button" value="<%text(_INSTALL_INSTALL)%>" /></td></tr>
</table>
</ins></form>
</div>
</body>
</html>
