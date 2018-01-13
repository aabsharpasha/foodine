<?php
$headerData = $this->headerlib->data();

$ADMINTYPE = $this->session->userdata('ADMINTYPE');
$permission = get_page_permission($ADMINTYPE, 46);
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
                                                <i class="fa fa-tachometer"></i>
                                                <a href="<?= BASEURL ?>">Home</a>
                                            </li>
                                            <li>Content</li>
                                        </ul>
                                        <div class="clearfix">
                                            <h3 class="content-title pull-left">Content Management</h3>
                                        </div>
                                        <div class="description">Content Listing</div>
                                    </div>
                                </div>
                            </div>
                            <?= $this->general_model->getMessages() ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="box border primary">
                                        <div class="box-title">
                                            <h4><i class="fa fa-eye"></i>Content List</h4>
                                            <div class="tools hidden-xs">
                                                <a href="javascript:;" class="reload">
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
                                                        <th>Page Name</th>
                                                        <th>Page Content</th>
                                                        <th>Last Updated</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th>Page Name</th>
                                                        <th>Page Content</th>
                                                        <th>Last Updated</th>
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

        <script>
            jQuery(document).ready(function () {
                App.setPage("dynamic_table");  //Set current page
                App.init(); //Initialise plugins and elements
            });
        </script>
        <script>
            $(document).ready(function () {
                var permission = <?= json_encode($permission); ?>;
                if (permission.indexOf('6') >= 0) {
                    alert('You don\'t have a permission to access this page');
                    window.location.href = '<?= BASEURL; ?>';
                }

                var oTable = $('#datatable').dataTable({
                    "sPaginationType": "bs_full",
                    "bJQueryUI": true,
                    sDom: "<'row'<'dataTables_header  clearfix'<'col-md-4'lC><'col-md-8'TRf>r>>t<'row'<'dataTables_footer clearfix'<'col-md-6'i><'col-md-6'p>>> ",
                    "bStateSave": true,
                    oTableTools: {
                        "sRowSelect": "multi",
                        "aButtons": [
                            {
                                "sExtends": "copy",
                                "sButtonText": "copy",
                                "mColumns": "visible"
                            },
                            {
                                "sExtends": "print",
                                "sButtonText": "print",
                                "mColumns": "visible"
                            },
                            {
                                "sExtends": "csv",
                                "sButtonText": "csv",
                                "mColumns": "visible"
                            },
                            {
                                "sExtends": "xls",
                                "sButtonText": "xls",
                                "mColumns": "visible"
                            },
                            {
                                "sExtends": "pdf",
                                "sButtonText": "pdf",
                                "mColumns": "visible"
                            }
                        ],
                        sSwfPath: BASEURL + "js/datatables/extras/TableTools/media/swf/copy_csv_xls_pdf.swf"
                    },
                    "bProcessing": true,
                    "bServerSide": true,
                    "sAjaxSource": BASEURL + "pagecontent/paginate",
                    "sServerMethod": "POST",
                    "aoColumns": [
                        {"mData": "vPageTitle", "sWidth": "25%"},
                        {"mData": "tContent", "sWidth": "35%"},
                        {"mData": "tModifiedAt", "sWidth": "20%"},
                        {"mData": "iPageID", "sWidth": "20%", bSortable: false}
                    ],
                    "aoColumnDefs": [
                        {
                            "aTargets": [3], // Column to target

                            "mRender": function (data, type, full)
                            {
                                return (permission.indexOf('2') >= 0) ? '<a  href="<?= BASEURL ?>pagecontent/add/' + data + '/y" class="btn btn-primary btn-sm marginright10 "><i class="fa fa-pencil-square-o"></i>Edit</a>' : '';
                            }
                        }],
                    "oLanguage": {
                        "sSearch": "Search:"
                    },
                    "bSortCellsTop": true
                            //"fnServerData": fnDataTablesPipeline
                });

                var $headerDOM = $('#datatable_wrapper').find('.dataTables_header');
                $headerDOM.parent().switchClass('row', 'container');

                var $footerDOM = $('#datatable_wrapper').find('.dataTables_footer');
                $footerDOM.parent().switchClass('row', 'container');

                $('#datatable').css('width', 'auto');



            });
        </script>
    </body>


