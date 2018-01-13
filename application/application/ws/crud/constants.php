<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
  |--------------------------------------------------------------------------
  | File and Directory Modes
  |--------------------------------------------------------------------------
  |
  | These prefs are used when checking and setting modes when working
  | with the file system.  The defaults are fine on servers with proper
  | security, but you may wish (or even need) to change the values in
  | certain environments (Apache running a separate process for each
  | user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
  | always be used to set the mode correctly.
  |
 */
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
  |--------------------------------------------------------------------------
  | File Stream Modes
  |--------------------------------------------------------------------------
  |
  | These modes are used when working with fopen()/popen()
  |
 */

define('FOPEN_READ', 'rb');
define('FOPEN_READ_WRITE', 'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE', 'ab');
define('FOPEN_READ_WRITE_CREATE', 'a+b');
define('FOPEN_WRITE_CREATE_STRICT', 'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');


/* End of file constants.php */
/* Location: ./application/config/constants.php */

define('BASEURL', 'http://' . $_SERVER['HTTP_HOST'] . '/2014/blacklist/');
define('BASEDIR', $_SERVER['DOCUMENT_ROOT'] . '/2014/blacklist/');
define('PROJECT_TITLE', 'Blacklist');
define('ASSET', BASEURL . 'assets/');
define('IMG_URL', BASEURL . 'images/');
define('USER_IMG', IMG_URL . 'user/');
define('SIGN_URL', IMG_URL . 'signature/');
define('CSS_URL', ASSET . 'css/');
define('JS_URL', ASSET . 'js/');
define('ENCRYPTION_KEY', 'cps.openxcell.key');

define('DIR_VIEW', BASEDIR . 'application/ws/views/');
define('DIR_LIB', BASEDIR . 'application/ws/libraries/');
define('DIR_PDF', BASEDIR . 'assets/pdf/');

define('UPLOADS', BASEDIR . "images/");


define('ACCOUNT_DEACTIVE', 'Your Account Is Inactivated Now.');
define('LOGIN_INVALID', 'You Have Entered Wrong User Name And Password.');
define('LOG_SUCCESS', 'You are logged in Successfully.');
define('LOGOUT_SUCCESS', 'You are logged out Successfully.');
define('INSUFF_DATA', 'Insufficient Data.');
define('LOGOUT_ERROR', 'Something Wrong with logout.!!');

define('FAIL_STATUS', 101);
define('SUCCESS_STATUS', 200);

/* EMAIL CONSTANTS */
define('EMAIL_SUBJECT', 'HungerMafia');
define('FROM_EMAIL_ID', 'support@hungermafia.com');
define('FROM_EMAIL_NAME', 'Hungermafia');
define('MAIL_PROTOCOL', 'smtp');
define('MAIL_HOST', 'ssl://smtp.googlemail.com');
define('MAIL_USERNAME', 'hungermafiakellton2015@gmail.com');
define('MAIL_PASSWORD', 'Hmkellton_2015');
define('MAIL_PORT', '465');
define('MAIL_TIMEOUT', '5');
define('MAIL_MAIL_TYPE', 'html');
define('MAIL_CHARSET', 'utf-8');
define('MAIL_VALIDATE_EMAIL_ID', FALSE);
define('MAIL_EMAIL_PRIORITY', 3);
define('MAIL_BCC_BATCH_MODE', FALSE);
define('MAIL_BCC_BATCH_SIZE', 200);

define('MYSQL_DATE_FORMAT', '%b %d, %Y');

define('CONTRACT_DATA_SUCCESS', 'Contract Record found successfully.');

define('ACCOUNT_SUCCESS', 'Your account has been created successfully.');
define('ACCOUNT_UPDATED', 'Your account has been updated successfully.');
define('ACCOUNT_ERROR', 'There is something wrong to create your account.');
define('ACCOUNT_EXISTS', 'This account is already exists.');
define('ACCOUNT_NOT_EXISTS', 'This account is not exists.');
define('ACCOUNT_EMAIL_ERROR', 'The email address is already exists.');

define('USERCHOICE_RECORD_FOUND', 'User choice record founded successfully.');
define('FORGOTPASS_RESET_SUCCESS', 'Your password has been changed successfully and newly created password sent it to your email address.');
define('FORGOTPASS_RESET_ERROR', 'The entered email address is not exists. Please register with the same email address.');

define('PASSWORD_CHANGE_SUCCESS', 'Your password has been changed successfully.');
define('PASSWORD_CHANGE_FAIL', 'Your password has not been changed.');
define('PASSWORD_INVALID', 'Please enter valid input.');
define('PASSWORD_WRONG', 'You have entered wrong password.');
define('PASSWORD_NOT_CONFIRM', 'New password and confirm password is not matched.');

define('RESTAURANT_FOUND', 'Restaurant Record found successfully.');
define('NO_RECORD_FOUND', 'No record found.');
define('RESTAURANT_OPEN_CLOSE_DAYS', serialize(array(
    1 => 'MON',
    2 => 'TUS',
    3 => 'WED',
    4 => 'THU',
    5 => 'FRI',
    6 => 'SAT',
    7 => 'SUN'
)));

define('RESTAURANT_LIKED_BEFORE', 'Restaurant liked already.');
define('RESTAURANT_DISLIKED_BEFORE', 'Restaurant disliked already.');
define('RESTAURANT_LIKED', 'You have liked restaurant successfully.');
define('RESTAURANT_DISLIKED', 'You have disliked restaurant  successfully.');

define('RESTAURANT_FAV_SET', 'You have already set as favourite restaurant.');
define('RESTAURANT_FAV', 'You have set as favourite restaurant successfully.');

define('REWARD_FOUND', 'Reward found successfully.');

define('REWARD_RQST', 'You have been requested for reward successfully.');
define('REWARD_RQST_FAIL', 'Your reward request has been failed.');
define('REWARD_RQST_OLD', 'You have been already requested for this reward.');
