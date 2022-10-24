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
