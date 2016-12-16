<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

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
defined('FILE_READ_MODE')  OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  OR define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ')                           OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE')  OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS')        OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code

define('USERS_TABLE', 'users');
define('ROLE_ADMIN', 0);
define('ROLE_PM', 1);
define('ROLE_CLIENT', 2);
// define('TABLE_ID', '134867379');
// define('TABLE_ID', '134760343');
define('TABLE_ID', '73364923');
// define('TABLE_ID', '134711528');
define('KEY_FILE_LOCATION', '../application/third_party/Google/reports-8563c96846a4.json');
define('TODAY', date('Y-m-d'));

function NDaysAgo($n, $day) {
	$d = new DateTime($day);
	return $d->sub(DateInterval::createFromDateString($n . 'days'))->format('Y-m-d');
}

function NMonthsAgo($n, $day) {
	$d = new DateTime($day);
	return $d->sub(DateInterval::createFromDateString($n . 'months'))->format('Y-m-d');
}

function NMonthsAgoFirstDay($n, $day) {
	$today = new DateTime($day);
	$n_months_ago = $today->sub(DateInterval::createFromDateString($n . 'months'));
	return $n_months_ago->modify('first day of this month')->format('Y-m-d');
}

function NMonthsAgoLastDay($n, $day) {
	$today = new DateTime($day);
	$n_months_ago = $today->sub(DateInterval::createFromDateString($n . 'months'));
	return $n_months_ago->modify('last day of this month')->format('Y-m-d');
}

function PreviousYearDay($day) {
	$d = new DateTime($day);
	return $d->sub(DateInterval::createFromDateString('1years'))->format('Y-m-d');
}

function MonthFirstDay($day) {
	$d = new DateTime($day);
	return $d->modify('first day of this month')->format('Y-m-d');
}

function MonthLastDay($day) {
	$d = new DateTime($day);
	return $d->modify('last day of this month')->format('Y-m-d');
}

function PrevDay($day) {
	return NDaysAgo(1, $day);
}

function GetYearMonth($day) {
	$d = new DateTime($day);
	return $d->modify('last day of this month')->format('Y-m');
}

$GLOBALS['LABELS'] = array(
	'Sessions' 						=> 'Organic Sessions Pages/Session',
	'Landing_Pages'				=> 'Organic Landing Pages',
	'Sessions_Channel'		=> 'Sessions by channel',
	'Sessions_Device' 		=> 'Sessions by device',
	'GBM_Local_Insights'	=> 'GBM Local Insights',								/// not yet
	'Keyword_Ranking'			=> 'Keyword Ranking Changes',   				/// not yet
	'PPC_Campaign'				=> 'PPC Performance by Campaign',
	'PPC_CTR' 						=> 'PPC CTR',
	'PPC_CPC' 						=> 'PPC CPC',
	'PPC_CPA'							=> 'PPC CPA',														/// not yet
	'PPC_ROI'							=> 'PPC ROI',														/// not yet
	'PPC_Cost' 						=> 'PPC Cost',
	'PPC_Conversions' 		=> 'PPC Conversions',
	'PPC_CPA_Box'					=> 'PPC CPA(Box)',
	'PPC_Conversion_Rate' => 'PPC Conversion Rate',
	'PPC_Overview' 				=> 'PPC Performance Overview',					/// not yet
	'Phone_Calls' 				=> 'Phone Calls',												/// not yet
	'Goal_Completions'		=> 'Goal Completions',
	'Conversion_Value'		=> 'Conversion Value'
);