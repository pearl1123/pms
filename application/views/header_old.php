<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <link rel="icon" type="image/png" href="<?php echo base_url('assets/img/LCP_logo.png'); ?>">
  <title>LCP | Electronic Medical Record (EMR) </title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="<?php echo base_url('assets/css/bootstrap.min.css'); ?>">
   <link rel="stylesheet" href="<?php echo base_url('assets/css/bootstrap.min.css'); ?>">
  <link rel="stylesheet" href="<?php echo base_url('assets/css/font-awesome.min.css'); ?>">
  <link rel="stylesheet" href="<?php echo base_url('assets/css/ionicons.min.css') ?>">
  <link rel="stylesheet" href="<?php echo base_url('assets/css/adminlte.min.css') ?>">
  <link rel="stylesheet" href="<?php echo base_url('assets/css/skin-blue-light.min.css') ?>">
  <link rel="stylesheet" href="<?php echo base_url('assets/css/blue.css'); ?>">
  <link rel="stylesheet" href="<?php echo base_url('assets/css/bootstrap-datepicker.css'); ?>">
  <link rel="stylesheet" href="<?php echo base_url('assets/css/dataTables.bootstrap.min.css'); ?>">
  <link rel="stylesheet" href="<?php echo base_url('assets/css/dataTables.checkboxes.css'); ?>">
  <link href="<?php echo base_url('assets/css/responsive.bootstrap4.min.css'); ?>" rel="stylesheet" media="all">
  <link rel="stylesheet" href="<?php echo base_url('assets/css/buttons.bootstrap4.min.css') ?>">
  <link rel="stylesheet" href="<?php echo base_url('assets/css/select2.min.css'); ?>">
  <link rel="stylesheet" href="<?php echo base_url('assets/css/daterangepicker.css'); ?>">

  <script src="<?php echo base_url('assets/js/jquery.min.js'); ?>"></script>
</head>

