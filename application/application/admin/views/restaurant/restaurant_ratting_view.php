<?php
$headerData = $this->headerlib->data();
?>
<!doctype html>
<html lang="en-us">
    <head>
        <title><?= $title ?></title>
        <?= $headerData['meta_tags']; ?>
        <?= $headerData['stylesheets']; ?>
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
                            <div id="divtoappend" class="row">
                                <div class="col-sm-12">
                                    <div class="page-header">
                                        <ul class="breadcrumb">
                                            <li>
                                                <i class="fa fa-home"></i>
                                                <a href="<?= BASEURL ?>">Home</a>
                                            </li>
                                            <li><?php echo $this->uppercase; ?></li>
                                        </ul>
                                        <div class="clearfix">
                                            <h3 class="content-title pull-left"><?php echo $this->uppercase; ?> Ratting</h3>
                                        </div>
                                        <div class="description">Restaurants Ratting Listing</div>
                                    </div>
                                </div>
                            </div>
                            <?= $this->general_model->getMessages() ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="box border primary">
                                        <div class="box-title">
                                            <h4><i class="fa fa-table"></i><?php echo $this->uppercase; ?> list</h4>
                                            <div class="tools ">
                                                <a id="fa-refresh" href="javascript:;" class="reload">
                                                    <i class="fa fa-refresh"></i>
                                                </a>
                                                <a href="javascript:;" class="collapse">
                                                    <i class="fa fa-chevron-up"></i>
                                                </a>
                                                <!-- <a href="javascript:;" class="remove">
                                                  <i class="fa fa-times"></i>
                                                </a> -->
                                            </div>
                                        </div>
                                        <div class="box-body">
                                            <table id="datatable" cellpadding="0" cellspacing="0" border="0" class="datatable table  table-bordered ">
                                                <thead>
                                                    <tr>
                                                        <th>Username</th>
                                                        <th>Rate Value</th>
                                                        <th>Rate Comment</th>
                                                        <th>Rate Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th>Username</th>
                                                        <th>Rate Value</th>
                                                        <th>Rate Comment</th>
                                                        <th>Rate Date</th>
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

        <script>
            var oTable, controller = '<?php echo $this->controller; ?>';
            $(document).ready(function() {
                App.setPage("dynamic_table");  //Set current page
                App.init(); //Initialise plugins and elements
                // var target=get_edit_defination (2,'admin');
                var btn_show_hide = parseInt('<?= $ADMINTYPE == 1 ? 1 : 0; ?>');

                var target = [{
                        "aTargets": [1], // Column to target
                        "orderDataType": "dom-hidden", type: 'string',
                        "mRender": function(data, type, full) {
                            var buttons = '';
                            var star = parseInt(full['iRateValue']);
                            for (var i = 0; i < star; i++) {
                                buttons += '<i class="fa fa-star-o fa-1x"></i> ';
                            }
                            buttons += '<input type="hidden" value="' + star + '" />';

                            return buttons;
                        }
                    }];
                var aoculumn = [
                    /*0*/ {"mData": "vFullName", "sWidth": "20%"},
                    /*1*/ {"mData": "iRateValue", "sWidth": "20%"},
                    /*2*/ {"mData": "tRateComment", "sWidth": "35%"},
                    /*2*/ {"mData": "tCreatedAt", "sWidth": "30%"}
                ];
                var delete_val = '';
                getdatatable(delete_val, controller + '/paginate_ratting/' + '<?= $iRestaurantID; ?>', aoculumn, target);
            });
        </script>

    </body>