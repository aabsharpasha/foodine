<?php
$headerData = $this->headerlib->data();
$ADMINTYPE = $this->session->userdata('ADMINTYPE');
$permission = get_page_permission($ADMINTYPE, 81);
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
                                            <h3 class="content-title pull-left">Menu Items</h3>
                                        </div>
                                        <div class="description">Menu Item Listing</div>
                                    </div>
                                </div>
                            </div>
                            <?= $this->general_model->getMessages(); ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="box border primary">
                                        <div class="box-title">
                                            <h4><i class="fa fa-eye"></i>Menu Items List</h4>
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
                                                        <th>Restaurant Name</th>
                                                        <th>Item Name</th>
                                                        <th>Item Description</th>
                                                        <th>Item Price</th>
                                                        <th>Item Image</th>
                                                        <th>Item Category</th>
                                                        <th>Meal Type</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th>Restaurant Name</th>
                                                        <th>Item Name</th>
                                                        <th>Item Description</th>
                                                        <th>Item Price</th>
                                                        <th>Item Image</th>
                                                        <th>Item Category</th>
                                                        <th>Meal Type</th>
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
            var imagepath   = '<?php echo IMGURL . '/orderMenuItem/'?>';
            url = controller + '/paginateMenuItems';
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
                        "aTargets": [7], // Column to target
                        "mRender": function (data, type, full) {
                            var buttons = ' <a title="Edit" href="<?= BASEURL; ?>' + controller + '/addMenuItem/' + full['iItemId'] + '/y"  class="btn btn-sm btn-primary marginright10 margintop10 "><i class="fa fa-pencil-square-o"></i> Edit </a>';
                            buttons += '\n <button title="Delete" class="btn btn-sm btn-danger marginright10 margintop10"  onclick="return validateRemove(' + full['iItemId'] + ',' + "'" + controller + "/deleteMenuItems'" + ');"><i class="fa fa-times"></i> Delete</button>';
                            if (full['eStatus'] == "Active") {
                                buttons += '<a title="Click here to inactive" id="atag' + full['iItemId'] + '" onclick="return changeStatus(' + full['iItemId'] + ',' + "'" + controller + "/menuItemStatus/" + full['iItemId'] + "/y'" + ')"  class="btn btn-sm btn-success  marginright10 margintop10"><i class="fa fa-check-circle-o"></i> Active </a>'
                            } else {
                                buttons += '<a title="Click here to Active" id="atag' + full['iItemId'] + '" onclick="return changeStatus(' + full['iItemId'] + ',' + "'" + controller + "/menuItemStatus/" + full['iItemId'] + "/y'" + ')"  class="btn btn-sm btn-inverse  marginright10 margintop10"><i class="fa fa-times-circle-o "></i> Inactive </a>'
                            }
                            return buttons;
                        }
                    },
                    {
                        "aTargets": [4], // Column to target
                        "mRender": function (data, type, full) {
                            if (full['vItemImage'] != '') {
                                return '<a class="fancybox" rel="group" href="' + imagepath + full['iItemId'] + '/' + full['vItemImage'] + '"><img class="thumbnail img-responsive" src="' + imagepath + full['iItemId'] + '/thumb/' + full['vItemImage'] + '"  height="70" width="90" /></a>';
                            } else {
                                return '<img class="thumbnail img-responsive" src="' + no_img_url + '"  height="70" width="90" />';
                            }
                        }
                    }
                ];
                var aoculumn = [
                    /*0*/ {"mData": "vRestaurantName", "sWidth": "15%"},
                    /*1*/ {"mData": "vItemName", "sWidth": "15%"},
                    /*2*/ {"mData": "tItemDesc", "sWidth": "15%"},
                    /*3*/ {"mData": "dItemPrice", "sWidth": "10%"},
                    /*4*/ {"mData": "vItemImage", "sWidth": "15%"},
                    /*5*/ {"mData": "itemCategory", "sWidth": "10%"},
                    /*6*/ {"mData": "mealType", "sWidth": "10%"},
                    /*7*/ {"mData": "iItemId", bSortable: false, bSearchable: false, "sWidth": "10%"}
                ];
                var delete_val =  controller + '/deleteMenuItems';
                getdatatable(delete_val, url, aoculumn, target, 0, 'asc');

            });

        </script>

    </body>