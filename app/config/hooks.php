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
| WhatsApp Verification Check Hook
| -------------------------------------------------------------------------
| This hook ensures that Google sign-in users must verify their WhatsApp
| before accessing protected pages
|
*/
$hook['post_controller_constructor'][] = array(
    'class'    => 'Whatsapp_verification_check',
    'function' => 'check',
    'filename' => 'Whatsapp_verification_check.php',
    'filepath' => 'hooks'
);

