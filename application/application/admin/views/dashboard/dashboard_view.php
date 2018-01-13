<?php
$headerData = $this->headerlib->data();
$total_users = $this->dashboard_model->getNumberOfUsers();
$total_category = $this->dashboard_model->getNumberOfCategories();
$total_cui = $this->dashboard_model->getNumberOfCuisine();
$total_facility = $this->dashboard_model->getNumberOfFacility();
$total_music = $this->dashboard_model->getNumberOfMusic();
$total_res = $this->dashboard_model->getNumberOfRestaurant();
$total_deals = $this->dashboard_model->getNumberOfDeals();
$ADMINTYPE = $this->session->userdata('ADMINTYPE');
// $total_subject = $this->dashboard_model->getNumberOfSubject();

?>
<!doctype html>
<html lang="en-us">
    <head>
        <title><?= $title; ?></title>
        <?= $headerData['meta_tags']; ?>
        <?= $headerData['stylesheets_dash']; ?>
    </head>
    <body>
        <?php $this->load->view('include/header_view') ?>
        <section id="page">
            <!-- SIDEBAR -->
            <?php $this->load->view('include/sidebar_view');?>
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
                                                <a href="#">Home</a>
                                            </li>
                                            <li>Dashboard</li>
                                        </ul>
                                        <div class="clearfix">
                                            <h3 class="content-title pull-left">Dashboard</h3>
                                        </div>
                                        <div class="description">Overview, Statistics and more</div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">

                                        <?php if ($ADMINTYPE !== 3) { ?>
                                            <a href="<?= BASEURL ?>user">
                                                <div class="col-lg-3">
                                                    <div class="dashbox panel panel-default">
                                                        <div class="panel-body">
                                                            <div class="panel-left blue">
                                                                <i class="fa fa-users fa-3x"></i>
                                                            </div>
                                                            <div class="panel-right">
                                                                <div class="number"><?php echo $total_users; ?></div>
                                                                <?php
                                                                if ($total_users > 1)
                                                                    echo '<div class="title">Total Users</div>';
                                                                else
                                                                    echo '<div class="title">Total User</div>';
                                                                ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>

<!--                                            <a href="<?= BASEURL ?> category">
                                                <div class="col-lg-3">
                                                    <div class="dashbox panel panel-default">
                                                        <div class="panel-body">
                                                            <div class="panel-left blue">
                                                                <i class="fa fa-list fa-3x"></i>
                                                            </div>
                                                            <div class="panel-right">
                                                                <div class="number"><?php echo $total_category; ?></div>
                                                                <?php
                                                                if ($total_category > 1)
                                                                    echo '<div class="title">Total Categories</div>';
                                                                else
                                                                    echo '<div class="title">Total Category</div>';
                                                                ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>-->



                                            <a href="<?= BASEURL ?>cuisine">
                                                <div class="col-lg-3">
                                                    <div class="dashbox panel panel-default">
                                                        <div class="panel-body">
                                                            <div class="panel-left blue">
                                                                <i class="fa fa-list fa-3x"></i>
                                                            </div>
                                                            <div class="panel-right">
                                                                <div class="number"><?php echo $total_cui; ?></div>
                                                                <?php
                                                                if ($total_cui > 1)
                                                                    echo '<div class="title">Total Cuisines</div>';
                                                                else
                                                                    echo '<div class="title">Total Cuisine</div>';
                                                                ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>

                                            <a href="<?= BASEURL ?>facility">
                                                <div class="col-lg-3">
                                                    <div class="dashbox panel panel-default">
                                                        <div class="panel-body">
                                                            <div class="panel-left blue">
                                                                <i class="fa fa-list fa-3x"></i>
                                                            </div>
                                                            <div class="panel-right">
                                                                <div class="number"><?php echo $total_facility; ?></div>
                                                                <?php
                                                                if ($total_facility > 1)
                                                                    echo '<div class="title">Total Facilities</div>';
                                                                else
                                                                    echo '<div class="title">Total Facility</div>';
                                                                ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>

<!--                                            <a href="<?= BASEURL ?>music">
                                                <div class="col-lg-3">
                                                    <div class="dashbox panel panel-default">
                                                        <div class="panel-body">
                                                            <div class="panel-left blue">
                                                                <i class="fa fa-music fa-3x"></i>
                                                            </div>
                                                            <div class="panel-right">
                                                                <div class="number"><?php echo $total_music; ?></div>
                                                                <?php
                                                                if ($total_music > 1)
                                                                    echo '<div class="title">Total Music</div>';
                                                                else
                                                                    echo '<div class="title">Total Music</div>';
                                                                ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>-->

                                            <a href="<?= BASEURL ?>restaurant">
                                                <div class="col-lg-3">
                                                    <div class="dashbox panel panel-default">
                                                        <div class="panel-body">
                                                            <div class="panel-left blue">
                                                                <i class="fa fa-home fa-3x"></i>
                                                            </div>
                                                            <div class="panel-right">
                                                                <div class="number"><?php echo $total_res; ?></div>
                                                                <?php
                                                                if ($total_res > 1)
                                                                    echo '<div class="title">Total Restaurants</div>';
                                                                else
                                                                    echo '<div class="title">Total Restaurant</div>';
                                                                ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        <?php } ?>

                                        <?php if ($ADMINTYPE == 1 || $ADMINTYPE == 2 || $ADMINTYPE == 3) { ?>
                                            <a href="<?= BASEURL ?>deals">
                                                <div class="col-lg-3">
                                                    <div class="dashbox panel panel-default">
                                                        <div class="panel-body">
                                                            <div class="panel-left blue">
                                                                <i class="fa fa-list-alt fa-3x"></i>
                                                            </div>
                                                            <div class="panel-right">
                                                                <div class="number"><?php echo $total_deals; ?></div>
                                                                <?php
                                                                if ($total_deals > 1)
                                                                    echo '<div class="title">Total Deals</div>';
                                                                else
                                                                    echo '<div class="title">Total Deal</div>';
                                                                ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        <?php } ?>

                                        <?php if ($ADMINTYPE == 3) {
                                            ?>
                                            <a href="<?= BASEURL ?>appcrash">
                                                <div class="col-lg-3">
                                                    <div class="dashbox panel panel-default">
                                                        <div class="panel-body">
                                                            <div class="panel-left blue">
                                                                <i class="fa fa-cogs fa-3x"></i>
                                                            </div>
                                                            <div class="panel-right">
                                                                <div class="number">Crash List</div>
                                                                <div class="title"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                            <?php }
                                        ?>
                                    </div>
                                </div>
                            </div>

                            <?php $this->load->view('include/footer_view') ?>
                        </div>
                    </div>
                    <div class="separator"></div>
                    <div class="container">
                        <?php $this->load->view('include/copy_right_view') ?>
                    </div>
                </div>
            </div>
        </section>
        <?= $headerData['javascript_dash']; ?>
        <script src="<?= DOMAIN_URL ?>/admin/js/flot/jquery.flot.min.js"></script>
        <script src="<?= DOMAIN_URL ?>/admin/js/flot/jquery.flot.time.min.js"></script>
        <script src="<?= DOMAIN_URL ?>/admin/js/flot/jquery.flot.selection.min.js"></script>
        <script src="<?= DOMAIN_URL ?>/admin/js/flot/jquery.flot.resize.min.js"></script>
        <script src="<?= DOMAIN_URL ?>/admin/js/flot/jquery.flot.pie.min.js"></script>
        <script src="<?= DOMAIN_URL ?>/admin/js/flot/jquery.flot.stack.min.js"></script>
        <script src="<?= DOMAIN_URL ?>/admin/js/flot/jquery.flot.crosshair.min.js"></script>
        <script src="<?= DOMAIN_URL ?>/admin/js/charts.js"></script>
        <script>
            jQuery(document).ready(function () {
                App.setPage("flot_charts");
                App.init();
            });
            jQuery('.panel-body').hover(function () {
                $(this).children("div").addClass('red').removeClass('blue');
            }, function () {
                $(this).children("div").addClass('blue').removeClass('red');
            });
        </script>
    </body>
</html>
