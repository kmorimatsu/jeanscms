<%note(
 * Jeans CMS (GPL license)
 * $Id$
)%>
<%select(template)%>
<%case(init)%>
	<h2><%text(_ADMIN_PLUGIN_LIST)%></h2>

	<%error.note(templates/note.inc)%>

	<%admin.plugin.list%>
	<%admin.plugin.install(templates/pluginlist.inc)%>
	<h3 style="margin-bottom:15px;"><%text(_ADMIN_PLUGIN_DELETEUNUSEDOPTIONS)%></h3>
	<div>
		<form method="post" action="">
		<input type="hidden" name="action" value="admin.plugin.deleteunusedoptions" />
		<%ticket.hidden(admin.plugin.deleteunusedoptions)%>
		<input type="submit" class="submit button" value="<%text(_ADMIN_DELETE)%>" />
		<input type="checkbox" name="sure" /> <%text(_ADMIN_IM_SURE)%>
		</form>
	</div>

<%case(head)%>
	<table class="grid">
	<tr><th><%text(_ADMIN_INFORMATION)%></th><th style="width: 60%;"><%text(_ADMIN_DESC)%></th><th><%text(_ADMIN_ACTIONS)%></th></tr>

<%case(body)%>
	<%if.data.isempty(notfound)%>
		<tr><td>
			<%if.data.isdata(id,_POST,ids)%><a name="moved"></a><%endif%>
			<a name="<%data(id)%>"></a><b><%data.t(name)%></b> (<%data(id)%>)<br />
			By: <%data(author)%><br />
			Version: <%data(version)%>
			<%ifnot.data.isempty(url)%>&nbsp;&nbsp;&nbsp;<a href="<%data(url)%>">Site</a><%endif%>
		</td><td>
			<b><%text(_ADMIN_DESC)%>:</b><br /><%data.t(desc)%><%admin.event.postparseplugindesc%>
			<%ifnot.data.isempty(events)%><br /><br /><b>Events:</b><br /><%data(events)%><%endif%>
		</td><td>
			<%ifnot.data.isempty(options)%><a href="<%text(_CONF_SELF)%>?poption=<%data(id)%>"><%text(_ADMIN_PLUGIN_OPTIONS)%></a><%endif%><br />
			<%ifnot.data.isempty(admin)%><a href="<%text(_CONF_SELF)%>?padmin=<%data(id)%>"><%text(_ADMIN_PLUGIN_ADMIN)%></a><%endif%><br />
			<br />
			<a href="#Delete" onclick="return libs_admin_batch_submit('admin.batch.deleteplugins','<%data(id)%>');"><%text(_ADMIN_DELETE)%></a> / 
			<a href="#Move" onclick="return libs_admin_batch_moveplugins('up','<%data(id)%>');"><%text(_ADMIN_PLUGIN_UP)%></a> / 
			<a href="#Move" onclick="return libs_admin_batch_moveplugins('down','<%data(id)%>');"><%text(_ADMIN_PLUGIN_DOWN)%></a>
		</td></tr>
		
	<%else%>
		<tr><td><%data(id)%></td>
		<td><%text(_ADMIN_PLUGIN_FILE_NOT_FOUND)%></td>
		<td><a href="#Delete" onclick="return libs_admin_batch_submit('admin.batch.deleteplugins','<%data(id)%>');"><%text(_ADMIN_DELETE)%></a></td></tr>

	<%endif%>

<%case(foot)%>
	</table>
	<%view.parse(batchform.inc)%>
	<form method="post" action="#moved" id="libs_admin_batch_moveplugins_form">
	<input type="hidden" name="action" value="admin.batch.moveplugins" />
	<input type="hidden" name="direction" value="" />
	<input type="hidden" name="ids" value="" />
	<%ticket.hidden(admin.batch.moveplugins)%>
	</form>
	<script type="text/javascript">
	/*<![CDATA[*/
	function libs_admin_batch_moveplugins(direction,id){
	  var form=$('#libs_admin_batch_moveplugins_form').get(0);
	  form.direction.value=''+direction;
	  form.ids.value=''+id;
	  form.submit();
	  return false;
	}
	/*]]>*/
	</script>

<%case(none)%>
	<%text(_ADMIN_NOTHING_TO_SHOW)%>

<%case(extra)%>
	<p>Install a plugin.</p>

<%endselect%>
