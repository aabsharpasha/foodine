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

define('ACCOUNT_STATUS_ACTIVE', 'Active');
define('ACCOUNT_STATUS_INACTIVE', 'Inactive');

/* End of file constants.php */
/* Location: ./application/config/constants.php */

define('WEB_USER_ACTIVATION', 'http://localhost:8000/auth/activate/');
define('WEB_USER_MERGE', 'http://localhost:8000/auth/merge/');
define('WEB_USER_FORGOT_PASSWORD', 'http://localhost:8000/password/verify/');
define('BASEURL', 'http://' . $_SERVER['HTTP_HOST'] . '/application/');
define('BASEDIR', $_SERVER['DOCUMENT_ROOT'] . '/application/');
define('PROJECT_TITLE', 'Blacklist');
define('ASSET', BASEURL . 'assets/');
define('IMG_URL', BASEURL . 'images/');
define('USER_IMG', IMG_URL . 'user/');
define('MENU_IMG', IMG_URL . 'restaurantMenu/');
define('SIGN_URL', IMG_URL . 'signature/');
define('CSS_URL', ASSET . 'css/');
define('JS_URL', ASSET . 'js/');
define('ENCRYPTION_KEY', 'cps.openxcell.key');

define('DIR_VIEW', BASEDIR . 'application/ws/views/');
define('DIR_LIB', BASEDIR . 'application/ws/libraries/');
define('DIR_PDF', BASEDIR . 'assets/pdf/');

define('UPLOADS', BASEDIR . "images/");


define('ACCOUNT_DEACTIVE', 'Your Account Is Inactive.');
define('LOGIN_INVALID', 'You Have Entered a Wrong User Name/Password.');
define('LOG_SUCCESS', 'You are logged in successfully.');
define('LOGOUT_SUCCESS', 'You are logged out Successfully.');
define('INSUFF_DATA', 'Insufficient Data.');
define('LOGOUT_ERROR', 'Something Wrong with logout.!!');

define('FAIL_STATUS', 101);
define('SUCCESS_STATUS', 200);

/* EMAIL CONSTANTS */
define('EMAIL_SUBJECT', 'HungerMafia');
define('FROM_EMAIL_ID', 'amit.laravel@gmail.com');
define('FROM_EMAIL_NAME', 'Amit HM');
define('MAIL_PROTOCOL', 'smtp');
define('MAIL_HOST', 'ssl://smtp.googlemail.com');
define('MAIL_USERNAME', 'amit.laravel@gmail.com');
define('MAIL_PASSWORD', 'lara123456');
define('MAIL_PORT', '465');
define('MAIL_TIMEOUT', '5');
define('MAIL_MAIL_TYPE', 'html');
define('MAIL_CHARSET', 'utf-8');
define('MAIL_VALIDATE_EMAIL_ID', FALSE);
define('MAIL_EMAIL_PRIORITY', 3);
define('MAIL_BCC_BATCH_MODE', FALSE);
define('MAIL_BCC_BATCH_SIZE', 200);


define('MYSQL_DATE_FORMAT', '%b %d, %Y');
define('MYSQL_DATE_FORMAT2', '%l:%i %p %M %e, %Y');

//define('CONTRACT_DATA_SUCCESS', 'Contract Record found successfully.');

define('ACCOUNT_ACTIVATED', 'Your account has been activated successfully.');
define('ACCOUNT_ACTIVATION_ERROR', 'Something went wrong while activating your account.');
define('ACCOUNT_SUCCESS', 'Your account has been created successfully.');
define('ACCOUNT_SUCCESS_WEB', 'Your account has been created successfully. Please check email for verification.');
define('ACCOUNT_UPDATED', 'Your account has been updated successfully.');
define('ACCOUNT_ERROR', 'Something went wrong while creating your account.');
define('ACCOUNT_EXISTS', 'This account already exists.');
define('ACCOUNT_NOT_EXISTS', 'This account does not exist.');
define('ACCOUNT_EMAIL_ERROR', 'The email address is already exists.');

define('USERCHOICE_RECORD_FOUND', 'User choice recorded successfully.');
//define('FORGOTPASS_RESET_SUCCESS', 'Your password has been changed successfully and has been sent to your registered email address.');
define('FORGOTPASS_RESET_SUCCESS', 'A link has been sent to your registered email address to reset password.');
define('FORGOTPASS_RESET_ERROR', 'The email address entered does not exist. Please Sign in again.');
define('FORGOTPASS_LINK_ERROR', 'Invalid link! This link has expired.');

define('PASSWORD_CHANGE_SUCCESS', 'Your password has been changed successfully.');
define('PASSWORD_CHANGE_FAIL', 'Your password has not been changed.');
define('PASSWORD_INVALID', 'Please enter valid input.');
define('PASSWORD_WRONG', 'You have entered a wrong password.');
define('PASSWORD_NOT_CONFIRM', 'Passwords don�t match.');

define('RESTAURANT_FOUND', 'Restaurant Record found successfully.');
define('NO_RECORD_FOUND', 'No record found.');
define('RESTAURANT_OPEN_CLOSE_DAYS', serialize(array(
    1 => 'SUN',
    2 => 'MON',
    3 => 'TUE',
    4 => 'WED',
    5 => 'THU',
    6 => 'FRI',
    7 => 'SAT'
)));


