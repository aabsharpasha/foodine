<?php
$headerData = $this->headerlib->data();
if (isset($getJobData) && $getJobData != '')
    extract($getJobData);

$ADMINTYPE = $this->session->userdata('ADMINTYPE');
$permission = get_page_permission($ADMINTYPE, 20);

//print_r($getCategoryData); 

$form_attr = array(
    'name' => 'addjob-form',
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
    "action" => "backoffice.jobedit"
);
$hiddenaddattr = array(
    "action" => "backoffice.jobadd"
);
$job_id = array(
    "iJobDetailID" => (isset($iJobDetailID) && $iJobDetailID != '') ? $iJobDetailID : ''
);
$submit_attr = array(
    'class' => 'submit btn-sm btn btn-primary marginright20',
    'value' => $ACTION_LABEL == 'Edit' ? 'Save Changes' : "$ACTION_LABEL Job",
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
        <title>Add Job</title>
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
                                            <li>Jobs</li>
                                        </ul>
                                        <div class="clearfix">
                                            <h3 class="content-title pull-left">Jobs</h3>
                                        </div>
                                        <div class="description">Add/Edit Jobs</div>
                                    </div>
                                </div>
                            </div>
                            <?= $this->general_model->getMessages() ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- BOX -->
                                    <div class="box border primary">
                                        <div class="box-title">
                                            <h4><i class="fa fa-plus-circle"></i><?php echo $ACTION_LABEL . " Job"; ?></h4>
                                            <div class="tools hidden-xs">
                                                <a href="javascript:void(0);" class="collapse">
                                                    <i class="fa fa-chevron-up"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="box-body big">
                                            <?php
                                            echo form_open("jobs/addJobs", $form_attr);
                                            if (isset($iJobDetailID) && $iJobDetailID != '') {
                                                echo form_hidden($job_id);
                                                echo form_hidden($hiddeneditattr);
                                            } else {
                                                echo form_hidden($hiddenaddattr);
                                            }
                                            ?>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Job Title <span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <input class="form-control maxwidth500" name="vJobTitle" value="<?php echo (isset($vJobTitle) && $vJobTitle != '') ? $vJobTitle : ''; ?>">
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Job Location <span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <!--input class="form-control maxwidth500" name="vJobLocation" value="<?php echo (isset($vJobLocation) && $vJobLocation != '') ? $vJobLocation : ''; ?>"-->
                                                
                                                 <select class="form-control maxwidth500" name="vJobLocation" id="vJobLocation">
                                                        <option value=""> - Select Location- </option>
                                                        <?php foreach($Locations as $locVal){ ?> 
                                                            
                                                        <option value="<?php echo $locVal['iLocZoneID']; ?>" <?php echo (isset($vJobLocation) && $vJobLocation == $locVal['iLocZoneID'] ? 'selected="selected"' : ''); ?> ><?php echo $locVal['vZoneName']; ?></option>    
                                                            
                                                     <?php   } ?>
                                                        
                                                       
                                                    </select>
                                                
                                                </div>
                                            </div>
                                            
                                            
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Experience </label>
                                                <div class="col-sm-9">
                                                    <!--input class="form-control maxwidth500" name="iMinExp" value="<?php echo (isset($iMinExp) && $iMinExp != '') ? $iMinExp : ''; ?>"-->
                                                    
                                                    <select class="form-control maxwidth500" name="iMinExp" id="iMinExp">
                                                       
                                                
                                                        <option value="Not Applicable" <?php echo (isset($iMinExp) && $iMinExp == "Not Applicable" ? 'selected="selected"' : ''); ?> >Not Applicable</option>
                                                         <option value="Entry Level" <?php echo (isset($iMinExp) && $iMinExp == "Entry Level" ? 'selected="selected"' : ''); ?> >Entry Level</option>
                                                          <option value="Associate" <?php echo (isset($iMinExp) && $iMinExp == "Associate" ? 'selected="selected"' : ''); ?> >Associate</option>
                                                           <option value="Mid – Senior Level" <?php echo (isset($iMinExp) && $iMinExp == "Mid – Senior Level" ? 'selected="selected"' : ''); ?> >Mid – Senior Level</option>
                                                            <option value="Director" <?php echo (isset($iMinExp) && $iMinExp == "Director" ? 'selected="selected"' : ''); ?> >Director</option>
                                                            <option value="Executive" <?php echo (isset($iMinExp) && $iMinExp == "Executive" ? 'selected="selected"' : ''); ?> >Executive</option>
                                                       
                                                    </select>
                                                    
                                                </div>
                                            </div>
                                            
                                            <!--div class="form-group">
                                                <label class="col-sm-3 control-label">Max Experience <span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <input class="form-control maxwidth500" name="iMaxExp" value="<?php echo (isset($iMaxExp) && $iMaxExp != '') ? $iMaxExp : ''; ?>">
                                                </div>
                                            </div-->
                                            
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Employment Type <span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <select class="form-control maxwidth500" name="iEmploymentType" id="iEmploymentType">
                                                        <option value=""> - Select - </option>
                                                
                                                        <option value="Full Time" <?php echo (isset($iEmploymentType) && $iEmploymentType == "Full Time" ? 'selected="selected"' : ''); ?> >Full Time</option>
                                                       <option value="Part Time" <?php echo (isset($iEmploymentType) && $iEmploymentType == "Part Time" ? 'selected="selected"' : ''); ?> >Part Time</option>
                                                       
                                                       
                                                       <option value="Temporary" <?php echo (isset($iEmploymentType) && $iEmploymentType == "Temporary" ? 'selected="selected"' : ''); ?> >Temporary</option>
                                                       <option value="Volunteer" <?php echo (isset($iEmploymentType) && $iEmploymentType == "Volunteer" ? 'selected="selected"' : ''); ?> >Volunteer</option>
                                                       
                                                       <option value="Intern" <?php echo (isset($iEmploymentType) && $iEmploymentType == "Intern" ? 'selected="selected"' : ''); ?> >Intern</option>
                                                    </select>
                                                </div>
                                                
                                            </div>
                                            
                                            
                                            
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Job Description</label>
                                                <div class="col-sm-9">
                                                    <!-- CKE -->
                                                    <div class="box border primary">
                                                        <div class="box-title">
                                                            <h4><i class="fa fa-pencil-square"></i>Job Description Editor</h4>
                                                            <div class="tools hidden-xs">

                                                                <!-- 	<a href="javascript:;" class="reload">
                                                                                <i class="fa fa-refresh"></i>
                                                                        </a> -->
                                                                <a href="javascript:;" class="collapse">
                                                                    <i class="fa fa-chevron-up"></i>
                                                                </a>
                                                                <!-- 	<a href="javascript:;" class="remove">
                                                                                <i class="fa fa-times"></i>
                                                                        </a> -->
                                                            </div>
                                                        </div>
                                                        <div class="box-body">
                                                            <textarea class="ckeditor" name="tJobDescription"  style="width: 529px; height: 193px;"> <?= (isset($tJobDescription) && $tJobDescription != '') ? $tJobDescription : '' ?></textarea>
                                                        </div>
                                                    </div>
                                                    <!-- /CKE -->

                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="margin0auto disptable">
                                                    <?php echo form_input($submit_attr); ?>
                                                    <a class="btn btn-sm btn-grey" href="<?= BASEURL .''. $this->controller; ?>/viewJobs">Cancel</a>
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
        <link rel="stylesheet" href="<?= base_url() ?>js/bootstrap-fileupload/jasny-bootstrap.min.css" type="text/css" media="screen" />
        <script type="text/javascript" src="<?= base_url() ?>js/ckeditor/ckeditor.js"></script>
        
        
        <script src="<?= base_url() ?>js/bootstrap-fileupload/jasny-bootstrap.min.js" type="text/javascript" charset="utf-8"></script>
        
        
        <script>
            var JobDetailID = parseInt('<?= (isset($iJobDetailID) && $iJobDetailID != '') ? $iJobDetailID : 0; ?>');
            jQuery(document).ready(function () {
                var permission = <?= json_encode($permission); ?>;
                if ((JobDetailID > 0 && permission.indexOf('2') >= 0) || (JobDetailID == 0 && permission.indexOf('1') >= 0)) {

                } else if (permission.indexOf('6') >= 0) {
                    alert('You don\'t have a permission to access this page');
                    window.location.href = '<?= BASEURL; ?>';
                }

                App.setPage("forms");  //Set current page
                App.init(); //Initialise plugins and elements
                $("#validateForm").validate({
                    rules: {
                        vJobTitle: {
                            required: true
                        },
                        vJobLocation: {
                            required: true
                          },
                          iMinExp: {
                            required: true,
                          },
                          iEmploymentType: {
                            required: true
                          },
                          tJobDescription: {
                            required: true
                          },
                    },
                    messages: {
                        vJobTitle: "Please enter Job Title",
                        vJobLocation: "Please enter a Job Location",
                        iMinExp: {required:"Please enter Min Experience required",
                                   number: "Please enter numaric value only"},
                        iEmploymentType: "Please enter Employment Type",
                        tJobDescription: "Please enter Job Description",
                    }
                });

                var _URL = window.URL || window.webkitURL;
               

                // $('.fileinput').fileinput()
                $('.uniform').uniform();
            });



        </script>
    </body>
</html>
