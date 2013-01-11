<?php
if (function_exists('set_magic_quotes_runtime'))
@set_magic_quotes_runtime(0);
ini_set('max_execution_time', '120');
error_reporting(0);

define('CWD1', (($getcwd = getcwd()) ? $getcwd : '.'));


require('config/config.php');
$mobiquo_config = get_mobiquo_config();
$current_plugin_version = $mobiquo_config['version'];
print_screen($current_plugin_version);
exit;



function print_screen($current_plugin_version)
{
	$latest_tp_plugin_version = 'sm-2a_'.get_latest_plugin_version();
	$mobiquo_path = get_path();
	$check_upload_status = file_get_contents("http://".$mobiquo_path."/upload.php?checkAccess");
	$check_push_status = file_get_contents("http://".$mobiquo_path."/push.php?checkAccess");
	require_once(dirname(dirname(__FILE__)) . '/SSI.php');
    global $modSettings;
    
	echo "Forum XMLRPC Interface for Tapatalk Application<br><br>";
	echo "Forum system version:".$modSettings['smfVersion']."<br>";
	echo "Current Tapatalk plugin version: ".$current_plugin_version."<br>";

	echo "Latest Tapatalk plugin version: <a href=\"http://tapatalk.com/activate_tapatalk.php?plugin=vbulletin\" target=\"_blank\">".$latest_tp_plugin_version."</a><br>";

	echo "Attachment upload interface status: <a href=\"http://".$mobiquo_path."/upload.php\" target=\"_blank\">".($check_upload_status ? 'OK' : 'Inaccessible')."</a><br>";

	echo "Push notification interface status: <a href=\"http://".$mobiquo_path."/push.php\" target=\"_blank\">".($check_push_status ? 'OK' : 'Inaccessible')."</a><br>";

	echo "<br>
<a href=\"http://tapatalk.com/api/api.php\" target=\"_blank\">Tapatalk API for Universal Forum Access</a> | <a href=\"http://tapatalk.com/mobile.php\" target=\"_blank\">Tapatalk Mobile Applications</a><br>
For more details, please visit <a href=\"http://tapatalk.com\" target=\"_blank\">http://tapatalk.com</a>";
}



function get_latest_plugin_version()
{
	$tp_lst_pgurl = 'http://tapatalk.com/v.php?sys=sm-2a&link';

	$response = 'CURL is disabled and PHP option "allow_url_fopen" is OFF. You can enable CURL or turn on "allow_url_fopen" in php.ini to fix this problem.';
	if (function_exists('curl_init'))
	{
		$ch = curl_init($tp_lst_pgurl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch,CURLOPT_TIMEOUT,10);

		$response = curl_exec($ch);
		curl_close($ch);
	}
	elseif (ini_get('allow_url_fopen'))
	{
		$params = array('http' => array(
                'method' => 'POST',
                'content' => http_build_query($data, '', '&'),
		));

		$ctx = stream_context_create($params);
		$timeout = 10;
		$old = ini_set('default_socket_timeout', $timeout);
		$fp = @fopen($tp_lst_pgurl, 'rb', false, $ctx);

		if (!$fp) return false;

		ini_set('default_socket_timeout', $old);
		stream_set_timeout($fp, $timeout);
		stream_set_blocking($fp, 0);


		$response = @stream_get_contents($fp);
	}
	return $response;
}


function get_path()
{
	$path =  '../';

	if (!empty($_SERVER['SCRIPT_NAME']) && !empty($_SERVER['HTTP_HOST']))
	{
		$path = $_SERVER['HTTP_HOST'];
		$path .= dirname($_SERVER['SCRIPT_NAME']);
	}
	return $path;
}