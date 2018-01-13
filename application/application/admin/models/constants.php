<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
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
/* Site Path and Urls */
define('_PATH', substr(dirname(__FILE__), 0, -25));
define('_URL', substr($_SERVER['PHP_SELF'], 0, - (strlen($_SERVER['SCRIPT_FILENAME']) - strlen(_PATH))));
define('SITE_PATH', _PATH . "/");
define('SITE_URL', _URL . "/");
define('WEBSITE_URL', SITE_URL . '/');
/*
define('DOMAIN_URL', 'http://' . $_SERVER['SERVER_NAME'] . '/stol');
define('DOC_ROOT', $_SERVER['DOCUMENT_ROOT'] . '/stol');
 
define('BASEURL', DOMAIN_URL . '/admin/');
*/
define('DOMAIN_URL', 'http://' . $_SERVER['SERVER_NAME'] . '/2014/blacklist');
define('DOC_ROOT', $_SERVER['DOCUMENT_ROOT'] . '/2014/blacklist');
define('BASEURL', DOMAIN_URL . '/admin/');
define('gmapkey', '');
define('MAINTITLE', 'blacklist');
/* Site Path and Urls */
/* Site Path and Urls */
/* Admin Site Path and Urls */
define('ADMIN_URL', SITE_URL . '/');
define('ADMIN_PATH', SITE_PATH . "/");
define('ADMIN_IMAGE_URL', DOMAIN_URL . '/admin/images/');
/* Admin Site Path and Urls */
/* URL AND PATH FOR IMAGES  */
define('IMAGE_PATH', BASEURL . "images/");
define('IMAGE_URL', BASEURL . "images/");
define('CSS_IMAGE_PATH', ADMIN_PATH . "css/");
define('CSS_IMAGE_URL', BASEURL . "css/");
/* URL AND PATH FOR IMAGES  */
/* URL FOR CSS AND JS */
define('CSS_URL', BASEURL);
define('PLUGIN_URL', BASEURL . "plugins/");
define('JS_URL', BASEURL);
define('JS_URL_GEO', BASEURL . "js/");
define('DESIGN_PLUGIN_URL', BASEURL . "plugins/");
/* URLS FOR FILE UPLOADED */
define('UPLOADS', DOC_ROOT . "/images/");
define('UPLOADS_URL', DOMAIN_URL . "/images/");
define('ENCRYPTION_KEY', 'OpenXcellTechnolabs');
define('DIR_ADMIN_VIEW', DOC_ROOT . '/application/admin/views/');

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

define('RESTAURANT_OPEN_CLOSE_DAYS', serialize(array(
    1 => 'MON',
    2 => 'TUE',
    3 => 'WED',
    4 => 'THU',
    5 => 'FRI',
    6 => 'SAT',
    7 => 'SUN'
)));


/*
  |--------------------------------------------------------------------------
  | CONSTANT MEASSAGES
  |--------------------------------------------------------------------------
  |
 */
/* * **************************************************
 * *                ERROR MEASSAGE
 * ************************************************** */
define('ACCOUNT_NOT_ACTIVE', 'Your account is not activated.');
define('LOGIN_ERROR', '<strong>Username</strong> and/or <strong>Password</strong> are wrong');
define('ACTIVATED_ERROR', 'Your account is already activated.');
define('ADMIN_ERROR', 'No user exists with this email.');
define('ACTIVE_ERROR', 'You have not activated your account yet. Please go to your inbox and activate your account first.');
define('ACCESS_ERROR', 'Access not authorized for this account.');
define('SETTING_NOT_FOUND', 'Settings not found.');
define('SETTINGS_NOT_CHANGED', 'Problem changing Settings, Please try again.');
define('OLD_PASSWORD_NOT_OK', 'Old Password you have entered is not correct.');
define('PASSWORD_SENT', 'An email has been sent to your email address <br /> which is the registered address for your account. Please reset your password.');
define('PASSWORD_NOT_CHANGED', 'Problem changing password, Please try again.');
define('ACTIVE_ACCOUNT', 'Your co-brick account activated successfully.');
define('PASSWORD_CONFIRM_NOT_MATCH', 'New Password and Re-type New Password do not match.');
/* * **************************************************
 * *                SUCCESS MEASSAGE
 * ************************************************** */
define('SETTINGS_CHANGED', 'Settings Changed successfully.');
define('PASSWORD_CHANGED', 'Your Password changed successfully.');
/* * **************************************************
 * *                INFO MEASSAGE
 * ************************************************** */

/* Admin Settings */
define('ADMIN_ADDED', 'Admin Added successfully.');
define('ADMIN_EDITED', 'Admin Edited successfully.');
define('ADMIN_DELETED', 'Admin Removed successfully.');
define('ADMIN_NOT_ADDED', 'Problem adding Admin, Please try again.');
define('ADMIN_NOT_EDITED', 'Problem editing Admin, Please try again.');
define('ADMIN_NOT_DELETED', 'Problem removing Admin, Please try again.');
define('ADMIN_EXISTS', 'Admin already exists with this email/username.');
/* Admin Settings */

