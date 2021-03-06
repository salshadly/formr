<?php
define('DEBUG', ONLINE ? 1 : 1);

if(DEBUG > -1)
	ini_set('display_errors',1);
ini_set("log_errors",1);
ini_set("error_log", INCLUDE_ROOT . "tmp/logs/errors.log");
error_reporting(-1);

date_default_timezone_set('Europe/Berlin');
mb_internal_encoding("UTF-8");

require_once INCLUDE_ROOT . "config/settings.php";
require_once INCLUDE_ROOT . "Model/DB.php";
$fdb = new DB();

require_once INCLUDE_ROOT."Model/User.php";

class Site
{
	public $alerts = array();
	public $last_outside_referrer;
	public function refresh()
	{
		$this->lastOutsideReferrer();
	}
	public function renderAlerts()
	{
		$now_handled = $this->alerts;
		$this->alerts = array();
		return implode($now_handled);
	}
	public function alert($msg, $class = 'alert-warning', $dismissable = true)
	{
		$this->alerts[] = "<div class='alert $class'>".'<button type="button" class="close" data-dismiss="alert">&times;</button>'."$msg</div>";
	}
	public function lastOutsideReferrer()
	{
		$ref = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
		if(mb_strpos($ref, WEBROOT) !== 0)
		{
			$this->last_outside_referrer = $ref;
		}
	}
	public function makeAdminMailer()
	{
		global $settings;
		$mail = new PHPMailer();
		$mail->SetLanguage("de","/");
	
		$mail->IsSMTP();  // telling the class to use SMTP
		$mail->Mailer = "smtp";
		$mail->Host = $settings['email']['host'];
		$mail->Port = $settings['email']['port'];
		if($settings['email']['tls'])
			$mail->SMTPSecure = 'tls';
		else
			$mail->SMTPSecure = 'ssl';
		$mail->SMTPAuth = true; // turn on SMTP authentication
		$mail->Username = $settings['email']['username']; // SMTP username
		$mail->Password = $settings['email']['password']; // SMTP password
	
		$mail->From = $settings['email']['from'];
		$mail->FromName = $settings['email']['from_name'];
		$mail->AddReplyTo($settings['email']['from'],$settings['email']['from_name']);
		$mail->CharSet = "utf-8";
		$mail->WordWrap = 65;                                 // set word wrap to 50 characters

		return $mail;
	}
}

session_start();

if(isset($_SESSION['site']) AND is_object($_SESSION['site']))
	$site = $_SESSION['site'];
else
	$site = new Site();

$site->refresh();

if(isset($_SESSION['user']))
{
	$sess_user = unserialize($_SESSION['user']);
	
	if(isset($sess_user->id))
		$user = new User($fdb, $sess_user->id, $sess_user->user_code);
	elseif(isset($sess_user->user_code))
		$user = new User($fdb, null, $sess_user->user_code);
}
if(!isset($user))
	$user = new User($fdb, null, null);

/*
HELPER FUNCTIONS
*/
function alert($msg, $class = 'alert-warning', $dismissable = true) // shorthand
{
	global $site;
	$site->alert($msg,$class, $dismissable);
}

function redirect_to($location) {
	global $site,$user;
	$_SESSION['site'] = $site;
	$_SESSION['user'] = serialize($user);

	if(mb_substr($location,0,4)!= 'http'){
		$base = WEBROOT;
		if(mb_substr($location,0,1)=='/')
			$location = $base . mb_substr($location,1);
		else $location = $base . $location;
	}
#	try
#	{
	    header("Location: $location");
		exit;
#	}
#	catch (Exception $e)
#	{ // legacy of not doing things properly, ie needing redirects after headers were sent. 
#		echo "<script type=\"text/javascript\">document.location.href = \"$location\";</script>";
#	}
}
function h($text) {
	return htmlspecialchars($text);
}

