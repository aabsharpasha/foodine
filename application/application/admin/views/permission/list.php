<?php $headerData = $this->headerlib->data(); ?>
<!doctype html>
<html lang="en-us">
    <head>
        <title>Manage Permission</title>
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
                                            <li>Manage Permission</li>
                                        </ul>
                                        <div class="clearfix">
                                            <h3 class="content-title pull-left">Manage Permission</h3>
                                        </div>
                                        <div class="description">Permission Type Listing</div>
                                    </div>
                                </div>
                            </div>
                            <?= $this->general_model->getMessages(); ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="box border primary">
                                        <div class="box-title">
                                            <h4><i class="fa fa-eye"></i>Permission Type List</h4>
                                            <div class="tools">
                                            </div>
                                        </div>
                                        <div class="box-body">
                                            <table id="datatable" cellpadding="0" cellspacing="0" border="0" class="datatable table  table-bordered ">
                                                <thead>
                                                    <tr>
                                                        <th>Type Name</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th>Type Name</th>
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
            url = controller + '/paginate_types';
            $(document).ready(function () {
                App.setPage("dynamic_table");
                App.init();

                var target = [{
                        "aTargets": [1], // Column to target
                        "mRender": function (data, type, full) {
                            var buttons = ' <a title="Edit" href="<?= BASEURL ?>' + controller + '/add_permission/' + full['iAdminTypeID'] + '/y"  class="btn btn-primary marginright10 margintop10 btn-xs"><i class="fa fa-pencil-square-o"></i> Edit </a>';
                            return buttons;
                        },
                    }];
                var aoculumn = [
                    {"mData": "vAdminTitle", "sWidth": "70%"},
                    {"mData": "iAdminTypeID", bSortable: false, bSearchable: false, "sWidth": "30%"}
                ];
                var delete_val = controller + '/delete_types';
                getdatatable(delete_val, url, aoculumn, target, 0, 'desc');
            });
        </script>

    </body>