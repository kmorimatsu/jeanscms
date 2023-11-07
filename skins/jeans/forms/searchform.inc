<form method="get" action="<%blog.setting(url)%>">
	<div class="searchform">
<input type="hidden" name="amount" value="0" />
<input type="hidden" name="gid" value="<%if.data.isset(1)%><%data(1)%><%else%><%blog.setting(id)%><%endif%>" />
		<input name="query_text" alt="<%text(_JEANS_SEARCHFORM_QUERY)%>" class="formfield" size="10" maxlength="60" accesskey="4" value="<%data(_GET,query_text)%>" />
		<input type="submit" class="submit button" alt="<%text(_JEANS_SEARCHFORM_SUBMIT)%>" value="<%text(_JEANS_SEARCHFORM_SUBMIT)%>" class="formbutton" />
	</div>
</form>
