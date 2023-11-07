<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja-JP" lang="ja-JP">
<head>
<%view.parse(head.inc)%>
<title><%blog.setting(name)%></title>
</head>
<body><div id="body">

<div id="header">
<h1>
<a href="<%blog.setting(url)%>"><%blog.setting(name)%></a>
<span class="desc"><%blog.setting(desc)%></span>
</h1>
</div>

<div id="mainwrapper">
<h2 class="userinfo">ユーザー情報：<%memberinfo(name)%></h2>
<ul class="ibody">
<li>本名: <%memberinfo(name)%></li>
<li>サイト: <a href="<%memberinfo(web)%>"><%memberinfo(web)%></a></li>
</ul>
<h3>メッセージフォーム</h3>
<%view.parse(forms/membermailform.inc)%></div>

<div id="sidebar">
<dl class="menu">
<dt>ナビゲーション</dt>
<dd><a href="<%blog.link(blog)%>">今日</a></dd>
</dl>

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