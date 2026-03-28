<?php

use App\Http\Controllers\AssetController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\RoleController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\Crm\ContactController;
use App\Http\Controllers\Crm\DashboardController;
use App\Http\Controllers\Crm\FinalLegalityController;
use App\Http\Controllers\Crm\LeadController;
use App\Http\Controllers\Crm\LeadDocumentController;
use App\Http\Controllers\Crm\LeadPaymentController;
use App\Http\Controllers\Crm\ReservationController;
use App\Http\Controllers\Crm\SurveyController;
use App\Http\Controllers\finance\BankAccountController;
use App\Http\Controllers\finance\CashFlowController;
use App\Http\Controllers\finance\CashInController;
use App\Http\Controllers\finance\CashOutController;
use App\Http\Controllers\finance\DebtController;
use App\Http\Controllers\finance\ReportController;
use App\Http\Controllers\finance\SubmissionController;
use App\Http\Controllers\Property\ClusterController;
use App\Http\Controllers\Property\ConstructionController;
use App\Http\Controllers\Property\ProjectController;
use App\Http\Controllers\Property\PropertyController;
use App\Http\Controllers\Property\RetentionCaseController;
use App\Http\Controllers\Property\SiteplanController;
use App\Http\Controllers\Property\SubContractorController;
use App\Http\Controllers\Property\UnitController;
use App\Http\Controllers\StorageController;

Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:api')->group(function () {
    // route group storage
    Route::get('/file/{path}', [StorageController::class, 'showFile'])
        ->where('path', '.*')
        ->name('file-show');
    Route::get('/download/{path}', [StorageController::class, 'downloadFile'])
        ->where('path', '.*')
        ->name('file-download');

    Route::delete('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);

    Route::post('/check-permissions', [AuthController::class, 'checkPermissions']);
    
    // route group role management
    Route::group(['prefix' => 'manage/role'], function () {
        Route::get('/permissions', [RoleController::class, 'getAllPermissionItems'])
            ->middleware('permission:role.get_all_permission_items');
        Route::get('/', [RoleController::class, 'index'])
            ->middleware('permission:role.get_all');
        Route::get('/group', [RoleController::class, 'getGroupRoles'])
            ->middleware('permission:role.get_all');
        Route::get('/select', [RoleController::class, 'getAllRoleForSelect'])
            ->middleware('permission:role.get_all');
        Route::post('/', [RoleController::class, 'store'])
            ->middleware('permission:role.create');
        Route::get('/{id}', [RoleController::class, 'show'])
            ->middleware('permission:role.show')
            ->where('id', config('app.format_uuid'));
        Route::put('/{id}', [RoleController::class, 'update'])
            ->middleware('permission:role.update')
            ->where('id', config('app.format_uuid'));
        Route::delete('/{id}', [RoleController::class, 'destroy'])
            ->middleware('permission:role.delete')
            ->where('id', config('app.format_uuid'));
    });

    // route group user management
    Route::group(['prefix' => 'manage/user'], function () {
        Route::get('/', [UserController::class, 'index'])
            ->middleware('permission:user.get_all');
        Route::post('/', [UserController::class, 'create'])
            ->middleware('permission:user.create');
        Route::put('/{id}', [UserController::class, 'update'])
            ->middleware('permission:user.update')
            ->where('id', config('app.format_uuid'));
        Route::get('/{id}', [UserController::class, 'show'])
            ->middleware('permission:user.show')
            ->where('id', config('app.format_uuid'));
        Route::delete('/{id}', [UserController::class, 'destroy'])
            ->middleware('permission:user.delete')
            ->where('id', config('app.format_uuid'));
        Route::put('/{id}/status', [UserController::class, 'toggleStatus'])
            ->middleware('permission:user.update')
            ->where('id', config('app.format_uuid'));
    });

    // route group dashboard
    Route::group(['prefix' => 'crm/dashboard'], function () {
        Route::get('/pending-tasks', [DashboardController::class, 'pendingTasks']);
        Route::get('/new-lead', [DashboardController::class, 'newLead']);
        Route::get('/marketing-performance', [DashboardController::class, 'getMarketingPerformance']);
        Route::get('/summary-status', [DashboardController::class, 'summaryStatus'])
            ->middleware('permission:dashboard.get_analytics');
        Route::get('/summary-source', [DashboardController::class, 'summarySource'])
            ->middleware('permission:dashboard.get_analytics');
        Route::get('/summary-changed', [DashboardController::class, 'summaryChanged'])
            ->middleware('permission:dashboard.get_analytics');
        Route::get('/lead-funnel', [DashboardController::class, 'leadFunnel'])
            ->middleware('permission:dashboard.get_analytics');
        Route::get('/task-performance', [DashboardController::class, 'taskPerformance'])
            ->middleware('permission:dashboard.get_analytics');
    });

    // route group contact management
    Route::group(['prefix' => 'crm/contact'], function () {
        Route::get('/', [ContactController::class, 'index'])
        ->middleware('permission:contact.get_all');
        Route::get('/select', [ContactController::class, 'getAllContactForSelect'])
        ->middleware('permission:contact.get_all');
        Route::post('/', [ContactController::class, 'create'])
        ->middleware('permission:contact.create');
        Route::get('/export', [ContactController::class, 'export'])->middleware('permission:contact.get_all');
        Route::post('/import', [ContactController::class, 'import'])
            ->middleware('permission:contact.create');
        Route::get('/{id}', [ContactController::class, 'show'])
            ->middleware('permission:contact.get_all')
            ->where('id', config('app.format_uuid'));
        Route::put('/{id}', [ContactController::class, 'update'])
            ->middleware('permission:contact.update')
            ->where('id', config('app.format_uuid'));
        Route::delete('/{id}', [ContactController::class, 'destroy'])
            ->middleware('permission:contact.delete')
            ->where('id', config('app.format_uuid'));
    });

    // route group lead management
    Route::group(['prefix' => 'crm/lead'], function () {
        Route::get('/get-available-status', [LeadController::class, 'getAvailablestatus'])
            ->middleware('permission:lead.get_all');
        Route::get('/get-marketing-agents', [LeadController::class, 'getMarketingAgents'])
            ->middleware('permission:lead.get_all');
        Route::get('/get-non-lead-contacts', [LeadController::class, 'getNonLeadContacts'])
            ->middleware('permission:lead.get_all');
        Route::get('/get-property-units', [LeadController::class, 'getUnitList'])
            ->middleware('permission:lead.get_all');
        Route::get('/get-survey-location', [LeadController::class, 'getSurveyLocation'])
            ->middleware('permission:lead.get_all');
        Route::get('/summary', [LeadController::class, 'summary'])
            ->middleware('permission:lead.get_all');
        Route::get('/', [LeadController::class, 'index'])
            ->middleware('permission:lead.get_all');
        Route::post('/', [LeadController::class, 'create'])
            ->middleware('permission:lead.create');
        Route::get('/{id}', [LeadController::class, 'show'])
            ->middleware('permission:lead.get_all')
            ->where('id', config('app.format_uuid'));
        Route::post('/{id}', [LeadController::class, 'update'])
            ->middleware('permission:lead.update')
            ->where('id', config('app.format_uuid'));
        Route::delete('/{id}', [LeadController::class, 'delete'])
            ->middleware('permission:lead.delete')
            ->where('id', config('app.format_uuid'));
    });

    // route group survey management
    Route::group(['prefix' => 'crm/survey'], function () {
        Route::get('/', [SurveyController::class, 'index'])
            ->middleware('permission:survey.get_all');
        Route::get('/summary', [SurveyController::class, 'summary'])
            ->middleware('permission:survey.get_all');
        Route::get('/get-marketing-agents', [LeadController::class, 'getMarketingAgents'])
            ->middleware('permission:lead.get_all');
        Route::get('/get-non-survey-lead', [SurveyController::class, 'getNonSurveyLead'])
            ->middleware('permission:survey.get_all');
        Route::get('/get-property-units', [LeadController::class, 'getUnitList'])
            ->middleware('permission:lead.get_all');
        Route::get('/get-survey-location', [LeadController::class, 'getSurveyLocation'])
            ->middleware('permission:survey.get_all');
        Route::post('/', [SurveyController::class, 'create'])
            ->middleware('permission:survey.create');
        Route::get('/{id}', [SurveyController::class, 'show'])
            ->middleware('permission:survey.get_all')
            ->where('id', config('app.format_uuid'));
        Route::post('/{id}', [SurveyController::class, 'update'])
            ->middleware('permission:survey.update')
            ->where('id', config('app.format_uuid'));
        Route::delete('/{id}', [SurveyController::class, 'delete'])
            ->middleware('permission:survey.delete')
            ->where('id', config('app.format_uuid'));
    });

    // route group reservation management
    Route::group(['prefix' => 'crm/reservation'], function () {
        Route::get('/summary', [ReservationController::class, 'summary'])
            ->middleware('permission:lead.get_reservation_summary');
        Route::get('/', [ReservationController::class, 'index'])
            ->middleware('permission:lead.get_all_reservation');
        Route::post('/', [ReservationController::class, 'store'])
            ->middleware('permission:lead.create_reservation');
        Route::post('/{id}', [ReservationController::class, 'update'])
            ->middleware('permission:lead.update_reservation')
            ->where('id', config('app.format_uuid'));
        Route::get('/{id}', [ReservationController::class, 'getById'])
            ->middleware('permission:lead.get_all,lead.get_all_documents')
            ->where('id', config('app.format_uuid'));
        Route::get('/{id}/properties', [ReservationController::class, 'listAllProperties'])
            ->middleware('permission:lead.get_all,lead.get_all_documents')
            ->where('id', config('app.format_uuid'));
        Route::get('/get-properties', [ReservationController::class, 'getProperties'])
            ->middleware('permission:lead.get_all');
        Route::get('/get-prospects', [ReservationController::class, 'getProspect'])
            ->middleware('permission:lead.get_all');
        Route::delete('/{id}', [ReservationController::class, 'delete'])
            ->middleware('permission:lead.delete')
            ->where('id', config('app.format_uuid'));

    });

    // route group lead document management
    Route::group(['prefix' => 'crm/lead-document'], function () {
        Route::get('/summary', [LeadDocumentController::class, 'summary'])
            ->middleware('permission:lead.get_all_documents');
        Route::get('/', [LeadDocumentController::class, 'index'])
            ->middleware('permission:lead.get_all_documents');
        Route::get('/buyer-document-types', [LeadDocumentController::class, 'buyerDocumentTypes'])
            ->middleware('permission:lead.get_all_documents');
        Route::get('/get-reserved-lead', [LeadDocumentController::class, 'getReservedLeads'])
            ->middleware('permission:lead.get_all_documents');
        Route::post('/', [LeadDocumentController::class, 'create'])
            ->middleware('permission:lead.upload_document');
        Route::get('/{id}', [LeadDocumentController::class, 'show'])
            ->middleware('permission:lead.get_all_documents,lead.get_all_payment')
            ->where('id', config('app.format_uuid'));
        Route::post('/{id}', [LeadDocumentController::class, 'update'])
            ->middleware('permission:lead.upload_document')
            ->where('id', config('app.format_uuid'));
        Route::put('/{id}/status', [LeadDocumentController::class, 'updateStatusDocument'])
            ->middleware('permission:lead.verify_document')
            ->where('id', config('app.format_uuid'));
        Route::delete('/{id}', [LeadDocumentController::class, 'delete'])
            ->middleware('permission:lead.delete')
            ->where('id', config('app.format_uuid'));
    });

    // route group payment
    Route::group(['prefix' => 'crm/lead-payment'], function () {
        Route::get('/bank-list', [LeadPaymentController::class, 'bankList'])
            ->middleware('permission:lead.get_all_payment');
        Route::post('/', [LeadPaymentController::class, 'create'])
            ->middleware('permission:lead.create_payment');
        Route::get('/summary', [LeadPaymentController::class, 'summary'])
            ->middleware('permission:lead.get_all_payment');
        Route::get('/', [LeadPaymentController::class, 'index'])
            ->middleware('permission:lead.get_all_payment');
        Route::get('/{id}', [LeadPaymentController::class, 'getById'])
            ->middleware('permission:lead.get_all_payment')
            ->where('id', config('app.format_uuid'));
        Route::post('/{id}', [LeadPaymentController::class, 'update'])
            ->middleware('permission:lead.update_payment')
            ->where('id', config('app.format_uuid'));
        Route::get('/get-completed-document-lead', [LeadPaymentController::class, 'getLeadCompletedDocument'])
            ->middleware('permission:lead.get_all_payment');
        Route::delete('/{id}', [LeadPaymentController::class, 'delete'])
            ->middleware('permission:lead.delete')
            ->where('id', config('app.format_uuid'));
    });

    // Hapus Nanti
    Route::delete('crm/kpr/{id}', [LeadPaymentController::class, 'delete'])
            ->middleware('permission:lead.delete')
            ->where('id', config('app.format_uuid'));

    Route::group(['prefix' => 'crm/final-legality'], function () {
        Route::get('/summary', [FinalLegalityController::class, 'summary'])
            ->middleware('permission:lead.get_all_final_legality');
        Route::get('/', [FinalLegalityController::class, 'index'])
            ->middleware('permission:lead.get_all_final_legality');
        Route::post('/', [FinalLegalityController::class, 'create'])
            ->middleware('permission:lead.create_final_legality');
        Route::get('/{id}', [FinalLegalityController::class, 'getById'])
            ->middleware('permission:lead.get_all_final_legality')
            ->where('id', config('app.format_uuid'));
        Route::post('/{id}', [FinalLegalityController::class, 'update'])
            ->middleware('permission:lead.update_final_legality')
            ->where('id', config('app.format_uuid'));
        Route::get('/get-completed-payment-lead', [FinalLegalityController::class, 'getLeadCompletedPayment'])
            ->middleware('permission:lead.get_all_final_legality');
        Route::delete('/{id}', [FinalLegalityController::class, 'delete'])
            ->middleware('permission:lead.delete')
            ->where('id', config('app.format_uuid'));
    });

    Route::group(['prefix' => 'property/projects'], function () {
        Route::get('/', [ProjectController::class, 'index'])
            ->middleware('permission:property.get_all_project');
        Route::post('/', [ProjectController::class, 'store'])
            ->middleware('permission:property.create_project');
        Route::get('/{id}', [ProjectController::class, 'getById'])
            ->middleware('permission:property.get_all_project')
            ->where('id', config('app.format_uuid'));
        Route::post('/{id}', [ProjectController::class, 'update'])
            ->middleware('permission:property.update_project')
            ->where('id', config('app.format_uuid'));
        Route::delete('/{id}', [ProjectController::class, 'destroy'])
            ->middleware('permission:property.delete_project')
            ->where('id', config('app.format_uuid'));
    });

    Route::group(['prefix' => 'property/cluster'], function () {
        Route::get('/', [ClusterController::class, 'index'])
            ->middleware('permission:property.get_all_cluster');
        Route::get('/projects', [ClusterController::class, 'getProject'])
            ->middleware('permission:property.get_all_cluster');
        Route::post('/', [ClusterController::class, 'store'])
            ->middleware('permission:property.create_cluster');
        Route::get('/{id}', [ClusterController::class, 'getById'])
            ->middleware('permission:property.get_all_cluster')
            ->where('id', config('app.format_uuid'));
        Route::put('/{id}', [ClusterController::class, 'update'])
            ->middleware('permission:property.update_cluster')
            ->where('id', config('app.format_uuid'));
        Route::delete('/{id}', [ClusterController::class, 'destroy'])
            ->middleware('permission:property.delete_cluster')
            ->where('id', config('app.format_uuid'));
    });

    Route::group(['prefix' => 'property/unit'], function () {
        Route::get('/', [UnitController::class, 'index'])
            ->middleware('permission:property.get_all_unit');
        Route::post('/', [UnitController::class, 'store'])
            ->middleware('permission:property.create_unit');
        Route::get('/{id}', [UnitController::class, 'getById'])
            ->middleware('permission:property.get_all_unit')
            ->where('id', config('app.format_uuid'));
        Route::put('/{id}', [UnitController::class, 'update'])
            ->middleware('permission:property.update_unit')
            ->where('id', config('app.format_uuid'));
        Route::delete('/{id}', [UnitController::class, 'destroy'])
            ->middleware('permission:property.delete_unit')
            ->where('id', config('app.format_uuid'));
    });

    Route::group(['prefix' => 'property/unit-property'], function () {
        Route::get('/', [PropertyController::class, 'index'])
            ->middleware('permission:property.get_all_property');
        Route::get('/projects-list', [PropertyController::class, 'projectOptionLists'])
            ->middleware('permission:property.get_all_property');
        Route::get('/clusters-list/{projectId}', [PropertyController::class, 'clusterOptionLists'])
            ->middleware('permission:property.get_all_property');
        Route::get('/unit-types-list', [PropertyController::class, 'unitTypeOptionLists'])
            ->middleware('permission:property.get_all_property');
        Route::post('/', [PropertyController::class, 'store'])
            ->middleware('permission:property.create_property');
        Route::get('/{id}', [PropertyController::class, 'getById'])
            ->middleware('permission:property.get_all_property')
            ->where('id', config('app.format_uuid'));
        Route::put('/{id}', [PropertyController::class, 'update'])
            ->middleware('permission:property.update_property')
            ->where('id', config('app.format_uuid'));
        Route::delete('/{id}', [PropertyController::class, 'destroy'])
            ->middleware('permission:property.delete_property')
            ->where('id', config('app.format_uuid'));
        Route::post('/{id}/quality-control-item', [PropertyController::class, 'createQcItem'])
            ->middleware('permission:property.create_property')
            ->where('id', config('app.format_uuid'));
        Route::get('/{id}/quality-control-item', [PropertyController::class, 'getQcItems'])
            ->middleware('permission:property.create_property')
            ->where('id', config('app.format_uuid'));
        Route::post('/{id}/quality-control-item/{idQcItem}', [PropertyController::class, 'updateQcItem'])
            ->middleware('permission:property.create_property')
            ->where('id', config('app.format_uuid'))
            ->where('idQcItem', config('app.format_uuid'));
        Route::delete('/{id}/quality-control-item/{idQcItem}', [PropertyController::class, 'deleteQcItem'])
            ->middleware('permission:property.create_property')
            ->where('id', config('app.format_uuid'))
            ->where('idQcItem', config('app.format_uuid'));
        Route::get('/{id}/quality-control-item/{idQcItem}', [PropertyController::class, 'getQcItem'])
            ->middleware('permission:property.create_property')
            ->where('id', config('app.format_uuid'))
            ->where('idQcItem', config('app.format_uuid'));
        Route::post('/{id}/quality-control-item/import', [PropertyController::class, 'importQcItems'])
            ->middleware('permission:property.create_property')
            ->where('id', config('app.format_uuid'))
            ->where('idQcItem', config('app.format_uuid'));
    });

    Route::group(['prefix' => 'property/siteplan'], function () {
        Route::get('/{projectId}', [SiteplanController::class, 'index'])
            ->middleware('permission:property.get_all_project')
            ->where('projectId', config('app.format_uuid'));
        Route::get('/{projectId}/list-option-property', [SiteplanController::class, 'getUnitPropertyList'])
            ->middleware('permission:property.get_all_project')
            ->where('projectId', config('app.format_uuid'));
        Route::post('/{projectId}/change-image', [SiteplanController::class, 'changeSiteplanImage'])
            ->middleware('permission:property.manage_site_plan')
            ->where('projectId', config('app.format_uuid'));
        Route::post('/{projectId}', [SiteplanController::class, 'storeUnit'])
            ->middleware('permission:property.manage_site_plan')
            ->where('projectId', config('app.format_uuid'));
        Route::get('/{projectId}/{propertyId}', [SiteplanController::class, 'showUnit'])
            ->middleware('permission:property.get_all_project')
            ->where('propertyId', config('app.format_uuid'))
            ->where('projectId', config('app.format_uuid'));
        Route::put('/{projectId}/{propertyId}', [SiteplanController::class, 'updateUnit'])
            ->middleware('permission:property.manage_site_plan');
        Route::delete('/{projectId}/{propertyId}', [SiteplanController::class, 'destroyUnit'])
            ->middleware('permission:property.manage_site_plan')
            ->where('propertyId', config('app.format_uuid'))
            ->where('projectId', config('app.format_uuid'));
    });

    // sub contractor
    Route::group(['prefix' => 'property/sub-contractor'], function () {
        Route::get('/', [SubContractorController::class, 'index'])
            ->middleware('permission:property.get_all_sub_contractor');
        Route::post('/', [SubContractorController::class, 'store'])
            ->middleware('permission:property.create_sub_contractor');
        Route::get('/{id}', [SubContractorController::class, 'getById'])
            ->middleware('permission:property.get_all_sub_contractor')
            ->where('id', config('app.format_uuid'));
        Route::put('/{id}', [SubContractorController::class, 'update'])
            ->middleware('permission:property.update_sub_contractor')
            ->where('id', config('app.format_uuid'));
        Route::delete('/{id}', [SubContractorController::class, 'destroy'])
            ->middleware('permission:property.delete_sub_contractor')
            ->where('id', config('app.format_uuid'));
    });

    // construction
    Route::group(['prefix' => 'property/construction'], function () {
        Route::get('/', [ConstructionController::class, 'index'])
            ->middleware('permission:property.get_all_construction');
        Route::get('/summary', [ConstructionController::class, 'summary'])
            ->middleware('permission:property.get_all_construction');
        Route::get('/sub-contractors', [ConstructionController::class, 'getAvailableSubCon'])
            ->middleware('permission:property.get_all_construction');
        Route::get('/project-lists', [ConstructionController::class, 'getProjects'])
            ->middleware('permission:property.get_all_construction');
        Route::get('/cluster-lists/{projectId}', [ConstructionController::class, 'getClusters'])
            ->middleware('permission:property.get_all_construction')
            ->where('projectId', config('app.format_uuid'));
        Route::get('/unit-type-lists', [ConstructionController::class, 'getUnitTypes'])
            ->middleware('permission:property.get_all_construction');
        Route::get('/property-lists/{projectId}/{clusterId}/{unitTypeId}', [ConstructionController::class, 'getProperties'])
            ->middleware('permission:property.get_all_construction')
            ->where('projectId', config('app.format_uuid'))
            ->where('clusterId', config('app.format_uuid'))
            ->where('unitTypeId', config('app.format_uuid'));
        Route::post('/', [ConstructionController::class, 'store'])
            ->middleware('permission:property.create_construction');
        Route::get('/{id}', [ConstructionController::class, 'getById'])
            ->middleware('permission:property.get_all_construction')
            ->where('id', config('app.format_uuid'));
        Route::post('/{id}', [ConstructionController::class, 'update'])
            ->middleware('permission:property.update_construction')
            ->where('id', config('app.format_uuid'));
        Route::delete('/{id}', [ConstructionController::class, 'destroy'])
            ->middleware('permission:property.delete_construction')
            ->where('id', config('app.format_uuid'));
        Route::get('/reserved-lead', [RetentionCaseController::class, 'getReservedLead'])
            ->middleware('permission:property.get_all_retention');
    });

    // retention
    Route::group(['prefix' => 'property/retention'], function () {
        Route::get('/', [RetentionCaseController::class, 'index'])
            ->middleware('permission:property.get_all_retention');
        Route::get('/summary', [RetentionCaseController::class, 'summary'])
            ->middleware('permission:property.get_all_retention');
        Route::get('/sub-contractors', [RetentionCaseController::class, 'getAvailableSubCon'])
            ->middleware('permission:property.get_all_retention');
        Route::get('/reserved-lead', [RetentionCaseController::class, 'getReservedLead'])
            ->middleware('permission:property.get_all_retention');
        Route::post('/', [RetentionCaseController::class, 'store'])
            ->middleware('permission:property.create_retention');
        Route::get('/{id}', [RetentionCaseController::class, 'getById'])
            ->middleware('permission:property.get_all_retention')
            ->where('id', config('app.format_uuid'));
        Route::post('/{id}', [RetentionCaseController::class, 'update'])
            ->middleware('permission:property.update_retention')
            ->where('id', config('app.format_uuid'));
        Route::delete('/{id}', [RetentionCaseController::class, 'destroy'])
            ->middleware('permission:property.delete_retention')
            ->where('id', config('app.format_uuid'));
    });

    // finance/cash-in
    Route::group(['prefix' => 'finance/cash-in'], function () {
        Route::get('export', [CashInController::class, 'export'])->middleware('permission:finance.manage_cash_in');
        Route::get('categories', [CashInController::class, 'categories'])
            ->middleware('permission:finance.manage_cash_in');
        Route::get('sub-categories/{category}', [CashInController::class, 'subCategories'])
            ->middleware('permission:finance.manage_cash_in');
        Route::get('sub-sub-categories/{subCategory}', [CashInController::class, 'subSubCategories'])
            ->middleware('permission:finance.manage_cash_in');
        Route::get('bank-list', [CashInController::class, 'bankList'])
            ->middleware('permission:finance.manage_cash_in');
        Route::post('/', [CashInController::class, 'create'])
            ->middleware('permission:finance.manage_cash_in');
        Route::get('/', [CashInController::class, 'index'])
            ->middleware('permission:finance.manage_cash_in');
        Route::get('/{id}', [CashInController::class, 'show'])
            ->middleware('permission:finance.manage_cash_in')
            ->where('id', config('app.format_uuid'));
        Route::put('/{id}', [CashInController::class, 'update'])
            ->middleware('permission:finance.manage_cash_in')
            ->where('id', config('app.format_uuid'));
        Route::delete('/{id}', [CashInController::class, 'delete'])
            ->middleware('permission:finance.manage_cash_in')
            ->where('id', config('app.format_uuid'));
        Route::put('/{parentId}/transaction', [CashInController::class, 'createTransaction'])
            ->middleware('permission:finance.manage_cash_in')
            ->where('parentId', config('app.format_uuid'));
        Route::get('/{parentId}/transaction', [CashInController::class, 'getTransaction'])
            ->middleware('permission:finance.manage_cash_in')
            ->where('parentId', config('app.format_uuid'));
        Route::delete('/transaction/{id}', [CashInController::class, 'deleteTransaction'])
            ->middleware('permission:finance.manage_cash_in')
            ->where('id', config('app.format_uuid'));
        Route::get('/property-list', [CashInController::class, 'getPropertyList'])
            ->middleware('permission:finance.manage_cash_in');
        
    });

    // finance/cash-out
    Route::group(['prefix' => 'finance/cash-out'], function () {
        Route::get('export', [CashOutController::class, 'export'])->middleware('permission:finance.manage_cash_out');
        Route::get('categories', [CashOutController::class, 'categories'])
            ->middleware('permission:finance.manage_cash_out');
        Route::get('sub-categories/{category}', [CashOutController::class, 'subCategories'])
            ->middleware('permission:finance.manage_cash_out');
        Route::get('bank-list', [CashOutController::class, 'bankList'])
            ->middleware('permission:finance.manage_cash_out');
        Route::post('/', [CashOutController::class, 'create'])
            ->middleware('permission:finance.manage_cash_out');
        Route::get('/', [CashOutController::class, 'index'])
            ->middleware('permission:finance.manage_cash_out');
        Route::get('/{id}', [CashOutController::class, 'show'])
            ->middleware('permission:finance.manage_cash_out')
            ->where('id', config('app.format_uuid'));
        Route::put('/{id}', [CashOutController::class, 'update'])
            ->middleware('permission:finance.manage_cash_out')
            ->where('id', config('app.format_uuid'));
        Route::delete('/{id}', [CashOutController::class, 'delete'])
            ->middleware('permission:finance.manage_cash_out')
            ->where('id', config('app.format_uuid'));
        Route::put('/{parentId}/transaction', [CashOutController::class, 'createTransaction'])
            ->middleware('permission:finance.manage_cash_out')
            ->where('parentId', config('app.format_uuid'));
        Route::get('/{parentId}/transaction', [CashOutController::class, 'getTransaction'])
            ->middleware('permission:finance.manage_cash_out')
            ->where('parentId', config('app.format_uuid'));
        Route::delete('/transaction/{id}', [CashOutController::class, 'deleteTransaction'])
            ->middleware('permission:finance.manage_cash_out')
            ->where('id', config('app.format_uuid'));
        
    });

    // finance/submission
    Route::group(['prefix' => 'finance/submission'], function () {
        Route::get('categories', [SubmissionController::class, 'categories'])
            ->middleware('permission:finance.create_submission,finance.approval_submission');
        Route::get('sub-categories/{category}', [SubmissionController::class, 'subCategories'])
            ->middleware('permission:finance.create_submission,finance.approval_submission');
        Route::post('/', [SubmissionController::class, 'create'])
            ->middleware('permission:finance.create_submission,finance.approval_submission');
        Route::get('/', [SubmissionController::class, 'index'])
            ->middleware('permission:finance.get_all_submissions');
        Route::get('/{id}', [SubmissionController::class, 'show'])
            ->middleware('permission:finance.create_submission,finance.approval_submission')
            ->where('id', config('app.format_uuid'));
        Route::put('/{id}', [SubmissionController::class, 'update'])
            ->middleware('permission:finance.create_submission,finance.approval_submission')
            ->where('id', config('app.format_uuid'));
        Route::delete('/{id}', [SubmissionController::class, 'delete'])
            ->middleware('permission:finance.create_submission,finance.approval_submission')
            ->where('id', config('app.format_uuid'));
        Route::put('/{id}/approve', [SubmissionController::class, 'approve'])
            ->middleware('permission:finance.approval_submission')
            ->where('id', config('app.format_uuid'));
        Route::put('/{id}/reject', [SubmissionController::class, 'reject'])
            ->middleware('permission:finance.approval_submission')
            ->where('id', config('app.format_uuid'));
        
    });

    Route::group(['prefix' => 'finance/bank-account'], function () {
        Route::get('/', [BankAccountController::class, 'index'])
            ->middleware('permission:finance.manage_bank_accounts');
        Route::post('/', [BankAccountController::class, 'store'])
            ->middleware('permission:finance.manage_bank_accounts');
        Route::get('/{id}', [BankAccountController::class, 'show'])
            ->middleware('permission:finance.manage_bank_accounts')
            ->where('id', config('app.format_uuid'));
        Route::put('/{id}', [BankAccountController::class, 'update'])
            ->middleware('permission:finance.manage_bank_accounts')
            ->where('id', config('app.format_uuid'));
        Route::delete('/{id}', [BankAccountController::class, 'delete'])
            ->middleware('permission:finance.manage_bank_accounts')
            ->where('id', config('app.format_uuid'));
        Route::get('/{id}/transaction', [BankAccountController::class, 'getTransaction'])
            ->middleware('permission:finance.manage_bank_accounts')
            ->where('id', config('app.format_uuid'));
        Route::post('/transfer', [BankAccountController::class, 'transferSaldo'])
            ->middleware('permission:finance.manage_bank_accounts');
        Route::get('/transfer', [BankAccountController::class, 'listTransfer'])
            ->middleware('permission:finance.manage_bank_accounts');
        
    });

    Route::group(['prefix' => 'asset'], function () {
        Route::get('/categories', [AssetController::class, 'listCategory'])
            ->middleware('permission:asset.manage_assets');
        Route::get('/sub-categories/{category}', [AssetController::class, 'listSubCategory'])
            ->middleware('permission:asset.manage_assets');
        Route::post('/', [AssetController::class, 'create'])
            ->middleware('permission:asset.manage_assets');
        Route::get('/', [AssetController::class, 'index'])
            ->middleware('permission:asset.manage_assets');
        Route::get('/{id}', [AssetController::class, 'show'])
            ->middleware('permission:asset.manage_assets')
            ->where('id', config('app.format_uuid'));
        Route::put('/{id}', [AssetController::class, 'update'])
            ->middleware('permission:asset.manage_assets')
            ->where('id', config('app.format_uuid'));
        Route::delete('/{id}', [AssetController::class, 'delete'])
            ->middleware('permission:asset.manage_assets')
            ->where('id', config('app.format_uuid'));
    });

    Route::group(['prefix' => 'finance/cashflow'], function () {
        Route::get('/', [CashFlowController::class, 'index'])
            ->middleware('permission:finance.view_cash_flow');
        Route::get('/export', [CashFlowController::class, 'export'])
            ->middleware('permission:finance.view_cash_flow');
    });

    Route::group(['prefix' => 'finance/report'], function () {
        Route::get('/laba-rugi', [ReportController::class, 'labaRugi'])
            ->middleware('permission:finance.view_laba_rugi');
        Route::get('/laba-rugi/export', [ReportController::class, 'export'])
            ->middleware('permission:finance.view_laba_rugi');
        Route::get('/cash-in', [ReportController::class, 'cashIn'])
            ->middleware('permission:finance.view_laba_rugi');
        Route::get('/cash-in/export', [ReportController::class, 'exportCashIn'])
            ->middleware('permission:finance.view_laba_rugi');
        Route::get('/neraca', [ReportController::class, 'neraca'])
            ->middleware('permission:finance.view_neraca');
        Route::get('/neraca/export', [ReportController::class, 'exportNeraca'])
            ->middleware('permission:finance.view_neraca');
    });

    Route::group(['prefix' => 'finance/loan'], function () {
        Route::get('/categories', [DebtController::class, 'categories'])
            ->middleware('permission:finance.manage_debt');
        Route::post('/', [DebtController::class, 'create'])
            ->middleware('permission:finance.manage_debt');
        Route::get('/', [DebtController::class, 'index'])
            ->middleware('permission:finance.manage_debt');
        Route::get('/{id}', [DebtController::class, 'show'])
            ->middleware('permission:finance.manage_debt')
            ->where('id', config('app.format_uuid'));
        Route::put('/{id}', [DebtController::class, 'update'])
            ->middleware('permission:finance.manage_debt')
            ->where('id', config('app.format_uuid'));
        Route::put('/{id}/payment', [DebtController::class, 'payment'])
            ->middleware('permission:finance.manage_debt')
            ->where('id', config('app.format_uuid'));
        Route::delete('/{id}', [DebtController::class, 'delete'])
            ->middleware('permission:finance.manage_debt')
            ->where('id', config('app.format_uuid'));
    });
    
});