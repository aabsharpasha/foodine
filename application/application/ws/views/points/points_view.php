<?php
$headerData = $this->headerlib->data();
$ADMINTYPE = $this->session->userdata('ADMINTYPE');
$permission = get_page_permission($ADMINTYPE, 30);
?>
<!doctype html>
<html lang="en-us">
    <head>
        <title><?= $title ?></title>
        <?= $headerData['meta_tags']; ?>
        <?= $headerData['stylesheets']; ?>

    </head>

    <style type="text/css">
        .modal-body { display:inline; }.delete_comment { position: absolute; right:0px; top:5px; }
    </style>
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
                            <div id="divtoappend" class="row">
                                <div class="col-sm-12">
                                    <div class="page-header">
                                        <ul class="breadcrumb">
                                            <li>
                                                <i class="fa fa-tachometer"></i>
                                                <a href="<?= BASEURL ?>">Home</a>
                                            </li>
                                            <li><?php echo $this->uppercase; ?></li>
                                        </ul>
                                        <div class="clearfix">
                                            <h3 class="content-title pull-left"><?php echo $this->uppercase; ?></h3>
                                        </div>
                                        <div class="description">Points Listing</div>
                                    </div>
                                </div>
                            </div>
                            <?= $this->general_model->getMessages(); ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="box border primary">
                                        <div class="box-title">
                                            <h4><i class="fa fa-eye"></i><?php echo $this->uppercase; ?> List</h4>
                                            <div class="tools ">
                                                <a id="fa-refresh" href="javascript:;" class="reload">
                                                    <i class="fa fa-refresh"></i>
                                                </a>
                                                <a href="javascript:;" class="collapse">
                                                    <i class="fa fa-chevron-up"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="box-body">
                                            <table id="datatable" cellpadding="0" cellspacing="0" border="0" class="datatable table  table-bordered ">
                                                <thead>
                                                    <tr>
                                                        <th>Points For</th>
                                                        <th>Points</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th>Points For</th>
                                                        <th>Points</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
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
        <?php $ADMINTYPE = $this->session->userdata('ADMINTYPE'); ?>
        <script src="<?= JS_URL; ?>js/bootbox/bootbox.min.js"></script>
        <script>
            var oTable, controller = '<?php echo $this->controller; ?>', imagepath = '<?php echo REWARD_IMAGE_PATH ?>', no_img_url = '<?php echo DOMAIN_URL; ?>/admin/img/no-image.png';
            var url;
            url = controller + '/paginate';
            $(document).ready(function () {
                var permission = <?= json_encode($permission); ?>;
                if (permission.indexOf('6') >= 0) {
                    alert('You don\'t have a permission to access this page');
                    window.location.href = '<?= BASEURL; ?>';
                }

                App.setPage("dynamic_table");  //Set current page
                App.init(); //Initialise plugins and elements
                // var target=get_edit_defination (2,'admin');
                var btn_show_hide = parseInt('<?= $ADMINTYPE == 1 ? 1 : 0; ?>');

                var target = [{
                        "aTargets": [2], // Column to target
                        "mRender": function (data, type, full) {
                            var buttons = '';
                            if (permission.indexOf('2') >= 0) {
                                buttons = ' <a title="Edit" href="<?= BASEURL; ?>' + controller + '/add/' + full['iUserPointSystemID'] + '/y"  class="btn btn-primary marginright10 margintop10 btn-sm"><i class="fa fa-pencil-square-o"></i> Edit </a>';
                            }
                            return buttons;
                        },
                    }];
                var aoculumn = [
                    /*0*/ {"mData": "vType", "sWidth": "25%"},
                    /*0*/ {"mData": "iPoints", "sWidth": "25%"},
                    /*4*/ {"mData": "iUserPointSystemID", bSortable: false, bSearchable: false, "sWidth": "20%"}
                ];
                var delete_val = btn_show_hide === 1 ? controller + '/deleteAll' : '';
                getdatatable(delete_val, url, aoculumn, target, 0, 'desc');
            });
        </script>
    </body>