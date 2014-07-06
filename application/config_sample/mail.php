<?php if ( ! defined('BASE_PATH') ) exit;

$mail['sender_method'] = 'php';

$mail['admin_email'] = 'youraddress@example.com';
$mail['from']        = 'noreply@example.com';
$mail['from_name']   = 'Test Site';

$mail['hostname']    = 'localhost';
$mail['port']        = 25;
$mail['crypto']      = false;
$mail['username']    = 'yourname';
$mail['password']    = 'yourpassword';
$mail['keepalive']   = true;

return $mail;
