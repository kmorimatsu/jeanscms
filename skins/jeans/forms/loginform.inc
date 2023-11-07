<%if.member.loggedin%>
<a id="jeans_lf"></a>
<div class="loginform">
	<%text(_JEANS_LOGINFORM_YOUARE)%> <%member.setting(loginname)%> ! (<a href="?action=member.logout" title="<%text(_JEANS_LOGOUT)%>"><%text(_JEANS_LOGOUT)%></a>)
</div>
<%else%>
<a id="jeans_lf"></a>
<form method="post" action="#jeans_lf">
	<div class="loginform">
		<%if.classloaded(error)%><%view.parse(../templates/errors.inc)%><%endif%>

		<label for="jeans_lf_name" accesskey="l"><%text(_JEANS_LOGINFORM_NAME)%></label>
		<input id="jeans_lf_name" name="login" size="10" value="" class="formfield" />

		<label for="jeans_lf_pwd"><%text(_JEANS_LOGINFORM_PWD)%></label>
		<input id="jeans_lf_pwd" name="password_text" size="10" type="password" value="" class="formfield" />

		<input type="checkbox" checked="true" value="1" name="shared" id="jeans_lf_shared" />
		<label for="jeans_lf_shared"><%text(_JEANS_LOGINFORM_SHARED)%></label>

		<input type="submit" class="submit button" alt="<%text(_JEANS_LOGIN)%>" value="<%text(_JEANS_LOGIN)%>" class="formbutton" />
	</div>
</form>
<%endif%>