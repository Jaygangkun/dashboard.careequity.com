<?php
defined('BASEPATH') or exit('No direct script access allowed');

class AdminAPIController extends CI_Controller
{

    public function __construct()
    {

        parent::__construct();

        $this->load->library('LibGlobal');
        $this->load->library('LibClinicalDB');

    }

    public function GetGroups()
    {
        $groups = get_url_resp($this->config->item("talent_lib_server").'PublicApi/GetGroups.ashx');

        echo json_encode($groups);
    }

    public function GetGroupProfiles()
    {
        $groups = post_url_resp($this->config->item("talent_lib_server").'PublicApi/GetGroupProfiles.ashx', array(
            'profile_ids' => $_POST['profile_ids']
        ));
        
        echo json_encode($groups);
    }

    public function GetProfileDiff($ProfileId)
    {
        $profiles = get_url_resp($this->config->item("talent_lib_server").'PublicApi/GetProfileDiff.ashx?ProfileId='.$ProfileId);
        
        echo json_encode($profiles);
    }

    public function GetCompanyProfiles($CompanyId, $GroupId)
    {
        $profiles = get_url_resp($this->config->item("talent_lib_server").'PublicApi/GetCompanyProfiles.ashx?CompanyId='.$CompanyId.'&GroupId='.$GroupId);
        
        echo json_encode($profiles);
    }
}
