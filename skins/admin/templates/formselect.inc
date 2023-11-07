<%note(
 * Jeans CMS (GPL license)
 * $Id$
)%>
<%select(extra)%>
<%case(grouplist)%>
	<%admin.select.grouplist%>
<%case(skinlist)%>
	<%admin.select.skinlist%>
<%case(adminskinlist)%>
	<%admin.select.skinlist(adminskin.inc)%>
<%case(languagelist)%>
	<%admin.select.languagelist%>
<%case(editorlist)%>
	<%admin.select.editorlist%>
<%case(mediamanagerlist)%>
	<%admin.select.mediamanagerlist%>
<%case.else%>
	<%admin.select.custom%>
<%endselect%>