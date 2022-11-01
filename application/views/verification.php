<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
        <link rel="icon" type="image/png" href="<?= base_url() ?>assets/img/favicon.png">
		<title>Verification</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
		<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">
		<!-- Bootstrap Core Css -->
        <link rel="stylesheet" href="<?= base_url() ?>assets/css/bootstrap.min.css">
		<!-- Waves Effect Css -->
		<link href="<?= base_url() ?>assets/css/waves.min.css" rel="stylesheet" />
		<!-- Animation Css -->
		<link href="<?= base_url() ?>assets/css/animate.min.css" rel="stylesheet" />
		<!-- Materialize Css -->
        <link rel="stylesheet" href="<?= base_url() ?>assets/css/materialize.css">
        <link rel="stylesheet" href="<?= base_url() ?>assets/css/style.css">
        
        <script type="text/javascript">
        var base_url = "<?php echo base_url()?>";
        </script>
	</head>
	<body class="login-page">
        <div class="login-box">
            <div class="logo" style="display: none;">
                <a href="javascript:void(0);">Care Equity Tools</a>
                
            </div>
            <?php if(isset($msg) || validation_errors() !== ''): ?>
            <div class="alert alert-warning alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-warning"></i> Alert!</h4>
                <?= validation_errors();?>
                <?= isset($msg)? $msg: ''; ?>
            </div>
            <?php endif; ?>
            <?php if(isset($warning)): ?>
                <div class="alert alert-warning">
                <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
                <?=$warning?>
                </div>
            <?php endif; ?>
            <?php if(isset($success)): ?>
                <div class="alert alert-success">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
                    <?=$success?>
                </div>
            <?php endif; ?>
            <div class="card">
                <div class="body">
                    <?php echo form_open(base_url('/verification'), 'class="login-form" '); ?>
                        <div class="msg">Verification</div>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="material-icons">lock</i>
                            </span>
                            <div class="form-line">
                                <input type="password" class="form-control" name="code" placeholder="Code" required>
                            </div>
                        </div>
                        <input type="hidden" name="email" value="<?php echo isset($email) ? $email : ''?>">
                        <input type="hidden" name="password" value="<?php echo isset($password) ? $password : ''?>">
                        <input type="hidden" name="okta_user_id" value="<?php echo isset($okta_user_id) ? $okta_user_id : ''?>">
                        <input type="hidden" name="okta_factor_id" value="<?php echo isset($okta_factor_id) ? $okta_factor_id : ''?>">
                        <div class="row">
                            <div class="col-xs-12 login-wrap">
                                <input type="submit" name="submit" id="btn_submit" class="btn btn-block btn-white btn-success waves-effect" value="Submit">
                            </div>
                        </div>
                        <!--
                        <div class="m-t-25 align-center">
                            <a href="<?php echo base_url('/register')?>">Create an account</a>
                        </div>        
                        -->
                    <?php echo form_close(); ?>
                </div>
            </div>
            </div>
        </div>
    </body>
    <script type="text/javascript" src="<?= base_url() ?>assets/js/jquery.min.js"></script>
    <!-- Bootstrap Core Js -->
    <script src="<?= base_url() ?>assets/js/bootstrap.min.js"></script>
    <!-- Waves Effect Plugin Js -->
    <script src="<?= base_url() ?>assets/js/waves.min.js"></script>
    <!-- Validation Plugin Js -->
    <script src="<?= base_url() ?>assets/js/jquery.validate.js"></script>
    <!-- Custom Js -->
	<script src="<?= base_url() ?>assets/js/admin.js"></script>
    <script src="<?= base_url() ?>assets/js/app.js?v=<?php echo time()?>"></script>
</html>