/* User Settings */
define('USER_ADDED', 'User details are successfully added');
define('USER_NOT_ADDED', 'Failed to add user details, Please try again!');
define('USER_EXISTS', 'Email ID already exist!');
define('USER_USERNAME_EXISTS', 'Username already exist!');
define('USER_MOBILENO_EXISTS', 'Mobile Number already exist!');
define('USER_EDITED', 'User details are successfully updated');
define('USER_NOT_EDITED', 'Failed to update user details, Please try again!');
define('USER_DELETED', 'User removed successfully');
define('USER_NOT_DELETED', 'Failed to remove user, Please try again!');
define('USER_IMPORT', 'Users successfully added');
define('USER_NOT_IMPORT', 'Failed to imports user details, Please try again!');
define('USER_EXCEL_ERROR', 'Please Select Valid Excel file!');
define('USER_IMAGE_PATH', DOMAIN_URL . "/images/user/");
define('TEMP_FILE_PATH', ADMIN_PATH . "images/tempfiles/");
/* User Settings */


/* Restaurant/Venues Settings */
define('RESTAURANT_ADDED', 'Restaurant/Venues details are successfully added');
define('RESTAURANT_NOT_ADDED', 'Failed to add restaurant details, Please try again!');
define('RESTAURANT_EXISTS', 'Email ID already exist!');
define('RESTAURANT_EDITED', 'Restaurant/Venues details are successfully updated');
define('RESTAURANT_NOT_EDITED', 'Failed to update restaurant details, Please try again!');
define('RESTAURANT_DELETED', 'Restaurant/Venues removed successfully');
define('RESTAURANT_NOT_DELETED', 'Failed to remove restaurant, Please try again!');
define('RESTAURANT_IMPORT', 'Restaurant/Venues(s) successfully added');
define('RESTAURANT_NOT_IMPORT', 'Failed to imports restaurant details, Please try again!');
define('RESTAURANT_EXCEL_ERROR', 'Please Select Valid Excel file!');
define('RESTAURANT_IMAGE_PATH', DOMAIN_URL . "/images/restaurant/");
define('CUISINE_IMAGE_PATH', DOMAIN_URL . "/images/cuisine/");
define('CATEGORY_IMAGE_PATH', DOMAIN_URL . "/images/category/");
define('RESTAURANT_PHOTO_IMAGE_PATH', DOMAIN_URL . "/images/restaurantPhoto/");
define('RESTAURANT_MENU_IMAGE_PATH', DOMAIN_URL . "/images/restaurantMenu/");
define('REWARD_IMAGE_PATH', DOMAIN_URL . "/images/reward/");
define('QRCODE_IMAGE_PATH', DOMAIN_URL . "/images/qrcode/");
/* Restaurant/Venues Settings */

/* Image Settings */
define('IMAGE_ADDED', 'Image details are successfully added');
define('IMAGE_NOT_ADDED', 'Failed to add image details, Please try again!');
define('IMAGE_EXISTS', 'Email ID already exist!');
define('IMAGE_EDITED', 'Image details are successfully updated');
define('IMAGE_NOT_EDITED', 'Failed to update image details, Please try again!');
define('IMAGE_DELETED', 'Image removed successfully');
define('IMAGE_NOT_DELETED', 'Failed to remove image, Please try again!');
/* Image Settings */

/* Menu Image Settings */
define('MENU_ADDED', 'Menu Image details are successfully added');
define('MENU_NOT_ADDED', 'Failed to add menu image details, Please try again!');
define('MENU_EXISTS', 'Email ID already exist!');
define('MENU_EDITED', 'Menu Image details are successfully updated');
define('MENU_NOT_EDITED', 'Failed to update menu image details, Please try again!');
define('MENU_DELETED', 'Menu Image removed successfully');
define('MENU_NOT_DELETED', 'Failed to remove menu image, Please try again!');
/* Menu Image Settings */


/* Category Settings */
define('CATEGORY_ADDED', 'Category details are successfully added');
define('CATEGORY_NOT_ADDED', 'Failed to add category details, Please try again!');
define('CATEGORY_EXISTS', 'Email ID already exist!');
define('CATEGORY_CATEGORYNAME_EXISTS', 'Category Name already exist!');
define('CATEGORY_EDITED', 'Category details are successfully updated');
define('CATEGORY_NOT_EDITED', 'Failed to update category details, Please try again!');
define('CATEGORY_DELETED', 'Category removed successfully');
define('CATEGORY_NOT_DELETED', 'Failed to remove category, Please try again!');
/* Category Settings */


