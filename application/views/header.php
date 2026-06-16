<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" type="image/png" href="<?php echo base_url('assets/img/LCP_logo.png'); ?>">
    <title>Procurement Management Information System </title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <link rel="stylesheet" href="<?php echo base_url('assets/frameworks/bootstrap5/css/bootstrap.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/frameworks/sbadmin/css/sb-admin-2.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/frameworks/sbadmin/vendor/datatables/dataTables.bootstrap4.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/frameworks/sbadmin/vendor/fontawesome-free/css/all.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/font-awesome.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/ionicons.min.css') ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/bootstrap-datepicker.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/dataTables.bootstrap.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/dataTables.checkboxes.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/select2.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/daterangepicker.css'); ?>">


    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <?php
            $can_procurement = $this->aauth->is_member(GROUP_PROCUREMENT);
            $can_admin       = $this->aauth->is_member(GROUP_ADMIN);
            $can_reviewer    = $this->aauth->is_member(GROUP_REVIEWER);
            $can_approver    = $this->aauth->is_member(GROUP_APPROVER);
        ?>

<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center"
        href="<?= base_url('PurchaseRequest'); ?>">

        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-cart-plus"></i>
        </div>

        <div class="sidebar-brand-text mx-3">
            PMIS 
        </div>
    </a>

    <hr class="sidebar-divider my-0">

    <!-- Dashboard -->
    <li class="nav-item">
        <a class="nav-link" href="<?= base_url('Dashboard'); ?>">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <div class="sidebar-heading">
        PROCUREMENT
    </div>

    <!-- Purchase Requests -->
    <li class="nav-item">
        <a class="nav-link" href="<?= base_url('PurchaseRequest'); ?>">
            <i class="fas fa-file-alt"></i>
            <span>Purchase Requests</span>
        </a>
    </li>

    <!-- PPMP -->
    <li class="nav-item">
        <a class="nav-link" href="<?= base_url('PPMP'); ?>">
            <i class="fas fa-shopping-cart"></i>
            <span>PPMP</span>
        </a>
    </li>

    <!-- BAC -->
    <li class="nav-item">
        <a class="nav-link" href="<?= base_url('BAC'); ?>">
            <i class="fas fa-file-contract"></i>
            <span>BAC</span>
        </a>
    </li>

    <?php if ($can_reviewer): ?>

        <hr class="sidebar-divider">

        <div class="sidebar-heading">
            REVIEWER
        </div>

        <li class="nav-item">
            <a class="nav-link" href="<?= base_url('PPMP/myInbox'); ?>">
                <i class="fas fa-inbox"></i>
                <span>Review Inbox</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="<?= base_url('PPMP/forReview'); ?>">
                <i class="fas fa-search"></i>
                <span>For Review</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="<?= base_url('PPMP/returnedDocuments'); ?>">
                <i class="fas fa-undo"></i>
                <span>Returned Documents</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="<?= base_url('PPMP/reviewHistory'); ?>">
                <i class="fas fa-history"></i>
                <span>Review History</span>
            </a>
        </li>

    <?php endif; ?>


    <?php if ($can_approver): ?>

        <hr class="sidebar-divider">

        <div class="sidebar-heading">
            APPROVER
        </div>

        <li class="nav-item">
            <a class="nav-link" href="<?= base_url('PPMP/approvalInbox'); ?>">
                <i class="fas fa-inbox"></i>
                <span>Approval Inbox</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="<?= base_url('PPMP/forApproval'); ?>">
                <i class="fas fa-check-circle"></i>
                <span>For Approval</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="<?= base_url('PPMP/disapprovedDocuments'); ?>">
                <i class="fas fa-times-circle"></i>
                <span>Disapproved</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="<?= base_url('PPMP/approvalHistory'); ?>">
                <i class="fas fa-history"></i>
                <span>Approval History</span>
            </a>
        </li>

    <?php endif; ?>


    <?php if ($can_procurement): ?>

        <hr class="sidebar-divider">

        <div class="sidebar-heading">
            PROCUREMENT OFFICE
        </div>

        <li class="nav-item">
            <a class="nav-link" href="<?= base_url('PPMP/all'); ?>">
                <i class="fas fa-list"></i>
                <span>All PPMP</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="<?= base_url('PurchaseRequest/all'); ?>">
                <i class="fas fa-folder-open"></i>
                <span>All Purchase Requests</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="<?= base_url('ProcurementStaff'); ?>">
                <i class="fas fa-clipboard-check"></i>
                <span>Procurement Review</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="<?= base_url('Tracking'); ?>">
                <i class="fas fa-route"></i>
                <span>Document Tracking</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="<?= base_url('Reports'); ?>">
                <i class="fas fa-chart-bar"></i>
                <span>Reports</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="<?= base_url('UserManagement'); ?>">
                <i class="fas fa-users"></i>
                <span>User Management</span>
            </a>
        </li>

        <!-- Libraries -->
        <li class="nav-item">

            <a class="nav-link collapsed"
                href="#"
                data-toggle="collapse"
                data-target="#collapseLibraries">

                <i class="fas fa-cogs"></i>
                <span>Libraries</span>

            </a>

            <div id="collapseLibraries"
                class="collapse"
                data-parent="#accordionSidebar">

                <div class="bg-white py-2 collapse-inner rounded">

                    <a class="collapse-item" href="<?= base_url('libraries/procurementSteps'); ?>">Procurement Steps</a>

                    <a class="collapse-item" href="<?= base_url('libraries/attachment'); ?>">Attachments</a>

                    <a class="collapse-item" href="<?= base_url('libraries/fund'); ?>">Fund Sources</a>

                    <a class="collapse-item" href="<?= base_url('libraries/item'); ?>">Items</a>

                    <a class="collapse-item" href="<?= base_url('libraries/office'); ?>">Offices</a>

                    <a class="collapse-item" href="<?= base_url('libraries/mode'); ?>">Procurement Modes</a>

                    <a class="collapse-item" href="<?= base_url('libraries/group'); ?>">Groups</a>

                    <a class="collapse-item" href="<?= base_url('libraries/permission'); ?>">Permissions</a>

                    <a class="collapse-item" href="<?= base_url('libraries/supplier'); ?>">Suppliers</a>

                    <a class="collapse-item" href="<?= base_url('libraries/unit'); ?>">Units</a>

                </div>

            </div>

        </li>

    <?php endif; ?>

    <hr class="sidebar-divider d-none d-md-block">

    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - Alerts -->
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-bell fa-fw"></i>
                                <!-- Counter - Alerts -->
                                <span class="badge badge-danger badge-counter">3+</span>
                            </a>
                            <!-- Dropdown - Alerts -->
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="alertsDropdown">
                                <h6 class="dropdown-header">
                                    Alerts Center
                                </h6>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-primary">
                                            <i class="fas fa-file-alt text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500">December 12, 2019</div>
                                        <span class="font-weight-bold">A new monthly report is ready to download!</span>
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-success">
                                            <i class="fas fa-donate text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500">December 7, 2019</div>
                                        $290.29 has been deposited into your account!
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-warning">
                                            <i class="fas fa-exclamation-triangle text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500">December 2, 2019</div>
                                        Spending Alert: We've noticed unusually high spending for your account.
                                    </div>
                                </a>
                                <a class="dropdown-item text-center small text-gray-500" href="#">Show All Alerts</a>
                            </div>
                        </li>

                        <!-- Nav Item - Messages -->
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-envelope fa-fw"></i>
                                <!-- Counter - Messages -->
                                <span class="badge badge-danger badge-counter">7</span>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="messagesDropdown">
                                <h6 class="dropdown-header">
                                    Message Center
                                </h6>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="dropdown-list-image mr-3">
                                        <img class="rounded-circle" src="img/undraw_profile_1.svg"
                                            alt="...">
                                        <div class="status-indicator bg-success"></div>
                                    </div>
                                    <div class="font-weight-bold">
                                        <div class="text-truncate">Hi there! I am wondering if you can help me with a
                                            problem I've been having.</div>
                                        <div class="small text-gray-500">Emily Fowler · 58m</div>
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="dropdown-list-image mr-3">
                                        <img class="rounded-circle" src="img/undraw_profile_2.svg"
                                            alt="...">
                                        <div class="status-indicator"></div>
                                    </div>
                                    <div>
                                        <div class="text-truncate">I have the photos that you ordered last month, how
                                            would you like them sent to you?</div>
                                        <div class="small text-gray-500">Jae Chun · 1d</div>
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="dropdown-list-image mr-3">
                                        <img class="rounded-circle" src="img/undraw_profile_3.svg"
                                            alt="...">
                                        <div class="status-indicator bg-warning"></div>
                                    </div>
                                    <div>
                                        <div class="text-truncate">Last month's report looks great, I am very happy with
                                            the progress so far, keep up the good work!</div>
                                        <div class="small text-gray-500">Morgan Alvarez · 2d</div>
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="dropdown-list-image mr-3">
                                        <img class="rounded-circle" src="https://source.unsplash.com/Mv9hjnEUHR4/60x60"
                                            alt="...">
                                        <div class="status-indicator bg-success"></div>
                                    </div>
                                    <div>
                                        <div class="text-truncate">Am I a good boy? The reason I ask is because someone
                                            told me that people say this to all dogs, even if they aren't good...</div>
                                        <div class="small text-gray-500">Chicken the Dog · 2w</div>
                                    </div>
                                </a>
                                <a class="dropdown-item text-center small text-gray-500" href="#">Read More Messages</a>
                            </div>
                        </li>

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo $this->session->userdata('fullname'); ?></span>
                                <img class="img-profile rounded-circle"
                                    src="img/undraw_profile.svg">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->