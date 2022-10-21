<?php
include(APPPATH . 'libraries/simple_html_dom.php');
if (!function_exists('other_dbs')) {
	function other_dbs()
	{
		return array(
			'biorxiv' => array(
				'user_table' => 'users'
			), 
			'clinical' => array(
				'user_table' => 'users'
			), 
			'insights' => array(
				'user_table' => 'ci_users'
			),
			'pubmed' => array(
				'user_table' => 'users'
			),
			// 'talent' => array(
			// 	'user_table' => 'users'
			// )
		);
	}
}

if (!function_exists('password_verify')) {
	function password_verify($password, $hash)
	{
		if (strlen($hash) !== 60 OR strlen($password = crypt($password, $hash)) !== 60)
		{
			return FALSE;
		}

		$compare = 0;
		for ($i = 0; $i < 60; $i++)
		{
			$compare |= (ord($password[$i]) ^ ord($hash[$i]));
		}

		return ($compare === 0);
	}
}

if (!function_exists('writeLog')) {
	function writeLog($log_text)
	{
		$fp = fopen('log.txt', 'a');
		fwrite($fp, date("Y/m/d h:i:sa") . "      " . $log_text . "\n");
		fclose($fp);
	}
}

if (!function_exists('isUserExist')) {
	function isUserExist($email, $password)
	{
		$CI =& get_instance();

		$user_data = [];
		$found_anyuser = false;
		$other_dbs = other_dbs();

		foreach($other_dbs as $db_name => $db_data) {
			$bfound = false;
			$other_db = $CI->load->database($db_name, TRUE); // the TRUE paramater tells CI that you'd like to return the database object.
			if($db_name == 'insights') {
				$result = $other_db->query('SELECT * FROM '.$db_data['user_table'].' WHERE email="'.$email.'"')->result_array();
				if(count($result) > 0) {
					if(password_verify($password, $result[0]['password'])) {
						$user_data[$db_name] = $result[0];
						$bfound = true;
						$found_anyuser = true;
					}
				}
			}
			else {
				$result = $other_db->query('SELECT * FROM '.$db_data['user_table'].' WHERE email="'.$email.'" AND password=PASSWORD("'.$password.'")')->result_array();
				if(count($result) > 0) {
					$user_data[$db_name] = $result[0];
					$bfound = true;
					$found_anyuser = true;
				}
			}
			$other_db->close();

			if(!$bfound) {
				$user_data[$db_name] = null;
			}
		}

		if($found_anyuser) {
			return $user_data;
		}
		
		return null;
	}
}

if (!function_exists('resetPassword')) {
	function resetPassword($email, $password)
	{
		$CI =& get_instance();

		$other_dbs = other_dbs();

		foreach($other_dbs as $db_name => $db_data) {
			
			$other_db = $CI->load->database($db_name, TRUE); // the TRUE paramater tells CI that you'd like to return the database object.
			if($db_name == 'insights') {

				$other_db->query('UPDATE FROM '.$db_data['user_table'].' SET password="'.password_hash($password, PASSWORD_BCRYPT).'" WHERE email="'.$email.'"');
			}
			else {
				$other_db->query('UPDATE FROM '.$db_data['user_table'].' SET password=PASSWORD("'.$password.'") WHERE email="'.$email.'"');
			}
			$other_db->close();

		}

		return;
	}
}

if (!function_exists('checkUserEmail')) {
	function checkUserEmail($email)
	{
		$CI =& get_instance();

		$other_dbs = other_dbs();

		foreach($other_dbs as $db_name => $db_data) {
			$other_db = $CI->load->database($db_name, TRUE); // the TRUE paramater tells CI that you'd like to return the database object.
			$result = $other_db->query('SELECT * FROM '.$db_data['user_table'].' WHERE email="'.$email.'"')->result_array();
			if(count($result) > 0) {
				if(isset($result[0]['first_name']) && isset($result[0]['last_name'])) {
					return array(
						'first_name' => $result[0]['first_name'],
						'last_name' => $result[0]['last_name'],
					);
				}
			}
			$other_db->close();
		}

		return null;
	}
}

if (!function_exists('isUserLogin')) {
	function isUserLogin($browser, $ip)
	{
		$ret_data = array(
			'login' => false
		);

		$CI =& get_instance();

		$local_db = $CI->load->database('default', TRUE); // the TRUE paramater tells CI that you'd like to return the database object.
		// $result = $local_db->query('SELECT * FROM login_users WHERE browser="'.$browser.'" AND ip="'.$ip.'" AND ABS(TIMESTAMPDIFF(MINUTE, login_at, NOW())) <= 5')->result_array();
		$result = $local_db->query('SELECT * FROM login_users WHERE browser="'.$browser.'" AND ip="'.$ip.'"')->result_array();
		if(count($result) > 0) {

			$result_timeout = $local_db->query('SELECT * FROM login_users WHERE browser="'.$browser.'" AND ip="'.$ip.'" AND ABS(TIMESTAMPDIFF(MINUTE, login_at, NOW())) <= 5')->result_array();

			if(count($result_timeout) > 0) {
				$ret_data['login'] = true;
				$ret_data['data'] = $result[0];
				$local_db->query('UPDATE login_users SET login_at=NOW() WHERE id="'.$result[0]['id'].'"');
			}
			else {
				$local_db->query('DELETE FROM login_users WHERE id="'.$result[0]['id'].'"');
			}
		}

		$local_db->close();

		return $ret_data;
	}
}

if ( ! function_exists('generateRandomString')){
    
  	function generateRandomString($length = 10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, strlen($characters) - 1)];
		}
		return $randomString;
	}
}