/* Cuisine Settings */
define('CUISINE_ADDED', 'Cuisine details are successfully added');
define('CUISINE_NOT_ADDED', 'Failed to add cuisine details, Please try again!');
define('CUISINE_EXISTS', 'Email ID already exist!');
define('CUISINE_CUISINENAME_EXISTS', 'Cuisine Name already exist!');
define('CUISINE_EDITED', 'Cuisine details are successfully updated');
define('CUISINE_NOT_EDITED', 'Failed to update cuisine details, Please try again!');
define('CUISINE_DELETED', 'Cuisine removed successfully');
define('CUISINE_NOT_DELETED', 'Failed to remove cuisine, Please try again!');
/* Cuisine Settings */

/* Facility Settings */
define('FACILITY_ADDED', 'Facility details are successfully added');
define('FACILITY_NOT_ADDED', 'Failed to add facility details, Please try again!');
define('FACILITY_EXISTS', 'Email ID already exist!');
define('FACILITY_FACILITYNAME_EXISTS', 'Facility Name already exist!');
define('FACILITY_EDITED', 'Facility details are successfully updated');
define('FACILITY_NOT_EDITED', 'Failed to update facility details, Please try again!');
define('FACILITY_DELETED', 'Facility removed successfully');
define('FACILITY_NOT_DELETED', 'Failed to remove facility, Please try again!');
/* Facility Settings */

/* Music Settings */
define('MUSIC_ADDED', 'Music details are successfully added');
define('MUSIC_NOT_ADDED', 'Failed to add music details, Please try again!');
define('MUSIC_EXISTS', 'Email ID already exist!');
define('MUSIC_MUSICNAME_EXISTS', 'Music Name already exist!');
define('MUSIC_EDITED', 'Music details are successfully updated');
define('MUSIC_NOT_EDITED', 'Failed to update music details, Please try again!');
define('MUSIC_DELETED', 'Music removed successfully');
define('MUSIC_NOT_DELETED', 'Failed to remove music, Please try again!');
/* Music Settings */

/* Deals Settings */
define('DEALS_ADDED', 'Deals details are successfully added');
define('DEALS_NOT_ADDED', 'Failed to add deals details, Please try again!');
define('DEALS_EDITED', 'Deals details are successfully updated');
define('DEALS_NOT_EDITED', 'Failed to update deals details, Please try again!');
define('DEALS_DELETED', 'Deals removed successfully');
define('DEAL_IMAGE_PATH', DOMAIN_URL . "/images/deal/");
define('DEALS_NOT_DELETED', 'Failed to remove deals, Please try again!');
/* Deals Settings */

/* Deals Settings */
define('REWARD_ADDED', 'Reward details are successfully added');
define('REWARD_NOT_ADDED', 'Failed to add reward details, Please try again!');
define('REWARD_EDITED', 'Reward details are successfully updated');
define('REWARD_NOT_EDITED', 'Failed to update deals reward, Please try again!');
define('REWARD_DELETED', 'Reward removed successfully');
define('REWARD_NOT_DELETED', 'Failed to remove reward, Please try again!');
/* Deals Settings */

/* Deals Settings */
define('QRCODE_ADDED', 'QRCode details are successfully added');
define('QRCODE_NOT_ADDED', 'Failed to add QRCode details, Please try again!');
define('QRCODE_EDITED', 'QRCode details are successfully updated');
define('QRCODE_NOT_EDITED', 'Failed to update deals QRCode, Please try again!');
define('QRCODE_DELETED', 'QRCode removed successfully');
define('QRCODE_NOT_DELETED', 'Failed to remove QRCode, Please try again!');
/* Deals Settings */

/* Page Content */
define('PAGECONTENT_ADDED', 'Sub Category successfully added');
define('PAGECONTENT_NOT_ADDED', 'Failed to add sub category , Please try again!');
define('PAGECONTENT_EXISTS', 'Sub category already exist!');
define('PAGECONTENT_NAME_EXISTS', 'Sub category Name already exist!');
define('PAGECONTENT_EDITED', 'Page details are successfully updated');
define('PAGECONTENT_NOT_EDITED', 'Failed to update sub page details, Please try again!');
define('PAGECONTENT_DELETED', 'Sub Category removed successfully');
define('PAGECONTENT_NOT_DELETED', 'Failed to remove sub category, Please try again!');
/* Page Content */

/* Deals Settings */
define('LOCATION_ADDED', 'Location details are successfully added');
define('LOCATION_NOT_ADDED', 'Failed to add location details, Please try again!');
define('LOCATION_EDITED', 'Location details are successfully updated');
define('LOCATION_NOT_EDITED', 'Failed to update location details, Please try again!');
define('LOCATION_DELETED', 'Location removed successfully');
define('LOCATION_NOT_DELETED', 'Failed to remove location, Please try again!');
/* Deals Settings */

/* End of file constants.php */
/* Location: ./system/application/config/constants.php */