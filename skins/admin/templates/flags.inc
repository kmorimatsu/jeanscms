<%note(
 * Jeans CMS (GPL license)
 * $Id$
)%>
<tr><td><%text(_ADMIN_FLAGS)%></td><td>
	<input type="hidden" name="flags[update][<%text(sql::FLAG_HIDDEN)%>]" value="<%text(sql::FLAG_HIDDEN)%>" />
		<input id="jeans_flag_hidden" type="checkbox" name="flags[value][<%text(sql::FLAG_HIDDEN)%>]" value="1" <%if.data.flag(sql::FLAG_HIDDEN,flags)%>checked="true"<%endif%> /><%text(_ADMIN_FLAG_HIDDEN)%>&nbsp;&nbsp;&nbsp;
	<%if.data.is(item,1)%>
		<input type="hidden" name="flags[update][<%text(sql::FLAG_DRAFT)%>]" value="<%text(sql::FLAG_DRAFT)%>" />
			<input type="checkbox" name="flags[value][<%text(sql::FLAG_DRAFT)%>]" value="1" onchange="
					if (this.checked) $('#jeans_flag_hidden').get(0).checked=true;
				"  <%if.data.flag(sql::FLAG_DRAFT,flags)%>checked="true"<%endif%> /><%text(_ADMIN_FLAG_DRAFT)%>&nbsp;&nbsp;&nbsp;
		<input type="hidden" name="flags[update][<%text(sql::FLAG_TEMP)%>]" value="<%text(sql::FLAG_TEMP)%>" />
			<input type="checkbox" name="flags[value][<%text(sql::FLAG_TEMP)%>]" value="1" onchange="
					if (this.checked) $('#jeans_flag_hidden').get(0).checked=true;
				" <%if.data.flag(sql::FLAG_TEMP,flags)%>checked="true"<%endif%> /><%text(_ADMIN_FLAG_TEMP)%>
	<%endif%>
	<input type="hidden" name="flags[update][<%text(sql::FLAG_INVALID)%>]" value="<%text(sql::FLAG_INVALID)%>" />
		<input id="jeans_flag_hidden" type="checkbox" name="flags[value][<%text(sql::FLAG_INVALID)%>]" value="1" onchange="
					if (this.checked) $('#jeans_flag_hidden').get(0).checked=true;
				" <%if.data.flag(sql::FLAG_INVALID,flags)%>checked="true"<%endif%> /><%text(_ADMIN_FLAG_INVALID)%>&nbsp;&nbsp;&nbsp;
</td></tr>
