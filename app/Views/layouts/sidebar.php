<!-- Alternative Sidebar -->
<div id="sidebar-alt" tabindex="-1" aria-hidden="true">
    <!-- Toggle Alternative Sidebar Button (visible only in static layout) -->
    <a href="javascript:void(0)" id="sidebar-alt-close" onclick="App.sidebar('toggle-sidebar-alt');"><i class="fa fa-times"></i></a>

    <!-- Wrapper for scrolling functionality -->
    <div id="sidebar-scroll-alt">
        <!-- Sidebar Content -->
        <div class="sidebar-content">
            <!-- Profile -->
             
            <div class="sidebar-section">
                
                <h2 class="text-light">Profile</h2>
                <form id="form_change_pass" method="post" class="form-control-borderless">
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <label for="side-profile-name">Name</label>
                        <input type="text" id="side-profile-name" name="side-profile-name" class="form-control" value="<?= session()->get('user_fullname') ?>" disabled>
                    </div>
                    <hr>
                    <h4>Change Password</h4>
                    <div class="error-box" style="display: none;">
                        <h5><b class="error-message"></b></h5>
                    </div>
                    <div class="form-group prof_old_password">
                        <label for="prof_old_password">Old Password</label>
                        <input type="password" id="prof_old_password" name="prof_old_password" class="form-control">
                        <span class="help-block prof_old_passwordMessage"></span>
                    </div>
                    <div class="form-group prof_new_password">
                        <label for="prof_new_password">New Password</label>
                        <input type="password" id="prof_new_password" name="prof_new_password" class="form-control">
                        <span class="help-block prof_new_passwordMessage"></span>
                    </div>
                    <div class="form-group prof_new_password_confirm">
                        <label for="prof_new_password_confirm">Confirm New Password</label>
                        <input type="password" id="prof_new_password_confirm" name="prof_new_password_confirm" class="form-control">
                        <span class="help-block prof_new_password_confirmMessage">test</span>
                    </div>
                    <div class="form-group remove-margin">
                        <button type="submit" class="btn btn-effect-ripple btn-primary">Save</button>
                        <button type="button" class="btn btn-effect-ripple btn-danger" id="btnCloseSidebar">Close</button>
                    </div>
                </form>
            </div>
            <!-- END Profile -->
        </div>
        <!-- END Sidebar Content -->
    </div>
    <!-- END Wrapper for scrolling functionality -->
</div>
<!-- END Alternative Sidebar -->

