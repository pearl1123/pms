<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'PatientRegistration/inpatientList';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
$route['ClinicalNotes/listview/(:num)'] = 'ClinicalNotes/listview/$1';
$route['patientregistration/save_clinical_note'] = 'PatientRegistration/save_clinical_note';
$route['patientregistration/save_selected_form_data'] = 'patientregistration/save_selected_form_data';
$route['patientregistration/get_form_fields_ajax'] = 'patientregistration/get_form_fields_ajax';
$route['patientregistration/dynamic_form_list/(:any)/(:num)'] = 'patientregistration/dynamic_form_list/$1/$2';
$route['patientregistration/dynamic_form_view/(:any)/(:num)'] = 'patientregistration/dynamic_form_view/$1/$2';
$route['patient-registration/form-list/(:any)/(:num)'] = 'PatientRegistration/form_list_view/$1/$2';
$route['patient-registration/ajax-form-list/(:any)/(:num)'] = 'PatientRegistration/ajax_form_list/$1/$2';
$route['patientregistration/generate_pdf/(:num)/(:num)/(:any)'] = 'PatientRegistration/generate_pdf/$1/$2/$3';
$route['load-physical-exam-view'] = 'PatientNotes/load_physical_exam_view';
$route['Clinical_Notes'] = 'PatientDocuments/PatientDocuments';
$route['PatientRegistration/public_profile/(:num)'] = 'PatientRegistration/patientProfileRev/$1';
$route['patient/profile/(:num)'] = 'PatientRegistration/patientProfilePublic/$1';