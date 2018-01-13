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
                                                <i class="fa fa-tachometer"></i>
                                                <a href="<?= BASEURL ?>">Home</a>
                                            </li>
                                            <li><?php echo $this->uppercase; ?></li>
                                        </ul>
                                        <div class="clearfix">
                                            <h3 class="content-title pull-left">Featured <?php echo $this->uppercase; ?></h3>
                                        </div>
                                        <div class="description">Featured Restaurant</div>
                                    </div>
                                </div>
                            </div>
                            <?= $this->general_model->getMessages() ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="box border primary">
                                        <div class="box-title">
                                            <h4><i class="fa fa-eye"></i><?php echo $this->uppercase; ?> list</h4>
                                            <div class="tools ">
                                                <a id="fa-refresh" href="javascript:;" class="reload">
                                                    <i class="fa fa-refresh"></i>
                                                </a>
                                                <a href="<?= BASEURL . $this->controller; ?>/featured/add">Add New</a>
                                            </div>
                                        </div>
                                        <div class="box-body">
                                            <table id="datatable" cellpadding="0" cellspacing="0" border="0" class="datatable table  table-bordered ">
                                                <thead>
                                                    <tr>
                                                        <th>Restaurant Name</th>
                                                        <th>Category Name</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th>Restaurant Name</th>
                                                        <th>Category Name</th>
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

        <script>
            var oTable, controller = '<?php echo $this->controller; ?>', imagepath = '<?php echo RESTAURANT_IMAGE_PATH ?>', no_img_url = '<?php echo DOMAIN_URL; ?>/admin/img/no-image.png';
            $(document).ready(function () {
                //$(".fancybox").fancybox();
                App.setPage("dynamic_table");  //Set current page
                App.init(); //Initialise plugins and elements
                // var target=get_edit_defination (2,'admin');
                var btn_show_hide = parseInt('<?= $ADMINTYPE == 1 ? 1 : 0; ?>');

                var target = [{
                        "aTargets": [2], // Column to target
                        "mRender": function (data, type, full)
                        {
                            /*<a title="Edit" href="<?= BASEURL ?>' + controller + '/add/' + data + '/y"  class="btn btn-primary marginright10 "><i class="fa fa-pencil-square-o"></i> Edit </a>\n\*/
                            var buttons = ' <a title="Edit" href="<?= BASEURL ?>' + controller + '/featured/add/' + full['iFeaturedID'] + '/y"  class="btn btn-sm btn-primary marginright10 margintop10 "><i class="fa fa-pencil-square-o"></i> Edit </a>';
                            buttons += '<button title="Delete" class="btn btn-sm btn-danger marginright10 margintop10"  onclick="return validateRemove(' + full['iFeaturedID'] + ',' + "'" + controller + "/deleteFeatured'" + ');"><i class="fa fa-times"></i> Delete</button>';

                            return buttons;
                        }
                    }
                ];
                var aoculumn = [
                    /* 0 */ {"mData": "vRestaurantName", "sWidth": "20%"},
                    /* 1 */ {"mData": "vCategoryName", "sWidth": "20%"},
                    /* 2 */ {"mData": "iFeaturedID", bSortable: false, bSearchable: false, "sWidth": "10%"}
                ];
                var delete_val = btn_show_hide == 1 ? controller + '/deleteFeatured' : '';
                getdatatable(delete_val, controller + '/featuredPaginate', aoculumn, target);

            });
        </script>

    </body>
</html>
<?php
$this->session->unset_userdata('SUCCESS');
$this->session->unset_userdata('ERROR');
?>