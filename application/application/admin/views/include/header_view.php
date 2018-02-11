<!-- HEADER -->
<header class="navbar clearfix" id="header">
    <div class="container">
        <div class="navbar-brand" style="padding-left: 60px;">
            <a href="<?= BASEURL ?>" style="text-decoration:blink; color:#FFF; font-size:25px; font-family:'Times New Roman', Times, serif;">Foodine</a>

            <div class="visible-xs">
                <a href="#" class="team-status-toggle switcher btn dropdown-toggle">
                    <i class="fa fa-users"></i>
                </a>
            </div>
            <div id="sidebar-collapse" class="sidebar-collapse btn">
                <i class="fa fa-bars" 
                   data-icon1="fa fa-bars" 
                   data-icon2="fa fa-bars" ></i>
            </div>
        </div>

        <ul class="nav navbar-nav pull-right">
            <?php
            $notification = getNotifications();
//echo '<pre>';print_r($notification); die;
            //mprd($notification);
            //$notification = array();
            if (isset($notification) && ($this->session->userdata('ADMINTYPE') == 1 || $this->session->userdata('ADMINTYPE') == 2) || 1) {
                $notifyCount = count($notification);
                ?>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-bell-o"></i>
<!--                        <span class="badge"><?= $notifyCount == 0 ? '' : $notifyCount; ?></span>-->
                        <span class="badge"></span>
                    </a>
                    <ul class="dropdown-menu notification">
                        <li class="dropdown-title">
                            <span id="badge-count"><i class="fa fa-bell"></i> <?= $notifyCount; ?> Notifications</span>
                        </li>
                        <?php
                        if ($notifyCount > 0) {
                            $limit = $notifyCount >= 5 ? 5 : $notifyCount;

                            for ($i = 0; $i < $limit; $i++) {
                                if (isset($notification[$i]) && !empty($notification[$i])) {
                                    $notifyId = $notification[$i]['notifyId'];
                                    $notifyId1 = $notification[0]['notifyId'];
                                    //echo "hellooooooooo".$notifyId; exit;
                                    $messageValue = '<i>' . $notification[$i]['restaurantName'] . '</i>';
                                   // $messageValue .= ' ' . $notification[$i]['tableString'];
				    if(!empty($notification[$i]['recordField'])){
                                    	$messageValue .= ' <i>' . $notification[$i]['recordField']. '</i>';
				    } if(!empty($notification[$i]['fromValue'])){
                                        $messageValue .= ' ' . $notification[$i]['fromValue'] . '.';
                                    }
                                    ?>
                                    <li class="notification-li">
                                        <a href="<?= BASEURL; ?>booking" >
                                            <span class="label label-success">
                                                <i class="fa fa-user"></i>
                                            </span>
                                            <span class="body">
                                                <span class="message"><?= $messageValue; ?></span>
                                                <span class="time">
                                                    <i class="fa fa-clock-o"></i>
                                                    <span><?= $notification[$i]['notifyDate']; ?></span>
                                                </span>
                                            </span>
                                        </a>
                                    </li>
                                    <?php
                                }
                            }
                           // echo "hello".$notifyId; exit;
                            ?>
                            <input type="hidden" id="lastNotifyId" value="<?= $notifyId1; ?>" />
                            <?php
                        } else {
                            ?>
                            <li class="no-notification">
                                <a href="javascript:void(0);">
                                    <span class="label label-success">
                                        <i class="fa fa-flag"></i>
                                    </span>
                                    <span class="body">
                                        <span class="message">No Notification available.</span>
                                    </span>
                                </a>
                            </li>
                            <?php
                        }
                        ?>
                        <li class="footer">
                            <a href="<?= BASEURL; ?>booking">See All Notifications<i class="fa fa-angle-double-right"></i></a>
                        </li>
                    </ul>
                </li>
            <?php } ?>
            <li class="dropdown user pull-right" id="header-user">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <img alt="" style="max-width: 30px;" src="<?= BASEURL . 'img/Avatar.png' ?>" />
                    <span class="username"><?php echo $this->session->userdata('ADMINFIRSTNAME') . " " . $this->session->userdata('ADMINLASTNAME'); ?></span>
                    <i class="fa fa-angle-down"></i>
                </a>
                <ul class="dropdown-menu">
                    <li><?php echo anchor("admin/add/" . $this->session->userdata('ADMINID') . "/y", "<i class='fa fa-user'></i> My Profile"); ?></li>
                    <li><?php echo anchor("changepassword/", "<i class='fa fa-eye'></i> Change Password"); ?></li>
                    <li>
                        <a href="<?= BASEURL; ?>logout">
                            <i class=" fa fa-power-off"></i> Logout
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
    <div id="sound"></div>
</header>

<!--/HEADER -->
