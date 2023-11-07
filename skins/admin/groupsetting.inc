<%note(
 * Jeans CMS (GPL license)
 * $Id$
)%>
<%select(template)%>
<%case(init)%>
	<%if.data.isset(_GET,sgid)%>
		<h2><%text(_ADMIN_GROUP_SETTINGS_OF_SUBGROUP)%> <a href=""><%group.sgsetting(name)%></a></h2>
	
		<%admin.group.subgroupsetting(groupsetting.inc)%>
	
	<%else%>
		<h2><%text(_ADMIN_GROUP_SETTINGS_OF_GROUP)%> <a href="<%group.setting(url)%>"><%group.setting(name)%></a></h2>
	
		<%admin.group.groupsetting(groupsetting.inc)%>
	
	<%endif%>

<%case(head)%>
	<form id="ajax_content" method="post" action=""><ins>
	<%error.note(templates/note.inc)%>
	<input type="hidden" name="action" value="admin.group.edit" />
	<%ticket.hidden(admin.group.edit)%>

	<%if.data.isset(_GET,sgid)%>
		<h3><%text(_ADMIN_GROUP_SUBGROUP_SETTINGS)%></h3>
	<%else%>
		<h3><%text(_ADMIN_GROUP_GROUP_SETTINGS)%></h3>
	<%endif%>
	<table class="nogrid">

<%case(body)%>
	<%if.data.is(text,type)%>
		<%if.data.is(hidden,extra)%><tr style="display:none;"><%else%><tr><%endif%>
	<%else%>
		<tr>
	<%endif%>
	<%if.data.is(separator,type)%>
		<th><%data(desc)%><%admin.help.link(name,group_)%></th><th></th>
	<%else%>
		<td><%data(desc)%><%admin.help.link(name,group_)%></td><td><%view.parse(templates/forminput.inc)%></td>
	<%endif%>
	</tr>

<%case(foot)%>
	<%view.parse(templates/flags.inc,group)%>
	<tr><td><%text(_ADMIN_SUBMIT)%></td><td><input type="submit" class="submit button" value="<%text(_ADMIN_GROUP_SAVE)%>" /></td></tr>
	</table></ins></form>

<%case(none)%>
	<%error.note(templates/note.inc)%>
	<%text(_ADMIN_NOTHING_TO_SHOW)%>

<%endselect%>
