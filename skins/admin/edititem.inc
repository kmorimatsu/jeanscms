<%note(
 * Jeans CMS (GPL license)
 * $Id$
)%>
<%select(template)%>
<%case(init)%>
	<%admin.item.edititemform%>

<%case(head)%>
	<div>
	<%if.data.isempty(_GET,itemid)%>
		<h2><%text(_ADMIN_ITEM_NEW_ITEM)%></h2>
	<%else%>
		<h2><%text(_ADMIN_ITEM_EDIT_ITEM)%></h2>
	<%endif%>


	<form id="ajax_content" method="post" action=""><ins>
	<%error.note(templates/note.inc)%>
	<input type="hidden" name="action" value="admin.item.edititem" />
	<%ticket.hidden(admin.item.edititem)%>

<%case(body)%>
	<%admin.item.mediamanager%>
	<input type="hidden" name="author" value="<%data(author)%>" />
	<h3><%if.data.is(0,id)%><%text(_ADMIN_ITEM_NEW_ITEM)%><%else%><%text(_ADMIN_ITEM_EDIT_ITEM)%><%endif%></h3>
	<table class="nogrid">
	<tr><td><%text(_ADMIN_ITEM_TITLE)%></td><td><input name="title_text" class="text" id="inputtitle" size="60" maxlength="160" value="<%data.hsc(title)%>" onfocus="jeans_edititem_focus(this);"></td></tr>
	<tr><td><%text(_ADMIN_ITEM_BODY)%></td><td><%admin.item.textarea(body)%></td></tr>
	<tr><td><%text(_ADMIN_ITEM_KEYWORDS)%></td><td><input name="keywords_text" class="text" id="inputkeywords" size="60" value="<%data.hsc(keywords)%>"/></td></tr>
	<tr><td><%text(_ADMIN_ITEM_SUBGROUP)%></td><td><%group.tree(templates/selectsubgroup.inc,0,1)%></td></tr>
	<tr><td><%text(_ADMIN_ITEM_TIME)%></td><td><input name="time_text" class="text" id="inputtime" size="30" maxlength="160" value="<%view.strftime(time)%>"> (YYYY-MM-DD HH:MM:SS) <%view.template(currenttime)%></td></tr>
	<%view.parse(templates/flags.inc,item)%>
	<tr><td><%text(_ADMIN_ITEM_SUBMIT)%></td><td><input type="submit" class="submit button" value="<%text(_ADMIN_ITEM_SUBMIT_BUTTON)%>" /></td></tr>
	
	<tr><th colspan="2"><%text(_ADMIN_ITEM_OPTIONAL)%></td></tr>
	<tr><td><%text(_ADMIN_ITEM_MORE)%></td><td><%admin.item.textarea(more)%></td></tr>
	<%skin(inline)%>
		<%select(template)%>
		<%case(body)%>
			<tr><td><%data(desc)%></td><td><%view.parse(templates/forminput.inc)%></td></tr>
		<%endselect%>
		
	<%endskin%>
	<%admin.item.edititemformextra(inline)%>
	<tr><td><%text(_ADMIN_ITEM_SUBMIT)%></td><td><input type="submit" class="submit button" value="<%text(_ADMIN_ITEM_SUBMIT_BUTTON)%>" /></td></tr>
	</table>

<%case(mediamanager)%>
	<script type="text/javascript">
		var jeans_edititem_popup=false;
		var jeans_add_media_button=function(){
			jeans_edititem_popup=false;
			return jeans_edititem_add_media_button();
		};
		var jeans_popup_media_button=function(){
			jeans_edititem_popup=true;
			return jeans_edititem_add_media_button();
		};
	</script>

<%case(textarea)%>
	<script type="text/javascript">
		var jeans_insert_image=function(src,alt,width,height){
			return jeans_edititem_insert_image(src,alt,width,height,jeans_edititem_popup);
		};
	</script>
	<%view.parse(ribbon.inc)%>
	<textarea name="<%data(key)%>_text" id="input<%data(key)%>" onfocus="jeans_edititem_focus(this);" onkeypress="return jeans_edititem_keypress(this,event);"><%data.hsc(value)%></textarea>

<%case(currenttime)%>
		<input id="currenttime" type="checkbox" onclick="
			setTimeout(function(){
					if (!$('#currenttime').get(0).checked) return;
					setTimeout(arguments.callee,1000);
					var obj=$('#inputtime').get(0);
					var two=function(i){
						var str='0'+i.toString(10);
						return str.substr(-2);
					};
					var server=parseInt('<%date(O)%>',10);
					server=parseInt(server/100)+(server%100)/60;
					var now=new Date();
					server=server+now.getTimezoneOffset()/60;
					now=new Date(now.getTime()+server*60*60*1000);
					var year=now.getFullYear();
					var month=two(now.getMonth()+1);
					var day=two(now.getDate());
					var hour=two(now.getHours());
					var min=two(now.getMinutes());
					var sec=two(now.getSeconds());
					obj.value=(year+'-'+month+'-'+day+' '+hour+':'+min+':'+sec);
				},1);
		"/> <%text(_ADMIN_ITEM_CURRENT_TIME)%>

<%case(foot)%>
	</ins></form>
	</div>

<%case(none)%>
	<%text(_ADMIN_NOTHING_TO_SHOW)%>

<%case(time)%>
	%Y-%m-%d %H:%M:%S
<%endselect%>
