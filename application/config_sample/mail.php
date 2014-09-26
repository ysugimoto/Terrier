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
 * Send mail subject
 * @value string
 */
$mail['subject'] = '[Contact]';

/**
 * Send mail subject ( for admin )
 * @value string
 */
$mail['subject_for_admin'] = '[Contact(Admin)]';

/* ================= SMTP Congiuration ================ */

/*
 * Authenticate support AUTH LOGIN only.
 *
 * Gmail example:
 * Gmail must connect with SSL/TLS.
 * Therefore, We recommend these settings:
 *
 * $mail['hostname'] = 'ssl://smtp.gmail.com';
 * $mail['port']     = 465;
 * $mail['secure']   = true;
 * $mail['username'] = 'your gmail mailaddress';
 * $mail['password'] = 'generated app password';
 * $mail['timeout']  = 3;
 */

/**
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
 * SMTP Secure connection
 * @value bool
 */
$mail['secure'] = false;


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


/**
 * SMTP Connection timeout
 * @value int
 * @default 3
 */
$mail['timeout'] = 3;

return $mail;
