<?php
$headerData = $this->headerlib->data();

$ADMINTYPE = $this->session->userdata('ADMINTYPE');
$permission = get_page_permission($ADMINTYPE, 20);

$form_attr = array(
    'name' => 'category-form',
    "id" => "validateForm",
    'method' => 'post',
    'class' => "form-horizontal",
    'role' => 'form',
    'enctype' => 'multipart/form-data'
);

$submit_attr = array(
    'class' => 'submit btn-sm btn btn-primary marginright20',
    'value' => "Save",
    'type' => 'submit'
);
$cancel_attr = array(
    'class' => 'btn btn-inverse ',
    'value' => "Reset",
    'type' => 'reset'
);
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
            .multiselect-container{
                max-height: 400px;
                overflow-y: auto;
                width:100% !important;
                max-width: 500px !important;
            }
            .btn-group{
                width:100% !important;
            }
            .btn{
                width:100% !important;
                max-width: 500px !important;
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
                                            <li><?php echo $this->uppercase?></li>
                                        </ul>
                                        <div class="clearfix">
                                            <h3 class="content-title pull-left"><?php echo $this->uppercase?></h3>
                                        </div>
                                        <div class="description">Indirect Search Report</div>
                                    </div>
                                </div>
                            </div>
                            <?= $this->general_model->getMessages() ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- BOX -->
                                    <div class="box border primary">
                                        <div class="box-title">
                                            <h4><i class="fa fa-plus-circle"></i>Indirect Search Report</h4>
                                            <div class="tools hidden-xs">
                                                <a href="javascript:void(0);" class="collapse">
                                                    <i class="fa fa-chevron-up"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="box-body big">
                                            <?php
                                            echo form_open("report/checkInReport", $form_attr);
                                            ?>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label" for="searchText">Select Search Text<span class="required">*</span></label>
                                                <div class="col-sm-9 user-select">
                                                    <select class="form-control maxwidth500" name="searchText" id="searchText">
                                                        <option value=""> - All -</option>
                                                        <?php foreach($searchTextList AS $searchText){ ?>
                                                        <option value="<?php echo $searchText["vSearchText"]; ?>" ><?php echo ucfirst($searchText["vSearchText"]); ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group" id="ageDiv">
                                                <label class="col-sm-3 control-label">Search Date</label>
                                                <div class="col-sm-2 ">
                                                    <?=
                                                    form_input(array(
                                                        'name' => 'startDate',
                                                        'id' => 'startDate',
                                                        'placeholder' => 'start date',
                                                        "data-errortext" => "This is start date text!",
                                                        'value' => (isset($tStartDate) && $tStartDate != '') ? date('m/d/Y', strtotime($tStartDate)) : '',
                                                        'type' => 'text',
                                                        'class' => 'form-control maxwidth500',
                                                        "readonly"
                                                    ));
                                                    ?>
                                                </div>
                                                <div class="col-sm-1 center">
                                                    To
                                                </div>
                                                <div class="col-sm-2">
                                                    <?=
                                                    form_input(array(
                                                        'name' => 'endDate',
                                                        'id' => 'endDate',
                                                        'placeholder' => 'end date',
                                                        "data-errortext" => "This is end date text!",
                                                        'value' => (isset($tStartDate) && $tStartDate != '') ? date('m/d/Y', strtotime($tStartDate)) : '',
                                                        'type' => 'text',
                                                        'class' => 'form-control maxwidth500',
                                                        "readonly"
                                                    ));
                                                    ?>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group" style="text-align: center">
                                                <div style="display: inline-block;width:100px;">
                                                    <a class="btn btn-sm btn-grey" href="javascript:void(0);" id="submitForm">Submit</a>
                                                </div>
                                                <div style="display: inline-block;width:100px;">
                                                    <button class="btn btn-sm btn-grey" type="reset">Reset</button>
                                                </div>
                                            </div>
                                            <?php echo form_close(); ?>
                                        </div>
                                        <div id="repostDataDiv"></div>
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
        <?= $headerData['javascript_view']; ?>
        <script type="text/javascript" src="<?= JS_URL; ?>js/datepicker/picker.js"></script>
        <script type="text/javascript" src="<?= JS_URL; ?>js/datepicker/picker.date.js"></script>
        <script type="text/javascript" src="<?= JS_URL; ?>js/datepicker/picker.time.js"></script>
        <script type="text/javascript" src="<?= JS_URL; ?>js/uniform/jquery.uniform.min.js"></script>
        <script>
            var controller = '<?php echo $this->controller; ?>'
            var catId = parseInt('<?= (isset($iCategoryID) && $iCategoryID != '') ? $iCategoryID : 0; ?>');
                
            function getReportData(){
                $.ajax({
                    type: 'POST',
                    dataType: 'html',
                    data: {
                        searchText:$("#searchText").val(),
                        startDate:$("#startDate").val(),
                        endDate:$("#endDate").val(),
                    },
                    url: BASEURL + controller + '/indirectSearchReport',
                    success: function (resp) {
                        $("#repostDataDiv").html(resp);

                    }
                });

            }
            jQuery(document).ready(function () {
                var permission = <?= json_encode($permission); ?>;
                if ((catId > 0 && permission.indexOf('2') >= 0) || (catId == 0 && permission.indexOf('1') >= 0)) {

                } else if (permission.indexOf('6') >= 0) {
                    alert('You don\'t have a permission to access this page');
                    window.location.href = '<?= BASEURL; ?>';
                }
                
                $("#startDate").datepicker({
//                    minDate: 0,
                    changeMonth: true,
                    changeYear: true,
                    onClose: function(selectedDate) {
                        $("#endDate").datepicker("option", "minDate", selectedDate);
                    },
                    onSelect: function(dateText, inst) {
                        var date = $(this).datepicker('getDate');
                        _minDay = date.getDay();
//                        dayShowHide($("#redemptionStartDate"),$("#redemptionEndDate"));
                    }
                });
                $("#endDate").datepicker({
                    changeMonth: true,
                    changeYear: true,
                    onSelect: function(dateText, inst) {
                        var date = $(this).datepicker('getDate');
                        _maxDay = date.getDay();
//                        dayShowHide($("#redemptionStartDate"),$("#redemptionEndDate"));
                    }
                });
                $("#submitForm").click(function(){
                    getReportData();
                });
                getReportData();
                App.setPage("forms");  //Set current page
                App.init(); //Initialise plugins and elements
                
                // $('.fileinput').fileinput()
                $('.uniform').uniform();
            });



        </script>
    </body>
</html>
