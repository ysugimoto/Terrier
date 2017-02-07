<?php

namespace Terrier;

/**
 *
 * Terrier Mailform application
 * Application File uploader
 *
 * @namespace Terrier
 * @class Upload
 * @author Yoshiaki Sugimoto <sugimoto@wnotes.net>
 */
class Upload
{
    /**
     * Stack error message
     * @type string
     */
    protected $_error;

    /**
     * Double extension list ( ex tar.gz, tar.bz2 and more... )
     * @type array
     */
    protected $_doubleDotExtensions = array('gz' => 'tar', 'bz2' => 'tar');


    /**
     * Image extension for Web
     * @type array
     */
    protected $_imageExtension  = array('gif', 'jpg', 'jpeg', 'png');


    /**
     * Safety add suffix extensions list
     * @type array
     */
    protected $_suffixExtension = array('js', 'php', 'exe', 'rb', 'py', 'pl');


    /**
     * Current process settings
     * @type array
     */
    protected $_settings = array();


    /**
     * Upload default settings
     * @type array
     */
    protected $_defaultSettings = array(
        'upload_dir'         => '',
        'encrypt_filename'   => FALSE,
        'multiple_numbering' => TRUE,
        'allowed_extension'  => 'gif|jpg|jpeg|png',
        'suffix_scriptfile'  => TRUE,
        'extension_tolower'  => TRUE,
        'image_only'         => FALSE

        // extended validate parameters enables:
        // 'max_filesize' => int -- validate filesize
        // 'max_width'    => int -- validate image width  ( image file only )
        // 'max_height'   => int -- validate image height ( image file only )
    );

    /**
     * Constructor
     *
     * @constructor
     * @param array $conf
     */
    public function __construct($conf = array())
    {
        $this->_settings = new Variable(array_merge($this->_defaultSettings, $conf));
    }


    // ---------------------------------------------------------------


    /**
     * Get error message
     *
     * @method processError
     * @public
     * @return string
     */
    public function processError()
    {
        return $this->_error;
    }


    // ---------------------------------------------------------------


    /**
     * Execute upload from settings
     *
     * @method process
     * @public
     * @param  mixed  $handle
     * @param  string $destDir
     * @return mixed ( object or bool )
     */
    public function process($handle, $destDir = '')
    {
        if ( ! $destDir )
        {
            $destDir = $this->_settings->upload_dir;
        }

        // Does upload destination directory exists?
        if ( empty($destDir) )
        {
            return $this->_setError('Upload destination directory must not empty!');
        }
        else if ( ! is_dir($destDir) )
        {
            return $this->_setError('Upload destination directory is not exists!');
        }
        else if ( ! is_writable($destDir) )
        {
            return $this->_setError('Upload destination directroy can\'t has write permission!');
        }

        $destDir = rtrim($destDir, '/') . '/';
        $isArray = is_array($handle);
        $handle  = ( $isArray ) ? $handle : array($handle);
        $resp    = array();

        foreach ( $handle as $field )
        {
            if ( FALSE === ($data = $this->_uploadProcess($field, $destDir)) )
            {
                return FALSE;
            }
            $resp[$field] = $data;
        }
        return ( $isArray ) ? $resp : reset($resp);
    }


    // ---------------------------------------------------------------


    /**
     * Main upload process
     *
     * @method _uploadProcess
     * @protected
     * @param  string $field
     * @param  string $destDir
     * @return mixed
     */
    protected function _uploadProcess($field, $destDir)
    {
        // Does uploaded field exists?
        $file = Request::file($field);
        if ( ! $file )
        {
            return $this->_setError($field . ' field not exists.');
        }

        $result = new \stdClass;

        // Uploaded file is really uploaded by form?
        if ( ! is_uploaded_file($file['tmp_name']) )
        {
            return $this->_setError('Uploaded file does not come from HTML form uploaded.');
        }

        // check uploaded file status
        switch ( $file['error'] )
        {
            case UPLOAD_ERR_OK: // upload OK!!
                break;
            case UPLOAD_ERR_INI_SIZE:
                return $this->_setError('Upload_filesize_over php_settings_limit');
            case UPLOAD_ERR_FORM_SIZE:
                return $this->_setError('Uploaded_filesize_over_html_form_limit');
            case UPLOAD_ERR_PARTIAL:
                return $this->_setError('Uploaded_file_partial');
            case UPLOAD_ERR_NO_FILE:
                return $this->_setError('Uploaded_no_file');
            case UPLOAD_ERR_NO_TMP_DIR:
                return $this->_setError('Uploaded_no_temp_file');
            case UPLOAD_ERR_CANT_WRITE:
                return $this->_setError('Uploaded_file_cant_write');
            case UPLOAD_ERR_EXTENSION:
                return $this->_setError('Uploaded_file_error_extension');
            default:
                return $this->_setError('Uploaded_no_file');
        }

        // detect filename
        if ( FALSE === ( $dat = $this->_validateFile($file)) )
        {
            return FALSE;
        }

        // Numbering suffix same file if you need
        $dat->fullpath = $destDir . $dat->filename . '.' . $dat->extension;
        if ( $this->_settings->multiple_numbering === TRUE )
        {
            $idx = 0;
            while ( file_exists($dat->fullpath) )
            {
                $dat->filename = $dat->filename . '_' . ++$idx;
                $dat->fullpath = $destDir . $dat->filename . '.' . $dat->extension; 
            }
        }

        // try movefile
        if ( ! @move_uploaded_file($file['tmp_name'], $dat->fullpath) )
        {
            return $this->_setError('Uploaded file didn\'t move target directory!');
        }

        return $dat;
    }


