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
                                                <i class="fa fa-home"></i>
                                                <a href="<?= BASEURL ?>">Home</a>
                                            </li>
                                            <li><?php echo $this->uppercase; ?></li>
                                        </ul>
                                        <div class="clearfix">
                                            <h3 class="content-title pull-left"><?php echo $this->uppercase; ?></h3>
                                        </div>
                                        <div class="description">Posts Listing</div>
                                    </div>
                                </div>
                            </div>
                            <?= $this->general_model->getMessages() ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="box border primary">
                                        <div class="box-title">
                                            <h4><i class="fa fa-table"></i><?php echo $this->uppercase; ?> list</h4>
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
                                                        <th>Post</th>
                                                        <th>Post Type</th>
                                                        <th>Posted On</th>
                                                        <th>Comment</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th>Post</th>
                                                        <th>Post Type</th>
                                                        <th>Posted On</th>
                                                        <th>Comment</th>
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

                    <div class="row" id="print_comments_div" style="display:none">
                        <!-- NEW ORDERS -->
                        <div class="col-md-12">
                            <div class="box border">
                                <div class="box-title">
                                    <h4><i class="fa fa-columns"></i>
                                        <span class="hidden-inline-mobile">Comments</span></h4>
                                </div>
                                <div class="box-body">
                                    <div class="tabbable header-tabs">
                                        <ul class="nav nav-tabs">
                                            <li class="active">
                                                <a href="#feed" data-toggle="tab">
                                                    <i class="fa fa-bookmark"></i>
                                                    <span class="hidden-inline-mobile">Comments </span>
                                                </a>
                                            </li>
                                        </ul>
                                        <div class="tab-content ">
                                            <div class="tab-pane active " id="feed">
                                                <div class=" comments_list" style="max-height:450px;overflow-y: scroll;padding-right:10px" >
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /NEW ORDERS -->
                    </div>

                </div>
            </div>



        </section>
        <?= $headerData['javascript_view']; ?>

        <script src="<?= base_url() ?>js/bootbox/bootbox.min.js" type="text/javascript" charset="utf-8"></script>






        <script>
            var oTable,
                    controller = '<?php echo $this->controller; ?>';

            var url;

            var iSubjectID = "<?php echo $get_iSubjectID ?>";
            if (iSubjectID == '') {
                url = controller + '/paginate_like'
            } else {
                url = controller + '/paginate_like/' + iSubjectID
            }


            $(document).ready(function()
            {
                App.setPage("dynamic_table");  //Set current page
                App.init(); //Initialise plugins and elements
                // var target=get_edit_defination (2,'admin');

                var target = [
                    {
                        "aTargets": [4], // Column to target
                        "mRender": function(data, type, full)
                        {
                            /*<a title="Edit" href="<?= BASEURL ?>' + controller + '/add/' + data + '/y"  class="btn btn-primary marginright10 "><i class="fa fa-pencil-square-o"></i> Edit </a>\n\*/
                            var buttons = ' <a title="Edit" href="<?= BASEURL ?>' + controller + '/add/' + data + '/y"  class="btn btn-primary marginright10 margintop10 "><i class="fa fa-pencil-square-o"></i> Edit </a> \n <button title="Delete" class="btn btn-danger marginright10 margintop10"  onclick="return validateRemove(' + data + ',' + "'" + controller + "/deleteAll'" + ');"><i class="fa fa-times"></i> Delete</button>';
                            if (full['eStatus'] == "Active") {
                                buttons += '<a title="Click here to inactive" id="atag' + full['iPostID'] + '" onclick="return changeStatus(' + data + ',' + "'" + controller + "/status/" + full['iPostID'] + "/y'" + ')"  class="btn btn-success marginright10 margintop10"><i class="fa fa-check-circle-o"></i> Active </a>'
                            } else {
                                buttons += '<a title="Click here to Active" id="atag' + full['iPostID'] + '" onclick="return changeStatus(' + data + ',' + "'" + controller + "/status/" + full['iPostID'] + "/y'" + ')"  class="btn btn-inverse marginright10 margintop10"><i class="fa fa-times-circle-o "></i> Inactive </a>'
                            }
                            return buttons;
                        }
                    },
                    {
                        "aTargets": [3], // Column to target
                        "mRender": function(data, type, full)
                        {
                            return full['vPostText'];
                        }
                    },
                    {
                        "aTargets": [0], // Column to target
                        "mRender": function(data, type, full)
                        {
                            return '<b>' + full['vName'] + '</b> ' + full['ePosttype'] + ' <b>' + full['vSubjectName'] + '</b>';
                        }
                    }
                ];
                var aoculumn = [
                    /*0*/ {"mData": "vSubjectName", "sWidth": "50%"},
                    /*1*/ {"mData": "ePosttype", "sWidth": "10%"},
                    /*2*/ {"mData": "tCreatedAt", "sWidth": "10%"},
                    /*2*/ {"mData": "vPostText", "sWidth": "10%"},
                    /*3*/ {"mData": "iPostID", bSortable: false, bSearchable: false, "sWidth": "10%"}
                ];
                getdatatable(controller + '/deleteAll', url, aoculumn, target, 2, 'desc');



            });

            function viewcomment(iPostID) {

                var pid = iPostID;
                var base_url = "<?= BASEURL ?>";
                $.ajax({
                    url: base_url + controller + '/getCommentList',
                    type: 'POST',
                    // dataType: 'JSON',
                    data: {iPostID: pid},
                })
                        .done(function(data) {
                    if (data != '') {
                        var data1 = $.parseJSON(data);
                        var title = '';
                        $('.comments_list').html('');

                        $.each(data1, function(index, val) {
                            var pic = '';
                            if (val.vProfilePicture != '') {
                                pic = "<?php echo DOMAIN_URL ?>/images/user/" + val.iSubjectID + '/thumb/' + val.vProfilePicture;
                            } else {
                                pic = "<?php echo DOMAIN_URL ?>/images/user/no-image.png";
                            }

                            str = '<div class="feed-activity clearfix comment_div_' + val.iCommentID + '"  ><div style=" height: 75px; padding-right: 31px;"><i class="pull-left roundicon fa  btn"> <img src="' + pic + '" height="30px" width="30px"> </i>' +
                                    '<a class="user" href="javascript:void(0)"> ' + val.vName + ' :  </a>  ' + val.vCommentText + '<br>' +
                                    '</div> <button class="btn btn-xs  btn-default delete_comment" onclick="deletecomment(' + val.iCommentID + ')"> <a aria-hidden="true" href="javascript:void(0)" data-dismiss="alert" class="close">×</a></button> <div class="time"> <i class="fa fa-clock-o"></i> ' + val.tCreatedAt + ' </div> </div>';

                            $('.comments_list').append(str);

                            title = val.vSubject;

                        });

                        var msg = $('#print_comments_div').html();

                        bootbox.dialog({
                            message: msg,
                            title: title,
                            buttons: {
                                main: {
                                    label: "Close",
                                    className: "btn-primary",
                                    callback: function() {
                                        $(".bootbox").modal("hide");
                                    }
                                }
                            }
                        });


                    } else {

                    }
                    // $('.scroller').slimscroll();

                })
                        .fail(function() {
                    console.log("error");
                })
                        .always(function() {
                    console.log("complete");
                });
            }


            function deletecomment(iCommentID) {
                var cid = iCommentID;
                $.ajax({
                    url: controller + '/deleteComment',
                    type: 'POST',
                    data: {iCommentID: cid},
                })
                        .done(function(data) {
                    if (data != '') {
                        var str = ".comment_div_" + iCommentID;
                        $(str).remove();
                    } else {

                    }
                })
                        .fail(function() {
                    console.log("error");
                })
                        .always(function() {
                    console.log("complete");
                });
            }
        </script>

    </body>