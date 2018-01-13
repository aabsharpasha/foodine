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
                                            <li><a href="<?= BASEURL ?>offers/index">Offers</a></li>
                                        </ul>
                                        <div class="clearfix">
                                            <h3 class="content-title pull-left">
                                                <?php
                                                echo $record_set['vOfferText'] . "'s  Images";
                                                ?>
                                            </h3>
                                        </div>
                                        <div class="description">Offer Menu Image Listing</div>
                                    </div>
                                </div>
                            </div>
                            <?= $this->general_model->getMessages() ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="box border primary">
                                        <div class="box-title">
                                            <h4><i class="fa fa-table"></i>Offer Menu Image list</h4>
                                            <div class="tools">
                                                <a href="<?= BASEURL . $this->controller . '/offer_menu_add/' . $iComboOffersID; ?>" class="">
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
                                                        <th>Type</th>
                                                        <th>Added On</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th>Image</th>
                                                        <th>Type</th>
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
                imagepath = '<?php echo DOMAIN_URL . '/images/comboOfferMenu/'; ?>',
                no_img_url = '<?php echo DOMAIN_URL; ?>/admin/img/no-image.png';

            var url = '';

            var iComboOffersID = '<?php echo $iComboOffersID ?>';
            if (iComboOffersID == '')
                url = controller + '/offer_menu_paginate';
            else
                url = controller + '/offer_menu_paginate/' + iComboOffersID;

            $(document).ready(function () {
                $(".fancybox").fancybox();
                App.setPage("dynamic_table");  //Set current page
                App.init(); //Initialise plugins and elements
                // var target=get_edit_defination (2,'admin');

                var target = [
                    {
                        "aTargets": [3], // Column to target
                        "mRender": function (data, type, full)
                        {
                            /*<a title="Edit" href="<?= BASEURL ?>' + controller + '/add/' + data + '/y"  class="btn btn-primary marginright10 "><i class="fa fa-pencil-square-o"></i> Edit </a>\n\*/
                            var buttons = ' <a title="Edit" href="<?= BASEURL ?>' + controller + '/offer_menu_add/' + iComboOffersID + '/' + full['iMenuPictureID'] + '/y"  class="btn btn-primary marginright10 margintop10 "><i class="fa fa-pencil-square-o"></i> Edit </a> \n <button title="Delete" class="btn btn-danger marginright10 margintop10"  onclick="return validateRemove(' + data + ',' + "'" + controller + "/deleteOfferMenu/'" + ');"><i class="fa fa-times"></i> Delete</button>';
                            if (full['eStatus'] === "Active") {
                                buttons += '<a title="Click here to inactive" id="atag' + full['iMenuPictureID'] + '" onclick="return changeStatus(' + data + ',' + "'" + controller + "/offerMenuStatus/" + full['iMenuPictureID'] + "/y'" + ')"  class="btn btn-success marginright10 margintop10"><i class="fa fa-check-circle-o"></i> Active </a>'
                            } else {
                                buttons += '<a title="Click here to Active" id="atag' + full['iMenuPictureID'] + '" onclick="return changeStatus(' + data + ',' + "'" + controller + "/offerMenuStatus/" + full['iMenuPictureID'] + "/y'" + ')"  class="btn btn-inverse marginright10 margintop10"><i class="fa fa-times-circle-o "></i> Inactive </a>'
                            }
                            return buttons;
                        }
                    },
                    {
                        "aTargets": [0], // Column to target
                        "mRender": function (data, type, full)
                        {
                            if (full['vPictureName'] != '') {
                                return '<a class="fancybox" rel="group" href="' + imagepath + full['iComboOffersID'] + '/' + full['vPictureName'] + '"><img class="thumbnail img-responsive" src="' + imagepath + full['iComboOffersID'] + '/thumb/' + full['vPictureName'] + '"  height="150" width="150" style="margin:auto" /></a>';
                            } else {
                                return '<img class="thumbnail img-responsive" src="' + no_img_url + '"  height="150" width="150" style="margin:auto" />';
                            }
                        }
                    },
                ];
                var aoculumn = [
                    /*0*/ {"mData": "vPictureName", bSearchable: false, "sWidth": "30%"},
                    /*0*/ {"mData": "eMenuType", "sWidth": "20%"},
                    /*1*/ {"mData": "tCreatedAt", "sWidth": "20%"},
                    /*2*/ {"mData": "iMenuPictureID", bSearchable: false, "sWidth": "30%"}
                ];
                getdatatable(controller + '/deleteOfferMenu', url, aoculumn, target, 3, 'ASC');
            });
        </script>

    </body>