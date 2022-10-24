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
