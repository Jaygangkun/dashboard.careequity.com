<?php

class OktaApi 
{

    private $api_token = '00YSMQXMViWA4OupMZ5qJGTudijXucNMXCAD_LA7Co';
    
    private $url = 'https://trial-2004462.okta.com';

	function __construct()
	{
		$this->CI =& get_instance();

	}

    function test(){
        
        return $this->api_token;
    }

    function do_mfa($email) {
        $resp = array(
            'success' => true
        );

        $user =  $this->find_user($email);
        if(!$user) {
            $user = $this->create_user($email);
        }

        $user_id = $user['id'];
        $factor = $this->get_factor($user_id);
        if(!$factor) {
            $resp = array(
                'success' => false,
                'message' => 'not find factor'
            );
            
            return $resp;
        }
        $factor_id = $factor['id'];

        if($this->send_email($user_id, $factor_id)) {
            
        }
        else {
            $resp = array(
                'success' => false,
                'message' => 'fail to send email'
            );
        }

        $resp['user_id'] = $user_id;
        $resp['factor_id'] = $factor_id;
        
        return $resp;
    }

    function create_user($email) {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->url.'/api/v1/users?activate=false',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'{
            "profile": {
              "firstName": "'.$email.'",
              "lastName": "'.$email.'",
              "email": "'.$email.'",
              "login": "'.$email.'"
            }
          }',
            CURLOPT_HTTPHEADER => array(
              'Accept: application/json',
              'Content-Type: application/json',
              'Authorization: SSWS 00YSMQXMViWA4OupMZ5qJGTudijXucNMXCAD_LA7Co',
              'Cookie: JSESSIONID=03D6703FAAF8AB3122B7B34F5ABBDA40'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $resp_data = json_decode($response, true);
        
        return $resp_data;
    }

    function find_user($email) {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->url.'/api/v1/users?q='.$email.'&limit=1',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Content-Type: application/json',
                'Authorization: SSWS '.$this->api_token,
                'Cookie: JSESSIONID=307565152CD61F77759D61295DD6CB2E'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $resp_data = json_decode($response, true);
        if(count($resp_data) == 0) {
            return null;
        }

        return $resp_data[0];
    }

    function get_factor($user_id) {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->url.'/api/v1/users/'.$user_id.'/factors',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Content-Type: application/json',
                'Authorization: SSWS '.$this->api_token,
                'Cookie: JSESSIONID=D8E2231203D3B6BB1FE4EB08BBEAF2BB'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        
        $resp_data = json_decode($response, true);

        if(isset($resp_data['errorCode'])){
            return null;
        }

        foreach($resp_data as $factor) {
            if($factor['factorType'] == 'email') {
                return $factor;
            }
        }

        return null;
    }

    function send_email($user_id, $factor_id) {
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => $this->url.'/api/v1/users/'.$user_id.'/factors/'.$factor_id.'/verify',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_HTTPHEADER => array(
            'Accept: application/json',
            'Content-Type: application/json',
            'Authorization: SSWS '.$this->api_token,
            'Cookie: JSESSIONID=2CD58924BF196A827EDA77F8A31C1528'
          ),
        ));
        
        $response = curl_exec($curl);
        
        curl_close($curl);

        $resp_data = json_decode($response, true);

        if(isset($resp_data['errorCode'])){
            return false;
        }

        if(isset($resp_data['factorResult']) && $resp_data['factorResult'] == 'CHALLENGE'){
            return true;
        }
        
        return false;
    }

    function verify_email_otp($user_id, $factor_id, $pass) {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->url.'/api/v1/users/'.$user_id.'/factors/'.$factor_id.'/verify',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'{
                "passCode": "'.$pass.'"
            }  ',
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Content-Type: application/json',
                'Authorization: SSWS '.$this->api_token,
                'Cookie: JSESSIONID=15EDD9ED979B5A7627E5CC52B28874B1'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        
        $resp_data = json_decode($response, true);

        if(isset($resp_data['errorCode'])){
            return false;
        }

        if(isset($resp_data['factorResult']) && $resp_data['factorResult'] == 'SUCCESS'){
            return true;
        }
        
        return false;
    }

}
?>