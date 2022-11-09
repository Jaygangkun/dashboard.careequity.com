<?php
defined('BASEPATH') or exit('No direct script access allowed');

class BackendController extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model("Dashboards");
        $this->load->model("Users");
        $this->load->library('mailer');
    }

    public function reports_load()
    {
        $reports = array(
            'linkedin' => '',
            'biorxiv' => '',
            'pubmed' => '',
            'clinical' => '',
        );

        $linkedin = [];
        $linkedin_profiles_resp = get_url_resp($this->config->item("talent_lib_server").'PublicApi/GetAllProfiles.ashx');

        if($linkedin_profiles_resp && isset($linkedin_profiles_resp['success']) && $linkedin_profiles_resp['success']) {
            $linkedin = json_decode($linkedin_profiles_resp['profiles'], true);
        }
        
        ob_start();
        foreach($linkedin as $report) {
            ?>
            <tr>
                <td>
                    <label class="checkbox-container">
                        <input type="checkbox" class="checkbox-linkedin" data-type="linkedin" data-id="<?php echo $report['Id']?>" id="linkedin_checkbox_<?php echo $report['Id']?>">
                        <span class="checkmark"></span>
                    </label>
                </td>
                <td><?php echo $report['Name']?></td>
            </tr>
            <?php
        }

        $reports['linkedin'] = ob_get_contents();
        ob_end_clean();

        $local_db = $this->load->database('biorxiv', TRUE); 
        $result = $local_db->query('SELECT * FROM reports')->result_array();
        $biorxiv = $result;
        ob_start();
        foreach($biorxiv as $report) {
            ?>
            <tr>
                <td>
                    <label class="checkbox-container">
                        <input type="checkbox" class="checkbox-biorxiv" data-type="biorxiv" data-id="<?php echo $report['id']?>" id="biorxiv_checkbox_<?php echo $report['id']?>">
                        <span class="checkmark"></span>
                    </label>
                </td>
                <td><?php echo $report['title']?></td>
            </tr>
            <?php
        }
        $local_db->close();
        $reports['biorxiv'] = ob_get_contents();
        ob_end_clean();

        $local_db = $this->load->database('pubmed', TRUE); 
        $result = $local_db->query('SELECT * FROM reports')->result_array();
        $pubmed = $result;
        ob_start();
        foreach($pubmed as $report) {
            ?>
            <tr>
                <td>
                    <label class="checkbox-container">
                        <input type="checkbox" class="checkbox-pubmed" data-type="pubmed" data-id="<?php echo $report['id']?>" id="pubmed_checkbox_<?php echo $report['id']?>">
                        <span class="checkmark"></span>
                    </label>
                </td>
                <td><?php echo $report['title']?></td>
            </tr>
            <?php
        }
        $local_db->close();
        $reports['pubmed'] = ob_get_contents();
        ob_end_clean();


        $local_db = $this->load->database('clinical', TRUE); 
        $result = $local_db->query('SELECT * FROM reports')->result_array();
        $clinical = $result;
        ob_start();
        foreach($clinical as $report) {
            ?>
            <tr>
                <td>
                    <label class="checkbox-container">
                        <input type="checkbox" class="checkbox-clinical" data-type="clinical" data-id="<?php echo $report['id']?>" id="clinical_checkbox_<?php echo $report['id']?>">
                        <span class="checkmark"></span>
                    </label>
                </td>
                <td><?php echo $report['title']?></td>
            </tr>
            <?php
        }
        $local_db->close();
        $reports['clinical'] = ob_get_contents();
        ob_end_clean();

        echo json_encode($reports);
    }

    public function dashboard_publish()
    {
        $dashboard_id = $this->Dashboards->add(array(
            'name' => isset($_POST['dashboard_name']) ? $_POST['dashboard_name'] : '',
            'reports' => isset($_POST['reports']) ? $_POST['reports'] : '',
        ));

        if(isset($_POST['users'])) {
            foreach($_POST['users'] as $user) {
                if($user['email'] != '' && $user['password'] != '') {
                    $this->Users->add(array(
                        'email' => $user['email'],
                        'password' => $user['password'],
                        'dashboard_id' => $dashboard_id
                    ));
                }
            }
        }

        $dashboard_url = base_url(isset($_POST['dashboard_name']) ? slugify($_POST['dashboard_name']) : '');
        echo json_encode(array(
            'success' => true,
            'dashboard_id' => $dashboard_id,
            'dashboard_tr' => <<<EOD
                <div class="admin-manager-dashboard-list-table-tr">
                    <div class="admin-manager-dashboard-list-table-td">
                        <label class="checkbox-container">
                            <input type="checkbox" class="dashboard-checkbox" data-id="$dashboard_id" name="dashboard_checkbox_$dashboard_id">
                            <span class="checkmark"></span>
                        </label>
                    </div>
                    <div class="admin-manager-dashboard-list-table-td">
                        <a class="admin-manager-dashboard-list-url" href="$dashboard_url" target="blank">$dashboard_url</a>
                    </div>
                </div>
            EOD
        ));
    }

    public function dashboard_update()
    {

        $dashboard_id = isset($_POST['dashboard_id']) ? $_POST['dashboard_id'] : '';
        $this->Dashboards->update(array(
            'id' => isset($_POST['dashboard_id']) ? $_POST['dashboard_id'] : '',
            'name' => isset($_POST['dashboard_name']) ? $_POST['dashboard_name'] : '',
            'reports' => isset($_POST['reports']) ? $_POST['reports'] : '',
        ));

        $this->Users->deleteByDashboardID(isset($_POST['dashboard_id']) ? $_POST['dashboard_id'] : '');
        if(isset($_POST['users'])) {
            foreach($_POST['users'] as $user) {
                if($user['email'] != '' && $user['password'] != '') {
                    $this->Users->add(array(
                        'email' => $user['email'],
                        'password' => $user['password'],
                        'dashboard_id' => $dashboard_id
                    ));
                }
            }
        }

        $dashboard_trs = '';
        $dashboards = $this->Dashboards->load();
        foreach($dashboards as $dashboard) {
            $dashboard_url = base_url($dashboard['slug']);
            $dashboard_id = $dashboard['id'];
            $dashboard_trs .= <<<EOD
                <div class="admin-manager-dashboard-list-table-tr">
                    <div class="admin-manager-dashboard-list-table-td">
                        <label class="checkbox-container">
                            <input type="checkbox" class="dashboard-checkbox" data-id="$dashboard_id" name="dashboard_checkbox_$dashboard_id">
                            <span class="checkmark"></span>
                        </label>
                    </div>
                    <div class="admin-manager-dashboard-list-table-td">
                        <a class="admin-manager-dashboard-list-url" href="$dashboard_url" target="blank">$dashboard_url</a>
                    </div>
                </div>
            EOD;
        }

        echo json_encode(array(
            'success' => true,
            'dashboard_trs' => $dashboard_trs
        ));
    }

    public function dashboard_delete()
    {
        if(isset($_POST['ids'])) {
            foreach($_POST['ids'] as $id) {
                $this->Dashboards->deleteByID($id);
            }
        }

        $dashboard_trs = '';
        $dashboards = $this->Dashboards->load();
        foreach($dashboards as $dashboard) {
            $dashboard_url = base_url($dashboard['slug']);
            $dashboard_id = $dashboard['id'];
            $dashboard_trs .= <<<EOD
                <div class="admin-manager-dashboard-list-table-tr">
                    <div class="admin-manager-dashboard-list-table-td">
                        <label class="checkbox-container">
                            <input type="checkbox" class="dashboard-checkbox" data-id="$dashboard_id" name="dashboard_checkbox_$dashboard_id">
                            <span class="checkmark"></span>
                        </label>
                    </div>
                    <div class="admin-manager-dashboard-list-table-td">
                        <a class="admin-manager-dashboard-list-url" href="$dashboard_url" target="blank">$dashboard_url</a>
                    </div>
                </div>
            EOD;
        }

        echo json_encode(array(
            'success' => true,
            'dashboard_trs' => $dashboard_trs
        ));
    }

    public function dashboard_edit()
    {
        $dashboard = null;
        $users = null;
        if(isset($_POST['id'])) {
            $dashboard = $this->Dashboards->getByID($_POST['id']);
            $dashboard = $dashboard[0];
        }
        
        if($dashboard) {
            $users = $this->Users->load($dashboard['id']);
        }

        echo json_encode(array(
            'success' => true,
            'dashboard' => $dashboard,
            'users' => $users
        ));
    }

}
