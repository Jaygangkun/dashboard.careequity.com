<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" type="image/png" href="<?= base_url() ?>assets/img/favicon.png">
    <title>Admin Manager</title>
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
    <link rel="stylesheet" href="<?= base_url() ?>assets/css/style.css">
    <link rel="stylesheet" href="<?= base_url() ?>assets/css/dashboard.css">
    
    <script type="text/javascript">
        const base_url = "<?php echo base_url()?>";
        const talent_lib_server = "<?php echo $this->config->item("talent_lib_server")?>";
    </script>
</head>
<body class="admin-manager-page">
    <div class="loading-screen"><div class="loader"></div></div>
    <div class="admin-manager-container">
        <h1 class="">Admin Manager</h1>
        <div class="admin-manager-form-container">
            <div class="admin-manager-form-col">
                <div class="admin-manager-form-input-wrap" step="1">
                    <div class="admin-manager-form-input-wrap-left">
                        <div class="admin-manager-form-input-numb">01</div>
                    </div>
                    <div class="admin-manager-form-input-wrap-right">
                        <div class="admin-manager-form-input-wrap-right-left">
                            <div class="admin-manager-form-input-plus-icon">+</div>
                        </div>
                        <div class="admin-manager-form-input-wrap-right-right">
                            <div class="admin-manager-form-input-label">create a dashboard</div>
                            <input type="text" class="admin-manager-form-input-control" placeholder="Name" name="dashboard_name"></input>
                            <div class="admin-manager-form-input-desc">URL = dashboard.careequity.com/dashboard</div>
                        </div>
                    </div>
                </div>
                <div class="admin-manager-form-input-wrap">
                    <div class="admin-manager-form-input-wrap-left">
                        <div class="admin-manager-form-input-numb">02</div>
                    </div>
                    <div class="admin-manager-form-input-wrap-right">
                        <div class="admin-manager-form-input-wrap-right-left">
                            <div class="admin-manager-form-input-plus-icon">+</div>
                        </div>
                        <div class="admin-manager-form-input-wrap-right-right">
                            <div class="admin-manager-form-input-label">add users</div>
                            <div class="admin-manager-form-input-controls">
                                <input type="text" class="admin-manager-form-input-control" placeholder="Email" name="username1"></input>
                                <input type="password" class="admin-manager-form-input-control" placeholder="Password" name="password1"></input>
                            </div>
                            <div class="admin-manager-form-input-controls">
                                <input type="text" class="admin-manager-form-input-control" placeholder="Email" name="username2"></input>
                                <input type="password" class="admin-manager-form-input-control" placeholder="Password" name="password2"></input>
                            </div>
                            <div class="admin-manager-form-input-controls">
                                <input type="text" class="admin-manager-form-input-control" placeholder="Email" name="username3"></input>
                                <input type="password" class="admin-manager-form-input-control" placeholder="Password" name="password3"></input>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="admin-manager-form-col">
                <div class="admin-manager-form-input-wrap">
                    <div class="admin-manager-form-input-wrap-left">
                        <div class="admin-manager-form-input-numb">03</div>
                    </div>
                    <div class="admin-manager-form-input-wrap-right trigger-click" type="linkedin">
                        <div class="admin-manager-form-input-wrap-right-left">
                            <div class="admin-manager-form-input-plus-icon">+</div>
                        </div>
                        <div class="admin-manager-form-input-wrap-right-right">
                            <div class="admin-manager-form-input-label">select linkedin accounts</div>
                            <div class="admin-manager-form-input-result" data-type="linkedin"></div>
                        </div>
                    </div>
                </div>
                <div class="admin-manager-form-input-wrap">
                    <div class="admin-manager-form-input-wrap-left">
                        <div class="admin-manager-form-input-numb">04</div>
                    </div>
                    <div class="admin-manager-form-input-wrap-right trigger-click" type="biorxiv">
                        <div class="admin-manager-form-input-wrap-right-left">
                            <div class="admin-manager-form-input-plus-icon">+</div>
                        </div>
                        <div class="admin-manager-form-input-wrap-right-right">
                            <div class="admin-manager-form-input-label">select biorxiv reports</div>
                            <div class="admin-manager-form-input-result" data-type="biorxiv"></div>
                        </div>
                    </div>
                </div>
                <div class="admin-manager-form-input-wrap">
                    <div class="admin-manager-form-input-wrap-left">
                        <div class="admin-manager-form-input-numb">05</div>
                    </div>
                    <div class="admin-manager-form-input-wrap-right trigger-click" type="pubmed">
                        <div class="admin-manager-form-input-wrap-right-left">
                            <div class="admin-manager-form-input-plus-icon">+</div>
                        </div>
                        <div class="admin-manager-form-input-wrap-right-right">
                            <div class="admin-manager-form-input-label">select pubmed reports</div>
                            <div class="admin-manager-form-input-result" data-type="pubmed"></div>
                        </div>
                    </div>
                </div>
                <div class="admin-manager-form-input-wrap">
                    <div class="admin-manager-form-input-wrap-left">
                        <div class="admin-manager-form-input-numb">06</div>
                    </div>
                    <div class="admin-manager-form-input-wrap-right trigger-click" type="clinical">
                        <div class="admin-manager-form-input-wrap-right-left">
                            <div class="admin-manager-form-input-plus-icon">+</div>
                        </div>
                        <div class="admin-manager-form-input-wrap-right-right">
                            <div class="admin-manager-form-input-label">select clinical trials reports</div>
                            <div class="admin-manager-form-input-result" data-type="clinical"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="admin-manager-form-col">
                <div class="admin-manager-form-input-wrap admin-manager-form-btn">
                    <div class="admin-manager-form-input-wrap-left">
                        <div class="admin-manager-form-input-numb">07</div>
                    </div>
                    <div class="admin-manager-form-input-wrap-right">
                        <div class="admin-manager-form-input-wrap-right-right">
                            <div class="admin-manager-form-input-label" id="btn_dashboard_publish" dashboard-id="">publish</div>
                        </div>
                    </div>
                </div>
                <div class="admin-manager-dashboard-list">
                    <h3 class="admin-manager-dashboard-list-title">DASHBORD LIST</h3>
                    <div class="admin-manager-dashboard-list-table">
                        <div class="admin-manager-dashboard-list-table-tr" id="dasbboard_list_action_tr">
                            <div class="admin-manager-dashboard-list-table-td">
                                <label class="checkbox-container">
                                    <input type="checkbox">
                                    <span class="checkmark"></span>
                                </label>
                            </div>
                            <div class="admin-manager-dashboard-list-table-td">
                                <span class="admin-manager-dashboard-list-btn" id="btn_dashboard_new">NEW</span>
                                <span class="admin-manager-dashboard-list-btn" id="btn_dashboard_edit">EDIT</span>
                                <span class="admin-manager-dashboard-list-btn" id="btn_dashboard_delete">DELETE</span>
                            </div>
                        </div>
                        <div id="dashboard_list_table">
                            <?php
                            if(isset($dashboards)) {
                                foreach($dashboards as $dashboard) {
                                    ?>
                                    <div class="admin-manager-dashboard-list-table-tr">
                                        <div class="admin-manager-dashboard-list-table-td">
                                            <label class="checkbox-container">
                                                <input type="checkbox" class="dashboard-checkbox" data-id="<?php echo $dashboard['id']?>" name="dashboard_checkbox_<?php echo $dashboard['id']?>">
                                                <span class="checkmark"></span>
                                            </label>
                                        </div>
                                        <div class="admin-manager-dashboard-list-table-td">
                                            <span class="admin-manager-dashboard-list-url">URL = <?php echo base_url($dashboard['name'])?></span>
                                        </div>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                        </div>                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="modal_select" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <!-- <button type="button" class="close" data-dismiss="modal">&times;</button> -->
                    <h4 class="modal-title"></h4>
                    <div class="modal-header-btns-wrap">
                        <span class="" id="btn_modal_select_cancel" data-dismiss="modal">Cancel</span>
                        <span class="" id="btn_modal_select_save">Save</span>
                    </div>
                </div>
                <div class="modal-body">
                <?php
                    if(isset($reports['linkedin'])) {
                        ?>
                        <div class="modal-table-content" id="linkedin">
                            <table id="linkedin_list" class="table table-bordered table-striped table-hover dataTable">
                                <thead>
                                    <tr>
                                        <td></td>
                                        <td>Name</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach($reports['linkedin'] as $report) {
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
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <?php
                    }
                    ?>
                    <?php
                    if(isset($reports['biorxiv'])) {
                        ?>
                        <div class="modal-table-content" id="biorxiv">
                            <table id="biorxiv_list" class="table table-bordered table-striped table-hover dataTable">
                                <thead>
                                    <tr>
                                        <td></td>
                                        <td>Title</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach($reports['biorxiv'] as $report) {
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
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <?php
                    }
                    ?>
                    <?php
                    if(isset($reports['pubmed'])) {
                        ?>
                        <div class="modal-table-content" id="pubmed">
                            <table id="pubmed_list" class="table table-bordered table-striped table-hover dataTable">
                                <thead>
                                    <tr>
                                        <td></td>
                                        <td>Title</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach($reports['pubmed'] as $report) {
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
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <?php
                    }
                    ?>
                    <?php
                    if(isset($reports['clinical'])) {
                        ?>
                        <div class="modal-table-content" id="clinical">
                            <table id="clinical_list" class="table table-bordered table-striped table-hover dataTable">
                                <thead>
                                    <tr>
                                        <td></td>
                                        <td>Title</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach($reports['clinical'] as $report) {
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
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>

        </div>
    </div>
</body>
<script type="text/javascript" src="<?= base_url() ?>assets/js/jquery.min.js"></script>
<script type="text/javascript" src="<?= base_url() ?>assets/js/jquery.dataTables.js"></script>
<script type="text/javascript" src="<?= base_url() ?>assets/js/dataTables.bootstrap.min.js"></script>
<!-- Bootstrap Core Js -->
<script src="<?= base_url() ?>assets/js/bootstrap.min.js"></script>
<!-- Waves Effect Plugin Js -->
<script src="<?= base_url() ?>assets/js/waves.min.js"></script>
<!-- Validation Plugin Js -->
<script src="<?= base_url() ?>assets/js/jquery.validate.js"></script>
<!-- Custom Js -->
<script src="<?= base_url() ?>assets/js/page/admin_manager.js?v=<?php echo time()?>"></script>
</html>