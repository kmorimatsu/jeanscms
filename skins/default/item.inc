<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja-JP" lang="ja-JP">
<head>
<%view.parse(head.inc)%>
<title><%item.data(title)%> &raquo; <%blog.setting(name)%></title>
</head>
<body><div id="body">

<div id="header">
<h1>
<a href="<%blog.setting(url)%>"><%blog.setting(name)%></a>
<span class="desc"><%blog.setting(desc)%></span>
</h1>
</div>

<div id="mainwrapper">
<%item(templates/full.inc)%>
<div id="coment">
<h3>コメント</h3>
<%comments(templates/comment.inc)%>

<h3>コメント送信</h3>
<%view.parse(forms/commentform.inc)%>
</div>
</div>

<div id="sidebar">
<dl class="menu">
<dt>カテゴリー</dt>
<%blog.categorylist(templates/menu_category.inc)%>
</dl>

<dl class="menu">
<dt>最近の記事</dt>
<%blog(templates/menu.inc)%>
</dl>

<dl class="menu">
<dt>いままでの記事</dt>
<%blog.archivelist(templates/menu_archive.inc)%>
</dl>

<dl class="menu">
<dt>ナビゲーション</dt>
<dd><a href="<%item.older(link)%>">前の記事</a></dd>
<dd><a href="<%item.newer(link)%>">次の記事</a></dd>
<dd><a href="<%blog.link(blog)%>">今日</a></dd>
<dd><a href="<%blog.link(archivelist)%>">過去ログ</a></dd>
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