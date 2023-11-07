<%select(template)%>
<%case(init)%>
	<%if.member.isadmin%>
		<%header(Content-type: text/plain; charset=UTF-8)%>

		<%data.set(jeans_config,table)%>
		<%view.query(backup.inc,SELECT type,name,contextid,value FROM jeans_config WHERE value LIKE 'http://%')%>

		<%data.set(jeans_group,table)%>
		<%view.query(backup.inc,SELECT id,name,desc,gid,sgid,xml as x FROM jeans_group)%>

		<%data.set(jeans_item,table)%>
		<%view.query(backup.inc,SELECT id,author,time,title,body,more,gid,sgid,xml as x FROM jeans_item)%>

		<%data.set(jeans_comment,table)%>
		<%view.query(backup.inc,SELECT id, author,itemid,body,xml as x FROM jeans_comment)%>

		<%data.set(jeans_binary,table)%>
		<%view.query(backup.inc,SELECT id,type,name,contextid,bindata,binsize,time,mime, xml as x FROM jeans_binary)%>

	<%endif%>
<%case(head)%>

	/*****************
	  SQLite dump
	  <%data(table)%>

	*****************/

<%case(body)%>
	<%select(table)%>
	<%case(jeans_group)%>
		INSERT OR REPLACE INTO jeans_group (id,name,desc,gid,sgid,xml)
		VALUES (<%sql.quote(id)%>,<%sql.quote(name)%>,<%sql.quote(desc)%>,<%sql.quote(gid)%>,<%sql.quote(sgid)%>,<%sql.quote(x)%>);

	<%case(jeans_item)%>
		INSERT OR REPLACE INTO jeans_item (id,author,time,title,body,more,gid,sgid,xml)
		VALUES (<%sql.quote(id)%>,<%sql.quote(author)%>,<%sql.quote(time)%>,
		<%sql.quote(title)%>,<%sql.quote(body)%>,<%sql.quote(more)%>,
		<%sql.quote(gid)%>,<%sql.quote(sgid)%>,<%sql.quote(x)%>);

	<%case(jeans_comment)%>
		INSERT OR REPLACE INTO jeans_comment(id,author,itemid,body,xml)
		VALUES (<%sql.quote(id)%>,<%sql.quote(author)%>,<%sql.quote(itemid)%>,<%sql.quote(body)%>,<%sql.quote(x)%>);

	<%case(jeans_config)%>
		INSERT OR REPLACE INTO jeans_config(type,name,contextid,value)
		VALUES (<%sql.quote(type)%>,<%sql.quote(name)%>,<%sql.quote(contextid)%>,<%sql.quote(value)%>);

	<%case(jeans_binary)%>
		INSERT OR REPLACE INTO jeans_binary(id,type,name,contextid,bindata,binsize,time,mime,xml)
		VALUES (<%sql.quote(id)%>,<%sql.quote(type)%>,<%sql.quote(name)%>,<%sql.quote(contextid)%>,
			base64decode('<%data.base64(bindata)%>'),
			<%sql.quote(binsize)%>,<%sql.quote(time)%>,<%sql.quote(mime)%>,<%sql.quote(x)%>);

	<%endselect%>
<%case(foot)%>

	/*** end <%data(table)%> ***/

<%endselect%>