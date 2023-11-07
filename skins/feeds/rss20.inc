<%select(template)%>
<%case(init)%>
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0">
  <channel>
    <title><%blog.setting(name)%><%if.blog.categoryis(selected)%> / <%blog.sgsetting(name)%><%endif%></title>
    <link><%blog.setting(url)%></link>
    <description><%blog.setting(desc)%><%if.blog.categoryis(selected)%> / <%blog.sgsetting(desc)%><%endif%></description>
    <language>ja</language>
    <generator>Jeans CMS</generator>
    <copyright>&#169;</copyright>
    <category>Weblog</category>
    <docs>http://backend.userland.com/rss</docs>
    <image>
      <url><%view.skinfile(images/jeans2.gif)%></url>
      <title><%blog.setting(name)%></title>
      <link><%blog.setting(url)%></link>
    </image>
    <%blog.narrowby(category)%>
    <%blog(rss20.inc,10)%>
  </channel>
</rss>
<%case(body)%>
<item>
 <title><%data(title)%></title>
 <link><%blog.setting(url)%>?itemid=<%data(id)%></link>
 <description><![CDATA[<%data.raw(body)%><%data.raw(more)%>]]></description>
 <category><%data(cname)%></category>
 <comments><%blog.setting(url)%>?itemid=<%data(id)%></comments>
 <pubDate><%date(D%2c d M Y H:i:s T)%></pubDate>
 <guid isPermaLink="true">ITEM<%data(id)%>_<%date(Ymd)%></guid>
</item>
<%endselect%>