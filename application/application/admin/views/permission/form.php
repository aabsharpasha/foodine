<?php
$headerData = $this->headerlib->data();
if (isset($getPermissionTypeData) && $getPermissionTypeData != '')
    extract($getPermissionTypeData);


//print_r($getMusicData);

$form_attr = array(
    'name' => 'music-form',
    "id" => "validateForm",
    'method' => 'post',
    'class' => "form-horizontal",
    'role' => 'form',
    'enctype' => 'multipart/form-data'
);

$activestatus = array(
    'name' => 'eStatus',
    'value' => 'Active',
    'checked' => (isset($eStatus) && $eStatus == 'Active') ? 'TRUE' : 'FALSE'
);

$inactivestatus = array(
    'name' => 'eStatus',
    'value' => 'Inactive',
    'checked' => (isset($eStatus) && $eStatus == 'Inactive') ? 'TRUE' : 'FALSE'
);
// Setting Hidden action attributes for Add/Edit functionality.
$hiddeneditattr = array(
    "action" => "backoffice.edit"
);
$hiddenaddattr = array(
    "action" => "backoffice.add"
);
$type_id = array(
    "iAdminTypeID" => (isset($iAdminTypeID) && $iAdminTypeID != '') ? $iAdminTypeID : ''
);
$submit_attr = array(
    'class' => 'submit btn-sm btn btn-primary marginright20',
    'value' => $ACTION_LABEL == 'Edit' ? 'Save Changes' : "$ACTION_LABEL Type",
    'type' => 'submit'
);
$cancel_attr = array(
    'class' => 'btn btn-inverse ',
    'value' => "Reset",
    'type' => 'reset'
);
?>
<!doctype html>
<html lang="en-us">
    <head>
        <title><?= $title ?></title>
        <?= $headerData['meta_tags']; ?>
        <?= $headerData['stylesheets_form']; ?>
        <style type="text/css"> .genderlabel{ display:inline-block; margin-right: 10px; }  .genderlabel .radio{ padding-top: 5px;} </style>
    </head>
    <body>
        <?php $this->load->view('include/header_view') ?>
        <section id="page">
            <!-- SIDEBAR -->
            <?php $this->load->view('include/sidebar_view') ?>
            <!-- /SIDEBAR -->
            <div id="main-content">
                <div class="container">
                    <div class="row">
                        <div id="content" class="col-lg-12">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="page-header">
                                        <ul class="breadcrumb">
                                            <li>
                                                <i class="fa fa-tachometer"></i>
                                                <a href="<?= BASEURL ?>">Home</a>
                                            </li>
                                            <li>Manage Permission</li>
                                        </ul>
                                        <div class="clearfix">
                                            <h3 class="content-title pull-left">Manage Permission</h3>
                                        </div>
                                        <div class="description">Add/Edit Permission</div>
                                    </div>
                                </div>
                            </div>
                            <?= $this->general_model->getMessages() ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- BOX -->
                                    <div class="box border primary">
                                        <div class="box-title">
                                            <h4><i class="fa fa-plus-circle"></i><?php echo $ACTION_LABEL . " Permission Type"; ?></h4>
                                            <div class="tools hidden-xs">
                                                <a href="javascript:void(0);" class="collapse">
                                                    <i class="fa fa-chevron-up"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="box-body big">
                                            <?php
                                            echo form_open("permission/add_permission", $form_attr);
                                            if (isset($iAdminTypeID) && $iAdminTypeID != '') {
                                                echo form_hidden($type_id);
                                                echo form_hidden($hiddeneditattr);
                                            } else {
                                                echo form_hidden($hiddenaddattr);
                                            }
                                            ?>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Type Name <span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <label class="form-control maxwidth500"><?php echo (isset($vAdminTitle) && $vAdminTitle != '') ? $vAdminTitle : ''; ?></label>
                                                </div>
                                            </div> <br/> <br/>

                                            <div class="form-group">
                                                <table class="table table-bordered table-hover">
                                                    <tr>
                                                        <th rowspan="2" class="text-left" style="vertical-align: middle;">Page</th>
                                                        <th colspan="7">Action</th>
                                                    </tr>
                                                    <tr>
                                                        <th>Insert</th>
                                                        <th>Update</th>
                                                        <th>Delete</th>
                                                        <th>Status</th>
                                                        <th>View</th>
                                                        <th>No Access</th>
                                                        <th>All Access</th>
                                                    </tr>
                                                    <?php
                                                    $allPage = getPagePermission($admin_type_id);
                                                    //echo '<pre>';
                                                    //mprd($allPage);
                                                    //exit;
                                                    //$allPage = array();
                                                    foreach ($allPage as $pkey => $pval) {
                                                        ?>
                                                        <tr>
                                                            <td class="text-left"><?= $pval['module'] . ': <strong>' . $pval['title'] . '</strong>'; ?></td>
                                                            <?php foreach ($pval['action'] AS $perkey => $perval) { ?>
                                                                <td>
                                                                    <input type="checkbox" class="action_<?= $pval['id']; ?>" id="action_<?= $pval['id'] . '_' . $perval['actionId']; ?>" onclick="checkuncheck(<?= $pval['id']; ?>, <?= $perval['actionId']; ?>)" value="1" name="permission[<?= $pval['id']; ?>][<?= $perval['actionId']; ?>]" <?= $perval['actionPermission'] ? 'checked="checked"' : '' ?>/>
                                                                </td>
                                                            <?php } ?>
                                                        </tr>
                                                    <?php } ?>

                                                </table>
                                            </div>

                                            <div class="form-group">
                                                <div class="margin0auto disptable">
                                                    <?php echo form_input($submit_attr); ?>
                                                    <a class="btn btn-sm btn-grey" href="<?= BASEURL .''. $this->controller; ?>/manage">Cancel</a>
                                                </div>
                                            </div>
                                            <?php echo form_close(); ?>
                                        </div>
                                    </div>
                                    <!-- /BOX -->
                                </div>
                            </div>
                            <?php $this->load->view('include/footer_view') ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?= $headerData['javascript_form']; ?>
        <script>
            function checkuncheck(page_id, action_id) {
                var is_checked = $('#action_' + page_id + '_' + action_id).is(':checked');

                if (action_id == 7) {
                    if (is_checked) {
                        $('.action_' + page_id).prop('checked', true);
                        $('#action_' + page_id + '_' + action_id).prop('checked', true);
                        $('#action_' + page_id + '_6').prop('checked', false);
                    } else {
                        $('.action_' + page_id).prop('checked', false);
                        $('#action_' + page_id + '_6').prop('checked', true);
                    }
                } else if (action_id == 6) {
                    if (is_checked) {
                        $('.action_' + page_id).prop('checked', false);
                        $('#action_' + page_id + '_' + action_id).prop('checked', true);
                    } else {
                        $('.action_' + page_id).prop('checked', true);
                        $('#action_' + page_id + '_' + action_id).prop('checked', true);
                        $('#action_' + page_id + '_6').prop('checked', false);
                    }
                } else {
                    individualCheck(page_id);
                }
            }
            function individualCheck(page_id) {
                var counter = new Array();
                counter['y'] = counter['n'] = 0;
                for (var i = 1; i <= 5; i++) {
                    var is_true = $('#action_' + page_id + '_' + i).is(':checked');
                    if (is_true)
                        counter['y']++;
                    else
                        counter['n']++;
                }

                if (counter['y'] < 5) {
                    $('#action_' + page_id + '_7').prop('checked', false);
                    $('#action_' + page_id + '_6').prop('checked', false);
                }
                if (counter['y'] == 5) {
                    $('#action_' + page_id + '_7').prop('checked', true);
                    $('#action_' + page_id + '_6').prop('checked', false);
                }
                if (counter['n'] == 5) {
                    $('.action_' + page_id).prop('checked', false);
                    $('#action_' + page_id + '_6').prop('checked', true);
                }
            }
            jQuery(document).ready(function () {
                App.setPage("forms");
                App.init();
                $("#validateForm").validate({
                    rules: {
                        vAdminTitle: {
                            required: true
                        },
                    },
                    messages: {
                        vAdminTitle: "Please enter a Type Name",
                    }
                });
                $('.uniform').uniform();
            });
        </script>
    </body>
</html>
