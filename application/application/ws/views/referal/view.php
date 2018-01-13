<?php
$headerData = $this->headerlib->data();
$ADMINTYPE = $this->session->userdata('ADMINTYPE');
$permission = get_page_permission($ADMINTYPE, 38);
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
                                        <div class="description">Reference Code History</div>
                                    </div>
                                </div>
                            </div>
                            <?= $this->general_model->getMessages() ?>
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
                                                        <th>Username</th>
                                                        <th>Reference Code</th>
                                                        <th>Used Code</th>
                                                        <th>Created On</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th>Username</th>
                                                        <th>Reference Code</th>
                                                        <th>Used Code</th>
                                                        <th>Created On</th>
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
            var oTable, controller = '<?php echo $this->controller; ?>';
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

                var target = [
                    {
                        "aTargets": [4], // Column to target
                        "mRender": function (data, type, full) {
                            /*<a title="Edit" href="<?= BASEURL ?>' + controller + '/add/' + data + '/y"  class="btn btn-primary marginright10 "><i class="fa fa-pencil-square-o"></i> Edit </a>\n\*/
                            var buttons = '';
                            if (permission.indexOf('2') >= 0) {
                                buttons += ' <a title="Edit" href="<?= BASEURL ?>' + controller + '/add/' + full['referalId'] + '/y"  class="btn btn-xs btn-primary marginright10 margintop10 "><i class="fa fa-pencil-square-o"></i> Edit </a>';
                            }
                            if (permission.indexOf('3') >= 0) {
                                buttons += '<button title="Delete" class="btn btn-danger btn-xs marginright10 margintop10"  onclick="return validateRemove(' + full['referalId'] + ',' + "'" + controller + "/deleteAll'" + ');"><i class="fa fa-times"></i> Delete</button>';
                            }

                            if (permission.indexOf('4') >= 0) {
                                if (full['status'] === "active") {
                                    buttons += '<a title="Click here to inactive" id="atag' + full['referalId'] + '" onclick="return changeStatus(' + full['referalId'] + ',' + "'" + controller + "/status/" + full['referalId'] + "/y'" + ')"  class="btn btn-success  btn-xs marginright10 margintop10"><i class="fa fa-check-circle-o"></i> Active </a>';
                                } else {
                                    buttons += '<a title="Click here to Active" id="atag' + full['referalId'] + '" onclick="return changeStatus(' + full['referalId'] + ',' + "'" + controller + "/status/" + full['referalId'] + "/y'" + ')"  class="btn btn-inverse btn-xs marginright10 margintop10"><i class="fa fa-times-circle-o "></i> Inactive </a>';
                                }
                            }

                            return buttons;
                        }
                    }
                ];
                var aoculumn = [
                    /*0*/ {"mData": "referalName", "sWidth": "25%"},
                    /*1*/ {"mData": "referalCode", "sWidth": "25%"},
                    /*1*/ {"mData": "referalCounter", "sWidth": "10%"},
                    /*1*/ {"mData": "createdDate", "sWidth": "20%"},
                    /*1*/ {"mData": "referalId", "sWidth": "20%"},
                ];
                getdatatable(controller + '/deleteAll', url, aoculumn, target, 1, 'desc');
            });

        </script>

    </body>