<%note(
 * Jeans CMS (GPL license)
 * $Id$
)%>
<%select(template)%>
<%case(language)%>
	<%view.parse(language.inc)%>
<%case(install)%>
	<%view.parse(install.inc)%>
<%case(done)%>
	<%view.parse(done.inc)%>
<%case(failed)%>
	<%view.parse(failed.inc)%>
<%endselect%>
