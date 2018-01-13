<?php
$headerData = $this->headerlib->data();

$uid = $getUserData['iUserID'];
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
                                            <h3 class="content-title pull-left"><?php echo $getUserData['vFullName'] ?>'s Favorite Restaurants </h3>
                                        </div>
                                        <div class="description">Restaurants Listing</div>
                                    </div>
                                </div>
                            </div>
                            <?= $this->general_model->getMessages() ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="box border primary">
                                        <div class="box-title">
                                            <h4><i class="fa fa-table"></i><?php echo $this->uppercase; ?> Favorite Restaurants list</h4>
                                            <div class="tools ">
                                                <a id="fa-refresh" href="javascript:;" class="reload">
                                                    <i class="fa fa-refresh"></i>
                                                </a>
                                                <a href="javascript:;" class="collapse">
                                                    <i class="fa fa-chevron-up"></i>
                                                </a>
                                                <!-- <a href="javascript:;" class="remove">
                                                  <i class="fa fa-times"></i>
                                                </a> -->
                                            </div>
                                        </div>
                                        <div class="box-body">
                                            <table id="datatable" cellpadding="0" cellspacing="0" border="0" class="datatable table  table-bordered ">
                                                <thead>
                                                    <tr>
                                                        <th>Restaurant Name</th>
                                                        <th>Email</th>
                                                        <th>Address</th>
                                                        <th>Restaurant Logo</th>
                                                        <th>Photos</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>Email</th>
                                                        <th>Address</th>
                                                        <th>Restaurant Logo</th>
                                                        <th>Photos</th>
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
                    imagepath = '<?php echo RESTAURANT_IMAGE_PATH ?>',
                    no_img_url = '<?php echo DOMAIN_URL; ?>/admin/img/no-image.png';

            var seturl = '';
            var iUserID = '<?php echo $uid; ?>';
            if (iUserID == '') {
                window.location.href = '<?php echo DOMAIN_URL; ?>/user';
            } else {
                seturl = controller + '/paginate_favorite/' + iUserID;
            }

            $(document).ready(function()
            {
                $(".fancybox").fancybox();
                App.setPage("dynamic_table");  //Set current page
                App.init(); //Initialise plugins and elements
                // var target=get_edit_defination (2,'admin');

                var target = [
                    {
                        "aTargets": [5], // Column to target
                        "mRender": function(data, type, full)
                        {
                            /*<a title="Edit" href="<?= BASEURL ?>' + controller + '/add/' + data + '/y"  class="btn btn-primary marginright10 "><i class="fa fa-pencil-square-o"></i> Edit </a>\n\*/
                            var buttons = ' <button title="Delete" class="btn btn-danger marginright10 margintop10"  onclick="return validateRemovefav(' + full['iFavoriteID'] + ',' + "'" + controller + "/deleteAll_favorite'" + ');"><i class="fa fa-times"></i> Remove <br> from Favorite </button>';
                            return buttons;
                        }
                    },
                    {
                        "aTargets": [4], // Column to target
                        "mRender": function(data, type, full)
                        {
                            /*<a title="Edit" href="<?= BASEURL ?>' + controller + '/add/' + data + '/y"  class="btn btn-primary marginright10 "><i class="fa fa-pencil-square-o"></i> Edit </a>\n\*/
                            var buttons = '';
                            if (full['total_picture'] == "0") {
                                buttons = ' <a title="View Photos" href="<?= BASEURL ?>image/index/' + full['iRestaurantID'] + '/y"  class="btn btn-primary marginright10 margintop10 "><i class="fa fa-pencil-square-o"></i> View Photos ( ' + full['total_picture'] + ' ) </a>';
                            } else {
                                buttons = ' <a title="View Photos" href="<?= BASEURL ?>image/index/' + full['iRestaurantID'] + '/y"  class="btn btn-primary marginright10 margintop10 "><i class="fa fa-pencil-square-o"></i>  View Photos ( ' + full['total_picture'] + ' ) </a>';
                            }

                            if (full['total_menu'] == "0") {
                                buttons += ' <a title="View Menu" href="<?= BASEURL ?>menu/index/' + full['iRestaurantID'] + '/y"  class="btn btn-primary marginright10 margintop10 "><i class="fa fa-pencil-square-o"></i> View Menu ( ' + full['total_menu'] + ' ) </a>';
                            } else {
                                buttons += ' <a title="View Menu" href="<?= BASEURL ?>menu/index/' + full['iRestaurantID'] + '/y"  class="btn btn-primary marginright10 margintop10 "><i class="fa fa-pencil-square-o"></i>  View Menu ( ' + full['total_menu'] + ' ) </a>';
                            }

                            return buttons;
                        }
                    },
                    {
                        "aTargets": [2], // Column to target
                        "mRender": function(data, type, full)
                        {
                            return full['tAddress'] + '<br>' + full['vCityName'] + '<br>' + full['vStateName'] + '<br>' + full['vCityName'];
                        }
                    },
                    {
                        "aTargets": [3], // Column to target
                        "mRender": function(data, type, full)
                        {
                            if (full['vRestaurantLogo'] != '') {
                                return '<a class="fancybox" rel="group" href="' + imagepath + full['iRestaurantID'] + '/' + full['vRestaurantLogo'] + '"><img class="thumbnail img-responsive" src="' + imagepath + full['iRestaurantID'] + '/thumb/' + full['vRestaurantLogo'] + '"  height="70" width="90" /></a>';
                            } else {
                                return '<img class="thumbnail img-responsive" src="' + no_img_url + '"  height="70" width="90" />';
                            }
                        }
                    },
                ];
                var aoculumn = [
                    /*0*/ {"mData": "vRestaurantName", "sWidth": "30%"},
                    /*1*/ {"mData": "vEmail", "sWidth": "35%"},
                    /*2*/ {"mData": "tAddress", "sWidth": "35%"},
                    /*3*/ {"mData": "vRestaurantLogo", bSortable: false, bSearchable: false, "sWidth": "10%"},
                    /*4*/ {"mData": "iFavoriteID", bSortable: false, bSearchable: false, "sWidth": "10%"}
                ];
                getdatatable(controller + '/deleteAll_favorite', seturl, aoculumn, target);
            });


            function  validateRemovefav(id, adrs)
            {
                $confirm = confirm("Are you sure you want to delete this?");
                if ($confirm == true)
                {
                    var keys = [];
                    keys.push(id);
                    var form_data = {rows: keys}
                    $.ajax({
                        url: BASEURL + adrs,
                        type: 'POST',
                        data: form_data,
                        success: function(output_string) {
                            if (output_string == '1')
                            {
                                $("#" + id).remove();
                            }
                            else
                            {
                                $("#divtoappend").append('<div id="alert"><div class="alert alert-danger center">Please try after some time</div></div>');
                            }
                        }
                    });
                }
                else
                {
                    return false;
                }
            }

        </script>

    </body>