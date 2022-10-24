<?php
defined('BASEPATH') or exit('No direct script access allowed');

class PageController extends CI_Controller
{

    public function __construct()
    {

        parent::__construct();

        $this->load->model("Dashboards");
        $this->load->library('mailer');

        $this->load->library('LibGlobal');
        $this->load->library('LibBiorxivDB');
        $this->load->library('LibPubmedDB');
        $this->load->library('LibClinicalDB');

    }

    public function index()
    {
        $data = array();
        $data['dashboards'] = $this->Dashboards->load();

        $this->load->view('admin_manager', $data);
        
    }

    public function dashboard($dashboard_name)
    {
        $dashboard = $this->Dashboards->getByName($dashboard_name);
        
        if(!$dashboard) {
            echo "no found dashboard";
            die();
        }
        
        $data = array(
            'reports' => json_decode($dashboard[0]['reports'], true)
        );

        $this->load->view('main_dashboard', $data);
    }

	public function resetPassword($pwd_reset_code){
		if($this->input->post('submit')){
			$this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[8]');
			$this->form_validation->set_rules('confirm_password', 'Password Confirmation', 'trim|required|matches[password]');

			if ($this->form_validation->run() == FALSE) {
				$result = false;
				$data['reset_code'] = $pwd_reset_code;
                $data['email'] = $this->input->post('email');
				$this->load->view('reset-password',$data);
			}   
			else{
                $local_db = $this->load->database('default', TRUE); 
                $result = $local_db->query('DELETE FROM rscodes WHERE code="'.$pwd_reset_code.'"');
                $local_db->close();

                resetPassword($this->input->post('email'), $this->input->post('password'));
				// $this->Users->reset_password($pwd_reset_code, $this->input->post('password'));
				$this->session->set_flashdata('success','New password has been Updated successfully.Please login below');
				redirect(base_url('/login'));
			}
		}
		else{
            $local_db = $this->load->database('default', TRUE); 
            $result = $local_db->query('SELECT * FROM rscodes WHERE code="'.$pwd_reset_code.'"')->result_array();
            $local_db->close();

			if(count($result) > 0){
				$data['reset_code'] = $pwd_reset_code;
                $data['email'] = $result[0]['email'];
				$this->load->view('reset-password',$data);
			}
			else{
				$this->session->set_flashdata('error','Password Reset Code is either invalid or expired.');
				redirect(base_url('/forgot-password'));
			}
		}
	}

    public function test(){
        $local_db = $this->load->database('talent', TRUE); 
        // $result = $local_db->query('SELECT * FROM reports')->result_array();
        // $data['reports']['biorxiv'] = $result;
        $local_db->close();
        echo "Test:";die();
        $email = 'evan@orangelinelab.com';
        $email = 'peter@careequity.com';
        $user =  $this->oktaapi->find_user($email);
        if(!$user) {
            echo "no find user";
            die();
        }

        echo $user['id'];die();
        // $user_id = $user['id'];

        $user_id = '00u2oe2e039CUdnTo697';
        // $factor = $this->oktaapi->get_factor($user_id);
        // if(!$factor) {
        //     echo "no find factor";
        //     die();
        // }
        // $factor_id = $factor['id'];

        $factor_id = 'emf2oe2e05ECqHYak697';
        // if($this->oktaapi->send_email($user_id, $factor_id)) {
        //     echo "sent email";
        // }
        // else {
        //     echo "fail email";
        // }

        $pw = '547201';
        if($this->oktaapi->verify_email_otp($user_id, $factor_id, $pw)) {
            echo "ok";
        }
        else {
            echo "wrong";
        }
    }
}
