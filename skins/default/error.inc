<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja-JP" lang="ja-JP">
<head>
<%view.parse(head.inc)%>
<title><%text(_CONF_SITE_NAME)%></title>
</head>
<body><div id="body">

<div id="header">
<h1><a href="<%text(_CONF_URL_INDEX)%>"><%text(_CONF_SITE_NAME)%></a></h1>
</div>

<div id="mainwrapper">
<h2 class="error">エラーが発生しました</h2>
<dl>
<!--dt><%data(error,fatal)%></dt-->
<%error.note(templates/error.inc)%>
<dd><a href="javascript:history.go(-1);">Go back</a></dd>
</dl>
</div>

<div id="sidebar">
<dl class="menu">
<dt>ナビゲーション</dt>
<dd><a href="<%blog.link(blog)%>">今日</a></dd>
<%if.member.loggedin%><dd><a href="<%text(_CONF_URL_ADMIN)%>">管理メニュー</a></dd>
<%endif%></dl>

<dl id="search">
<dt>検索</dt>
<dd>
<%view.parse(forms/searchform.inc)%></dd>
</dl>

<dl id="login">
<dt>ログイン</dt>
<dd>
<%view.parse(forms/loginform.inc)%></dd>
</dl>

</div>

<div id="footer">Powered by
  Jeans CMS
</div>

</div>
</body>
</html>