<body class="skin-blue-light fixed sidebar-mini sidebar-mini-expand-feature" style="height: auto; min-height: 100%;">

  <div class="wrapper" style="height: auto; min-height: 100%;">
    <header class="main-header">
      <a href="" class="logo">
        <span class="logo-mini"><img src="<?php echo base_url('assets/img/LCP_logo.png'); ?>" style="height: 20px;width: 30px;margin-bottom: 5px"></span>
        <span class="logo-lg"><img src="<?php echo base_url('assets/img/LCP_logo.png'); ?>" style="height: 25px;width: 40px;margin-bottom: 5px"><small> e-Medical Record</small></span>
      </a>

      <!-- Navbar  on the upper right corner of the page -->
      <nav class="navbar navbar-static-top">
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </a>

        <div class="navbar-custom-menu">
          <ul class="nav navbar-nav">

            <!-- NOTIFICATIONS-->
            <li class="dropdown notifications-menu">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <i class="fa fa-bell-o faa-ring "></i>
                <span class="label lbl_notif label-danger" id="tot_notif"><?php echo (($notif || $confi || $ondue) ? $total = $notif + $confi + $ondue : 0); ?></span>
              </a>
              <ul class="dropdown-menu">
                <li class="header">You have a notifications</li>
                <li>
                  <ul class="menu">
                    <li>
                      <a href="<?php echo base_url('Notification/notificationList'); ?>">
                        <i class="fa fa-envelope lbl_notif1 text-danger"></i> <span id="tot_notif1"><?php echo ($notif ?  $notif :  0); ?></span> unread documents to receive
                      </a>
                    </li>

                    <li>
                      <a href="<?php echo base_url('Confidential/confidentialList'); ?>">
                        <i class="fa fa-lock text-primary"></i><span id="tot_notif_confidential"><?php echo ($confi ?  $confi : 0); ?></span> confidential documents to receive
                      </a>
                    </li>

                    <li>
                      <a href="<?php echo base_url('Notification/notificationOnDueList'); ?>">
                        <i class="fa fa-flag lbl_notif1 text-danger"></i> <span id="tot_notif1"><?php echo ($ondue ?  $ondue :  0); ?></span> on due documents to approve
                      </a>
                    </li>
                  </ul>
                </li>
                <li class="footer"><a href="<?php echo base_url('Notification/notificationList'); ?>">View all</a></li>
              </ul>
            </li>

            <!-- User Account Details -->
            <li class="dropdown user user-menu">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <img src="<?php echo base_url('assets/img/user_def.png'); ?>" class="user-image" alt="User Image">
                <span class="hidden-xs"><?php echo ucwords($fullname); ?></span>
              </a>
              <ul class="dropdown-menu">
                <!-- User image -->
                <li class="user-header">
                  <img src="<?php echo base_url('assets/img/user_def.png'); ?>" class="img-circle" alt="User Image">
                  <p>
                    <?php echo ucwords($fullname); ?>
                    <small>[<?php echo $this->session->office; ?>]</small>
                    <small>Registered since <?php echo date("F Y", strtotime($this->session->date_created)); ?></small>
                  </p>
                </li>

                <li class="user-footer">
                  <div class="row">
                    <div class="col-xs-12 text-center">
                      <div class="btn-group btn-block btn-group-toggle" data-toggle="buttons">
                        <label class="btn btn-sm <?= ($this->session->ui == 1) ? 'bg-primary' : 'bg-default'; ?>">
                          <input type="radio" name="ui" value="1" autocomplete="off" checked> Table-Based
                        </label>
                        <label class="btn btn-sm <?= ($this->session->ui == 2) ? 'bg-primary' : 'bg-default'; ?>">
                          <input type="radio" name="ui" value="2" autocomplete="off"> Card-Based
                        </label>
                      </div>
                    </div>
                  </div>
                </li>

                <!-- Menu Footer-->
                <li class="user-footer">
                  <div class="pull-left">
                    <a href="<?php echo base_url('User/changePassword/') . $this->session->uid; ?>" class="btn btn-default btn-flat">Change Password</a>
                  </div>
                  <div class="pull-right">
                    <button name="btnLogout" id="btnLogout" class="btn btn-default btn-flat">Sign Out</button>
                  </div>
                </li>
              </ul>
            </li>

          </ul>
        </div>
      </nav>
    </header>

    <!-- =============================================== -->

    <!-- Left side column. contains the sidebar -->
    <aside class="main-sidebar">
      <!-- sidebar: style can be found in sidebar.less -->
      <div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; height: 99px;">
        <section class="sidebar" style="overflow: hidden; width: auto; height: 99px;">



          <ul class="sidebar-menu" data-widget="tree">
            <?php if (empty($guest_view)): ?>

              <?php if ($this->session->logged_in): ?>
                <?php if ($this->session->is_super_admin == 1) { ?>
                  <li class="header">ADMINISTRATION</li>
                  <li class="treeview">
                    <a href="<?php echo base_url('HospitalProfile/hospitalProfile'); ?>"><i class="fa fa-hospital-o"></i><span> Hospital Profile</span></a>
                  </li>
                  <li class="treeview">
                    <a href="#">
                      <i class="fa fa-user"></i> <span>User Management</span>
                      <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                      </span>
                    </a>
                    <ul class="treeview-menu">
                      <li><a href="<?php echo base_url('UserManagement'); ?>"><i class="fa fa-circle-o"></i>Users</a></li>
                      <li><a href="<?php echo base_url('Libraries/groupList'); ?>"><i class="fa fa-circle-o"></i>Group Library</a></li>
                      <li class="treeview">
                        <a href="#">
                          <i class="fa fa-circle-o" aria-hidden="true"></i> <span>Permission Library</span>
                          <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                          </span>
                        </a>
                        <ul class="treeview-menu">
                          <li><a href="<?php echo base_url('Libraries/region_listview'); ?>"><i class="fa fa-circle"></i>Module Main Group</a></li>
                          <li><a href="<?php echo base_url('Libraries/province_listview'); ?>"><i class="fa fa-circle"></i>Module Sub Group</a></li>
                          <li><a href="<?php echo base_url('Libraries/permissionList'); ?>"><i class="fa fa-circle"></i>Permission List</a></li>
                        </ul>
                      </li>
                    </ul>
                  </li>

                  <li class="treeview">
                    <a href="#">
                      <i class="fa fa-th-large" aria-hidden="true"></i> <span>Clinical Notes Manager</span>
                      <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                      </span>
                    </a>
                    <ul class="treeview-menu">
                      <li><a href="<?php echo base_url('ClinicalNotes/listview'); ?>"><i class="fa fa-circle-o"></i>Clinical Notes List</a></li>

                    </ul>
                  </li>

                  <li class="treeview">
                    <a href="#">
                      <i class="fa fa-th-large" aria-hidden="true"></i> <span>System Libraries</span>
                      <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                      </span>
                    </a>
                    <ul class="treeview-menu">

                      <!-- Area Demographics -->
                      <li class="treeview">
                        <a href="#">
                          <i class="fa fa-circle-o" aria-hidden="true"></i> <span>Area Demographics</span>
                          <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                          </span>
                        </a>
                        <ul class="treeview-menu">
                          <li><a href="<?php echo base_url('Libraries/region_listview'); ?>"><i class="fa fa-circle"></i> Region</a></li>
                          <li><a href="<?php echo base_url('Libraries/province_listview'); ?>"><i class="fa fa-circle"></i> Province</a></li>
                          <li><a href="<?php echo base_url('Libraries/city_listview'); ?>"><i class="fa fa-circle"></i> City</a></li>
                          <li><a href="<?php echo base_url('Libraries/barangay_listview'); ?>"><i class="fa fa-circle"></i> Barangay</a></li>
                        </ul>
                      </li>

                      <!-- General Libraries -->
                      <li><a href="<?php echo base_url('Libraries/appointmentStatus_listview'); ?>"><i class="fa fa-circle-o"></i> Appointment Status</a></li>
                      <li><a href="<?php echo base_url('Libraries/appointmentType_listview'); ?>"><i class="fa fa-circle-o"></i> Appointment Type</a></li>
                      <li><a href="<?php echo base_url('Libraries/billType_listview'); ?>"><i class="fa fa-circle-o"></i> Bill Type</a></li>
                      <li><a href="<?php echo base_url('Libraries/bloodtype'); ?>"><i class="fa fa-circle-o"></i> Blood Type</a></li>

                      <li><a href="<?php echo base_url('Libraries/category_listview'); ?>"><i class="fa fa-circle-o"></i> Category</a></li>
                      <li><a href="<?php echo base_url('Libraries/subcategory_listview'); ?>"><i class="fa fa-circle-o"></i> Sub-Category</a></li>
                      <li><a href="<?php echo base_url('Libraries/chiefComplaint_listview'); ?>"><i class="fa fa-circle-o"></i> Chief Complaint</a></li>

                      <li><a href="<?php echo base_url('Libraries/title_listview'); ?>"><i class="fa fa-circle-o"></i> Title</a></li>
                      <li><a href="<?php echo base_url('Libraries/ext_name_listview'); ?>"><i class="fa fa-circle-o"></i> Extension Name</a></li>
                      <li><a href="<?php echo base_url('Libraries/marital_listview'); ?>"><i class="fa fa-circle-o"></i> Marital Status</a></li>
                      <li><a href="<?php echo base_url('Libraries/nationality_listview'); ?>"><i class="fa fa-circle-o"></i> Nationality</a></li>
                      <li><a href="<?php echo base_url('Libraries/occupation_listview'); ?>"><i class="fa fa-circle-o"></i> Occupation</a></li>
                      <li><a href="<?php echo base_url('Libraries/office_listview'); ?>"><i class="fa fa-circle-o"></i> Office</a></li>
                      <li><a href="<?php echo base_url('Libraries/religion_listview'); ?>"><i class="fa fa-circle-o"></i> Religion</a></li>
                      <li><a href="<?php echo base_url('Libraries/highest_educational_attainment'); ?>"><i class="fa fa-circle-o"></i>Highest Educ Attainment</a></li>
                      <li><a href="<?php echo base_url('Libraries/relationshipToPatient_listview'); ?>"><i class="fa fa-circle-o"></i> Relationship to Patient</a></li>
                      <li><a href="<?php echo base_url('Libraries/doctor_listview'); ?>"><i class="fa fa-circle-o"></i> Doctor</a></li>
                      <li><a href="<?php echo base_url('Libraries/specialization_listview'); ?>"><i class="fa fa-circle-o"></i> Specialization</a></li>
                      <li><a href="<?php echo base_url('Libraries/ward_listview'); ?>"><i class="fa fa-circle-o"></i> Ward</a></li>


                      <!-- Medication Tab -->
                      <li class="treeview">
                        <a href="#"><i class="fa fa-circle-o"></i> Medication
                          <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                        </a>
                        <ul class="treeview-menu">
                          <li><a href="<?php echo base_url('Libraries/dosage_listview'); ?>"><i class="fa fa-circle-o"></i> Dosage</a></li>
                          <li><a href="<?php echo base_url('Libraries/duration_listview'); ?>"><i class="fa fa-circle-o"></i> Duration</a></li>
                          <li><a href="<?php echo base_url('Libraries/frequency_listview'); ?>"><i class="fa fa-circle-o"></i> Frequency</a></li>
                          <li><a href="<?php echo base_url('Libraries/medicine_listview'); ?>"><i class="fa fa-circle-o"></i> Medicine</a></li>
                          <li><a href="<?php echo base_url('Libraries/generic_listview'); ?>"><i class="fa fa-circle-o"></i> Generic</a></li>
                          <li><a href="<?php echo base_url('Libraries/route_listview'); ?>"><i class="fa fa-circle-o"></i> Route</a></li>
                        </ul>
                      </li>

                      <!-- Immunization Tab -->
                      <li class="treeview">
                        <a href="#"><i class="fa fa-circle-o"></i> Immunization
                          <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                        </a>
                        <ul class="treeview-menu">
                          <li><a href="<?php echo base_url('Libraries/dose_listview'); ?>"><i class="fa fa-circle-o"></i> Dose</a></li>
                          <li><a href="<?php echo base_url('Libraries/site_listview'); ?>"><i class="fa fa-circle-o"></i> Site</a></li>
                          <li><a href="<?php echo base_url('Libraries/vaccine_listview'); ?>"><i class="fa fa-circle-o"></i> Vaccine</a></li>
                        </ul>
                      </li>

                      <!-- Documents Tab -->
                      <li class="treeview">
                        <a href="#"><i class="fa fa-circle-o"></i> Documents
                          <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                        </a>
                        <ul class="treeview-menu">
                          <li><a href="<?php echo base_url('Libraries/document_type_listview'); ?>"><i class="fa fa-circle-o"></i> Document Type</a></li>
                        </ul>
                      </li>

                      <!-- Death Certificate Tab -->
                      <li class="treeview">
                        <a href="#"><i class="fa fa-circle-o"></i> Death Certificate
                          <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                        </a>
                        <ul class="treeview-menu">
                          <li><a href="<?php echo base_url('Libraries/corpse_disposal_listview'); ?>"><i class="fa fa-circle-o"></i> Corpse Disposal</a></li>
                          <li><a href="<?php echo base_url('Libraries/method_of_delivery_listview'); ?>"><i class="fa fa-circle-o"></i> Method of Delivery</a></li>
                          <li><a href="<?php echo base_url('Libraries/multiple_birth_listview'); ?>"><i class="fa fa-circle-o"></i> If Multiple Birth</a></li>
                          <li><a href="<?php echo base_url('Libraries/type_of_birth_listview'); ?>"><i class="fa fa-circle-o"></i> Type of Birth</a></li>
                          <li><a href="<?php echo base_url('Libraries/postmortem_listview'); ?>"><i class="fa fa-circle-o"></i> Postmortem</a></li>
                          <li><a href="<?php echo base_url('Libraries/embalmer_listview'); ?>"><i class="fa fa-circle-o"></i> Embalmer</a></li>
                        </ul>
                      </li>

                    </ul>
                  </li>
                <?php } ?>


                <li class="header">MAIN NAVIGATION</li>

                <?php if ($this->session->is_super_admin == 1 || $this->session->is_doctor == 1) { ?>
                  <li class="treeview">
                    <a href="<?php echo base_url('Doctor/doctor_listview'); ?>">
                      <i class="fa fa-user-md"></i><span> Doctor</span>
                    </a>
                  </li>
                <?php } ?>

                <?php if ($this->session->is_super_admin == 1 || $this->session->is_med_rec == 1) { ?>

                  <li class="treeview">
                    <a href="#">
                      <i class="fa fa-user-md" aria-hidden="true"></i><span>Medical Records</span>
                      <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                      </span>
                    </a>
                    <ul class="treeview-menu">
                      <li><a href="<?php echo base_url('MedicalRecords/medicalrecords_listview'); ?>"><i class="fa fa-circle-o"></i>Patient Records</a></li>
                    </ul>
                  </li>
                <?php } ?>



                <?php if ($this->session->is_super_admin == 1 || $this->session->is_nurse == 1 || $this->session->is_med_rec == 1) { ?>
                  <li class="treeview">
                    <a href="#">
                      <i class="fa fa-user" aria-hidden="true"></i><span>Patient List</span>
                      <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                      </span>
                    </a>
                    <ul class="treeview-menu">
                      <li><a href="<?php echo base_url('PatientRegistration/allpatientList'); ?>"><i class="fa fa-circle-o"></i>All Patient</a></li>
                      <li><a href="<?php echo base_url('PatientRegistration/inpatientList'); ?>"><i class="fa fa-circle-o"></i>Inpatient</a></li>
                      <li><a href="<?php echo base_url('PatientRegistration/outpatientList'); ?>"><i class="fa fa-circle-o"></i>Outpatient</a></li>
                      <li><a href="<?php echo base_url('PatientRegistration/erpatientList'); ?>"><i class="fa fa-circle-o"></i>Emergency Room</a></li>
                    </ul>
                  </li>
                <?php } ?>

                <?php if ($this->session->is_super_admin == 1 || $this->session->is_doctor != 1 && $this->session->is_med_rec != 1) { ?>
                  <li class="treeview">
                    <a href="<?php echo base_url('Beds/beds_listview'); ?>">
                      <i class="fa fa-bed"></i><span> Beds</span>
                    </a>
                  </li>
                <?php } ?>

                <?php if ($this->session->is_super_admin == 1 || $this->session->is_doctor != 1 && $this->session->is_med_rec != 1) { ?>
                  <li class="treeview">
                    <a href="#">
                      <i class="fa fa-medkit"></i><span> Wards</span>
                      <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                      </span>
                    </a>
                    <ul class="treeview-menu">
                      <li><a href="<?php echo base_url('Wards/wards_listview'); ?>"><i class="fa fa-circle-o"></i>CARED Monitoring</a></li>

                    </ul>
                  </li>
                <?php } ?>
                <!-- <li class="treeview">
              <a href="<?php echo base_url('Wards/wards_listview'); ?>">
                <i class="fa fa-medkit"></i><span> Wards</span>
              </a>
            </li> -->
                <!-- <li class="treeview">
              <a href="<?php echo base_url('Reports/generate_all_reports'); ?>">
                <i class="fa fa-bar-chart"></i><span> Reports</span>
              </a>
            </li> -->
                <?php if ($this->session->is_super_admin == 1 || $this->session->is_nurse != 1 && $this->session->is_doctor != 1) { ?>
                  <li class="treeview">
                    <a href="#">
                      <i class="fa fa-bar-chart" aria-hidden="true"></i><span>Reports</span>
                      <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                      </span>
                    </a>
                    <ul class="treeview-menu">
                      <li><a href="<?php echo base_url('Reports/hospitalDailyCensusReport'); ?>"><i class="fa fa-circle-o"></i>Daily Census Report</a></li>
                      <li><a href="<?php echo base_url('Reports/statisticalReport'); ?>"><i class="fa fa-circle-o"></i> Statistical Report</a></li>
                      <li><a href="<?php echo base_url('Reports/monthlyHospitalReport'); ?>"><i class="fa fa-circle-o"></i> Monthly Hospital Report</a></li>
                      <li><a href="<?php echo base_url('Reports/annualHospitalStatisticalReport'); ?>"><i class="fa fa-circle-o"></i> AHSR</a></li>
                    </ul>
                  </li>
                <?php } ?>
        </section>
        <div class="slimScrollBar" style="background: rgb(0, 0, 0); width: 7px; position: absolute; top: 0px; opacity: 0.4; display: none; border-radius: 7px; z-index: 99; right: 1px; height: 30px;"></div>
        <div class="slimScrollRail" style="width: 7px; height: 100%; position: absolute; top: 0px; display: none; border-radius: 7px; background: rgb(51, 51, 51); opacity: 0.2; z-index: 90; right: 1px;"></div>
      </div>
    <?php endif; ?>

  <?php endif; ?>
    </aside>