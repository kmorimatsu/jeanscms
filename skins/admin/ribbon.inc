<%note(
 * Jeans CMS (GPL license)
 * $Id$
)%>
<%admin.item.addtoribbon%>
<%admin.item.addtoribbon(blod,Bold,jeans_edititem_add_around_cursor('<b>'%2c'</b>');,img,images/button-bold.gif,16,16)%>
<%admin.item.addtoribbon(italic,Italic,jeans_edititem_add_around_cursor('<i>'%2c'</i>');,img,images/button-italic.gif,16,16)%>
<%admin.item.addtoribbon(link,Make Link,var url=prompt('URL?'%2c'http://'); if (url!=null) jeans_edititem_add_around_cursor('<a href="'+url+'">'%2c'</a>');,img,images/button-link.gif,16,16)%>
<%admin.item.addtoribbon(separator1,-)%>
<%admin.item.addtoribbon(align_left,Align Left,jeans_edititem_add_around_cursor('<div style="text-align: left">'%2c'</div>');,img,images/button-alignleft.gif,16,16)%>
<%admin.item.addtoribbon(align_center,Align Center,jeans_edititem_add_around_cursor('<div style="text-align: center">'%2c'</div>');,img,images/button-aligncenter.gif,16,16)%>
<%admin.item.addtoribbon(align_right,Align Right,jeans_edititem_add_around_cursor('<div style="text-align: right">'%2c'</div>');,img,images/button-alignright.gif,16,16)%>
<%admin.item.addtoribbon(align_justify,Align Justify,jeans_edititem_add_around_cursor('<div style="text-align: justify">'%2c'</div>');,img,images/button-alignjustify.gif,16,16)%>
<%admin.item.addtoribbon(serparator2,-)%>
<%admin.item.addtoribbon(left_box,Add left box,jeans_edititem_add_around_cursor('<div class="leftbox">'%2c'</div>');,img,images/button-left.gif,16,16)%>
<%admin.item.addtoribbon(right_box,Add right box,jeans_edititem_add_around_cursor('<div class="rightbox">'%2c'</div>');,img,images/button-right.gif,16,16)%>
<%admin.item.addtoribbon(separator3,-)%>
<%admin.item.addtoribbon(media,Add Media,jeans_add_media_button();,img,images/button-media.gif,16,16)%>
<%admin.item.addtoribbon(popup,Popup Media,jeans_popup_media_button();,img,images/button-popup.gif,16,16)%>
<%admin.item.addtoribbon(separator4,-)%>
<%admin.item.ribbon(templates/ribbon.inc)%>