define('RESTAURANT_LIKED_BEFORE', 'Restaurant liked.');
define('RESTAURANT_DISLIKED_BEFORE', 'Restaurant disliked.');
define('RESTAURANT_LIKED', 'You have liked this restaurant successfully.');
define('RESTAURANT_DISLIKED', 'You have disliked this restaurant successfully.');

define('RESTAURANT_FAV_SET', 'You have already marked this as your favourite restaurant.');
define('RESTAURANT_FAV', 'You have marked this as your favourite restaurant successfully.');

define('MERGE_VERIFY_EMAIL', 'Merge verification email sent successfuly.');
define('MERGE_SUCCESSFUL', 'Merged users successfuly.');
define('REWARD_FOUND', 'Reward found successfully.');
define('REWARD_RQST', 'You are requesting to redeem your points for this voucher.');
define('REWARD_RQST_FAIL', 'Reward request failed. Please try again.');
define('REWARD_RQST_OLD', 'You have already requested for this reward.');

define('CHECKIN_SUCC', 'You have successfully checked in.');
define('CHECKIN_FAIL', 'Your checkin has failed.');
define('CHECKIN_COVERAGE_FAIL', 'You\'re not under the restaurant area.');
define('TABLE_BOOK_ERROR', 'You have an error while booking the table.');
define('TABLE_BOOK_SUCC', 'You have successfully booked the table.');
define('TABLE_BOOK_WARN', 'This time slot is already booked.');
define('TABLE_BOOK_LIMIT', 'No more table can not be booked for this restaurant and selected date.');
define('TABLE_BOOK_CLOSED', 'Restaurant closed. Please choose another time slot.');
define('CHECKIN_ALREADY', 'You have already checked in at this restaurant.');
define('CHECKIN_REC_SUCC', 'Check in record found successfully.');

define('REVIEW_FOUND_SUCC', 'Review found successfully.');
define('CHOICES_FOUND', 'Choices found successfully.');

define('OFFER_FOUND_SUCC', 'Offer found successfully.');
define('OFFER_DEL_SUCC', 'Offer deleted successfully.');
define('OFFER_DEL_ERR', 'Error in offer delete operation.');
define('OFEER_ADD_SUCC', 'Offer added successfully.');
define('OFEER_ADD_ERR', 'Error in offer add operation.');

define('REQUEST_CHANGE_ERR', 'Please enter change request value.');
define('REQUEST_CHANGE_SUCC', 'Your reuqest has been sent to the requested admin.');

define('OFEER_ID_EXISTS', 'This offer code already exists');

define('RATEUS_ALREADY', 'You have already rate this application.');
define('RATEUS_SUCC', 'You have successfully rate this application.');

define('REWARD_REQUEST_NILL', 'Please enter valid input record.');
define('REWARD_REQUEST_ALREADY', 'You have been already requested for this reward.');
define('REWARD_REQUEST_SUCC', 'You have requested for reward successfully.');
define('REWARD_REQUEST_INSUFF', 'You don\'t have enough balance to redeem this reward.');

define('INVITE_ERR', 'You don\'t have enough value to invite the friends.');
define('INVITE_SUCC', 'You have been invited friends successfully.');


define('SPECIALTY_FOUND_SUCC', 'Specialty record found successfully.');
define('SPECIALTY_ADD_ERR', 'This specialty name is already exist.');
define('SPECIALTY_ADD_SUCC', 'Specialty record added successfully.');
define('SPECIALTY_DEL_SUCC', 'Specialty record deleted successfully.');

define('BOOK_TABLE_FOUND', 'Booked table record found successfully.');
define('BOOK_TABLE_CANCEL', 'You have successfully cancelled your reservation.');
define('BOOK_TABLE_CANCEL_ERR', 'You\'re not allow to cancel this reservation.');

define('BOOK_TABLE_CART_CANCEL', 'You have successfully deleted your booking cart.');
define('BOOK_TABLE_CART_CANCEL_ERR', 'You\'re not allow to delete this reservation.');

define('SLOTS_FOUND', 'Slots found successfully.');
define('SUBSCRIBED_BEFORE', 'You have already subscribed before.');
define('SUBSCRIBED_DONE', 'You have subscribed successfully.');

define('REVIEW_RECORD_SAVE', 'Your review has been posted');
define('BOOKMARK_RECORD_SAVE', 'Bookmark has been saved');
define('REVIEWED_FAVOURITE', 'Review information saved');
define('REVIEWED_COMMENT', 'Review comment information saved');
define('REMARKS_POSTED', 'Remarks has been saved');
define('ERROR_POSTED', 'Error details has been saved');

define('NOTIFY_CHANGE', 'Notification settings has been changed successfully.');
define('TABLE_REQUEST_FOUND', 'Table request record found successfully.');
define('TABLE_REQUEST_UPDT', 'You have updated table request successfully.');

define('NOTIFY_CLR_SUCC', 'Your notification has been cleared successfully.');
define('BADGE_CLR_SUCC', 'Badge count reset successfully.');
define('NOTIFY_FOUND', 'Notification record found successfully.');

define('IS_NOTIFICATION_LIVE', TRUE); 
define('TOPIC_ARN', 'arn:aws:sns:ap-southeast-1:224401661599:broadcast');