function debug($string) {
    if( DEBUG ) {
		echo "<pre>";
        var_dump($string);
		echo "</pre>";
    }
}
function pr($string) {
    if( DEBUG > -1 ) {
		echo "<pre>";
        var_dump($string);
#		print_r(	debug_backtrace());
		echo "</pre>";
    }
}

if (!function_exists('_')) {
	function _($text) {
		return $text;
	}
}


if (!function_exists('__')) {

/**
taken from cakePHP
 */
	function __($singular, $args = null) {
		if (!$singular) {
			return;
		}

		$translated = _($singular);
		if ($args === null) {
			return $translated;
		} elseif (!is_array($args)) {
			$args = array_slice(func_get_args(), 1);
		}
		return vsprintf($translated, $args);
	}
}

if (!function_exists('__n')) {

/**
taken from cakePHP
 */
	function __n($singular, $plural, $count, $args = null) {
		if (!$singular) {
			return;
		}

		$translated = ngettext($singular, $plural, null, 6, $count);
		if ($args === null) {
			return $translated;
		} elseif (!is_array($args)) {
			$args = array_slice(func_get_args(), 3);
		}
		return vsprintf($translated, $args);
	}

}

function endsWith($haystack, $needle)
{
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }

    return (mb_substr($haystack, -$length) === $needle);
}

/**
 * Gets an environment variable from available sources, and provides emulation
 * for unsupported or inconsistent environment variables (i.e. DOCUMENT_ROOT on
 * IIS, or SCRIPT_NAME in CGI mode).  Also exposes some additional custom
 * environment information.
 *
 * @param  string $key Environment variable name.
 * @return string Environment variable setting.
 * @link http://book.cakephp.org/2.0/en/core-libraries/global-constants-and-functions.html#env
 */
