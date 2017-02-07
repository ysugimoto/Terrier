<?php

namespace Terrier;

/**
 *
 * Terrier Mailform application
 * Application MimeType class
 *
 * @namespace Terrier
 * @class MimeType
 * @author Yoshiaki Sugimoto <sugimoto@wnotes.net>
 */
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

    /**
     * Constructor
     *
     * @constructor
     */
    public function __construct()
    {
        // feature detection
        $this->_featureDetection();
    }

    /**
     * Get instance
     *
     * @method getInstance
     * @public static
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
     * @method _fettureDetection
     * @protected
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
     * @method detect
     * @public
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

        return $mime;
    }


    // ---------------------------------------------------------------


    /**
     * Simple file extension getter
     *
     * @method getFileExtension
     * @protected
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
     * @method finfo
     * @protected
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
     * @method finfoClass
     * @protected
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
     * @method command
     * @protected
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
