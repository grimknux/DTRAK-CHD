<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
$routes->setAutoRoute(true);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
//$routes->get('/', 'Login::index');
// $routes->get('/welcome/index', 'Welcome::index');


// $routes->add('home-page', 'Home::index');
// $routes->add('office-page', 'Home::office');

$myroutes = [];

$myroutes['/'] = 'Login::index';
$myroutes['logout'] = 'Login::logout';

//Outgoing Routes
$myroutes['docview/outgoing'] = 'OutgoingDocument::index';
$myroutes['outgoingTbl'] = 'OutgoingDocument::foroutgoing';
$myroutes['outgoingData'] = 'OutgoingDocument::getOutgoingData';
$myroutes['addDocument'] = 'OutgoingDocument::addOutgoingDocument';
$myroutes['updateDocument'] = 'OutgoingDocument::updateOutgoingDocument';
$myroutes['docview/outgoing/viewfile/(:segment)'] = 'OutgoingDocument::viewFile/$1';
$myroutes['editDocumentData'] = 'OutgoingDocument::editOutgoingData';
$myroutes['deleteDocument'] = 'OutgoingDocument::deleteOutgoingDocument';
$myroutes['doneDocuments'] = 'OutgoingDocument::getDoneDocs';
$myroutes['editAttachmentData'] = 'OutgoingDocument::editOutgoingAttach';
$myroutes['updateAttachment'] = 'OutgoingDocument::updateDocumentAttachment';
    //destination
    $myroutes['docview/outgoing/destination/(:segment)'] = 'OutgoingDestination::index/$1';
    $myroutes['addDestination'] = 'OutgoingDestination::addDocumentDestination';
    $myroutes['destinationTbl'] = 'OutgoingDestination::controlDestinationData';
    $myroutes['changeDestinationData'] = 'OutgoingDestination::getDestinationData';
    $myroutes['populateActOffByOffice'] = 'OutgoingDestination::getActionOfficerByOffice';
    $myroutes['submitChangeDestination'] = 'OutgoingDestination::changeDestination';
    $myroutes['deleteDestination'] = 'OutgoingDestination::deleteDestination';

//Receive Routes
$myroutes['doctoreceive/receive'] = 'IncomingReceiving::index';
$myroutes['receiveTbl'] = 'IncomingReceiving::forreceive';
$myroutes['receiveData'] = 'IncomingReceiving::getReceiveData';
$myroutes['receiveDoc'] = 'IncomingReceiving::receiveDocument';
$myroutes['receiveBulkDoc'] = 'IncomingReceiving::receiveBulkDocument';
$myroutes['doctoreceive/receive/viewfile/(:segment)'] = 'IncomingReceiving::viewFile/$1';

//Action Routes
$myroutes['doctoreceive/action'] = 'IncomingAction::index';
$myroutes['actionTbl'] = 'IncomingAction::foraction';
$myroutes['actionData'] = 'IncomingAction::getActionData';
$myroutes['actionDoc'] = 'IncomingAction::actionDocument';
$myroutes['instaActionDoc'] = 'IncomingAction::instaActionDocument';
$myroutes['actionBulkDoc'] = 'IncomingAction::actionBulkDocument';

//Release Routes
$myroutes['doctoreceive/release'] = 'IncomingRelease::index';
$myroutes['releaseTbl'] = 'IncomingRelease::forrelease';
$myroutes['releaseData'] = 'IncomingRelease::getReleaseData';
$myroutes['releaseDoc'] = 'IncomingRelease::releaseDocument';
$myroutes['instaReleaseDoc'] = 'IncomingRelease::instaReleaseDocument';
$myroutes['releaseBulkDoc'] = 'IncomingRelease::releaseBulkDocument';
$myroutes['getBulkReleaseData'] = 'IncomingRelease::releaseBulkData';
$myroutes['disseminateData'] = 'IncomingRelease::getDisseminateData';
$myroutes['addDissemination'] = 'IncomingRelease::addDocumentDissemination';
$myroutes['tagData'] = 'IncomingRelease::getTagData';

    //Released Routes
    $myroutes['miscellaneous/released'] = 'IncomingReleased::index';
    $myroutes['releasedTbl'] = 'IncomingReleased::forreleased';
    $myroutes['releasedGetDestinationDataChange'] = 'IncomingReleased::getDestinationDataChange';
    $myroutes['submitChangeDestinationReld'] = 'IncomingReleased::changeDestination';
    $myroutes['releasedGetDestinationDataAdd'] = 'IncomingReleased::getDestinationDataAdd';
    $myroutes['submitAddDestinationReld'] = 'IncomingReleased::addDestination';

    //Undone Routes
    $myroutes['miscellaneous/undonedocs'] = 'UndoneDocument::index';
    $myroutes['undoneTbl'] = 'UndoneDocument::forundone';
    $myroutes['undoneDoc'] = 'UndoneDocument::undoneDocument';


