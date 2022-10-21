<?php
defined('BASEPATH') or exit('No direct script access allowed');

class AdminAPIController extends CI_Controller
{

    public function __construct()
    {

        parent::__construct();

        $this->load->library('LibGlobal');
        $this->load->library('LibBiorxivDB');

    }

    public function reportUpdate()
    {
        $response = array(
            'success' => false
        );

        $report_update = $this->Reports->update(array(
            'id' => isset($_POST['id']) ? $_POST['id'] : '',
            'title' => isset($_POST['title']) ? $_POST['title'] : '',
            'conditions' => isset($_POST['conditions']) ? $_POST['conditions'] : '',
            'study' => isset($_POST['study']) ? $_POST['study'] : '',
            'country' => isset($_POST['country']) ? $_POST['country'] : '',
            'terms' => isset($_POST['terms']) ? $_POST['terms'] : ''
        ));

        $reports = $this->Reports->getByID(isset($_POST['id']) ? $_POST['id'] : '');

        if (count($reports)) {
            $report = $reports[0];
        } else {
            $report = null;
        }

        $found_count = 0;

        if ($report) {

            $terms = $report['terms'];
            $study = $report['study'];
            $conditions = $report['conditions'];
            $country = $report['country'];
            $old_guids = $report['guids'];
            $new_updated_at =  $report['updated_at'];

            $guids =  array();

            $changed = false;

            $db_guids = array();

            $total_day = 0;

            if ($report['guids'] != "") {
                $db_guids = json_decode($report['guids'], true);
            }



            //Getting week_list and week_reports
            $current_week_list = $report['week_list'];
            $current_week_reports = $report['week_reports'];



            if ($report['reporting'] == '1') {

                if ($_SESSION['user_id'] == $report['user_id']) {
                    if ($report['updated_at'] == "" || $report['updated_at'] == null) {
                        $cur_updated_at =  date("Ymd");

                        $guids = getPubmedGuids(array(
                            'days' => 7,
                            'terms' => $terms,
                            'study' => $study,
                            'conditions' => $conditions,
                            'country' => $country,
                            'count' => 500
                        ));

                        // echo $guids;

                        $reports = $this->Reports->updateOnlyGuids(isset($_POST['id']) ? $_POST['id'] : '', array(
                            'updated_at' => $cur_updated_at,
                            'guids' => json_encode($guids)
                        ));

                        $found_count = count($guids);
                        $status = 'new';
                    } else {
                        $new_updated_at =  date("Ymd", strtotime($report['updated_at']));
                        $cur_updated_at = date("Ymd");
                        $old_month = intval(substr($new_updated_at, 4, 2));
                        $old_day = intval(substr($new_updated_at, 6, 2));

                        $new_month = intval(substr($cur_updated_at, 4, 2));
                        $new_day = intval(substr($cur_updated_at, 6, 2));

                        $total_month = 0;


                        if ($old_month == 12 && $new_month == 1) {
                            $total_month = 1 * 30;
                        } else {
                            $total_month = ($new_month - $old_month) * 30;
                            $total_day = $new_day - $old_day;
                        }

                        $total_days = $total_month + $total_day;






                        $guids = getPubmedGuids(array(
                            'days' => 7,
                            'terms' => $terms,
                            'study' => $study,
                            'conditions' => $conditions,
                            'country' => $country,
                            'count' => 500
                        ));





                        foreach ($guids as $guid) {
                            if (strpos($report['guids'], $guid) === false) {
                                $found_count++;
                            }
                        }

                        if ($found_count != count($db_guids) || $found_count != count($guids)) {
                            $changed = true;
                        }


                        if ($changed) {
                            $status = 'new';
                            $reports = $this->Reports->updateOnlyGuids(isset($_POST['id']) ? $_POST['id'] : '', array(
                                'updated_at' => $cur_updated_at,
                                'guids' => json_encode($guids)
                            ));
                        } else {
                            if ($total_days > 7 && $total_days <= 30) {
                                $status = 'recent';
                            } else if ($total_days > 30 && $total_days < 90) {
                                $status = 'old';
                            } else if ($total_days <= 7) {
                                $status = 'new';
                            }
                        }

                        $found_count = count($guids);
                    }

                    // echo $found_count;


                    $week_update = $this->Reports->updateWeek(array(
                        'id' => isset($_POST['id']) ? $_POST['id'] : '',
                        'week_list' => '1',
                        'week_reports' => $found_count
                    ));
                    $on_reporters = $report['on_reporters'];

                    $current_user_id = $_SESSION['user_id'];

                    $report_status = '';

                    if ($on_reporters == null || $on_reporters == '') {
                        $report_status = $current_user_id;
                    } else {
                        if (strpos($on_reporters, $current_user_id) === false) {
                            $report_status = $on_reporters . ',' . $current_user_id;
                        } else {
                            $report_status = $on_reporters;
                        }
                    }

                    $on_reporters_update = $this->Reports->updateOnReporters(array(
                        'id' => isset($_POST['id']) ? $_POST['id'] : '',
                        'on_reporters' => $report_status
                    ));
                } else {

                    $on_reporters = $report['on_reporters'];

                    $current_user_id = $_SESSION['user_id'];

                    $report_status = '';

                    if ($on_reporters == null || $on_reporters == '') {
                        $report_status = $current_user_id;
                    } else {
                        if (strpos($on_reporters, $current_user_id) === false) {
                            $report_status = $on_reporters . ',' . $current_user_id;
                        } else {
                            $report_status = $on_reporters;
                        }
                    }


                    $on_reporters_update = $this->Reports->updateOnReporters(array(
                        'id' => isset($_POST['id']) ? $_POST['id'] : '',
                        'on_reporters' => $report_status
                    ));
                }
            } else {
                /*
                $status = 'no';
                $cur_updated_at = $report['updated_at'];

                $week_update = $this->Reports->updateWeek(array(
                    'id' => isset($_POST['id']) ? $_POST['id'] : '',
                    'week_list' => '',
                    'week_reports' => ''
                ));
                */
                if ($_SESSION['user_id'] == $report['user_id']) {
                    $status = 'no';
                    $cur_updated_at = $report['updated_at'];



                    $week_update = $this->Reports->updateWeek(array(
                        'id' => isset($_POST['id']) ? $_POST['id'] : '',
                        'week_list' => '',
                        'week_reports' => ''
                    ));


                    $on_reporters = $report['on_reporters'];

                    $current_user_id = $_SESSION['user_id'];

                    $report_status = '';
                    if ($on_reporters == null || $on_reporters == '') {
                        $report_status = '';
                    } else {
                        if (strpos($on_reporters, $current_user_id) === false) {
                            $report_status = $on_reporters;
                        } else if (strpos($on_reporters, $current_user_id) === 0) {
                            $delete_second_str =  $current_user_id . ',';
                            $report_status = str_replace($delete_second_str, "", $on_reporters);
                        } else {
                            $delete_second_str = ',' . $current_user_id;
                            $report_status = str_replace($delete_second_str, "", $on_reporters);
                        }
                    }

                    $on_reporters_update = $this->Reports->updateOnReporters(array(
                        'id' => isset($_POST['id']) ? $_POST['id'] : '',
                        'on_reporters' => $report_status
                    ));
                } else {

                    $status = 'no';

                    $on_reporters = $report['on_reporters'];

                    $current_user_id = $_SESSION['user_id'];

                    $report_status = '';
                    if ($on_reporters == null || $on_reporters == '') {
                        $report_status = '';
                    } else {
                        if (strpos($on_reporters, $current_user_id) === false) {
                            $report_status = $on_reporters;
                        } else if (strpos($on_reporters, $current_user_id) === 0) {
                            $report_status = str_replace($current_user_id, "", $on_reporters);
                        } else {
                            $delete_second_str = ',' . $current_user_id;
                            $report_status = str_replace($delete_second_str, "", $on_reporters);
                        }
                    }

                    $on_reporters_update = $this->Reports->updateOnReporters(array(
                        'id' => isset($_POST['id']) ? $_POST['id'] : '',
                        'on_reporters' => $report_status
                    ));
                }
            }

            /*
            $report_update = $this->Reports->updateField(array(
                'id' => isset($_POST['id']) ? $_POST['id'] : '',
                'status' => $status
            ));
    
            if($report_update){
                $response['success'] = true;
            }
    
            $response['status'] = $status;
            $response['status_str'] = getStatusString($status);
            */

            $report_update = $this->Reports->updateField(array(
                'id' => isset($_POST['id']) ? $_POST['id'] : '',
                'status' => $status
            ));


            if ($report_update) {
                $response['success'] = true;
            }

            $response['change_count'] = $found_count;
            $response['status'] = $status;
            $response['guids'] = $guids;
            $response['terms'] = $terms;
            $response['new_updated_at'] = $new_updated_at;
            $response['db_guids'] = $db_guids;
            $response['changed'] = $changed;
            $response['status_str'] = getStatusString($status);
        } else if ($report_update) {
            $response['success'] = true;
        }

        echo json_encode($response);
    }

