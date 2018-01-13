<?php
$headerData = $this->headerlib->data();
//extract($data);
/* * ***************************************************



 * *      DEFINE FORM ATTRIBUTES



 * *************************************************** */

$form_attr = array(
    'name' => 'reset-form',
    'id' => 'reset-form',
    'class' => 'form-horizontal row-fluid',
    'role' => 'form'
);



$new_pass_attr = array(
    'name' => 'vPassword',
    'id' => 'inputPassword',
    "autofocus" => "autofocus",
    "required" => "required",
    "class" => "form-control paddingtopbottom5",
    "data-errortext" => "This is your New Password!",
    "placeholder" => "Your Password",
    "type" => "password"
);


$hidden_attr = array(
    'admin_id' => $id
);

$btn_attr = array(
    'class' => 'btn btn-info',
    'type' => 'submit',
    'content' => 'Reset Password'
);
?>



<!doctype html>



<html lang="en-us">



    <head>



        <title><?= $title ?></title>



        <?= $headerData['meta_tags']; ?>

  <!-- STYLESHEETS --><!--[if lt IE 9]><script src="js/flot/excanvas.min.js"></script><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><script src="http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script><![endif]-->
        <?= $headerData['login_stylesheets']; ?>

        <style>
            label
            {
                color: black;
            }
            h2
            {
                color: black;
            }
        </style>

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
                                <a href="index.html">

                                    <img src="<?= BASEURL ?>img/logo/logo.png" alt="logo name" />
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <!--/NAV-BAR -->
            </header>
            <!--/HEADER -->


            <!-- FORGOT PASSWORD -->
            <section>
                <div class="container">
                    <div class="row">
                        <div class="col-md-4 col-md-offset-4">
                            <div class="login-box">
                                <h2 class="bigintro">New Password</h2>
                                <div class="divide-40"></div>
                                <?= form_open('forgot_pass/reset_pass', $form_attr) ?>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Enter your Email address</label>
                                    <i class="fa fa-envelope"></i>
                                    <?php
                                    echo form_password($new_pass_attr);
                                    echo form_hidden($hidden_attr);
                                    ?>
                                </div>
                                <div>
                                    <?php echo form_button($btn_attr); ?>
                                </div>
                                <?= form_close(); ?>

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
            jQuery(document).ready(function() {
                App.setPage("login_bg");  //Set current page
                App.init(); //Initialise plugins and elements



                $("#reset-form").validate({
                    rules: {
                        vPassword: {
                            required: true,
                            minlength: 5
                        },
                    },
                    messages: {
                        vPassword: "Please enter a Password"

                    }
                });


            });

        </script>
        <!-- /JAVASCRIPTS -->

    </body>


</html>