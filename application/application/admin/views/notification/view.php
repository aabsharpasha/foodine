<?php
$headerData = $this->headerlib->data();
$ADMINTYPE = $this->session->userdata('ADMINTYPE');
$permission = get_page_permission($ADMINTYPE, 36);

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
                                        <div class="description">Notification History</div>
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
                                                <!-- <a href="javascript:;" class="remove">
                                                  <i class="fa fa-times"></i>
                                                </a> -->
                                            </div>
                                        </div>
                                        <div class="box-body">
                                            <table id="datatable" cellpadding="0" cellspacing="0" border="0" class="datatable table  table-bordered ">
                                                <thead>
                                                    <tr>
                                                        <th>Criteria</th>
                                                        <th>Notification</th>
                                                        <th>Link</th>
                                                        <th>Image</th>
                                                        <th>Date & Time</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th>Criteria</th>
                                                        <th>Notification</th>
                                                        <th>Link</th>
                                                        <th>Image</th>
                                                        <th>Date & Time</th>
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
            var imagepath = '<?php echo IMGURL ?>/pushNotification/'; 
            var no_img_url = '<?php echo IMGURL; ?>/img/no-image.png';

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
                        "aTargets": [5], // Column to target
                        "mRender": function (data, type, full) {
                            var buttons  = '';
                            var buttons = ' <a title="Edit" href="<?= BASEURL; ?>' + controller + '/addNotification/' + full['iPushNotifyID'] + '/y"  class="btn btn-sm btn-primary marginright10 margintop10 "><i class="fa fa-pencil-square-o"></i> Edit </a> \n <button title="Delete" class="btn btn-sm btn-danger  marginright10 margintop10"  onclick="return validateRemove(' + data + ',' + "'" + controller + "/notificationDeleteAll'" + ');"><i class="fa fa-times"></i> Delete</button>';
                            if (full['eStatus'] == "Active") {
                                buttons += '<a title="Click here to inactive" id="atag' + full['iPushNotifyID'] + '" onclick="return changeStatus(' + data + ',' + "'" + controller + "/notificationStatus/" + full['iPushNotifyID'] + "/y'" + ')"  class="btn btn-sm btn-success marginright10 margintop10"><i class="fa fa-check-circle-o"></i> Active </a>';
                            } else {
                                buttons += '<a title="Click here to Active" id="atag' + full['iPushNotifyID'] + '" onclick="return changeStatus(' + data + ',' + "'" + controller + "/notificationStatus/" + full['iPushNotifyID'] + "/y'" + ')"  class="btn btn-sm btn-inverse marginright10 margintop10"><i class="fa fa-times-circle-o "></i> Inactive </a>';
                            }
//                            buttons += '<a href="#table-modal" data-toggle="modal" class="btn btn-sm btn-primary marginright10 margintop10 btn-view-detail " data-id="' + full['iPushNotifyID'] + '">View Detail</a>';
                            return buttons;
                        }
                    },
                    {
                        "aTargets": [3], // Column to target
                        "mRender": function (data, type, full)
                        {
                            if (full['vImage'] != '') {
                                return '<a class="fancybox" rel="group" href="' + imagepath + full['iPushNotifyID'] + '/' + full['vImage'] + '"><img class="thumbnail img-responsive" src="' + imagepath + full['iPushNotifyID'] + '/thumb/' + full['vImage'] + '"  height="70" width="90" /></a>';
                            } else {
                                return '<img class="thumbnail img-responsive" src="' + no_img_url + '"  height="70" width="90" />';
                            }
                        }
                    }
                ];
                var aoculumn = [
                    /*0*/ {"mData": "eCriteria", "sWidth": "15%"},
                    /*1*/ {"mData": "vNotifyText", "sWidth": "25%"},
                    /*2*/ {"mData": "eLink", "sWidth": "10%"},
                    /*3*/ {"mData": "vImage", "sWidth": "15%"},
                    /*4*/ {"mData": "scheduleDate", "sWidth": "15%"},
                    /*5*/ {"mData": "iPushNotifyID", bSortable: false, bSearchable: false, "sWidth": "20%"}
                ];
                getdatatable(controller + '/notificationDeleteAll', url, aoculumn, target, 4, 'desc');


            });

        </script>

    </body>