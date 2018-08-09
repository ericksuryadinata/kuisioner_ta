<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta name="author" content="Universitas 17 Agustus 1945 Surabaya">
    <meta name="keyword" content="Teknik Informatika, Untag Surabaya, Informatika Untag">
    <link rel="apple-touch-icon" sizes="57x57" href="{{base_url('assets/ico/apple-icon-57x57.png')}}">
    <link rel="apple-touch-icon" sizes="60x60" href="{{base_url('assets/ico/apple-icon-60x60.png')}}">
    <link rel="apple-touch-icon" sizes="72x72" href="{{base_url('assets/ico/apple-icon-72x72.png')}}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{base_url('assets/ico/apple-icon-76x76.png')}}">
    <link rel="apple-touch-icon" sizes="114x114" href="{{base_url('assets/ico/apple-icon-114x114.png')}}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{base_url('assets/ico/apple-icon-120x120.png')}}">
    <link rel="apple-touch-icon" sizes="144x144" href="{{base_url('assets/ico/apple-icon-144x144.png')}}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{base_url('assets/ico/apple-icon-152x152.png')}}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{base_url('assets/ico/apple-icon-180x180.png')}}">
    <link rel="icon" type="image/png" sizes="192x192"  href="{{base_url('assets/ico/android-icon-192x192.png')}}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{base_url('assets/ico/favicon-32x32.png')}}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{base_url('assets/ico/favicon-96x96.png')}}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{base_url('assets/ico/favicon-16x16.png')}}">
    <link rel="manifest" href="{{base_url('assets/ico/manifest.json')}}">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="{{base_url('assets/ico/ms-icon-144x144.png')}}">
    <meta name="theme-color" content="#ffffff">
    <title>Login Page</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">

    <!-- Bootstrap Core Css -->
    <link href="{{base_url('assets/plugins/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">

    <!-- Waves Effect Css -->
    <link href="{{base_url('assets/plugins/node-waves/waves.min.css')}}" rel="stylesheet" />

    <!-- Animation Css -->
    <link href="{{base_url('assets/plugins/animate-css/animate.min.css')}}" rel="stylesheet" />

    <!-- Custom Css -->
    <link href="{{base_url('assets/css/style.min.css')}}" rel="stylesheet">

</head>

<body class="login-page">
    <div class="login-box">
        <div class="logo">
            <a href="javascript:void(0);">Admin<b> Panel</b></a>
            <small>Universitas 17 Agustus 1945 Surabaya</small>
            <!-- <?php echo password_hash('dianokta',PASSWORD_DEFAULT)?> -->
            <!--<?php echo password_hash('dian1234',PASSWORD_DEFAULT)?> -->
        </div>
        <div class="card">
            <div class="body">
                <?php echo form_open(route('admin.auth.login'),'id="sign_in"')?>
                    <div class="alert bg-pink alert-dismissible" role="alert" {{isset($hidden) ? $hidden : 'hidden'}}>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        {{isset($message) ? $message : validation_errors()}}
                    </div>
                    <div class="msg">Silakan masuk untuk mengakses Admin Panel</div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">person</i>
                        </span>
                        <div class="form-line">
                            <input type="text" class="form-control" name="username" placeholder="Email" required autofocus>
                        </div>
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">lock</i>
                        </span>
                        <div class="form-line">
                            <input type="password" class="form-control" name="password" placeholder="Password" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-8 p-t-5">
                            {{-- <input type="checkbox" name="rememberme" id="rememberme" class="filled-in chk-col-pink">
                            <label for="rememberme">Remember Me</label> --}}
                        </div>
                        <div class="col-xs-4">
                            <button class="btn btn-block bg-pink waves-effect" type="submit">SIGN IN</button>
                        </div>
                    </div>
                <?php echo form_close()?>
            </div>
        </div>
    </div>

    <!-- Jquery Core Js -->
    <script src="{{base_url('assets/plugins/jquery/jquery.min.js')}}"></script>

    <!-- Bootstrap Core Js -->
    <script src="{{base_url('assets/plugins/bootstrap/js/bootstrap.min.js')}}"></script>

    <!-- Waves Effect Plugin Js -->
    <script src="{{base_url('assets/plugins/node-waves/waves.min.js')}}"></script>

    <!-- Validation Plugin Js -->
    <script src="{{base_url('assets/plugins/jquery-validation/jquery.validate.js')}}"></script>

    <!-- Custom Js -->
    <script src="{{base_url('assets/js/admin.js')}}"></script>
    <script src="{{base_url('assets/js/pages/examples/sign-in.js')}}"></script>
    <!-- Bootstrap Notify Plugin Js -->
    <script src="{{base_url('assets/plugins/bootstrap-notify/bootstrap-notify.js')}}"></script>
    <script src="{{base_url('assets/js/custom-admin.js')}}"></script>
    <?php
        if(isset($_SESSION['message'])){
            if($_SESSION['message'] === 'success'){
                echo "<script>showNotif('".$_SESSION['pesan']."','success')</script>";
            }else{
                echo "<script>showNotif('".$_SESSION['pesan']."','error')</script>";
            }
        }
    ?>
</body>

</html>