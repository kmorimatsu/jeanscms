<%note(
 * Jeans CMS (GPL license)
 * $Id$
)%>
<%select(type)%>
<%case(text)%>
	<%if.data.is(hidden,extra)%>
		<input type="hidden" name="<%data(name)%>" value="<%data.hsc(value)%>" />
	<%elseif.data.contains(size=",extra)%>
		<input type="text" class="text" name="<%data(name)%>" value="<%data.hsc(value)%>" size="<%data.after(size=",extra)%>" />
	<%else%>
		<input type="text" class="text" name="<%data(name)%>" value="<%data.hsc(value)%>" size="40" />
	<%endif%>
<%case(yesno)%>
	<%if.data.is(1,value)%>
		<input name="<%data(name)%>" value="1" checked="true" id="<%data(name)%>1" type="radio">
	<%else%>
		<input name="<%data(name)%>" value="1" id="<%data(name)%>1" type="radio">
	<%endif%>
	<label for="<%data(name)%>1"><%text(_JEANS_YES)%></label>
	<%if.data.is(1,value)%>
		<input name="<%data(name)%>" value="0" id="<%data(name)%>0" type="radio">
	<%else%>
		<input name="<%data(name)%>" value="0" checked="true" id="<%data(name)%>0" type="radio">
	<%endif%>
	<label for="<%data(name)%>0"><%text(_JEANS_NO)%></label>
<%case(textarea)%>
	<textarea name="<%data(name)%>" cols="
		<%if.data.contains(cols=",extra)%><%data.after(cols=",extra)%><%else%>40<%endif%>
	" rows="
		<%if.data.contains(rows=",extra)%><%data.after(rows=",extra)%><%else%>5<%endif%>
	"><%data.hsc(value)%></textarea>
<%case(select)%>
	<%view.parse(formselect.inc)%>
<%case.else%>
	<%if.data.contains(size=",extra)%>
		<input type="<%data(type)%>" name="<%data(name)%>" value="<%data.hsc(value)%>" size="<%data.after(size=",extra)%>" />
	<%else%>
		<input type="<%data(type)%>" name="<%data(name)%>" value="<%data.hsc(value)%>" size="40" />
	<%endif%>
<%endselect%>

