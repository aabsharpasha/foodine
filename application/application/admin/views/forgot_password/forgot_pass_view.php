<?php
//$headerData['doctype'] = 'Transitional';
$headerData = $this->headerlib->data();

/* * ***************************************************
 * *			DEFINE FORM ATTRIBUTES
 * *************************************************** */


$form_attr = array(
    'name' => 'forgot-form',
);

$email_attr = array(
    'name' => 'vAdminEmail',
    'id' => 'vAdminEmail',
    "autofocus" => "autofocus",
    "required" => "required",
    "data-errortext" => "Please Enter Your Email Address!"
);

$pwd_btn = array(
    'class' => 'fr submit',
    'content' => 'Forgot Password'
);

$back_btn = array(
    'class' => 'fr reset',
    'content' => 'Back',
    'onclick' => "window.location.href = BASEURL"
);
?>
<!doctype html>
<html lang="en-us">
    <head>

        <title><?= $title ?></title>
        <?= $headerData['meta_tags']; ?>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=PT+Sans:regular,bold">
        <?= $headerData['stylesheets']; ?>
        <link rel="apple-touch-icon-precomposed" href="apple-touch-icon-precomposed.png">

        <?= $headerData['login_javascripts']; ?>		
    </head>

    <body id="login">

        <header>
            <div id="logo">
                <?php echo anchor('forgot_pass', MAINTITLE . ' Forgot Password'); ?>
            </div>
        </header>

        <section id="content">
            <?= $this->general_model->getMessages() ?>
            <?= form_open('forgotpass', $form_attr) ?>
            <fieldset>
                <section>
                    <?php echo form_label('Enter Your Email Address'); ?>
                    <div>
                        <?php
                        echo form_input($email_attr);
                        ?>
                    </div>
                </section>
                <section>
                    <div>
                        <?php echo form_button($pwd_btn); ?>
                        <?php echo form_button($back_btn); ?>
                    </div>
                </section>
            </fieldset>
            <?= form_close(); ?>
        </section>
        <footer><?= FOOTER ?></footer>

    </body>
</html>