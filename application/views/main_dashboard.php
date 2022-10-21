<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" type="image/png" href="<?= base_url() ?>assets/img/favicon.png">
    <title>User Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">
    <!-- Bootstrap Core Css -->
    <link rel="stylesheet" href="<?= base_url() ?>assets/css/bootstrap.min.css">
    <!-- Waves Effect Css -->
    <link href="<?= base_url() ?>assets/css/waves.min.css" rel="stylesheet" />
    <!-- Animation Css -->
    <link href="<?= base_url() ?>assets/css/animate.min.css" rel="stylesheet" />

    <link rel="stylesheet" href="<?= base_url() ?>assets/css/dataTables.bootstrap.min.css">
    <!-- Materialize Css -->
    <link rel="stylesheet" href="<?= base_url() ?>assets/css/materialize.css">
    <link rel="stylesheet" href="<?= base_url() ?>assets/css/sweetalert.css">	
	<link rel="stylesheet" href="<?= base_url() ?>assets/css/jquery-ui.css">
	<link rel="stylesheet" href="<?= base_url() ?>assets/css/biorxiv/style.css?v=<?php echo time() ?>">
    <link rel="stylesheet" href="<?= base_url() ?>assets/css/pubmed/style.css?v=<?php echo time() ?>">
    <link rel="stylesheet" href="<?= base_url() ?>assets/css/clinical/style.css?v=<?php echo time() ?>">
    <!-- <link rel="stylesheet" href="<?= base_url() ?>assets/css/style.css"> -->
	<link rel="stylesheet" href="<?= base_url() ?>assets/css/dashboard.css?v=<?php echo time() ?>">


    <script type="text/javascript" src="<?= base_url() ?>assets/js/jquery.min.js"></script>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script> -->
    <script type="text/javascript" src="<?= base_url() ?>assets/js/jquery-ui.js"></script>
    <script type="text/javascript" src="<?= base_url() ?>assets/js/sweetalert.min.js"></script>
    <script type="text/javascript" src="<?= base_url() ?>assets/js/jquery.dataTables.js"></script>
    <script type="text/javascript" src="<?= base_url() ?>assets/js/dataTables.bootstrap.min.js"></script>
    <!-- Bootstrap Core Js -->
    <script src="<?= base_url() ?>assets/js/bootstrap.min.js"></script>
    <!-- Waves Effect Plugin Js -->
    <script src="<?= base_url() ?>assets/js/waves.min.js"></script>
    <!-- Validation Plugin Js -->
    <script src="<?= base_url() ?>assets/js/jquery.validate.js"></script>

    <script type="text/javascript" src="<?= base_url() ?>assets/js/graphiq.js"></script>

    <script type="text/javascript">
        var base_url = "<?php echo base_url()?>";
        const ALERT_SUCCESS = 'success';
        const ALERT_FAIL = 'fail';
        const ALERT_NORMAL = 'normal';

    </script>
</head>
<body class="user-dashboard-page">
    <div class="loading-screen"><div class="loader"></div></div>
    <div class="admin-manager-container">
        <h1 class="">User Dashboard</h1>
        <div class="reports-header">
            <div class="report-header-wrap">
                <div class="report-header-updates-count">32 Updates</div>
                <div class="report-header-btn-switch active" data-target="#reports_linkedin">linkedin accounts</div>
            </div>
            <div class="report-header-wrap">
                <div class="report-header-updates-count">5 Updates</div>
                <div class="report-header-btn-switch" data-target="#reports_biorxiv">biorxiv reports</div>
            </div>
            <div class="report-header-wrap">
                <div class="report-header-updates-count disable">0 Updates</div>
                <div class="report-header-btn-switch" data-target="#reports_pubmed">pubmed reports</div>
            </div>
            <div class="report-header-wrap">
                <div class="report-header-updates-count">32 Updates</div>
                <div class="report-header-btn-switch" data-target="#reports_clinical">clinical trials reports</div>
            </div>
        </div>
        <div class="reports-container">
            <?php
            if(isset($reports) && isset($reports['linkedin'])) {
                ?>
                <div class="reports-wrap" id="reports_linkedin">
                    <?php
                    echo "linkedin";
                    ?>
                </div>                
                <?php
            }
            ?>
            <?php
            if(isset($reports) && isset($reports['biorxiv'])) {
                $report_ids = [];
                foreach($reports['biorxiv'] as $report_id => $report_id_val) {
                    $report_ids[] = $report_id;
                }

                $_SESSION['biorxiv_report_ids'] = $report_ids;

                $data = array();
                $data['studies'] = $this->libglobal->getAllStudies();
                $data['fields'] = $this->libglobal->getAllFields();
                $data['plues'] = $this->libglobal->getAllPlues();
                $data['countries'] = $this->libglobal->getAllCountries();
                //$data['users'] =$_SESSION['username'];
                $data['reports'] = $this->libbiorxivdb->reports_load_by_ids($report_ids);
                
                ?>
                <div class="reports-wrap" id="reports_biorxiv" style="display: none">
                    <?php
                    $this->load->view('biorxiv/dashboard', $data);
                    ?>
                </div>                
                <?php
            }
            else {
                $_SESSION['biorxiv_report_ids'] = [];
            }
            ?>
            <?php
            if(isset($reports) && isset($reports['pubmed'])) {
                $report_ids = [];
                foreach($reports['pubmed'] as $report_id => $report_id_val) {
                    $report_ids[] = $report_id;
                }

                $_SESSION['pubmed_report_ids'] = $report_ids;

                $data = array();
                $data['studies'] = $this->libglobal->getAllStudies();
                $data['fields'] = $this->libglobal->getAllFields();
                $data['plues'] = $this->libglobal->getAllPlues();
                $data['countries'] = $this->libglobal->getAllCountries();
                //$data['users'] =$_SESSION['username'];
                $data['reports'] = $this->libpubmeddb->reports_load_by_ids($report_ids);
                ?>
                <div class="reports-wrap" id="reports_pubmed" style="display: none">
                    <?php
                    $this->load->view('pubmed/dashboard', $data);
                    ?>
                </div>                
                <?php
            }
            else {
                $_SESSION['pubmed_report_ids'] = [];
            }
            ?>
            <?php
            if(isset($reports) && isset($reports['clinical'])) {
                $report_ids = [];
                foreach($reports['clinical'] as $report_id => $report_id_val) {
                    $report_ids[] = $report_id;
                }

                $_SESSION['clinical_report_ids'] = $report_ids;

                $data = array();
                $data['studies'] = $this->libglobal->getAllStudies();
                $data['countries'] = $this->libglobal->getAllCountries();
                //$data['users'] =$_SESSION['username'];
                $data['reports'] = $this->libclinicaldb->reports_load_by_ids($report_ids);
                ?>
                <div class="reports-wrap" id="reports_clinical" style="display: none">
                    <?php
                    $this->load->view('clinical/dashboard', $data);
                    ?>
                </div>                
                <?php
            }
            else {
                $_SESSION['clinical_report_ids'] = [];
            }
            ?>
        </div>
    </div>

</body>

<!-- Custom Js -->
<script src="<?= base_url() ?>assets/js/page/main_dashboard.js?v=<?php echo time()?>"></script>

</html>