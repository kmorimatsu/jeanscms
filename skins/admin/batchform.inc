<%note(
 * Jeans CMS (GPL license)
 * $Id$
)%>
<form id="libs_admin_batch_form" method="post" action="<%text(_CONF_SELF)%>?page=batch">
<input type="hidden" name="action_url" value="<%data(_SERVER,REQUEST_URI)%>" />
<input type="hidden" name="batch_action" value="" />
<input type="hidden" name="ids" value="" />
</form>
<script type="text/javascript">
/*<![CDATA[*/
function libs_admin_batch_submit(action,id){
  var form=$('#libs_admin_batch_form').get(0);
  form.batch_action.value=''+action;
  form.ids.value=''+id;
  form.submit();
  return false;
}
/*]]>*/
</script>
