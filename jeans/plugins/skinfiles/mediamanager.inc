<%note(
 * Jeans CMS (GPL license)
 * $Id$
)%>
<script type="text/javascript">
	var jeans_edititem_popup=false;
	var jeans_add_media_button=function(){
		jeans_edititem_popup=false;
		window.open('?action=jp.skinfiles.class.mediamanager','jeans_media');
	};
	var jeans_popup_media_button=function(){
		jeans_edititem_popup=true;
		window.open('?action=jp.skinfiles.class.mediamanager','jeans_media');
	};
</script>
