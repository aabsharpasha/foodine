<?php $headerData = $this->headerlib->data(); ?>
<!doctype html>
<html lang="en-us">
    <head>
        <title>Permission Users</title>
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
                                            <li>Permission Users</li>
                                        </ul>
                                        <div class="clearfix">
                                            <h3 class="content-title pull-left">Permission Users</h3>
                                        </div>
                                        <div class="description">Permission Users Listing</div>
                                    </div>
                                </div>
                            </div>
                            <?= $this->general_model->getMessages(); ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="box border primary">
                                        <div class="box-title">
                                            <h4><i class="fa fa-eye"></i>Permission User List</h4>
                                            <div class="tools">
                                                <a href="<?= BASEURL . $this->controller; ?>/add_user" class="btn btn-xs btn-default">
                                                    <i class="fa fa-plus fa-fw  text-dark"></i> <span class="text-dark">Add New</span>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="box-body">
                                            <table id="datatable" cellpadding="0" cellspacing="0" border="0" class="datatable table  table-bordered ">
                                                <thead>
                                                    <tr>
                                                        <th>User Name</th>
                                                        <th>Email</th>
                                                        <th>Type</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th>User Name</th>
                                                        <th>Email</th>
                                                        <th>Type</th>
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
            url = controller + '/paginate_user';
            $(document).ready(function () {
                var admType = parseInt('<?= $this->session->userdata('ADMINTYPE'); ?>');
                App.setPage("dynamic_table");
                App.init();

                var target = [{
                        "aTargets": [3], // Column to target
                        "mRender": function (data, type, full) {
                            var buttons = '<a title="Edit" href="<?= BASEURL ?>' + controller + '/add_user/' + full['iAdminID'] + '/y"  class="btn btn-primary marginright10 margintop10 btn-xs"><i class="fa fa-pencil-square-o"></i> Edit </a>';
                            buttons += '<button title="Delete" class="btn btn-danger btn-xs marginright10 margintop10"  onclick="return validateRemove(' + full['iAdminID'] + ',' + "'" + controller + "/delete_user'" + ');"><i class="fa fa-times"></i> Delete</button>';
                            if (full['eStatus'] === "Active") {
                                buttons += '<a title="Click here to inactive" id="atag' + full['iAdminID'] + '" onclick="return changeStatus(' + full['iAdminID'] + ',' + "'" + controller + "/status/user/" + full['iAdminID'] + "/y'" + ')" class="btn btn-success btn-xs marginright10 margintop10"><i class="fa fa-check-circle-o"></i> Active</a>';
                            } else {
                                buttons += '<a title="Click here to Active" id="atag' + full['iAdminID'] + '" onclick="return changeStatus(' + full['iAdminID'] + ',' + "'" + controller + "/status/user/" + full['iAdminID'] + "/y'" + ')" class="btn btn-inverse btn-xs marginright10 margintop10"><i class="fa fa-times-circle-o"></i> Inactive</a>';
                            }
                            return buttons;
                        }
                    }];
                var aoculumn = [
                    {"mData": "full_name", "sWidth": "30%"},
                    {"mData": "vEmail", "sWidth": "30%"},
                    {"mData": "admin_type", "sWidth": "10%"},
                    {"mData": "iAdminID", bSortable: false, bSearchable: false, "sWidth": "30%"}
                ];
                var delete_val = controller + '/delete_user';
                getdatatable(delete_val, url, aoculumn, target, 0, 'desc');
            });
        </script>

    </body>