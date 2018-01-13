<?php
$headerData = $this->headerlib->data();

if(empty($restaurants)){
    $restaurants  = array(); 
}

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
                                            <li>Category</li>
                                        </ul>
                                        <div class="clearfix">
                                            <h3 class="content-title pull-left">Category</h3>
                                        </div>
                                        <div class="description">Map Category Restaurants</div>
                                    </div>
                                </div>
                            </div>
                            <?= $this->general_model->getMessages() ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- BOX -->
                                    <div class="box border primary">
                                        <div class="box-title">
                                            <h4><i class="fa fa-plus-circle"></i>Map Restaurants To Category </h4>
                                            <div class="tools hidden-xs">
                                                <a href="javascript:void(0);" class="collapse">
                                                    <i class="fa fa-chevron-up"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="box-body big">
                                            <?php
                                            echo form_open("category/mapCategoryRestaurants", $form_attr);
                                            ?>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label" for="iCategoryID">Select Category<span class="required">*</span></label>
                                                <div class="col-sm-9 user-select">
                                                    <select class="form-control maxwidth500" name="iCategoryID" id="iCategoryID">
                                                        <option value=""> - Select -</option>
                                                        <?php foreach($categoryList AS $category){ ?>
                                                        <option value="<?php echo $category["iCategoryID"]; ?>" <?php if(isset($iCategoryID) && $category["iCategoryID"]==$iCategoryID) echo "selected" ?>><?php echo ucfirst($category["vCategoryName"]); ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-3 control-label" for="tUsers">Select Restaurants<span class="required">*</span></label>
                                                <div class="col-sm-9 user-select" id="restaurantSelectDiv">
                                                </div>
                                            </div>
                                            <br>
                                            <div class="form-group" style="text-align: center">
                                                <div style="display: inline-block;width:100px;">
                                                    <?php echo form_input($submit_attr); ?>
                                                </div>
                                                <div style="display: inline-block;width:100px;">
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
        <script>
            var controller = '<?php echo $this->controller; ?>'
            var catId = parseInt('<?= (isset($iCategoryID) && $iCategoryID != '') ? $iCategoryID : 0; ?>');
            jQuery(document).ready(function () {
                var permission = <?= json_encode($permission); ?>;
                if ((catId > 0 && permission.indexOf('2') >= 0) || (catId == 0 && permission.indexOf('1') >= 0)) {

                } else if (permission.indexOf('6') >= 0) {
                    alert('You don\'t have a permission to access this page');
                    window.location.href = '<?= BASEURL; ?>';
                }

                App.setPage("forms");  //Set current page
                App.init(); //Initialise plugins and elements
                
                function changeCategory(){
                    $.ajax({
                        type: 'POST',
                        dataType: 'html',
                        data: {},
                        url: BASEURL + controller + '/getMappedRestaurants/' +  $("#iCategoryID").val(),
                        success: function (resp) {
                            $("#restaurantSelectDiv").html(resp);

                        }
                    });
                }
                changeCategory();
                $("#iCategoryID").change(changeCategory);
                    
                
                $("#validateForm").validate({
                    rules: {
                        vCategoryName: {
                            required: true
                        },
                    },
                    messages: {
                        vCategoryName: "Please enter a Category Name",
                    }
                });

                // $('.fileinput').fileinput()
                $('.uniform').uniform();
            });



        </script>
    </body>
</html>
