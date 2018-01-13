<?php
$headerData = $this->headerlib->data();
$ADMINTYPE = $this->session->userdata('ADMINTYPE');
$permission = get_page_permission($ADMINTYPE, 80);
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
                                            <h3 class="content-title pull-left">Menu Item Category</h3>
                                        </div>
                                        <div class="description">Menu Item Category</div>
                                    </div>
                                </div>
                            </div>
                            <?= $this->general_model->getMessages(); ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="box border primary">
                                        <div class="box-title">
                                            <h4><i class="fa fa-eye"></i>Menu Item Category List</h4>
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
                                                        <th>Category Name</th>
                                                        <th>Created On</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th>Category Name</th>
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
        <?php $ADMINTYPE = $this->session->userdata('ADMINTYPE'); ?>
        <script src="<?= JS_URL; ?>js/bootbox/bootbox.min.js"></script>
        <script>
            var oTable, controller = '<?php echo $this->controller; ?>';
            var url;
            url = controller + '/paginateMenuItemCategory';
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
                        "aTargets": [2], // Column to target
                        "mRender": function (data, type, full) {
                            var buttons = ' <a title="Edit" href="<?= BASEURL; ?>' + controller + '/addMenuItemCategory/' + full['iItemCategoryId'] + '/y"  class="btn btn-sm btn-primary marginright10 margintop10 "><i class="fa fa-pencil-square-o"></i> Edit </a>';
                            buttons += '\n <button title="Delete" class="btn btn-sm btn-danger marginright10 margintop10"  onclick="return validateRemove(' + full['iItemCategoryId'] + ',' + "'" + controller + "/deleteMenuItemCategory'" + ');"><i class="fa fa-times"></i> Delete</button>';
                            if (full['eStatus'] == "Active") {
                                buttons += '<a title="Click here to inactive" id="atag' + full['iItemCategoryId'] + '" onclick="return changeStatus(' + full['iItemCategoryId'] + ',' + "'" + controller + "/menuItemCategoryStatus/" + full['iItemCategoryId'] + "/y'" + ')"  class="btn btn-sm btn-success  marginright10 margintop10"><i class="fa fa-check-circle-o"></i> Active </a>'
                            } else {
                                buttons += '<a title="Click here to Active" id="atag' + full['iItemCategoryId'] + '" onclick="return changeStatus(' + full['iItemCategoryId'] + ',' + "'" + controller + "/menuItemCategoryStatus/" + full['iItemCategoryId'] + "/y'" + ')"  class="btn btn-sm btn-inverse  marginright10 margintop10"><i class="fa fa-times-circle-o "></i> Inactive </a>'
                            }
                            return buttons;
                        }
                    }
                ];
                var aoculumn = [
                    /*5*/ {"mData": "vName", "sWidth": "10%"},
                    /*6*/ {"mData": "createdAt", "sWidth": "10%"},
                    /*7*/ {"mData": "iItemCategoryId", bSortable: false, bSearchable: false, "sWidth": "10%"}
                ];
                var delete_val =  controller + '/deleteMenuItemCategory';
                getdatatable(delete_val, url, aoculumn, target, 0, 'asc');

            });

        </script>

    </body>