<%note(
 * Jeans CMS (GPL license)
 * $Id$
)%>
<%ifnot.member.loggedin%>
	<h2><%text(_ADMIN_MENU_INTRODUCTION)%></h2>
	<p><%text(_ADMIN_MENU_DESC1)%></p>
	<p><%text(_ADMIN_MENU_DESC2)%></p>

<%else%>

	<h2><%text(_ADMIN_MENU_YOUR_SETTINGS)%></h2>
	<ul>
	<li><a href="<%text(_CONF_SELF)%>?page=membersetting&amp;mid=<%member.setting(id)%>"><%text(_ADMIN_MENU_PROFILE)%></a></li>
	<%if.member.isadmin%>
		<li><a href="<%text(_CONF_SELF)%>?page=itemlist&amp;mid=<%member.setting(id)%>"><%text(_ADMIN_MENU_ITEMS)%></a></li>
		<li><a href="<%text(_CONF_SELF)%>?page=itemlist&amp;mid=<%member.setting(id)%>&amp;option[draft]=1"><%text(_ADMIN_MENU_DRAFTS)%></a></li>
	<%endif%>
	<li><a href="<%text(_CONF_SELF)%>?page=commentlist&amp;mid=<%member.setting(id)%>"><%text(_ADMIN_MENU_COMMENTS)%></a></li>
	<li><a href="<%text(_CONF_SELF)%>?page=media"><%text(_ADMIN_MENU_MEDIA)%></a></li>
	</ul>

	<%if.member.isadmin%>
		<h2><%text(_ADMIN_MENU_MANAGEMENT)%></h2>
		<ul>
		<li><a href="<%text(_CONF_SELF)%>?page=map"><%text(_ADMIN_MENU_SITEMAP)%></a></li>
		<li><a href="<%text(_CONF_SELF)%>?page=config"><%text(_ADMIN_MENU_CONFIGURATION)%></a></li>
		<li><a href="<%text(_CONF_SELF)%>?page=plugin"><%text(_ADMIN_MENU_PLUGIN)%></a></li>
		<li><a href="<%text(_CONF_SELF)%>?page=log"><%text(_ADMIN_MENU_LOG)%></a></li>
		<li><a href="<%text(_CONF_SELF)%>?page=memberlist"><%text(_ADMIN_MENU_MEMBERS)%></a></li>
		<li><a href="<%text(_CONF_SELF)%>?page=newgroup"><%text(_ADMIN_MENU_NEW_GROUP)%></a></li>
		<%if.classavailable(admin_sql)%><li><a href="<%text(_CONF_SELF)%>?page=db"><%text(_ADMIN_MENU_DATABASE)%></a></li><%endif%>
		</ul>

	<%endif%>
	
	<%note(event_quick_menu/event_member_quick_menu implementation follows)%>
	<%skin(inline)%>
		<%select(template)%>
		<%case(head)%>
			<h2><%text(_ADMIN_PLUGIN)%></h2>
			<ul>
		<%case(body)%>
			<li><a href="<%data(url)%>" title="<%data.t(tooltip)%>"><%data.t(title)%></a></li>
		<%case(foot)%>
			</ul>
		<%endselect%>
		
	<%endskin%>
	<%admin.event.quickmenu(inline)%>
	
<%endif%>