<div id="sidebar">
    <div id="sidebar-brand" class="themed-background">
        <a class="sidebar-title">
            <img src="<?= base_url(); ?>img/dohlogo.png" style="width: 30px; height: 30px;"> <span class="sidebar-nav-mini-hide">CHD-I<strong>DTRAK</strong></span>
        </a>
    </div>

    <div id="sidebar-scroll">
        <div class="sidebar-content">
            <ul class="sidebar-nav">

                <li id="inbox" class="<?= ($navactive === 'incoming') ? 'active' : '' ?>">
                    <a href="#" class="sidebar-nav-menu"><i class="fa fa-chevron-left sidebar-nav-indicator sidebar-nav-mini-hide"></i><i class="fa fa-download sidebar-nav-icon"></i><span class="sidebar-nav-mini-hide">Incoming</span></a>
                    <ul>
                        <li>
                            <a href="<?= base_url('doctoreceive/receive') ?>" class="<?= ($navsubactive === 'receiveaction') ? 'active' : '' ?>">Receiving and Releasing</a>
                        </li>
                    </ul>
                </li>
                <li id="outbox" class="<?= ($navactive === 'outgoing') ? 'active' : '' ?>">
                    <a href="#" class="sidebar-nav-menu"><i class="fa fa-chevron-left sidebar-nav-indicator sidebar-nav-mini-hide"></i><i class="fa fa-send sidebar-nav-icon"></i><span class="sidebar-nav-mini-hide">Outgoing</span></a>
                    <ul>
                        <li>
                            <a href="<?= base_url('docview/outgoing') ?>" class="<?= ($navsubactive === 'outgoaction') ? 'active' : '' ?>">Originating and Outgoing</a>
                        </li>
                    </ul>
                </li>

                <li id="report" class="<?= ($navactive === 'report_tables') ? 'active' : '' ?>">
                    <a href="#" class="sidebar-nav-menu"><i class="fa fa-chevron-left sidebar-nav-indicator sidebar-nav-mini-hide"></i><i class="fa fa-file-text-o  sidebar-nav-icon"></i><span class="sidebar-nav-mini-hide">Reports</span></a>
                    <ul>
                        <li>
                                <a href="<?= base_url('report/received') ?>" class="<?= ($navsubactive === 'report_receive') ? 'active' : '' ?>">Received Documents</a>
                        </li>
                        <li>
                                <a href="<?= base_url('report/action_taken') ?>" class="<?= ($navsubactive === 'report_action') ? 'active' : '' ?>">Acted-upon Documents</a>
                        </li>
                        <li>
                            <a href="<?= base_url('report/released_processed') ?>" class="<?= ($navsubactive === 'report_release') ? 'active' : '' ?>">Released and Processed</a>
                        </li>
                    </ul>
                </li>


                <?php if($admin): ?>
                    <div class="sidebar-separator push">
                        <i class="fa fa-ellipsis-h"></i>
                    </div>

                    <li id="reference-tables" class="<?= ($navactive === 'reference') ? 'active' : '' ?>">
                        <a href="#" class="sidebar-nav-menu"><i class="fa fa-chevron-left sidebar-nav-indicator sidebar-nav-mini-hide"></i><i class="fa fa-gears  sidebar-nav-icon"></i><span class="sidebar-nav-mini-hide">Reference Tables</span></a>
                        <ul>

                            <?php if (in_array('1', $admin_menu)): ?>
                                <li id="action-officer">
                                    <a href="<?= base_url('admin/reference/action_officer') ?>" class="<?= ($navsubactive === 'ref_action_officer') ? 'active' : '' ?>">Action Officer</a>
                                </li>
                            <?php endif; ?>
                            <?php if (in_array('2', $admin_menu)): ?>
                                <li id="action-required">
                                    <a href="<?= base_url('admin/reference/action_required') ?>" class="<?= ($navsubactive === 'ref_action_require') ? 'active' : '' ?>">Action Required</a>
                                </li>
                            <?php endif; ?>
                            <?php if (in_array('7', $admin_menu)): ?>
                                <li id="action-taken">
                                    <a href="<?= base_url('admin/reference/action_taken') ?>" class="<?= ($navsubactive === 'ref_action_taken') ? 'active' : '' ?>">Action Taken</a>
                                </li>
                            <?php endif; ?>
                            <?php if (in_array('3', $admin_menu)): ?>
                                <li id="document-type">
                                    <a href="<?= base_url('admin/reference/document_type') ?>" class="<?= ($navsubactive === 'ref_document_type') ? 'active' : '' ?>">Document Type</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </li>

                    <?php if (in_array('5', $admin_menu)): ?>
                        <li>
                            <a href="<?= base_url('admin/document_management') ?>" class="<?= ($navsubactive === 'admin_doc_mgmt') ? 'active' : '' ?>"><i class="fa fa-exclamation-circle sidebar-nav-icon"></i>Document Management</a>
                        </li>
                    <?php endif; ?>

                    <div class="sidebar-separator push">
                        <i class="fa fa-ellipsis-h"></i>
                    </div>

                    <li id="admin-reference-tables" class="<?= ($navactive === 'admin_report_tables') ? 'active' : '' ?>">
                        <a href="#" class="sidebar-nav-menu"><i class="fa fa-chevron-left sidebar-nav-indicator sidebar-nav-mini-hide"></i><i class="gi gi-folder_lock sidebar-nav-icon"></i><span class="sidebar-nav-mini-hide">Administrative Report</span></a>
                        <ul>
                            <?php if (in_array('6', $admin_menu)): ?>
                                <li>
                                    <a href="<?= base_url('admin/report/document_timeline') ?>" class="<?= ($navsubactive === 'admin_doc_time') ? 'active' : '' ?>">Document Timeline</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <div id="sidebar-extra-info" class="sidebar-content sidebar-nav-mini-hide">
        <div class="progress progress-mini push-bit">
            <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>
        </div>
        <div class="text-left">
            <small> </small><br>
            <small> &copy; 2022-23, Version 2.0</small>
        </div>
    </div>
</div>