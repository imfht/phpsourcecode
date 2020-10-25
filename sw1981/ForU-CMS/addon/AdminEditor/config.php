<?php
if (strpos(self_name(), 'channel') !== false) {
  cms('kindeditor', array(array('image', '#c_picture_upload'), array('image','#c_cover_upload'), array('multiimage', '#c_slideshow_upload')));
}
elseif (strpos(self_name(), 'detail') !== false) {
  cms('kindeditor', array(array('image', '#d_picture_upload'), array('multiimage', '#d_slideshow_upload'), array('insertfile', '#d_attachment_upload'), array('flv', '#d_video_upload')));
}
elseif (strpos(self_name(), 'link') !== false) {
  cms('kindeditor', array(array('image', '#l_picture_upload')));
}
elseif (strpos(self_name(), 'slideshow') !== false) {
  cms('kindeditor', array(array('image', '#s_picture_upload')));
}
else {
  cms('kindeditor', '');
}
