<%note(
 * Jeans CMS (GPL license)
 * $Id$
)%>
<%select(template)%>
<%case(init)%>
	<div class="jp_skinfiles_breadcrumbs">
		<%text(_JP_SKINFILES_CURRENT_PATH)%>: 
		<%jp.skinfiles.class.breadcrumbs%>
	</div>

<%case(head)%>
	<a href="?padmin=jp_skinfiles"><img src="<%view.skinfile(images/home.gif)%>" width="16" height="16" alt="home" /> skins</a> / 

<%case(body)%>
	<a href="<%data(link)%>"><%data(name)%></a><%if.data.is(dir,type)%> / <%endif%>

<%case(foot)%>
<%case(none)%>
	<a href="?padmin=jp_skinfiles"><img src="<%view.skinfile(images/home.gif)%>" width="16" height="16" alt="home" /> skins</a> / 

<%endselect%>