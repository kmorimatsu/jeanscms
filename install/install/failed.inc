<%note(
 * Jeans CMS (GPL license)
 * $Id$
)%>
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<%view.parse(head.inc)%>
<title><%text(_INSTALL_FAILED)%></title>
</head>
<body>
<div style="text-align: center;"><%text(_INSTALL_FAILED_UNFORTUNATELY)%></div>

<table><%error.note(templates/note.inc)%></table>

<%text(_INSTALL_FAILED_DESCRIPTION)%>

</body>
</html>
