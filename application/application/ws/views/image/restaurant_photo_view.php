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
                                            <h3 class="content-title pull-left">
                                                <?php
                                                //print_r($record_set_restaurant);
                                                echo $record_set['vRestaurantName'] . "'s  Images";
                                                ?>
                                            </h3>
                                        </div>
                                        <div class="description">Restaurant Image Listing</div>
                                    </div>
                                </div>
                            </div>
                            <?= $this->general_model->getMessages() ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="box border primary">
                                        <div class="box-title">
                                            <h4><i class="fa fa-table"></i>Restaurant Image list</h4>
                                            <div class="tools">
                                                <a href="<?= BASEURL . $this->controller . '/restaurant_image_add/' . $iRestaurantID; ?>" class="">
                                                    Add New
                                                </a>
                                                <a id="fa-refresh" href="javascript:;" class="reload">
                                                    <i class="fa fa-refresh"></i>
                                                </a>
                                                <!--  <a href="javascript:;" class="collapse">
                                                    <i class="fa fa-chevron-up"></i>
                                                </a> -->
                                            </div>
                                        </div>
                                        <div class="box-body">
                                            <table id="datatable" cellpadding="0" cellspacing="0" border="0" class="datatable table  table-bordered ">
                                                <thead>
                                                    <tr>
                                                        <th>Image</th>
                                                        <th>Added On</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th>Image</th>
                                                        <th>Added On</th>
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
        <link rel="stylesheet" href="<?php echo JS_URL; ?>/js/fancybox/source/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />
        <script type="text/javascript" src="<?php echo JS_URL; ?>/js/fancybox/source/jquery.fancybox.pack.js?v=2.1.5"></script>
        <!-- Optionally add helpers - button, thumbnail and/or media -->
        <link rel="stylesheet" href="<?php echo JS_URL; ?>/js/fancybox/source/helpers/jquery.fancybox-buttons.css?v=1.0.5" type="text/css" media="screen" />
        <script type="text/javascript" src="<?php echo JS_URL; ?>/js/fancybox/source/helpers/jquery.fancybox-buttons.js?v=1.0.5"></script>
        <script type="text/javascript" src="<?php echo JS_URL; ?>/js/fancybox/source/helpers/jquery.fancybox-media.js?v=1.0.6"></script>
        <link rel="stylesheet" href="<?php echo JS_URL; ?>/js/fancybox/source/helpers/jquery.fancybox-thumbs.css?v=1.0.7" type="text/css" media="screen" />
        <script type="text/javascript" src="<?php echo JS_URL; ?>/js/fancybox/source/helpers/jquery.fancybox-thumbs.js?v=1.0.7"></script>
        <script>
            var oTable,
                    controller = '<?php echo $this->controller; ?>',
                    imagepath = '<?php echo RESTAURANT_PHOTO_IMAGE_PATH ?>',
                    no_img_url = '<?php echo IMGURL; ?>/admin/img/no-image.png';

            var url = '';

            var iRestaurantID = '<?php echo $iRestaurantID ?>';
            if (iRestaurantID == '')
                url = controller + '/restaurant_photo_paginate';
            else
                url = controller + '/restaurant_photo_paginate/' + iRestaurantID;

            $(document).ready(function () {
                $(".fancybox").fancybox();
                App.setPage("dynamic_table");  //Set current page
                App.init(); //Initialise plugins and elements
                // var target=get_edit_defination (2,'admin');

                var target = [
                    {
                        "aTargets": [2], // Column to target
                        "mRender": function (data, type, full)
                        {
                            /*<a title="Edit" href="<?= BASEURL ?>' + controller + '/add/' + data + '/y"  class="btn btn-primary marginright10 "><i class="fa fa-pencil-square-o"></i> Edit </a>\n\*/
                            var buttons = ' <a title="Edit" href="<?= BASEURL ?>' + controller + '/restaurant_image_add/' + iRestaurantID + '/' + full['iPictureID'] + '/y"  class="btn btn-primary marginright10 margintop10 "><i class="fa fa-pencil-square-o"></i> Edit </a> \n <button title="Delete" class="btn btn-danger marginright10 margintop10"  onclick="return validateRemove(' + data + ',' + "'" + controller + "/deleteRestaurantImage/'" + ');"><i class="fa fa-times"></i> Delete</button>';
                            if (full['eStatus'] === "Active") {
                                buttons += '<a title="Click here to inactive" id="atag' + full['iPictureID'] + '" onclick="return changeStatus(' + data + ',' + "'" + controller + "/status/" + full['iPictureID'] + "/y'" + ')"  class="btn btn-success marginright10 margintop10"><i class="fa fa-check-circle-o"></i> Active </a>'
                            } else {
                                buttons += '<a title="Click here to Active" id="atag' + full['iPictureID'] + '" onclick="return changeStatus(' + data + ',' + "'" + controller + "/status/" + full['iPictureID'] + "/y'" + ')"  class="btn btn-inverse marginright10 margintop10"><i class="fa fa-times-circle-o "></i> Inactive </a>'
                            }
                            return buttons;
                        }
                    },
                    {
                        "aTargets": [0], // Column to target
                        "mRender": function (data, type, full)
                        {
                            if (full['vPictureName'] != '') {
                                return '<a class="fancybox" rel="group" href="' + imagepath + full['iRestaurantID'] + '/' + full['vPictureName'] + '"><img class="thumbnail img-responsive" src="' + imagepath + full['iRestaurantID'] + '/thumb/' + full['vPictureName'] + '"  height="150" width="150" style="margin:auto" /></a>';
                            } else {
                                return '<img class="thumbnail img-responsive" src="' + no_img_url + '"  height="150" width="150" style="margin:auto" />';
                            }
                        }
                    },
                ];
                var aoculumn = [
                    /*0*/ {"mData": "vPictureName", bSearchable: false, "sWidth": "60%"},
                    /*1*/ {"mData": "tCreatedAt", "sWidth": "30%", bSortable: true},
                    /*2*/ {"mData": "iPictureID", bSearchable: false, "sWidth": "10%"}
                ];
                getdatatable(controller + '/deleteRestaurantImage', url, aoculumn, target, 2, 'ASC');
            });
        </script>

    </body>