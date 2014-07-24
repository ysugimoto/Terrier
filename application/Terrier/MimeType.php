<?php

namespace Terrier;

class Mimetype
{
    /**
     * Stack mimetype list
     * @var array
     */
    protected $mimeList = array();


    /**
     * Detection method backend
     * @var string
     */
    protected $backend  = '';

    /**
     * Singleton instalce
     * @var MimeType
     */
    protected static $instance;

    public function __construct()
    {
        // feature detection
        $this->_featureDetection();
    }

    /**
     * Get instance
     * 
     * @access public static
     * @return MymeType
     */
    public static function getInstance()
    {
        if ( ! static::$instance )
        {
            static::$instance = new static();
        }

       return static::$instance;
    }


    // ---------------------------------------------------------------


    /**
     * Bakcend detection on our environment
     * 
     * @access protected
     */
    protected function _featureDetection()
    {
        if ( function_exists('finfo_open') )
        {
            $this->backend = 'finfo';
        }
        else if ( class_exists('finfo') )
        {
            $this->backend = 'finfoClass';
        }
        else if ( ! $env->isWindows && ( FALSE !== @shell_exec('file -bi')) )
        {
            $this->backend = 'command';
        }
    }


    // ---------------------------------------------------------------


    /**
     * Detect mimetype
     * 
     * @access public
     * @param  string $filePath
     * @param  string $ext
     * @return mixed
     */
    public function detect($filePath, $ext = '')
    {
        $mime = null;
        if ( ! empty($this->backend) && method_exists($this, $this->backend) )
        {
            $mime = $this->{$this->backend}($filePath, $ext);
        }

        if ( ! $mime )
        {
            $mime = $this->_detectFromMimeList($filePath, $ext);
        }

        return $mime;
    }


    // ---------------------------------------------------------------


    /**
     * Detection from mimetype file list
     * 
     * @access protected
     * @param  string $path
     * @param  string $ext
     * @return mixed
     */
    protected function _detectFromMimeList($path, $ext)
    {
        $mime      = null;
        $mimes     = $this->_getMimeTypeList();
        $extension = ( ! empty($ext) ) ? $ext : $this->getFileExtension($path);
        if ( isset($mimes[$extension]) )
        {
            $mime = ( is_array($mimes[$extension]) ) ? $mimes[$extension][0] : $mimes[$extension];
        }
        return $mime;
    }


    protected function _getMimeTypeList()
    {
        if ( ! file_exists(COREPATH . 'system/mimetypes.php') )
        {
            throw new RuntimeException('Mimetype list file is not exists.');
        }
        include(COREPATH . 'system/mimetypes.php');
        return $mymetypes;
    }

    // ---------------------------------------------------------------


    /**
     * Simple file extension getter
     * 
     * @access protected
     * @param  string $path
     * @return string
     */
    protected function getFileExtension($path)
    {
        $exp = explode('.', $path);
        return end($exp);
    }


    // ---------------------------------------------------------------


    /**
     * Detect mimetype by finfo function
     * 
     * @access protected
     * @param  string $path
     * @return mixed
     */
    protected function finfo($path)
    {
        $mime = null;
        if ( FALSE !== ($magicPath = getenv('MAGIC')) )
        {
            $finfo = @finfo_open(FILEINFO_MIME, $magicPath);
        }
        else
        {
            $finfo = @finfo_open(FILEINFO_MIME);
        }

        if ( $finfo )
        {
            $mime = finfo_file($finfo, $path);
            if ( preg_match('/\A([a-zA-Z]+\/[a-zA-Z\-]+);.+/u', trim($mime), $matches) )
            {
                $mime = $matches[1];
            }
            finfo_close($finfo);
        }
        return $mime;
    }


    // ---------------------------------------------------------------


    /**
     * Detect mimetype by finfo class
     * 
     * @access protected
     * @param  string $path
     * @return mixed
     */
    protected function finfoClass($path)
    {
        $mime  = null;
        $finfo = new finfo(FILEINFO_MIME);
        $fp    = $finfo->file($filepath);
        if ( preg_match('/\A([a-zA-Z]+\/[a-zA-Z\-]+);.+/u', trim($fp), $matches) )
        {
            $mime = $matches[1];
        }
        return $mime;
    }


    // ---------------------------------------------------------------


    /**
     * Detect mimetype by file -bi command on Unix/Linux
     * 
     * @access protected
     * @param  string $path
     * @return mixed
     */
    protected function command($path)
    {
        $mime = null;
        $cmd  = @shell_exec('file -bi ' . escapeshellarg(realpath($path)));
        if ( strlen($cmd) > 0 )
        {
            if ( preg_match('/\A([a-zA-Z]+\/[a-zA-Z\-]+);.+/u', trim($cmd), $matches) )
            {
                $mime = $matches[1];
            }
        }
        return $mime;
    }
}
