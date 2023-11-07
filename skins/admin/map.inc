<%note(
 * Jeans CMS (GPL license)
 * $Id$
)%>
<%select(template)%>
<%case(init)%>
	<h2><%text(_ADMIN_SITEMAP)%></h2>

	<%error.note(templates/note.inc)%>

	<%group.tree(map.inc,0,1)%>

<%case(head)%>
	<script type="text/javascript" src="<%view.skinfile(scripts/map.js)%>"></script>
	<script type="text/javascript">
		libs_admin_map_text('expand','<%text(_ADMIN_SITEMAP_EXPAND)%>');
		libs_admin_map_text('collapse','<%text(_ADMIN_SITEMAP_COLLAPSE)%>');
		libs_admin_map_text('showitems','<%text(_ADMIN_SITEMAP_SHOWITEMS)%>');
		libs_admin_map_text('hideitems','<%text(_ADMIN_SITEMAP_HIDEITEMS)%>');
	</script>
	<div id="libs_admin_map_busystatus" style="display:none;" >
		<img src="<%view.skinfile(images/busy24.gif)%>" alt="busy" width="24" height="24"/>
	</div>
	<table class="nogrid"><tr><td id="libs_admin_map_leftwindow">
		<table class="nogrid">

<%case(body)%>
	<%if.data.is(0,gid)%>
		<tr style="display:block;" id="libs_admin_map_tr_<%data(id)%>">
		<td onmouseover="$('#libs_admin_map_righttriangle_<%data(id)%>').get(0).style.visibility='visible';" onmouseout="$('#libs_admin_map_righttriangle_<%data(id)%>').get(0).style.visibility='hidden';">
			<img class="sitemap" src="<%view.skinfile(images/triangle_right.gif)%>" id="libs_admin_map_triangle_<%data(id)%>" alt="triangle" title="<%text(_ADMIN_SITEMAP_EXPAND)%>" width="16" height="16" onclick="libs_admin_map_clickicon(this,'<%data(id)%>');" />
			<img class="sitemap" src="<%view.skinfile(images/folder.gif)%>" alt="group" width="16" height="16" />
			<a <%if.data.flag(sql::FLAG_HIDDEN,flags)%>style="color: grey;"<%endif%> href="<%text(_CONF_SELF)%>?page=groupsetting&amp;gid=<%data(id)%>" onclick="return libs_admin_map_clickname(this,'<%data(id)%>');"><%data(name)%></a>
			<img class="sitemap" style="visibility:hidden;" src="<%view.skinfile(images/triangle_left.gif)%>" id="libs_admin_map_righttriangle_<%data(id)%>" alt="triangle" title="<%text(_ADMIN_SITEMAP_SHOWITEMS)%>" width="16" height="16" onclick="libs_admin_map_clickright(this,'<%data(id)%>');" />
			<table class="nogrid" style="display:none;" id="libs_admin_map_itemtr_<%data(id)%>"><tr>
				<td><%group.treetab(tab)%><%view.template(tab)%>&nbsp;&nbsp;</td>
				<td><div id="libs_admin_map_itemtd_<%data(id)%>"><img src="<%view.skinfile(images/busy24.gif)%>" width="24" height="24" /></div></td>
			</tr></table>
		</td>
	<%else%>
		<tr style="display:none;" id="libs_admin_map_tr_<%data(id)%>">
		<td onmouseover="$('#libs_admin_map_righttriangle_<%data(id)%>').get(0).style.visibility='visible';" onmouseout="$('#libs_admin_map_righttriangle_<%data(id)%>').get(0).style.visibility='hidden';">
			<%group.treetab(tab)%>
			<img class="sitemap" src="<%view.skinfile(images/triangle_right.gif)%>" id="libs_admin_map_triangle_<%data(id)%>" alt="triangle" title="<%text(_ADMIN_SITEMAP_EXPAND)%>" width="16" height="16" onclick="libs_admin_map_clickicon(this,'<%data(id)%>');" />
			<img class="sitemap" src="<%view.skinfile(images/folder.gif)%>" alt="group" width="16" height="16" />
			<a <%if.data.flag(sql::FLAG_HIDDEN,flags)%>style="color: grey;"<%endif%> href="<%text(_CONF_SELF)%>?page=groupsetting&amp;sgid=<%data(id)%>" onclick="return libs_admin_map_clickname(this,'<%data(id)%>');"><%data(name)%></a>
			<img class="sitemap" style="visibility:hidden;" src="<%view.skinfile(images/triangle_left.gif)%>" id="libs_admin_map_righttriangle_<%data(id)%>" alt="triangle" title="<%text(_ADMIN_SITEMAP_SHOWITEMS)%>" width="16" height="16" onclick="libs_admin_map_clickright(this,'<%data(id)%>');" />
			<table class="nogrid" style="display:none;" id="libs_admin_map_itemtr_<%data(id)%>"><tr>
				<td><%group.treetab(tab)%><%view.template(tab)%>&nbsp;&nbsp;</td>
				<td id="libs_admin_map_itemtd_<%data(id)%>"></td>
			</tr></table>
		</td>
		<script type="text/javascript">
			libs_admin_map_childs('<%data(id)%>','<%data(sgid)%>');
		</script>
	<%endif%>
	<td style="display:none;" id="libs_admin_map_info_<%data(id)%>" onmouseout="this.style.display='none';">
		id: <%data(id)%>
		<%view.include(templates/flagstatus.inc)%>
		<br />
		<%text(_ADMIN_NAME)%>: <%data(name)%><br />
		<%text(_ADMIN_GROUP_DESCRIPTION)%>: <%data(desc)%><br />
		<%if.data.is(0,gid)%>
			<a href="<%text(_CONF_SELF)%>?page=groupsetting&amp;gid=<%data(id)%>"><%text(_ADMIN_GROUP_EDIT)%></a><br />
		<%else%>
			<a href="<%text(_CONF_SELF)%>?page=groupsetting&amp;sgid=<%data(id)%>"><%text(_ADMIN_GROUP_EDIT)%></a><br />
		<%endif%>
		<%text(_ADMIN_GROUP_MOVE)%><br />
		<a href="#Delete" onclick="return libs_admin_batch_submit('admin.batch.deletegroups','<%data(id)%>');"><%text(_ADMIN_GROUP_DELETE)%></a><br />
		<%text(_ADMIN_GROUP_CREATE)%>
		<a href="<%text(_CONF_SELF)%>?page=newgroup&amp;sgid=<%data(id)%>"><%text(_ADMIN_GROUP_A_SUBGROUP)%></a> / 
		<a href="<%text(_CONF_SELF)%>?page=edititem&amp;itemid=0&amp;sgid=<%data(id)%>"><%text(_ADMIN_GROUP_AN_ITEM)%></a>
	</td>
	</tr>
	<%group.treesub%>

<%case(foot)%>
		</table></td>
		<td id="libs_admin_map_divider" style="background-image: url(<%view.skinfile(images/vline.gif)%>);"></td>
		<td id="libs_admin_map_rightwindow"></td></tr>
		<script type="text/javascript">
			jeans_vdivider_register('#libs_admin_map_divider','#libs_admin_map_leftwindow','320px');
		</script>
	</table>
	<script type="text/javascript">
	/*<![CDATA[*/
	var libs_admin_batch_submit=function(action,id){
		var target=$('#libs_admin_map_rightwindow');
		var data={
			'action_url' : '<%data(_SERVER,REQUEST_URI)%>',
			'batch_action' : action,
			'ids' : id
		};
		var url='<%text(_CONF_SELF)%>?page=batch';
		libs_admin_map_loadtorightwindow(url,data);
		return false;
	}
	/*]]>*/
	</script>


<%case(tab)%>
	&nbsp;&nbsp;&nbsp;
<%case(none)%>
	There is no group.
<%endselect%>

