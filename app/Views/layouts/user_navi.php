<ul class="nav navbar-nav-custom pull-right">
    <li>
        <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown">
            Welcome, <strong><?= session()->get('user_fullname'); ?></strong>
            <img src="<?= base_url('img/placeholders/avatars/avatar9.jpg'); ?>" alt="avatar">
        </a>
        <ul class="dropdown-menu dropdown-menu-right">
            <li class="dropdown-header">
               What do you want to do?
            </li>
            <!--<li>
                <a href="<?= base_url('changepassword') ?>" title="Change Password" data-placement="left">
                    <i class="fa fa-inbox fa-fw pull-right"></i>
                    Change Password
                </a>
            </li>
            <li>
                <a href="changelogs.php" title="Change Password" data-placement="left">
                    <i class="fa fa-inbox fa-fw pull-right"></i>
                    Changelogs
                </a>
            </li>-->
            <li>
                <a href="javascript:void(0)" onclick="App.sidebar('toggle-sidebar-alt');this.blur();">
                    <i class="fa fa-inbox fa-fw pull-right"></i>
                    Change Password
                </a>
            </li>
            <li>
                <a href="<?= base_url('logout'); ?>" id="btnlogout" method="post" data-toggle="tooltip" title="Change Password" onclick="cpass()" data-placement="left" title='Logout'>
                    <i class="fa fa-power-off fa-fw pull-right"></i>
                    Log out
                </a>
            </li>
        </ul>
    </li>
</ul>