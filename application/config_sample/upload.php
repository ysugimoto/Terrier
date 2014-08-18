<?php if ( ! defined('BASE_PATH') ) exit;

/**
 * Terrier Upload settings
 *
 * @author Yoshiaki Sugimoto <sugimoto@wnotes.net>
 * @copyright Yoshiaki Sugimoto
 */

/**
 * Upload directory
 * @value string
 */
$upload['upload_dir'] = TMP_PATH . 'upload';


/**
 * Uploaded file need to encrypt
 * @value bool
 */
$upload['encrypt_filename'] = FALSE;


/**
 * Uploaded file must be numbering suffix
 * @value bool
 */
$upload['multiple_numbering'] = TRUE;


/**
 * Allowed uploaded file extension ( "|" separatted )
 * @value string
 */
$upload['allowed_extension'] = 'gif|jpg|jpeg|png';


/**
 * Add ".txt" suffix to script file 
 * @value bool
 */
$upload['suffix_scriptfile'] = TRUE;


/**
 * Uploaded file extension to lower
 * @value bool
 */
$upload['extension_tolower'] = TRUE;


/**
 * Accepted image file only
 * @value bool
 */
$upload['image_only'] = FALSE;

// extended validate parameters enables:
//$upload['max_filesize'] = int -- validate filesize
//$upload['max_width']    = int -- validate image width  ( image file only )
//$upload['max_height']   = int -- validate image height ( image file only )

return $upload;
