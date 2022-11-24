<?php
include(APPPATH . 'libraries/simple_html_dom.php');

if (!function_exists('get_url_resp')) {
	function get_url_resp($url)
	{
		set_time_limit(0);

		$curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_SSL_VERIFYPEER => false
        ));

        $curl_response = curl_exec($curl);

        curl_close($curl);
		
		return json_decode($curl_response, true);
	}
}

if (!function_exists('post_url_resp')) {
	function post_url_resp($url, $data)
	{
		set_time_limit(0);

		$curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            // CURLOPT_POSTFIELDS =>'{
            //     "profile_ids": ["28c1ddb0-fc72-4ec2-a715-010b0d0f6985", "e9bb3a77-358f-4c59-b31f-01121127594b", "4930d0a8-eb64-4475-9051-00701866df69", "6d18e205-9164-44b3-9bdd-01070a6bd869", "cfc95862-a5f6-4322-90c5-0033d6372983"]
            // }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
            CURLOPT_SSL_VERIFYPEER => false
        ));

        $curl_response = curl_exec($curl);

        curl_close($curl);
		
		return json_decode($curl_response, true);
	}
}

if (!function_exists('slugify')) {
	function slugify($text)
    {
        // Strip html tags
        $text=strip_tags($text);
        // Replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        // Transliterate
        setlocale(LC_ALL, 'en_US.utf8');
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        // Remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);
        // Trim
        $text = trim($text, '-');
        // Remove duplicate -
        $text = preg_replace('~-+~', '-', $text);
        // Lowercase
        $text = strtolower($text);
        // Check if it is empty
        if (empty($text)) { return 'n-a'; }
        // Return result
        return $text;
    }
}

if (!function_exists('checkLogin')) {
	function checkLogin()
	{
        
		unset($_SESSION['user']);
        unset($_SESSION['slug']);

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://tools.careequity.com/check-login',
            // CURLOPT_URL => 'http://172.16.1.45:9011/check-login',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => array('browser' => $_SERVER['HTTP_USER_AGENT'],'ip' => $_SERVER['REMOTE_ADDR']),
			CURLOPT_HTTPHEADER => array(
				'Cookie: ci_session=246a246c5808e567b059fb14d851c4a2a2668cfa'
			),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		
		$resp_data = json_decode($response, true);

		if(isset($resp_data['login']) && $resp_data['login']) {

			if(isset($resp_data['data']) && isset($resp_data['data']['user_data'])) {
				$_SESSION['user'] = array(
                    'role' => 'admin'
                );

                $_SESSION['slug'] = '';
			}
		}

		return;
	}
}