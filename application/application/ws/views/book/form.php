<?php
$headerData = $this->headerlib->data();
if (isset($getCategoryData) && $getCategoryData != '')
    extract($getCategoryData);

$ADMINTYPE = $this->session->userdata('ADMINTYPE');
$permission = get_page_permission($ADMINTYPE, 34);

//print_r($getCategoryData);

$form_attr = array(
    'name' => 'book-form',
    "id" => "validateForm",
    'method' => 'post',
    'class' => "form-horizontal",
    'role' => 'form',
    'enctype' => 'multipart/form-data'
);

$activestatus = array(
    'name' => 'eStatus',
    'value' => 'Active',
    'checked' => (isset($eStatus) && $eStatus == 'Active') ? 'TRUE' : 'FALSE'
);

$inactivestatus = array(
    'name' => 'eStatus',
    'value' => 'Inactive',
    'checked' => (isset($eStatus) && $eStatus == 'Inactive') ? 'TRUE' : 'FALSE'
);
// Setting Hidden action attributes for Add/Edit functionality.
$hiddeneditattr = array(
    "action" => "backoffice.bookedit"
);
$hiddenaddattr = array(
    "action" => "backoffice.bookadd"
);
$table_book_id = array(
    "iTableBookID" => (isset($iTableBookID) && $iTableBookID != '') ? $iTableBookID : ''
);
$submit_attr = array(
    'class' => 'submit btn-sm btn btn-primary marginright20',
    'value' => "Book Table",
    'type' => 'submit'
);
$cancel_attr = array(
    'class' => 'btn btn-inverse',
    'value' => "Reset",
    'type' => 'reset'
);
$uid = (isset($iCategoryID) && $iCategoryID != '') ? $iCategoryID : '';
?>
<!doctype html>
<html lang="en-us">
    <head>
        <title><?= $title ?></title>
        <?= $headerData['meta_tags']; ?>
        <?= $headerData['stylesheets_form']; ?>

        <style type="text/css">
            .genderlabel{
                display:inline-block;
                margin-right: 10px;
            } 
            .genderlabel .radio{
                padding-top: 5px;
            }
        </style>

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
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="page-header">
                                        <ul class="breadcrumb">
                                            <li>
                                                <i class="fa fa-tachometer"></i>
                                                <a href="<?= BASEURL ?>">Home</a>
                                            </li>
                                            <li>Book Table</li>
                                        </ul>
                                        <div class="clearfix">
                                            <h3 class="content-title pull-left">Book Table</h3>
                                        </div>
                                        <div class="description">Add/Edit Book Table</div>
                                    </div>
                                </div>
                            </div>
                            <?= $this->general_model->getMessages() ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- BOX -->
                                    <div class="box border primary">
                                        <div class="box-title">
                                            <h4><i class="fa fa-plus-circle"></i>Book Table</h4>
                                            <div class="tools hidden-xs">
                                                <a href="javascript:void(0);" class="collapse">
                                                    <i class="fa fa-chevron-up"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="box-body big">
                                            <?php
                                            echo form_open("book/add", $form_attr);
                                            if (isset($iTableBookID) && $iTableBookID != '') {
                                                echo form_hidden($table_book_id);
                                                echo form_hidden($hiddeneditattr);
                                            } else {
                                                echo form_hidden($hiddenaddattr);
                                            }
                                            ?>

                                            <?php if ($ADMINTYPE == 1 || $ADMINTYPE == 2) { ?>
                                                <div class="form-group">
                                                    <label class="col-sm-3 control-label">Restaurant<span class="required">*</span></label>
                                                    <div class="col-sm-9">
                                                        <select class="maxwidth500 col-lg-12" name="restaurant_id" id="restaurant_id" onchange="return getTimeSlot(this);">
                                                            <option value=""> Select Restaurant </option>
                                                            <?php for ($i = 0; $i < count($restaurants); $i++) { ?>
                                                                <option value="<?= $restaurants[$i]['iRestaurantID']; ?>"><?= $restaurants[$i]['vRestaurantName']; ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            <?php } else { ?>
                                                <input type="hidden" name="restaurant_id" value="<?= $this->session->userdata('iRestaurantID'); ?>">
                                            <?php } ?>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">User<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <select class="maxwidth500 col-lg-12" name="user_id" id="user_id" required="required">
                                                        <option value=""> Select User </option>
                                                        <?php for ($i = 0; $i < count($users); $i++) { ?>
                                                            <option value="<?= $users[$i]['iUserID']; ?>"><?= $users[$i]['vName']; ?><?php if (!empty($users[$i]['vMobileNo'])) echo " [" . $users[$i]['vMobileNo'] . "]"; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Booking Date <span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <input class="form-control maxwidth500" 
                                                           name="book_date" 
                                                           id="book_date" required="required"/>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Booking Slot <span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <div class="maxwidth500" id="slot_container"></div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Total Person <span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <input class="form-control maxwidth500" 
                                                           name="total_person" 
                                                           id="total_person" required="required"/>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Offer</label>
                                                <div class="col-sm-9">
                                                    <select class="maxwidth500 col-lg-12" name="offerId" id="offerId">
                                                        <option value="0"> Select Offer </option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="margin0auto disptable">
                                                    <?php echo form_input($submit_attr); ?>
                                                    <a class="btn btn-sm btn-grey" href="<?= BASEURL .''. $this->controller; ?>">Cancel</a>
                                                </div>
                                            </div>
                                            <?php echo form_close(); ?>
                                        </div>
                                    </div>
                                    <!-- /BOX -->
                                </div>
                            </div>
                            <?php $this->load->view('include/footer_view') ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?= $headerData['javascript_form']; ?>
        <link rel="stylesheet" href="<?= base_url() ?>js/bootstrap-multiselect/bootstrap-multiselect.css" type="text/css" media="screen" />
        <script src="<?= base_url() ?>js/bootstrap-multiselect/bootstrap-multiselect.js" type="text/javascript" charset="utf-8"></script>

        <script>
                                                            function getTimeSlot($select) {
                                                                var res_id = $select.value;
                                                                var date = $("#book_date").val();
                                                                if (res_id != '') {
                                                                    $.ajax({
                                                                        type: 'POST',
                                                                        dataType: 'json',
                                                                        url: '<?= BASEURL; ?>book/time_slots',
                                                                        data: {
                                                                            id: res_id, date: date
                                                                        },
                                                                        success: function(resp) {
                                                                            var html = '';
                                                                            var need2Plus = false;
                                                                            $.each(resp.slots, function(key, value) {
                                                                                var id = parseInt(value.id);
                                                                                var val = value.val;
                                                                                need2Plus = need2Plus == false && id == 48 ? true : need2Plus ? true : false;
                                                                                html += '<label class="col-lg-3 slot_lbl" data-id="' + (need2Plus && id != 48 ? (id + 48) : id) + '">';
                                                                                html += '<input type="radio" name="slot_id" id="slot_id[]" value="' + id + '" />&nbsp;' + val + '</label>';
                                                                            });
                                                                            $('#slot_container').html(html);

                                                                            var offers = '<option value="0"> Select Offer </option>';
                                                                            $.each(resp.offer, function(key, value) {
                                                                                var id = parseInt(value.offerId);
                                                                                var val = value.offerText;
                                                                                offers += '<option value="' + id + '">' + val + '</option>';
                                                                            });
                                                                            $('#offerId').html(offers);
                                                                        }
                                                                    });
                                                                }
                                                            }
                                                            $(document).ready(function() {
                                                                var permission = <?= json_encode($permission); ?>;
                                                                if (permission.indexOf('6') >= 0) {
                                                                    alert('You don\'t have a permission to access this page');
                                                                    window.location.href = '<?= BASEURL; ?>';
                                                                }
                                                                var currSlot = parseInt('<?= $current_slot; ?>');
                                                                App.setPage("forms"); //Set current page
                                                                App.init(); //Initialise plugins and elements
                                                                var $bookDate = $("#book_date");
                                                                $('#user_id, #restaurant_id, #offerId').select2();
                                                                $("#validateForm").validate({
                                                                    rules: {
                                                                        book_date: {
                                                                            required: true
                                                                        },
                                                                        total_person: {
                                                                            required: true,
                                                                            number: true
                                                                        }
                                                                    },
                                                                    messages: {
                                                                        book_date: "Please select a booking date",
                                                                        total_person: {
                                                                            required: "Please enter total person",
                                                                            number: "Please enter number only"
                                                                        },
                                                                    }
                                                                });
                                                                var currDate = new Date();
                                                                currDate = currDate.getDate();
                                                                $bookDate.datepicker({
                                                                    minDate: 0,
                                                                    onSelect: function(dateText, inst) {
                                                                        var date = $(this).datepicker('getDate');
                                                                        var selectedDate = date.getDate();
                                                                        //alert(currDate + ' ' + selectedDate);
                                                                        $('#slot_container').children().each(function() {
                                                                            var $lbl = $(this);
                                                                            //console.log($lbl.html());
                                                                            if ($lbl.hasClass('slot_lbl')) {
                                                                                var lbl_id = $lbl.data('id');
                                                                                //console.log(lbl_id);
                                                                                if (currDate == selectedDate) {
                                                                                    if (lbl_id < currSlot || lbl_id > 48) {
                                                                                        $lbl.addClass('hide');
                                                                                    }
                                                                                } else {
                                                                                    $lbl.removeClass('hide');
                                                                                }
                                                                            }

                                                                        });
                                                                    },
                                                                    changeMonth: true,
                                                                    changeYear: true,
                                                                    maxDate: +30,
                                                                    minDate: 0
                                                                });
                                                                // $('.fileinput').fileinput()
                                                                $('.uniform').uniform();
                                                            });
        </script>
    </body>
</html>
