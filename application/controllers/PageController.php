<?php
defined('BASEPATH') or exit('No direct script access allowed');

class PageController extends CI_Controller
{

    public function __construct()
    {

        parent::__construct();

        $this->load->model("Dashboards");
        $this->load->model("Users");

        $this->load->library('mailer');

        $this->load->library('LibGlobal');
        $this->load->library('LibBiorxivDB');
        $this->load->library('LibPubmedDB');
        $this->load->library('LibClinicalDB');

        $this->load->library('OktaApi');

    }

    public function index()
    {
        $data = array();
        $data['dashboards'] = $this->Dashboards->load();

        $this->load->view('admin_manager', $data);
        
    }

    public function dashboard($slug)
    {
        if(!isset($_SESSION['user']) || !isset($_SESSION['slug'])){
            redirect('/login');
            return;
        }

        if($_SESSION['slug'] != $slug) {
            redirect('/login');
            return;
        }

        $dashboard = $this->Dashboards->getBySlug($slug);
        
        if(!$dashboard) {
            echo "no found dashboard";
            die();
        }
        
        $data = array(
            'reports' => json_decode($dashboard[0]['reports'], true),
            'dashboard' => $dashboard[0]
        );

        $this->load->view('main_dashboard', $data);
    }

    public function login(){
		if($this->input->post('submit')){
		    
		    // for google recaptcha
    		$this->form_validation->set_rules('email', 'Email', 'trim|required');
			$this->form_validation->set_rules('password', 'Password', 'trim|required');

			if ($this->form_validation->run() == FALSE) {
				$this->load->view('login');
			}
			else {

                $email = $this->input->post('email');
                $password = $this->input->post('password');

				$users = $this->Users->searchUser($this->input->post('email'), $this->input->post('password'));
				if(count($users)){
                    				
                    $dashboard = $this->Dashboards->getByID($users[0]['dashboard_id']);

                    // if(!$dashboard) {
                    //     echo "no found dashboard";
                    //     die();
                    // }
                    
                    // $data = array(
                    //     'reports' => json_decode($dashboard[0]['reports'], true),
                    //     'dashboard' => $dashboard[0]
                    // );
            
                    // $this->load->view('main_dashboard', $data);

                    // $_SESSION['user'] = $users[0];
                    // $_SESSION['slug'] = $dashboard[0]['slug'];
                    // redirect(base_url('/'.$dashboard[0]['slug']));

                    // return;


                    $okta_resp = $this->oktaapi->do_mfa($email);
                    
                    $data = array(
                        'email' => $email,
                        'password' => $password
                    );

                    if($okta_resp['success']){
                        $data['success'] = 'We have sent verification code to your email';
                        $data['okta_user_id'] = $okta_resp['user_id'];
                        $data['okta_factor_id'] = $okta_resp['factor_id'];
                    }
                    else {
                        $this->session->set_flashdata('warning', 'We are not able to send verification code to your email');
                        redirect(base_url('/login'));
                    }

                    $this->session->set_flashdata('data', $data);
                    // $this->load->view('verification', $data);
                    redirect(base_url('/verification'));
                    return;
				
				}
				else{
					$data['msg'] = 'Invalid Email or Password!';
					$this->load->view('login', $data);
				}
			}
		}
		else{
			unset($_SESSION['user']);
            unset($_SESSION['slug']);

			$this->load->view('login');
		}
	}

    public function verification(){

        if($this->input->post('submit')){
		    
		    // for google recaptcha
    		$this->form_validation->set_rules('code', 'code', 'trim|required');			

			if ($this->form_validation->run() == FALSE) {
				$this->load->view('login');
			}
			else {

                $code = $this->input->post('code');
                $email = $this->input->post('email');
                $password = $this->input->post('password');
                $okta_user_id = $this->input->post('okta_user_id');
                $okta_factor_id = $this->input->post('okta_factor_id');

                if($this->oktaapi->verify_email_otp($okta_user_id, $okta_factor_id, $code)) {
                    $users = $this->Users->searchUser($email, $password);
                    if(count($users)){
                        $dashboard = $this->Dashboards->getByID($users[0]['dashboard_id']);
                        $_SESSION['user'] = $users[0];
                        $_SESSION['slug'] = $dashboard[0]['slug'];
                        redirect(base_url('/'.$dashboard[0]['slug']));
                    }
                }
                else {
                    $data = array(
                        'email' => $email,
                        'password' => $password,
                        'msg' => 'Code Incorrect!',
                        'okta_user_id' => $okta_user_id,
                        'okta_factor_id' => $okta_factor_id,
                    );
					$this->load->view('verification', $data);
                }
			}
		}
		else{
            if($this->session->flashdata('data')) {
                $data = $this->session->flashdata('data');
                $this->load->view('verification', $data);
            }
            else {
                redirect(base_url('/'));
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
