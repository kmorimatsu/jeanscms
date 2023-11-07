<%note(
 * Jeans CMS (GPL license)
 * $Id$
)%>
<%select(template)%>
<%case(head)%>

	<form method="post" action="" id="libs_admin_sql_editlink"><ins>
	<input type="hidden" name="action" value="admin.sql.editform" />
	<%ticket.hidden(admin.sql.editform)%>
	<input type="hidden" name="tablename" value="" />
	<input type="hidden" name="itemid" value="" />
	<input type="hidden" name="query_text" value="<%data.hsc(_POST,query_text)%>" />
	</ins></form>

	<script type="text/javascript">
	/*<![CDATA[*/
	function libs_admin_sql_editlink(table,id) {
	  var form=$('#libs_admin_sql_editlink').get(0);
	  form.tablename.value=''+table;
	  form.itemid.value=''+id;
	  form.submit();
	}
	function libs_admin_sql_flipSpan(element){
	  var t1=$('#'+element.id+' >span:first').get(0).innerHTML;
	  var t2=$('#'+element.id+' >span:last').get(0).innerHTML;
	  $('#'+element.id+' >span:first').html(t2);
	  $('#'+element.id+' >span:last').html(t1);
	}
	function libs_admin_sql_itemlist(table){
	  var form=$('#libs_admin_sql_queryform').get(0);
	  form.query_text.value="SELECT 'itemlist' as libs_admin_sql_mode,"+
	    "'"+table+"' as libs_admin_sql_table, * FROM "+table+" LIMIT 10 OFFSET 0";
	  form.submit();
	}
	function libs_admin_sql_postquery(query){
	  var form=$('#libs_admin_sql_queryform').get(0);
	  form.query_text.value=query;
	  form.submit();
	}
	function libs_admin_sql_changeorder(table,key){
	  var form=$('#libs_admin_sql_queryform').get(0);
	  var query=(''+form.query_text.value).replace(/;?[\s]*$/,'');
	  var query2=query.replace(/(?:(')[^']*'|(")[^"]*"|(`)[^`]*`|\s+ORDER\s+BY\s+\S+\s+(ASC|DESC))/ig,
	      function(m,m1){
	    switch (m1) {
	      case "'": case '"': case '`':
	        return m;
	      default:
            if (m.slice(-4).toUpperCase()=='DESC') return ' ORDER BY '+key+' ASC';
	        else return ' ORDER BY '+key+' DESC';
	    }
	  });
	  if (query==query2) {
	    query2=query.replace(/(?:(')[^']*'|(")[^"]*"|(`)[^`]*`|\s+(LIMIT)\s+)/ig,
	        function(m,m1){
	      switch (m1.toUpperCase()) {
	        case "'": case '"': case '`':
	        return m;
	        default:
	          return ' ORDER BY '+key+' ASC LIMIT ';
	      }
	    });
        if (query==query2) query2=query+' ORDER BY '+key+' ASC ';
	  }
	  form.query_text.value=query2;
	  form.submit();
	}
	function libs_admin_sql_changeoffset(page,limit){
	  var form=$('#libs_admin_sql_queryform').get(0);
	  var query=(''+form.query_text.value).replace(/;?[\s]*$/,'');
	  query=query.replace(/\s+OFFSET\s+[0-9]+\s*$/i,'');
	  query=query+' OFFSET '+(page-1)*limit;
	  form.query_text.value=query;
	  form.submit();
	}
	/*]]>*/
	</script>

	<%if.data.is(itemlist,libs,admin_sql,mode)%>
		<%text(_ADMIN_SQL_TABLE_CONTENTS)%>: <%data(libs,admin_sql,table)%><br />
		<a href="javascript:libs_admin_sql_editlink('<%data(libs,admin_sql,table)%>','0');"><%text(_ADMIN_SQL_ADD_ITEM_TO_TABLE)%></a>
	<%endif%>

	<div style="float: right;"><%page.init%>
	Page <%data(libs,page,this)%> of <%data(libs,page,pages)%>
	<%if.data.ismorethan(1,libs,page,pages)%><%page(paging.inc)%><%endif%>
	</div>

	<table class="grid">

<%case(tr)%>
	<tr>
<%case(/tr)%>
	</tr>

<%case(th)%>
	<th><a href="javascript:libs_admin_sql_changeorder('<%data(libs,admin_sql,table)%>','<%data(key)%>');"><%data(key)%></a></th>
<%case(td)%>

	<%select(libs,admin_sql,mode)%>
	<%case(tablelist)%>
		<%if.data.is(name,key)%>
		<td title="Item list"><a href="javascript:libs_admin_sql_itemlist('<%data(value)%>');"><%data(value)%></a></td>
		<%else%><%view.template(td-default)%><%endif%>
	<%case(itemlist)%>
		<%if.data.is(id,key)%>
			<td title="Edit this item."><a href="javascript:libs_admin_sql_editlink('<%data(libs,admin_sql,table)%>','<%data(value)%>');"><%data(value)%></a></td>
		<%else%><%view.template(td-default)%><%endif%>
	<%case.else%>
		<%view.template(td-default)%>
	<%endselect%>

<%case(td-default)%>
	<td title="<%data.hsc(value)%>" id="<%data.uniqueid%>"  onClick="libs_admin_sql_flipSpan(this);">
	<span><%data.shorten(20,...,value)%></span><span style="display:none;"><%data.hsc(value)%></span></td>
<%case(foot)%>
	</table>
<%case(none)%>
	<%text(_ADMIN_NOTHING_TO_SHOW)%>
<%case(error)%>
	<div style="color:red;">
	<%data.hsc(errormessage)%><br />
	SQLSTATE: <%data(sqlstate)%> (code: <%data(errorcode)%>)
	</div>
<%endselect%>
