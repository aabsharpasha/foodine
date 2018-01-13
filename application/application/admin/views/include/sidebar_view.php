<?php $segment = $this->uri->segment(1); ?>
<!-- SIDEBAR -->
<div id="sidebar" class="sidebar">
    <div class="sidebar-menu nav-collapse">
        <ul>
            <li class="<?= ($segment == 'dashboard') ? 'active' : '' ?>">
                <a href="<?= BASEURL ?>">
                    <i class="fa fa-tachometer fa-fw"></i> 
                    <span class="menu-text">Dashboard</span>
                    <span class="selected"></span>
                </a>
            </li>
            <?php
            /*
             * For the developer purpose You have to allow user 
             * to add multiple user types...
             * 
             * If admin_type id is 1, THAN Logged in User is DEVELOPER
             */
            $ADMINTYPE = $this->session->userdata('ADMINTYPE');
            
            if ($ADMINTYPE == 1 || $ADMINTYPE == 2) {
                ?>
                <li class="has-sub <?= ($segment == 'permission') ? 'active' : '' ?>">
                    <a href="javascript:void(0);" class="">
                        <i class="fa fa-cogs fa-fw"></i><span class="menu-text">Settings</span>
                        <span class="arrow"></span>
                    </a>
                    <ul class="sub">
                        <li>
                            <a href="<?= BASEURL; ?>permission/types">
                                <span class="sub-menu-text">
                                    <i class="fa fa-angle-double-right"></i>Manage Roles
                                </span>
                            </a>
                        </li>
                        <?php if ($ADMINTYPE == 1) { ?>
                            <li>
                                <a href="<?= BASEURL; ?>permission/module">
                                    <span class="sub-menu-text">
                                        <i class="fa fa-angle-double-right"></i> Manage Modules
                                    </span>
                                </a>
                            </li>
                            <li>
                                <a href="<?= BASEURL; ?>permission/pages">
                                    <span class="sub-menu-text">
                                        <i class="fa fa-angle-double-right"></i> Manage Pages
                                    </span>
                                </a>
                            </li>
                        <?php } ?>
                        <li>
                            <a href="<?= BASEURL; ?>permission/manage">
                                <span class="sub-menu-text">
                                    <i class="fa fa-angle-double-right"></i> Manage Permission
                                </span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= BASEURL; ?>permission/user">
                                <span class="sub-menu-text">
                                    <i class="fa fa-angle-double-right"></i> Manage Users
                                </span>
                            </a>
                        </li>
                    </ul>
                </li> 
                <?php
            }
            ?>
            <?php
            $ADMINTYPE = $this->session->userdata('ADMINTYPE');
            $allPage = page_permission($ADMINTYPE);
            //mprd($allPage);
            foreach ($allPage AS $mod_value) {
                $module_name = $mod_value['module']['name'];
                $module_icon = $mod_value['module']['icon'];
                $mod_pages = $mod_value['pages'];

                if ($module_name != '' && !empty($mod_pages)) {
                    $classOpen  = '';
                    $mod_pagesValues   = array_values($mod_pages);
                    if (!empty($mod_pagesValues[0]['url'])) {
                        if(strpos(strtolower("/".$mod_pagesValues[0]['url'])."/", "/".strtolower($this->router->fetch_class())."/")!==false){
                            $classOpen  = "defaultopen";
                        }
                    }
                    ?>
                    <li class="has-sub <?= ($segment == 'appcrash') ? 'active' : '' ?>">
                        <a href="javascript:void(0);" class="<?php echo $classOpen?>">
                            <i class="fa fa-<?= $module_icon; ?> fa-fw"></i> <span class="menu-text"><?= $module_name; ?></span>
                            <span class="arrow"></span>
                        </a>
                        <?php
                        if (!empty($mod_pages)) {
                            ?>
                            <ul class="sub">
                                <?php
                                foreach ($mod_pages as $page_value) {
                                    if (in_array(5, $page_value['permission'])) {
                                        ?>
                                        <li><?php echo anchor($page_value['url'], '<span class="sub-menu-text"><i class="fa fa-angle-double-right fa-fw"></i> ' . $page_value['name'] . '</span>'); ?></li>
                                        <?php
                                    }
                                }
                                ?>
                            </ul>
                            <?php
                        }
                        ?>

                    </li>
                    <?php
                }
            }
            ?>
                    </li>  
        </ul>
    </div>
</div>