//Forward Routes
$myroutes['forwardData'] = 'IncomingFwdRet::getForwardData';
$myroutes['forwardDoc'] = 'IncomingFwdRet::forwardDocument';

//Return Routes
$myroutes['returnData'] = 'IncomingFwdRet::getReturnData';
$myroutes['returnDoc'] = 'IncomingFwdRet::returnDocument';


//Miscellaneous Routes
$myroutes['getuserbyoffice'] = 'Miscellaneous::getUserByOffice';

//Reports Routes
$myroutes['report/received'] = 'Reports\DocumentReceived::index';
$myroutes['report/table/received'] = 'Reports\DocumentReceived::reportReceived';
$myroutes['report/action_taken'] = 'Reports\DocumentAction::index';
$myroutes['report/table/action'] = 'Reports\DocumentAction::reportAction';
$myroutes['report/released_processed'] = 'Reports\DocumentReleased::index';
$myroutes['report/table/released'] = 'Reports\DocumentReleased::reportReleased';


//ADMINISTRATOR
//Action Officer Routes
$myroutes['admin/reference/action_officer'] = 'Administrator\ActionOfficer::index';
$myroutes['admin/reference/action_officer/table'] = 'Administrator\ActionOfficer::view_action_officer_table';
$myroutes['admin/reference/action_officer/get'] = 'Administrator\ActionOfficer::get_action_officer';
$myroutes['admin/reference/action_officer/add'] = 'Administrator\ActionOfficer::add_action_officer';
$myroutes['admin/reference/action_officer/delete'] = 'Administrator\ActionOfficer::delete_action_officer';
$myroutes['admin/reference/action_officer/update'] = 'Administrator\ActionOfficer::update_action_officer';
$myroutes['admin/reference/action_officer/inactive'] = 'Administrator\ActionOfficer::inactive_action_officer';
$myroutes['admin/reference/action_officer/reactivate'] = 'Administrator\ActionOfficer::reactivate_action_officer';
$myroutes['admin/reference/action_officer/reset_password'] = 'Administrator\ActionOfficer::reset_password_action_officer';

//Action Required Routes
$myroutes['admin/reference/action_required'] = 'Administrator\ActionRequired::index';
$myroutes['admin/reference/action_required/table'] = 'Administrator\ActionRequired::view_action_required_table';

$myroutes['admin/reference/action_required/get'] = 'Administrator\ActionRequired::get_action_required';
$myroutes['admin/reference/action_required/add'] = 'Administrator\ActionRequired::add_action_required';
$myroutes['admin/reference/action_required/delete'] = 'Administrator\ActionRequired::delete_action_required';
$myroutes['admin/reference/action_required/update'] = 'Administrator\ActionRequired::update_action_required';
$myroutes['admin/reference/action_required/inactive'] = 'Administrator\ActionRequired::inactive_action_required';
$myroutes['admin/reference/action_required/reactivate'] = 'Administrator\ActionRequired::reactivate_document_type';

//Document Type Routes
$myroutes['admin/reference/document_type'] = 'Administrator\DocumentType::index';
$myroutes['admin/reference/document_type/table'] = 'Administrator\DocumentType::view_document_type_table';
$myroutes['admin/reference/document_type/get'] = 'Administrator\DocumentType::get_document_type';
$myroutes['admin/reference/document_type/add'] = 'Administrator\DocumentType::add_document_type';
$myroutes['admin/reference/document_type/delete'] = 'Administrator\DocumentType::delete_document_type';
$myroutes['admin/reference/document_type/update'] = 'Administrator\DocumentType::update_document_type';
$myroutes['admin/reference/document_type/inactive'] = 'Administrator\DocumentType::inactive_document_type';
$myroutes['admin/reference/document_type/reactivate'] = 'Administrator\DocumentType::reactivate_document_type';

