<?php if ( ! defined('BASE_PATH') ) exit;

/**
 * Terrier Mail settings
 *
 * @author Yoshiaki Sugimoto <sugimoto@wnotes.net>
 * @copyright Yoshiaki Sugimoto
 */

/**
 * Mail sender method
 * @value string
 * values:
 *   'php' : send by php mail() function
 *   'smtp': send from SMTP server socket connection
 */
$mail['sender_method'] = 'php';


/**
 * Administrator mailaddress
 * Application send to this address when form sended
 * @value string
 */
$mail['admin_email'] = 'youraddress@example.com';


/**
 * Send mail from
 * @value string
 */
$mail['from'] = 'noreply@example.com';


/**
 * Send mail from name
 * @value string
 */
$mail['from_name'] = 'Test Site';


/**
 * SMTP Congiuration
 * SMTP hostname
 * @value string
 */
$mail['hostname'] = 'localhost';


/**
 * SMTP server port
 * @value int
 */
$mail['port'] = 25;


/**
 * SMTP Crypto connection
 * @value bool
 */
$mail['crypto'] = false;


/**
 * SMTP-Auth username
 * @value string
 */
$mail['username'] = 'yourname';


/**
 * SMTP-Auth password
 * @value string
 */
$mail['password'] = 'yourpassword';


/**
 * SMTP Connection keep-alived
 * @value bool
 * (usually false)
 */
$mail['keepalive'] = true;


return $mail;
