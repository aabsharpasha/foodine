<?php
$headerData = $this->headerlib->data();
$ADMINTYPE = $this->session->userdata('ADMINTYPE');
$permission = get_page_permission($ADMINTYPE, 75);
?>
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
                                        <div class="description">Suggested</div>
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
                                                        <th>Restaurant</th>
                                                        
                                                        
                                                        <th>Restaurant Address</th>
                                                        <th>Customer Name</th>
                                                        
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                                <tfoot>
                                                    <tr>
                                                       <th>Restaurant</th>
                                                        <th>Restaurant Address</th>
                                                        <th>Customer Name</th>
<!--                                                        <th>Category</th>
                                                        <th>Cuisine</th>-->
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
            var oTable, controller = '<?php echo $this->controller; ?>';
            var url;
            url = controller + '/paginate_suggested';
            $(document).ready(function () {
                var permission = <?= json_encode($permission); ?>;
                if (permission.indexOf('6') >= 0) {
                    alert('You don\'t have a permission to access this page');
                    window.location.href = '<?= BASEURL; ?>';
                }

                App.setPage("dynamic_table");  //Set current page
                App.init(); //Initialise plugins and elements
                // var target=get_edit_defination (2,'admin');
                //$('#datatable').DataTable();

                var btn_show_hide = parseInt('<?= ($ADMINTYPE == 1 || $ADMINTYPE == 2) ? 1 : 0; ?>');

                var target = [];
                var aoculumn = [
                    /*1*/ {"mData": "restaurantName", "sWidth": "20%"},
                    /*2*/ {"mData": "address", "sWidth": "20%"},
                    /*3*/ {"mData": "customerName", "sWidth": "20%"},
                    /*4*/ //{"mData": "category", "sWidth": "20%"},
                    /*5*/ //{"mData": "cuisine", "sWidth": "20%"},
                ];
                var delete_val = btn_show_hide == 1 ? controller + '/deleteAll' : '';
                getdatatable(delete_val, url, aoculumn, target, 2, 'desc');

                $('#ToolTables_datatable_6').on('click', function () {
                    $('#datatable tbody').children().each(function () {
                        var $tr = $(this);
                        if (!$tr.hasClass('row_selected')) {
                            $tr.addClass('row_selected');
                        }
                    });
                });
                $('#ToolTables_datatable_7').on('click', function () {
                    $('#datatable tbody').children().each(function () {
                        var $tr = $(this);
                        if ($tr.hasClass('row_selected')) {
                            $tr.removeClass('row_selected');
                        }
                    });
                });

            });

        </script>

    </body>