<?php
$headerData = $this->headerlib->data();
$ADMINTYPE = $this->session->userdata('ADMINTYPE');
$permission = get_page_permission($ADMINTYPE, 79);
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
                                            <h3 class="content-title pull-left"><?php echo $this->uppercase; ?> Meta Tags</h3>
                                        </div>
                                        <div class="description">Static Meta Tags Listing</div>
                                    </div>
                                </div>
                            </div>
                            <?= $this->general_model->getMessages(); ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="box border primary">
                                        <div class="box-title">
                                            <h4><i class="fa fa-eye"></i><?php echo $this->uppercase; ?> Static Meta Tags List</h4>
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
                                                        <th>Page</th>
                                                        <th>Page Title</th>
                                                        <th>Meta Title</th>
                                                        <th>Meta Description</th>
                                                        <th>Meta Keywords</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th>Page</th>
                                                        <th>Page Title</th>
                                                        <th>Meta Title</th>
                                                        <th>Meta Description</th>
                                                        <th>Meta Keywords</th>
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
            var oTable, controller = '<?php echo $this->controller; ?>';
            var url;
            url = controller + '/paginateStaticMetaTags';
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

                var target = [
                    {
                        "aTargets": [5], // Column to target
                        "mRender": function (data, type, full) {
                            var buttons = ' <a title="Edit" href="<?= BASEURL; ?>' + controller + '/addStaticMetaTags/' + full['iMetaTagId'] + '"  class="btn btn-sm btn-primary marginright10 margintop10 "><i class="fa fa-pencil-square-o"></i> Edit </a>';
                            return buttons;
                        }
                    }
                ];
                var aoculumn = [
                    /*0*/ {"mData": "vPageName", "sWidth": "20%"},
                    /*1*/ {"mData": "vPageTitle", "sWidth": "15%"},
                    /*2*/ {"mData": "vMetaTitle", "sWidth": "15%"},
                    /*3*/ {"mData": "vMetaDescription", "sWidth": "20%"},
                    /*4*/ {"mData": "vMetaKeywords", "sWidth": "20%"},
                    /*5*/ {"mData": "iMetaTagId", bSortable: false, bSearchable: false, "sWidth": "10%"}
                ];
                var delete_val =  '';
                getdatatable(delete_val, url, aoculumn, target, 0, 'asc');
            });

        </script>

    </body>