//Document Status
$myroutes['admin/document_management'] = 'Administrator\DocumentManagement::index';
$myroutes['admin/document_management/table'] = 'Administrator\DocumentManagement::view_document_management_table';
$myroutes['admin/document_management/delete'] = 'Administrator\DocumentManagement::delete_route_no';

//Document Destination
$myroutes['admin/document_management/(:any)/destination'] = 'Administrator\DocumentDestination::index/$1';
$myroutes['admin/document_management/destination/table'] = 'Administrator\DocumentDestination::controlDestinationData';
$myroutes['admin/document_management/destination/delete'] = 'Administrator\DocumentDestination::deleteDestination';
$myroutes['admin/document_management/destination/undone'] = 'Administrator\DocumentDestination::undoneDocument';
    //$myroutes['admin/document_management/destination/getdata'] = 'Administrator\DocumentDestination::getDestinationData';
    //$myroutes['admin/document_management/destination/change'] = 'Administrator\DocumentDestination::changeDestination';
//$myroutes['admin/document_management/delete'] = 'Administrator\DocumentManagement::delete_route_no';
//$myroutes['admin/report/table/timeline'] = 'Administrator\DocumentTimeline::reportTimeline';

//Administrative Report 
$myroutes['admin/report/document_timeline'] = 'Administrator\Report\DocumentTimeline::index';
$myroutes['admin/report/table/timeline'] = 'Administrator\Report\DocumentTimeline::reportTimeline';



/*$myroutes['generate-PDF/(:alphanum)'] = 'PdfController::generate_pdf/$1';
$myroutes['generate-bulk/(:segment)'] = 'PdfController::bulkprint_pdf/$1';
$myroutes['generate-bulk-emp/(:segment)'] = 'PdfController::bulkprint_pdf_emp/$1';
$myroutes['check-bulk'] = 'PdfController::checkbulk';
$myroutes['check-bulk-emp'] = 'PdfController::checkbulkemp';
$myroutes['bulk-print'] = 'BulkPrint::index';
$myroutes['bulk-upload'] = 'BulkUpload::index';
$myroutes['webex-meeting'] = 'Webex::index';
$myroutes['add-webex-schedule'] = 'Webex::addwebex';
$myroutes['createMeeting'] = 'Webex::createMeeting';
$myroutes['call-back'] = 'Webex::callback';
$myroutes['upload-csv'] = 'BulkUpload::uploadCSV';
$myroutes['login-page'] = 'Login::index';
$routes->post('get-employee', 'Home::getemployee');
$routes->post('get-data', 'Home::getdata');
$routes->post('get-section', 'Home::getsection');
$routes->post('get-division', 'Home::getdivision');
$routes->post('submit-data', 'Home::submitData');
$myroutes['auth-image'] = 'Home::authenticate';
$myroutes['process-image'] = 'Home::imageProcess';
$myroutes['logoutnow'] = 'Login::logout';
$myroutes['register-form'] = 'Register::index';
$myroutes['activate-user/(:alphanum)'] = 'Register::activate/$1';*/

//$routes->post('process-image', 'Home::imageProcess');
//$routes->get('idprint/call-back', 'Webex::callback');
//$myroutes['home-page'] = 'Home::index',;
//$myroutes['office-page'] = 'Home::office';
//$myroutes['library-page/(:alpha)/(:num)'] = 'Library::viewlibrary/$1/$2';
//$myroutes['user-page'] = 'Users::displayuser';
//$myroutes['email-send'] = 'TestMail::index';
//$myroutes['helper-page'] = 'TestHelpers::index';
//$myroutes['user-form'] = 'Users::userform';
//$routes->get('user-form', 'Users::userform');
//$routes->post('submit-form', 'Users::submitform');
//$myroutes['submit-form'] = 'Users::submitform';
//$myroutes['contact-form'] = 'Contacts::index';
//$myroutes['login-info'] = 'Home::loginActivity';
//$routes->get('home-page', 'Home::index', ['filter' => 'xframe']);


$routes->map($myroutes);

$routes->set404Override(function()
{

    echo view('errors/customerror');

});


/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
