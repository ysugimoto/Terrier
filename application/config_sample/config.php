<?php if ( ! defined('BASE_PATH') ) exit;

$config['logging_level']        = 1;
$config['template_path']        = 'templates';
$config['tmp_path']             = 'tmp';
$config['encrypt_cipher']       = 'thisisyourapplicationkey';
$config['disable_email_filter'] = false;

$config['session_name']      = 'terriermailformsession';
$config['session_auth_name'] = 'terrerisessionauth';
$config['session_lifetime']  = 1000 * 60 * 5;


return $config;
