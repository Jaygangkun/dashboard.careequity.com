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

    public function dashboard_publish()
    {
        $dashboard_id = $this->Dashboards->add(array(
            'name' => isset($_POST['dashboard_name']) ? $_POST['dashboard_name'] : '',
            'reports' => isset($_POST['reports']) ? $_POST['reports'] : '',
        ));

        if(isset($_POST['users'])) {
            foreach($_POST['users'] as $user) {
                if($user['username'] != '' && $user['password'] != '') {
                    $this->Users->add(array(
                        'username' => $user['username'],
                        'password' => $user['password'],
                        'dashboard_id' => $dashboard_id
                    ));
                }
            }
        }

        $dashboard_url = base_url(isset($_POST['dashboard_name']) ? $_POST['dashboard_name'] : '');
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
                        <span class="admin-manager-dashboard-list-url">URL = $dashboard_url</span>
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
                if($user['username'] != '' && $user['password'] != '') {
                    $this->Users->add(array(
                        'username' => $user['username'],
                        'password' => $user['password'],
                        'dashboard_id' => $dashboard_id
                    ));
                }
            }
        }

        $dashboard_trs = '';
        $dashboards = $this->Dashboards->load();
        foreach($dashboards as $dashboard) {
            $dashboard_url = base_url($dashboard['name']);
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
                        <span class="admin-manager-dashboard-list-url">URL = $dashboard_url</span>
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
            $dashboard_url = base_url($dashboard['name']);
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
                        <span class="admin-manager-dashboard-list-url">URL = $dashboard_url</span>
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