function env($key) {
	if ($key === 'HTTPS') {
		if (isset($_SERVER['HTTPS'])) {
			return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
		}
		return (mb_strpos(env('SCRIPT_URI'), 'https://') === 0);
	}

	if ($key === 'SCRIPT_NAME') {
		if (env('CGI_MODE') && isset($_ENV['SCRIPT_URL'])) {
			$key = 'SCRIPT_URL';
		}
	}

	$val = null;
	if (isset($_SERVER[$key])) {
		$val = $_SERVER[$key];
	} elseif (isset($_ENV[$key])) {
		$val = $_ENV[$key];
	} elseif (getenv($key) !== false) {
		$val = getenv($key);
	}

	if ($key === 'REMOTE_ADDR' && $val === env('SERVER_ADDR')) {
		$addr = env('HTTP_PC_REMOTE_ADDR');
		if ($addr !== null) {
			$val = $addr;
		}
	}

	if ($val !== null) {
		return $val;
	}

	switch ($key) {
		case 'SCRIPT_FILENAME':
			if (defined('SERVER_IIS') && SERVER_IIS === true) {
				return str_replace('\\\\', '\\', env('PATH_TRANSLATED'));
			}
			break;
		case 'DOCUMENT_ROOT':
			$name = env('SCRIPT_NAME');
			$filename = env('SCRIPT_FILENAME');
			$offset = 0;
			if (!mb_strpos($name, '.php')) {
				$offset = 4;
			}
			return mb_substr($filename, 0, -(strlen($name) + $offset));
			break;
		case 'PHP_SELF':
			return str_replace(env('DOCUMENT_ROOT'), '', env('SCRIPT_FILENAME'));
			break;
		case 'CGI_MODE':
			return (PHP_SAPI === 'cgi');
			break;
		case 'HTTP_BASE':
			$host = env('HTTP_HOST');
			$parts = explode('.', $host);
			$count = count($parts);

			if ($count === 1) {
				return '.' . $host;
			} elseif ($count === 2) {
				return '.' . $host;
			} elseif ($count === 3) {
				$gTLD = array(
					'aero',
					'asia',
					'biz',
					'cat',
					'com',
					'coop',
					'edu',
					'gov',
					'info',
					'int',
					'jobs',
					'mil',
					'mobi',
					'museum',
					'name',
					'net',
					'org',
					'pro',
					'tel',
					'travel',
					'xxx'
				);
				if (in_array($parts[1], $gTLD)) {
					return '.' . $host;
				}
			}
			array_shift($parts);
			return '.' . implode('.', $parts);
			break;
	}
	return null;
}
function join_builder($fdb,$q)
{
	$result_tables = $fdb->query("SELECT name FROM `survey_studies`");
	$results = array();
	while($res = $result_tables->fetch(PDO::FETCH_ASSOC))
		$results[] = $res['name'];
	
	$results[] = 'users';
	$results[] = 'survey_users';
	$results[] = 'survey_email_log';

	$tables = array();
	foreach($results AS $result):
		if(preg_match("/($result\.|`$result`\.)/",$q))
			$tables[] = $result;
	endforeach;
	
	$join = '
left join `survey_unit_sessions`
	on `survey_run_sessions`.id = `survey_unit_sessions`.run_session_id
';
	foreach($tables AS $table):
		if(!in_array($table,array('users','survey_users'))):
$join .= "
left join `$table`
	on `$table`.session_id = `survey_unit_sessions`.id";
	
		elseif($table == 'users'):
$join .= "
left join `$table`
	on `$table`.code = `survey_run_sessions`.session";
		elseif($table == 'survey_users'):
$join .= "
left join `$table`
	on `$table`.id = `survey_run_sessions`.user_id";
		endif;
			
	endforeach;
	
	return $join;
}
function makeUnit($dbh, $session, $unit)
{
	$type = $unit['type'];
	if(!in_array($type, array('Survey', 'Study','Pause','Email','External','Page','SkipBackward','SkipForward','End')))
		die('The unit type is not allowed!');
	
	require_once INCLUDE_ROOT . "Model/$type.php";
	return new $type($dbh, $session, $unit);
}
function emptyNull(&$x){
	$x = ($x=='') ? null : $x;
}
function stringBool($x)
{
	if($x===false) return 'false';
	elseif($x===true) return 'true';
	elseif($x===null)  return 'null';
	elseif($x===0)  return '0';
	else return $x;
}
function hardTrueFalse($x)
{
	if($x===false) return 'FALSE';
	elseif($x===true) return 'TRUE';
#	elseif($x===null)  return 'NULL';
	elseif($x===0)  return '0';
	else return $x;
}





if (!function_exists('http_parse_headers'))
{
    function http_parse_headers($raw_headers)
    {
        $headers = array();
        $key = ''; // [+]

        foreach(explode("\n", $raw_headers) as $i => $h)
        {
            $h = explode(':', $h, 2);

            if (isset($h[1]))
            {
                if (!isset($headers[$h[0]]))
                    $headers[$h[0]] = trim($h[1]);
                elseif (is_array($headers[$h[0]]))
                {
                    // $tmp = array_merge($headers[$h[0]], array(trim($h[1]))); // [-]
                    // $headers[$h[0]] = $tmp; // [-]
                    $headers[$h[0]] = array_merge($headers[$h[0]], array(trim($h[1]))); // [+]
                }
                else
                {
                    // $tmp = array_merge(array($headers[$h[0]]), array(trim($h[1]))); // [-]
                    // $headers[$h[0]] = $tmp; // [-]
                    $headers[$h[0]] = array_merge(array($headers[$h[0]]), array(trim($h[1]))); // [+]
                }

                $key = $h[0]; // [+]
            }
            else // [+]
            { // [+]
                if (mb_substr($h[0], 0, 1) == "\t") // [+]
                    $headers[$key] .= "\r\n\t".trim($h[0]); // [+]
                elseif (!$key) // [+]
                    $headers[0] = trim($h[0]); // [+]
            } // [+]
        }

        return $headers;
    }
}