    public function reportDelete()
    {
        $response = array(
            'success' => false
        );

        $report_delete = $this->Reports->deleteByID(isset($_POST['id']) ? $_POST['id'] : null);

        if ($report_delete) {
            $response['success'] = true;
        }

        echo json_encode($response);
    }

    public function reportGetWeekList()
    {
        $response = array(
            'success' => false
        );


        $reports = $this->libbiorxivdb->reports_get_by_id(isset($_POST['id']) ? $_POST['id'] : '');
        // $reports = $this->Reports->getByID(isset($_POST['id']) ? $_POST['id'] : '');

        if ($reports) {
            $response['success'] = true;

            if (count($reports)) {
                $report = $reports[0];
            } else {
                $report = null;
            }

            $response['title'] = $report['title'];
            $response['week_list'] = $report['week_list'];
            $response['week_reports'] = $report['week_reports'];
        }

        echo json_encode($response);
    }




    public function reportDuplicate()
    {
        $response = array(
            'success' => false
        );

        $report_id = $this->Reports->duplicateByID(isset($_POST['id']) ? $_POST['id'] : null);

        if ($report_id) {
            $response['success'] = true;
            $response['report_id'] = $report_id;

            $response['success'] = true;

            $reports = $this->Reports->getByID($report_id);

            if (count($reports)) {
                $data = array();
                $data['report'] = $reports[0];
                $data['studies'] = $this->libglobal->getAllStudies();
                $data['fields'] = $this->libglobal->getAllFields();
                $data['plues'] = $this->libglobal->getAllPlues();
                $data['countries'] = $this->libglobal->getAllCountries();

                $report_html = $this->load->view('biorxiv/template/report-template', $data, TRUE);
            }

            $response['report'] = $report_html;
        }

        echo json_encode($response);
    }