    // ---------------------------------------------------------------


    /**
     * Validate upload file
     *
     * @method _validateFile
     * @protected
     * @param  array $FILE
     * @return mixed
     */
    protected function _validateFile($FILE)
    {
        $info     = new \stdClass;
        $filepath = $FILE['tmp_name'];
        $filename = $this->_prepFilename($FILE['name']);

        // check limit filesize
        if ( $this->_settings->max_filesize
             && filesize($filepath) > (int)$this->_settings->max_filesize )
        {
            return $this->_setError('Uploaded file over max filesize.');
        }

        // Split filename to body/extension.
        if ( strpos($filename, '.') === FALSE )
        {
            $extension = '';
        }
        else
        {
            $exp = explode('.', $filename);
            $extension = array_pop($exp);
            // double dot extension ( ex. tar.gz, tar,bz2 )
            if ( isset($this->_doubleDotExtensions[$extension])
                 && end($exp) === $this->_doubleDotExtensions[$extension] )
            {
                $extension = array_pop($exp) . '.' . $extension;
            }
            $filename = implode('.', $exp);
        }

        if ( $this->_settings->extension_tolower === TRUE )
        {
            $extension = strtolower($extension);
        }

        // Is uploaded file is allowed extension?
        if ( ! $this->_checkAllowedExtension($extension) )
        {
            return $this->_setError('Uploaded file can not allowed extension.');
        }

        $info->orig_filename = $filename;
        $info->extension     = $extension;

        // Does uploaded file need ".txt" suffix for script execution attack?
        if ( $this->_settings->suffix_scriptfile === TRUE
              && in_array($extension, $this->_suffixExtension) )
        {
            $info->extension .= '.txt';
            $info->mimetype   = 'text/plain';
            $info->width      = 0;
            $info->height     = 0;
        }
        else
        {
            // set is_image and mimetype
            $img = @getimagesize($filepath);
            if ( ! $img )
            {
                $info->is_image = FALSE;
                $info->width    = 0;
                $info->height   = 0;

                $info->mimetype = MimeType::getInstance()->detect($filepath, $extension);
            }
            else
            {
                $info->is_image = TRUE;
                $info->width    = (int)$img[0];
                $info->height   = (int)$img[1];
                $info->mimetype = $img['mime'];

                // If upload file is image, check image width/height
                if ( $this->_settings->max_width
                     && (int)$img[0] > (int)$this->_settings->max_width )
                {
                    return $this->_setError('Uploaded file over max width.');
                }
                else if ( $this->_settings->max_height
                    && (int)$img[1] > (int)$this->_settings->max_height )
                {
                    return $this->_setError('Uploaded file over max height.');
                }

                // uploaded and scaned mimetype is same?
                if ( $info->mimetype !== $FILE['type'] )
                {
                    return $this->_setError('Uploaded file has illegal mimetype.');
                }
            }
        }

        if ( is_null($info->mimetype) )
        {
            return $this->_setError('Process stopped brcause mimetype can\'t detected.');
        }

        $info->filename = ( $this->_settings->encrypt_filename === TRUE )
                            ? sha1($filename . uniqid(mt_rand(), TRUE))
                            : $filename;

        return $info;
    }


    // ---------------------------------------------------------------


    /**
     * escape filename
     *
     * @method _prepFilename
     * @protected
     * @param  string $filename
     * @return string
     */
    protected function _prepFilename($filename)
    {
        $filename = str_replace('　', ' ', basename($filename));
        // remove special characters
        return preg_replace('/[!"#\$%&\'\(\)=;:\/\^~\\\|\?<>\\`]/', '', $filename);
    }


    // ---------------------------------------------------------------


    /**
     * Check uploaded file's extension is allowed
     *
     * @method _checkAllowedExtension
     * @protected
     * @param  string $ext
     * @return bool
     */
    protected function _checkAllowedExtension($ext)
    {
        $allowed = ( $this->_settings->image_only === TRUE )
                     ? $this->_imageExtension
                     : array_filter(explode('|', $this->_settings->allowed_extension));

        return in_array($ext, $allowed);
    }


    // ---------------------------------------------------------------


    /**
     * Set error string and always return FALSE
     *
     * @method _setError
     * @protected
     * @param  string $msg
     * @return FALSE
     */
    protected function _setError($msg)
    {
        $this->_error = $msg;
        return FALSE;
    }


    // ---------------------------------------------------------------


    /**
     * Get error string
     *
     * @method getError
     * @public
     * @return string
     */
    protected function getError()
    {
        return $this->_error;
    }
}
