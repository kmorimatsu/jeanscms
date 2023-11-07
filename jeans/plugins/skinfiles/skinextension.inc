<%note(
 * Jeans CMS (GPL license)
 * $Id$
)%>
<%select(template)%>
<%case(init)%>
	<%text(_JP_SKINFILES_THE_SKIN_EXTENDS_1)%><b><%jp.skinfiles.class.skinname%></b><%text(_JP_SKINFILES_THE_SKIN_EXTENDS_2)%>
	<%jp.skinfiles.class.extends%>

<%case(body)%>
	<%if.data.is(1,counter)%>: <%else%>, <%endif%>
	<a href="<%text(_CONF_SELF)%>?padmin=jp_skinfiles&amp;dir_path=<%data(name)%>/"><%data(name)%></a>

<%endselect%>
