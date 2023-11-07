<%if.member.loggedin%>
	<a id="jeans_mf"></a>

	<form method="post" action="#jeans_mf">
	<div class="mailform">
		<%if.classloaded(error)%><%view.parse(../templates/errors.inc)%><%endif%>
		<input type="hidden" name="mid" value="<%memberinfo(id)%>" />
		<input type="hidden" name="action" value="admin.mail.sendmessage" />
		<%ticket.hidden(admin.mail.sendmessage)%>

		<textarea alt="message:" name="message_text" class="formfield" rows="10" cols="40"><%data.hsc(_POST,message_text)%></textarea>
		
		<input type="checkbox" name="frommember" vaue="" />
		<%text(_JEANS_MEMBERMAIL_FROM_MEMBER)%>

		<input type="submit" class="submit button" alt="<%text(_JEANS_MEMBERMAIL_SUBMIT)%>" value="<%text(_JEANS_MEMBERMAIL_SUBMIT)%>" class="formbutton" />
	</div>
	</form>
<%else%>
	<div class="mailform"><%text(_JEANS_MEMBERMAIL_LOGIN_REQUIRED)%></div>
<%endif%>

