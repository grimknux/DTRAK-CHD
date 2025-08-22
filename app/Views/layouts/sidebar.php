<div id="sidebar">
    <div id="sidebar-brand" class="themed-background">
        <a class="sidebar-title">
            <img src="<?= base_url(); ?>public/img/dohlogo.png" style="width: 30px; height: 30px;"> <span class="sidebar-nav-mini-hide">CHD-I<strong>DTRAK</strong></span>
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
                                    <a href="action_required.php">Action Required</a>
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