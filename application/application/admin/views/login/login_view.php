<?php
$headerData = $this->headerlib->data();
/* * ***************************************************
 * *      DEFINE FORM ATTRIBUTES
 * *************************************************** */


$form_attr = array(
    'name' => 'login-form',
    'id' => 'loginform',
    'class' => 'form-horizontal row-fluid',
    'role' => 'form'
);
$user_email = array(
    "name" => "vEmail",
    "id" => "exampleInputEmail1",
    "autofocus" => "autofocus",
    "required" => "required",
    "data-errortext" => "This is your email!",
    "placeholder" => "Your Email",
    'value' => (isset($vEmail) && $vEmail != '') ? $vEmail : '',
    "class" => "form-control paddingtopbottom5",
    "type" => "email"
);
$pass_attr = array(
    'name' => 'vPassword',
    'id' => 'exampleInputPassword1',
    "required" => "required",
    "data-errortext" => "This is your password!",
    "placeholder" => "Your Password",
    'value' => (isset($vPassword) && $vPassword != '' ) ? $vPassword : '',
    "class" => "form-control paddingtopbottom5",
    "type" => "password",
);
$chkRem_attr = array(
    'name' => 'chkRemember',
    'id' => 'chkRemember',
    'value' => 'on'
);
$submit_attr = array(
    'name' => "login",
    'value' => "Login",
    'class' => "button",
    'id' => "login",
);
$anchor_attr = array(
    'class' => "button"
);
/* * ***************************************************
  DEFINE FORM ATTRIBUTES FOR FORGOT PASSWORD
 * *************************************************** */
$form_attr_forgot = array(
    'name' => 'forgot-form',
    'id' => 'forgot-form',
    'class' => 'form-horizontal row-fluid',
    'role' => 'form'
);
$email_attr_forgot = array(
    'name' => 'vEmail',
    'id' => 'exampleInputEmail1',
    "autofocus" => "autofocus",
    "required" => "required",
    'value' => (isset($vEmail) && $vEmail != '') ? $vEmail : '',
    "data-errortext" => "This is your email address!",
    "class" => "form-control paddingtopbottom5",
    "type" => "email",
);
$pwd_btn_forgot = array(
    'class' => 'btn btn-info',
    'type' => 'submit',
    'content' => 'Send Me Reset Instructions'
);
?>
<!doctype html>
<html lang="en-us">
    <head>
        <title>Application Login</title>
        <?= $headerData['meta_tags']; ?>

  <!-- STYLESHEETS --><!--[if lt IE 9]><script src="js/flot/excanvas.min.js"></script><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><script src="http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script><![endif]-->
        <?= $headerData['login_stylesheets']; ?>

    </head>
    <body class="login">  
        <!-- PAGE -->
        <section id="page">
            <!-- HEADER -->
            <header>
                <!-- NAV-BAR -->
                <div class="container">
                    <div class="row">
                        <div class="col-md-4 col-md-offset-4">
                            <div id="logo">
                              <!-- <a href="index.html"><img src="img/logo/logo.png" height="40" alt="logo name" /></a> -->
                            </div>
                        </div>
                    </div>
                </div>
                <!--/NAV-BAR -->
            </header>
            <!--/HEADER -->

            <!-- LOGIN -->
            <section id="login_bg" class="visible">
                <div class="container">
                    <div class="row">
                        <div class="col-md-4 col-md-offset-4">
                            <div class="login-box">
                                
                                <h2 class="bigintro">Sign In</h2>
                                <?= $this->general_model->getMessages() ?>
                                <div class="divide-40"></div>
                                <?= form_open('login', $form_attr) ?>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Email address</label>
                                    <i class="fa fa-envelope"></i>
                                    <?php echo form_input($user_email); ?>      
                                </div>
                                <div class="form-group"> 
                                    <label for="exampleInputPassword1">Password</label>
                                    <i class="fa fa-lock"></i>
                                    <?php echo form_input($pass_attr); ?>     
                                </div>
                                <div>
                                    <label class="checkbox "> <input type="checkbox" name="rememberme" <?= (isset($vEmail) && $vEmail != '') ? 'CHECKED' : '' ?>  class="uniform " value="true"> Remember me</label>
                                    <button type="submit" class="btn btn-danger">Submit</button>
                                </div>
                                <?= form_close(); ?>

                                <!-- /SOCIAL LOGIN -->
                                <div class="login-helpers">
                                    <a href="#" 
                                       onClick="swapScreen('forgot_bg');
                                               return false;">Forgot Password?</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!--/LOGIN -->
            <!-- FORGOT PASSWORD -->
            <section id="forgot_bg">
                <div class="container">
                    <div class="row">
                        <div class="col-md-4 col-md-offset-4">
                            <div class="login-box">
                                <h2 class="bigintro">Reset Password</h2>
                                <div class="divide-40"></div>
                                <?= form_open('forgot_pass', $form_attr_forgot) ?>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Enter your Email address</label>
                                    <i class="fa fa-envelope"></i>
                                    <?php echo form_input($email_attr_forgot); ?>
                                </div>
                                <div>
                                    <?php echo form_button($pwd_btn_forgot); ?>
                                </div>
                                <?= form_close(); ?>
                                <div class="login-helpers">
                                    <a href="#" onClick="swapScreen('login_bg');
                                            return false;">Back to Login</a> <br>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- FORGOT PASSWORD -->
        </section>
        <!--/PAGE -->
        <!-- JAVASCRIPTS -->
        <!-- Placed at the end of the document so the pages load faster -->
        <?= $headerData['login_javascripts']; ?>   
        <script>
            jQuery(document).ready(function () {
                App.setPage("login_bg");  //Set current page
                App.init(); //Initialise plugins and elements


                $("#loginform").validate({
                    rules: {
                        vEmail: {
                            required: true,
                            email: true
                        },
                        vPassword: {
                            required: true,
                            minlength: 5
                        },
                    },
                    messages: {
                        vEmail: "Please enter a Email",
                        vPassword: {
                            required: "Please provide a password",
                            minlength: "Your password must be at least 5 characters long"
                        }

                    }
                });

                $("#forgot-form").validate({
                    rules: {
                        vEmail: {
                            required: true,
                            email: true,
                            remote: {
                                url: '<?= BASEURL; ?>login/checkEmail',
                                type: 'post',
                                data: {}
                            }
                        },
                    },
                    messages: {
                        vEmail: {
                            required: "Please enter a email",
                            email: "Please eneter valid email address",
                            remote: "This email address is not exists"
                        }

                    }
                });


            });
            function swapScreen(id) {
                jQuery('.visible').removeClass('visible animated fadeInUp');
                jQuery('#' + id).addClass('visible animated fadeInUp');
            }

        </script>
        <!-- /JAVASCRIPTS -->

    </body>
</html>