    public function reportReporting()
    {
        $response = array(
            'success' => false
        );

        $reports = $this->Reports->getByID($_POST['id']);

        if (count($reports)) {
            $report = $reports[0];
        } else {
            $report = null;
            echo json_encode($response);
            die();
        }

        $terms = $report['terms'];
        $study = $report['study'];
        $conditions = $report['conditions'];
        $country = $report['country'];
        $old_guids = $report['guids'];
        $new_updated_at =  $report['updated_at'];

        $guids =  array();

        $changed = false;

        $db_guids = array();

        $total_day = 0;

        if ($report['guids'] != "") {
            $db_guids = json_decode($report['guids'], true);
        }


        $found_count = 0;

        //Getting week_list and week_reports
        $current_week_list = $report['week_list'];
        $current_week_reports = $report['week_reports'];


        $status = 'new';

        if (isset($_POST['reporting']) && $_POST['reporting'] == '1') {

            if ($_SESSION['user_id'] == $report['user_id']) {
                if ($report['updated_at'] == "" || $report['updated_at'] == null) {
                    $cur_updated_at =  date("Ymd");

                    $guids = getPubmedGuids(array(
                        'days' => 7,
                        'terms' => $terms,
                        'study' => $study,
                        'conditions' => $conditions,
                        'country' => $country,
                        'count' => 500
                    ));


                    $reports = $this->Reports->updateOnlyGuids(isset($_POST['id']) ? $_POST['id'] : '', array(
                        'updated_at' => $cur_updated_at,
                        'guids' => json_encode($guids)
                    ));

                    $found_count = count($guids);
                    $status = 'new';
                } else {
                    $new_updated_at =  date("Ymd", strtotime($report['updated_at']));
                    $cur_updated_at = date("Ymd");
                    $old_month = intval(substr($new_updated_at, 4, 2));
                    $old_day = intval(substr($new_updated_at, 6, 2));

                    $new_month = intval(substr($cur_updated_at, 4, 2));
                    $new_day = intval(substr($cur_updated_at, 6, 2));

                    $total_month = 0;


                    if ($old_month == 12 && $new_month == 1) {
                        $total_month = 1 * 30;
                    } else {
                        $total_month = ($new_month - $old_month) * 30;
                        $total_day = $new_day - $old_day;
                    }

                    $total_days = $total_month + $total_day;


                    $guids = getPubmedGuids(array(
                        'days' => 7,
                        'terms' => $terms,
                        'study' => $study,
                        'conditions' => $conditions,
                        'country' => $country,
                        'count' => 500
                    ));


                    foreach ($guids as $guid) {
                        if (strpos($report['guids'], $guid) !== false) {
                            $found_count++;
                        }
                    }

                    if ($found_count != count($db_guids) || $found_count != count($guids)) {
                        $changed = true;
                    }


                    if ($changed) {
                        $status = 'new';
                        $reports = $this->Reports->updateOnlyGuids(isset($_POST['id']) ? $_POST['id'] : '', array(
                            'updated_at' => $cur_updated_at,
                            'guids' => json_encode($guids)
                        ));
                    } else {
                        if ($total_days > 7 && $total_days <= 30) {
                            $status = 'recent';
                        } else if ($total_days > 30 && $total_days < 90) {
                            $status = 'old';
                        } else if ($total_days <= 7) {
                            $status = 'new';
                        }
                    }

                    $found_count = count($guids);
                }


                $week_update = $this->Reports->updateWeek(array(
                    'id' => isset($_POST['id']) ? $_POST['id'] : '',
                    'week_list' => '1',
                    'week_reports' => $found_count
                ));



                $on_reporters = $report['on_reporters'];

                $current_user_id = $_SESSION['user_id'];

                $report_status = '';

                if ($on_reporters == null || $on_reporters == '') {
                    $report_status = $current_user_id;
                } else {
                    if (strpos($on_reporters, $current_user_id) === false) {
                        $report_status = $on_reporters . ',' . $current_user_id;
                    } else {
                        $report_status = $on_reporters;
                    }
                }

                $on_reporters_update = $this->Reports->updateOnReporters(array(
                    'id' => isset($_POST['id']) ? $_POST['id'] : '',
                    'on_reporters' => $report_status
                ));
            } else {

                $on_reporters = $report['on_reporters'];

                $current_user_id = $_SESSION['user_id'];

                $report_status = '';

                if ($on_reporters == null || $on_reporters == '') {
                    $report_status = $current_user_id;
                } else {
                    if (strpos($on_reporters, $current_user_id) === false) {
                        $report_status = $on_reporters . ',' . $current_user_id;
                    } else {
                        $report_status = $on_reporters;
                    }
                }


                $on_reporters_update = $this->Reports->updateOnReporters(array(
                    'id' => isset($_POST['id']) ? $_POST['id'] : '',
                    'on_reporters' => $report_status
                ));
            }
        } else {

            if ($_SESSION['user_id'] == $report['user_id']) {
                $status = 'no';
                $cur_updated_at = $report['updated_at'];



                $week_update = $this->Reports->updateWeek(array(
                    'id' => isset($_POST['id']) ? $_POST['id'] : '',
                    'week_list' => '',
                    'week_reports' => ''
                ));

                $on_reporters = $report['on_reporters'];

                $current_user_id = $_SESSION['user_id'];

                $report_status = '';
                if ($on_reporters == null || $on_reporters == '') {
                    $report_status = '';
                } else {
                    if (strpos($on_reporters, $current_user_id) === false) {
                        $report_status = $on_reporters;
                    } else if (strpos($on_reporters, $current_user_id) === 0) {
                        $delete_second_str =  $current_user_id . ',';
                        $report_status = str_replace($delete_second_str, "", $on_reporters);
                    } else {
                        $delete_second_str = ',' . $current_user_id;
                        $report_status = str_replace($delete_second_str, "", $on_reporters);
                    }
                }

                $on_reporters_update = $this->Reports->updateOnReporters(array(
                    'id' => isset($_POST['id']) ? $_POST['id'] : '',
                    'on_reporters' => $report_status
                ));
            } else {

                $status = 'no';

                $on_reporters = $report['on_reporters'];

                $current_user_id = $_SESSION['user_id'];

                $report_status = '';
                if ($on_reporters == null || $on_reporters == '') {
                    $report_status = '';
                } else {
                    if (strpos($on_reporters, $current_user_id) === false) {
                        $report_status = $on_reporters;
                    } else if (strpos($on_reporters, $current_user_id) === 0) {
                        $report_status = str_replace($current_user_id, "", $on_reporters);
                    } else {
                        $delete_second_str = ',' . $current_user_id;
                        $report_status = str_replace($delete_second_str, "", $on_reporters);
                    }
                }

                $on_reporters_update = $this->Reports->updateOnReporters(array(
                    'id' => isset($_POST['id']) ? $_POST['id'] : '',
                    'on_reporters' => $report_status
                ));
            }
        }


        /*
        $report_update = $this->Reports->updateField(array(
            'id' => isset($_POST['id']) ? $_POST['id'] : '',
            'reporting' => isset($_POST['reporting']) ? $_POST['reporting'] : '',

            'status' => $status
        ));
        */

        //always showing reporting = 1
        $always_reporting = 1;
        $report_update = $this->Reports->updateField(array(
            'id' => isset($_POST['id']) ? $_POST['id'] : '',
            'reporting' => $always_reporting,

            'status' => $status
        ));






        /*
        $reports = $this->Reports->updateOnlyGuids(isset($_POST['id']) ? $_POST['id'] : '', array(           
            'guids' => json_encode($guids)
        ));
        */
        if ($report_update) {
            $response['success'] = true;
        }

        $response['status'] = $status;
        $response['guids'] = $guids;
        $response['terms'] = $terms;
        $response['new_updated_at'] = $new_updated_at;
        $response['db_guids'] = $db_guids;
        $response['changed'] = $changed;
        $response['status_str'] = getStatusString($status);

        echo json_encode($response);
    }

