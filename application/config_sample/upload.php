<?php if ( ! defined('BASE_PATH') ) exit;

$upload['upload_dir']         = TMP_PATH . 'upload';
$upload['encrypt_filename']   = FALSE;
$upload['multiple_numbering'] = TRUE;
$upload['allowed_extension']  = 'gif|jpg|jpeg|png';
$upload['suffix_scriptfile']  = TRUE;
$upload['extension_tolower']  = TRUE;
$upload['image_only']         = FALSE;

// extended validate parameters enables:
//$upload['max_filesize']       = int -- validate filesize
//$upload['max_width']          = int -- validate image width  ( image file only )
//$upload['max_height']         = int -- validate image height ( image file only )

return $upload;
