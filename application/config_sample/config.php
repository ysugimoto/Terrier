<?php if ( ! defined('BASE_PATH') ) exit;

/**
 * Terrier Mailform Configuration
 *
 * @author Yoshiaki Sugimoto <sugimoto@wnotes.net>
 * @copyright Yoshiaki Sugimoto
 */

/**
 * Logging lebel
 * constant defined at Log.php
 * @value int
 * values:
 *   1(0x01) : INFO
 *   2(0x02) : WARN
 */
$config['logging_level'] = 1;

/**
 * Timezone setting
 * @value string
 */
$config['timezone'] = 'Asia/Tokyo';;

/**
 * Template path
 * path to template directory name
 * @value string
 */
$config['template_path'] = 'templates';


/**
 * Tmp path
 * path to tmporary directory name
 * @value string
 */
$config['tmp_path'] = 'tmp';


/**
 * Session Encrypt cipher
 * please change your random seed string
 * @value string
 */
$config['encrypt_cipher'] = 'thisisyourapplicationkey';


/**
 * Disable email filter
 * Disable Email filter function ( for Japanese mailaddress )
 * @value bool
 * values:
 *   false: disable
 *   true:  enable
 */
$config['disable_email_filter'] = false;


/**
 * Admin mailbody filename
 * @value string
 */
$config['admin_mailbody'] = 'mailbody.txt';


/**
 * Replay mailbody filename
 * @value string
 */
$config['reply_mailbody'] = 'reply.txt';


/**
 * Session name
 * @value string
 */
$config['session_name'] = 'terriermailformsession';


/**
 * Session authenticate key name
 * @value string
 */
$config['session_auth_name'] = 'terrerisessionauth';


/**
 * Session lifetime
 * @value int
 */
$config['session_lifetime']  = 1000 * 60 * 5;


return $config;