    public function reportSearch()
    {
        $response = array(
            'success' => false
        );

        $reports = $this->libbiorxivdb->reports_search(isset($_POST['keyword']) ? $_POST['keyword'] : '', isset($_POST['sort']) ? $_POST['sort'] : 'az', $_SESSION['biorxiv_report_ids']);

        // $reports = $this->Reports->search(isset($_POST['keyword']) ? $_POST['keyword'] : '', isset($_POST['sort']) ? $_POST['sort'] : 'az', isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '');

        if (count($reports) > 0) {
            $response['success'] = true;
            $html = '';

            foreach ($reports as $report) {

                $data = array();
                $data['report'] = $report;
                $data['studies'] = $this->libglobal->getAllStudies();
                $data['fields'] = $this->libglobal->getAllFields();
                $data['plues'] = $this->libglobal->getAllPlues();
                $data['countries'] = $this->libglobal->getAllCountries();

                $html .= $this->load->view('biorxiv/template/report-template', $data, TRUE);
            }

            $response['reports'] = $html;
        }

        echo json_encode($response);
    }

    public function userDelete()
    {
        $response = array(
            'success' => false
        );

        $report_delete = $this->Users->deleteByID(isset($_POST['user_id']) ? $_POST['user_id'] : null);

        if ($report_delete) {
            $response['success'] = true;
        }

        echo json_encode($response);
    }
}
