<?php $headerData = $this->headerlib->data(); ?>
<!doctype html>
<html lang="en-us">
    <head>
        <title><?= $title ?></title>
        <?= $headerData['meta_tags']; ?>
        <?= $headerData['stylesheets']; ?>
    </head>
    <style type="text/css">
        .modal-body{
            display:inline;
        }
        .delete_comment{
            position: absolute;
            right:0px;
            top:5px;
        }
    </style>
    <body>
        <?php $this->load->view('include/header_view'); ?>
        <section id="page">
            <!-- SIDEBAR -->
            <?php $this->load->view('include/sidebar_view'); ?>
            <!-- /SIDEBAR -->
            <div id="main-content">
                <div class="container">
                    <div class="row">
                        <div id="content" class="col-lg-12">
                            <div id="divtoappend" class="row">
                                <div class="col-sm-12">
                                    <div class="page-header">
                                        <ul class="breadcrumb">
                                            <li>
                                                <i class="fa fa-tachometer"></i>
                                                <a href="<?= BASEURL ?>">Home</a>
                                            </li>
                                            <li>Notification Listing</li>
                                        </ul>
                                        <div class="clearfix">
                                            <h3 class="content-title pull-left">Notification</h3>
                                        </div>
                                        <div class="description">Notification List</div>
                                    </div>
                                </div>
                            </div>
                            <?= $this->general_model->getMessages() ?>
                            <div class="row">
                                <div class="col-lg-12" style="min-height: 768px;">

                                    <?php
                                    $notification = array_values($notification);
                                    if (count($notification) > 0) {
                                        for ($i = 0; $i < count($notification); $i++) {
                                            if (isset($notification[$i]) && !empty($notification[$i])) {
                                                $notify = $notification[$i];
                                                $messageValue = '<i>' . $notify['restaurantName'] . '</i>';
                                              //  $messageValue .= ' ' . $notify['tableString'];
                                                $messageValue .= ' <i>' . $notify['recordField'] . '</i>';
                                               // $messageValue .= ' ' . $notify['fromValue'] . '.';
                                                ?>
                                                <div class="notify-container background-fff">
                                                    <div class="notify-message"><?= $messageValue; ?><div><i class="fa fa-clock-o"></i> <?= $notify['notifyDate']; ?></div></div>
                                                    <hr class="margin0auto"/>
<!--                                                    <div class="notify-action margin-top-10">
                                                        <?php if ($notify['notifyAction'] == 'pending') { ?>
                                                            <button class="btn btn-warning btn-xs notify-btn" 
                                                                    data-type="yes" 
                                                                    data-id="<?= $notify['recordId']; ?>" 
                                                                    data-target="<?= $notify['notifyId']; ?>" 
                                                                    data-action="<?= $notify['activityId']; ?>">
                                                                <i class="fa fa-check"></i> Accept</button>
                                                            <button class="btn btn-danger btn-xs notify-btn" 
                                                                    data-type="no" 
                                                                    data-target="<?= $notify['notifyId']; ?>" 
                                                                    data-id="<?= $notify['recordId']; ?>" 
                                                                    data-action="<?= $notify['activityId']; ?>">
                                                                <i class="fa fa-times"></i> Decline</button>


                                                            <?php
                                                            if (in_array($notify['activityId'], array(1, 2))) {
                                                                $redirectURL = BASEURL . 'restaurant/add/' . $notify['restaurantId'] . '/y';
                                                                if ($notify['activityId'] == 2) {
                                                                    $redirectURL = BASEURL . 'deals/add/' . $notify['recordId'] . '/y';
                                                                }
                                                                ?> 
                                                                <a class="btn btn-success btn-xs" href="<?= $redirectURL; ?>"><i class="fa fa-pencil fa-fw"></i> Edit</a>
                                                            <?php } ?>
                                                            <?php
                                                        } else {
                                                            echo 'You have ' . ($notify['notifyAction'] == 'yes' ? 'accept' : 'decline') . ' this request.';
                                                        }
                                                        ?>
                                                    </div>-->
                                                </div>
                                                <?php
                                            }
                                        }
                                    } else {
                                        ?>
                                        <div class="notify-container background-fff no-notification">
                                            <div class="notify-message">No Notification available.</div>
                                        </div>
                                        <?php
                                    }
                                    ?>

                                    <div class="col-lg-12 padding0">

                                    </div>

                                </div>
                            </div>
                            <?php $this->load->view('include/footer_view') ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?= $headerData['javascript_view']; ?>
        <script type="text/javascript">
            $(function () {
                App.init();
                $('.notify-btn').live('click', function () {
                    if (confirm('Do you want to take the action?')) {
                        var $btn = $(this);
                        var id = $btn.data('id');
                        var action = $btn.data('action');
                        var type = $btn.data('type');
                        var target = $btn.data('target');

                        /*MAKE A AJAX CALL*/
                        $.ajax({
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                id: id,
                                action: action,
                                type: type,
                                target: target
                            },
                            url: BASEURL + 'notification/update',
                            success: function (resp) {
                                if (resp.STATUS == 200) {
                                    $btn.parent().html(resp.MSG);
                                }
                            }
                        });
                    }
                });
                /*MAKE THEM READABLE*/
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    data: {},
                    url: BASEURL + 'notification/status',
                    success: function (resp) {
                        if (resp.STATUS == 200) {
                            $('.notification-li').remove();
                            var li = '<li class="no-notification"><a href="javascript:void(0);"><span class="label label-success"><i class="fa fa-flag"></i></span><span class="body"><span class="message">No Notification available.</span></span></a></li>';
                            if ($('.no-notification').length <= 0)
                                $('.dropdown-title').after(li);
                            $('.badge').html('');
                            $('#badge-count').html('<i class="fa fa-bell"></i> 0 Notification');
                        }
                    }
                });
            });
        </script>
    </body>