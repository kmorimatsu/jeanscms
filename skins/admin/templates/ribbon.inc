<%note(
 * Jeans CMS (GPL license)
 * $Id$
)%>
<%select(template)%>
<%case(head)%>
	<div style="padding-top:4px;padding-bottom:4px;margin-bottom:1px;">
<%case(body)%>
	<%if.data.is(-,desc)%>
		<span class="jsbuttonspacer"></span>
	<%elseif.data.is(img,type)%>
		<span class="jsbutton" onclick="<%data.hsc(script)%>" title="<%data(desc)%>">
		<img src="<%data(text)%>" alt="<%data(desc)%>" width="<%data(width)%>" height="<%data(height)%>" />
		</span>
	<%else%>
		<span class="jsbutton" onclick="<%data.hsc(script)%>" title="<%data(desc)%>">
		<%data(text)%>
		</span>
	<%endif%>
<%case(foot)%>
	</div>
<%case(none)%>
	<div style="padding-top:4px;padding-bottom:4px;margin-bottom:1px;"></div>
<%endselect%>
