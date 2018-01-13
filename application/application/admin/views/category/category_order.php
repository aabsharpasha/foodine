<?php
$headerData = $this->headerlib->data();

$ADMINTYPE = $this->session->userdata('ADMINTYPE');
$permission = get_page_permission($ADMINTYPE, 21);
?>
<!doctype html>
<html lang="en-us">
    <head>
        <title>Category Recorder</title>
        <?= $headerData['meta_tags']; ?>
        <?= $headerData['stylesheets']; ?>
        <style>
            #sortable { list-style-type: none; margin: 0; padding: 0; width: 60%; }
            .ui-state-default, .ui-widget-content .ui-state-default, .ui-widget-header .ui-state-default{
                border: 1px solid #FFFFFF !important;
                background: #67798B !important;
                font-weight: normal !important;
                color: #FFF !important;
                padding: 7px !important;
                margin: 5px !important;
                cursor: move !important;
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
                                            <h3 class="content-title pull-left"><?php echo $this->uppercase; ?> Reorder</h3>
                                        </div>
                                        <div class="description">Category Reorder</div>
                                    </div>
                                </div>
                            </div>
                            <?= $this->general_model->getMessages() ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="box border primary">
                                        <div class="box-body">
                                            <form action="<?= BASEURL; ?>category/saveorder" method="post">
                                                <ul id="sortable">
                                                    <?php for ($i = 0; $i < count($reorderdata); $i++) { ?>
                                                        <li class="ui-state-default">
                                                            <input type="hidden" value="<?= $reorderdata[$i]['iCategoryID']; ?>" name="ordercat[]">
                                                            <?= $reorderdata[$i]['vCategoryName']; ?>
                                                        </li>
                                                    <?php } ?>
                                                </ul>

                                                <div class="margin-top-10">
                                                    <input type="submit" class="btn btn-primary" value="Save" />
                                                </div>
                                            </form>
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
            App.init();
            $(function () {
                var permission = <?= json_encode($permission); ?>;
                if (permission.indexOf('6') >= 0) {
                    alert('You don\'t have a permission to access this page');
                    window.location.href = '<?= BASEURL; ?>';
                }
                
                $("#sortable").sortable({
                    revert: true
                });
                $("#sortable").disableSelection();
            });
        </script>
    </body>
</html>