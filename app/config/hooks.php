<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	https://codeigniter.com/user_guide/general/hooks.html
|
*/

/*
| -------------------------------------------------------------------------
| WhatsApp Verification Guard
| -------------------------------------------------------------------------
| Check if Google sign-in users have verified their WhatsApp number
| before accessing protected pages.
|
*/
$hook['post_controller_constructor'][] = array(
    'class'    => 'Whatsapp_verification_guard',
    'function' => 'check_whatsapp_verification',
    'filename' => 'Whatsapp_verification_guard.php',
    'filepath' => 'hooks'
);

