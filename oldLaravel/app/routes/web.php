<?php
if(version_compare(PHP_VERSION, '7.2.0', '>=')) {
    error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
}
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



Auth::routes();

Route::get('/', 'TaskController@userIndex')->name('home');

Route::get('/home', 'TaskController@userIndex')->name('home');

Route::get('/project_user/create', 'ProjectUsersController@create');
Route::post('/project_user/store', 'ProjectUsersController@store');

Route::get('/projects', 'ProjectController@index');
Route::get('/projects/create', 'ProjectController@create');
Route::get('/projects/{project}', 'ProjectController@show');
Route::get('/projects/{project}/login_download', 'ProjectController@loginDownload');
Route::post('/projects', 'ProjectController@store');
Route::post('/projects/{project}/edit', 'ProjectController@edit');
Route::get('/projects/{project}/edit', 'ProjectController@edit');
Route::post('/projects/{project}/update', 'ProjectController@update');
Route::post('/projects/{project}/addUser', 'ProjectController@addUser');
Route::post('/projects/{project}/addProject', 'ProjectController@addProject');
Route::get('/projects/{project}/deleteUser/{user}', 'ProjectController@deleteUser');
Route::get('/projects/{project}/deleteProject/{user}', 'ProjectController@deleteProject');


Route::get('/prospects', 'ProspectsController@index');
Route::get('/prospects/create', 'ProspectsController@create');
Route::get('/prospects/{prospects}', 'ProspectsController@show');
Route::post('/prospects', 'ProspectsController@store');
Route::post('/prospects/{prospects}/edit', 'ProspectsController@edit');
Route::get('/prospects/{prospects}/edit', 'ProspectsController@edit');
Route::post('/prospects/{prospects}/update', 'ProspectController@update');

Route::get('/users', 'UserController@index');
Route::get('/users/create', 'UserController@create');
Route::get('/users/{user}', 'UserController@show');
Route::post('/users', 'UserController@store');
Route::post('/users/{user}/edit', 'UserController@edit');
Route::get('/users/{user}/edit', 'UserController@edit');
Route::post('/users/{user}/update', 'UserController@update');

// Reportes
Route::get('/documents', 'DocumentController@index');
Route::get('/documents/create', 'DocumentController@create');
Route::get('/documents/{document}/show', 'DocumentController@show');
Route::post('/documents' , 'ProjectDocumentsController@storeFromDocuments');
Route::post('/documents/{document}/edit', 'DocumentController@edit');
Route::get('/documents/{document}/edit', 'DocumentController@edit');
Route::post('/documents/{document}/update', 'DocumentController@update');
Route::get('/documents/{document}/destroy', 'DocumentController@destroy');



Route::get('/config', function(){ 
	return view('config.index');
});



Route::get('/tasks', 'TaskController@index');
Route::get('/tasks_responsive', 'TaskController@indexResponsive');
Route::get('/tasks/schedule', 'TaskController@schedule');

Route::get('/tasks/daily', 'TaskController@daily');
Route::get('/tasks/print', 'TaskController@printIndex');

Route::get('/tasks/create', 'TaskController@create');
Route::get('/tasks/storeFast', 'TaskController@storeFast');
Route::get('/tasks/{task}', 'TaskController@show');
Route::post('/tasks', 'TaskController@store');
Route::post('/tasks/{task}/edit', 'TaskController@edit');
Route::get('/tasks/{task}/edit', 'TaskController@edit');
Route::post('/tasks/{task}/updateStatus', 'TaskController@updateStatus');
Route::post('/tasks/{task}/updateStatusMini', 'TaskController@updateStatusMini');
Route::get('/tasks/{task}/updateStatusMini/status/{status}/token/{token}', 'TaskController@updateStatusRest');

Route::get('/tasks/{task}/updateUser/user/{user}/token/{token}', 'TaskController@updateUser');

Route::get('/tasks/{task}/updateNextStatusMini/status/{status}/token/{token}', 'TaskController@updateNextStatusRest');



Route::get('/tasks/{task}/updateDate/status/{status}/token/{token}', 'TaskController@updateDate');




Route::get('/tasks/setType/{parent}','TaskController@getType');


Route::get('/tasks/{task}/observer/{observer}/token/{token}', 'TaskController@observe');
Route::get('/tasks/{task}/deleteFile', 'TaskController@deleteFile');

Route::post('/tasks/{task}/update', 'TaskController@update');
Route::post('/tasks/{task}/parent/{parent}','TaskController@setParent');
Route::get('/tasks/{task}/setParent/{parent}','TaskController@setParent');
Route::get('/tasks/{task}/setType/{parent}','TaskController@setType');
Route::get('/tasks/{task}/setSubType/{parent}','TaskController@setSubType');
Route::get('/tasks/{task}/setUser/{user}','TaskController@setUser');
Route::get('/tasks/setTask/{tid}/setUser/{uid}','TaskController@setTaskUser');
Route::get('/get_notifications/{uid}','TaskController@getNotifications');
Route::get('/set_notification_reviewed/{nid}','TaskController@setNotificationReviewed');
Route::get('/get_all_notifications','TaskController@getAllNotifications');

Route::post('/tasks/{task}/ajax/update', 'TaskController@updateAJAX');

// buscador routes
Route::get('/search', 'SearchController@index');
Route::get('/tasks/search/{query}', 'TaskController@search');



//messages task


//Route::get('/task/message/{task_id}/{user_id}/{description}','TaskController@sendMessage');
Route::post('/task/message/post','TaskController@sendMessage');
Route::get('/task/get_messages/{task_id}','TaskController@getMessages');
Route::post('/task/message/update','TaskController@editCommentary');


Route::get('/task/approve/{task_id}','TaskController@approveTask');
Route::get('/task/reject/{task_id}','TaskController@rejectTask');


Route::get('/points', 'PointController@index');
Route::get('/points/create', 'PointController@create');
Route::get('/points/{point}', 'PointController@show');
Route::post('/points', 'points@store');
Route::post('/points/{point}/edit', 'PointController@edit');
Route::get('/points/{point}/edit', 'PointController@edit');
Route::post('/points/{point}/update', 'PointController@update');


Route::get('/reports', 'ReportController@index');
Route::get('/reports/weeks_team', 'ReportController@weeksByTeam');
Route::get('/reports/months_user', 'ReportController@monthsByUser');
Route::get('/reports/months_project', 'ReportController@monthsByProject');
Route::get('/reports/weeks_user', 'ReportController@weeksByUser');

Route::get('/reports/weeks_user2', 'ReportController@weeksByUser2');


Route::get('/reports/days_user', 'ReportController@daysByUser');
Route::get('/reports/projects_statuses', 'ReportController@projectsTaskByStatuses');//Manuel 2018-10-31
Route::get('/reports/users_statuses', 'ReportController@usersTaskByStatuses');//Nicolas 2021-06-06

Route::get('/reports/tasks_time', 'ReportController@taskTime');


Route::get('/contests/comments', 'ContestController@commentsIndex');
Route::post('/contests/comments/show', 'ContestController@commentsShow');



Route::get('/contests/instagram/comments', 'InstagramController@commentsIndex');
Route::post('/contests/instagram/show', 'InstagramController@commentsShow');
Route::get('/contests/instagram/redirect', 'InstagramController@redirect');


Route::get('/proximity','UserController@createProximity');
Route::post('/proximity','UserController@storeProximity');
Route::get('/proximity/list','UserController@viewProximity');
Route::get('/proximity/listMap','UserController@viewMapProximity');

Route::get('/keywordsFinder','KeywordController@keyFinder');
Route::get('/keywordsFinder/queryUpdate','KeywordController@queryUpdate');
Route::get('/keywordsFinder/queryUpdate/updateValue','KeywordController@updateValue');
Route::get('/keywordsFinder/estimateVolume','GoogleController@estimateVolume');
Route::get('/mapTest','KeywordController@mapTest');

Route::get('/stream','StreamingController@index');


Route::post('/project_documents','ProjectDocumentsController@store');
Route::post('/project_documents/{id}/update', 'ProjectDocumentsController@update');

Route::get('/project_documents/{id}/delete', 'ProjectDocumentsController@destroy');
Route::get('/project_documents/{id}/edit', 'ProjectDocumentsController@edit');





Route::post('/project_logins','ProjectLoginController@store');
Route::post('/project_logins/{id}/update','ProjectLoginController@updateLogin');
Route::get('/project_logins/{id}/delete','ProjectLoginController@deleteLogin');




Route::get('/accounts', 'AccountController@index');
//Route::get('/accounts/?parent_id={id}', 'AccountController@index');

Route::post('/accounts','AccountController@store');
Route::get('/accounts/create', 'AccountController@create');
Route::get('/accounts/{id}/show', 'AccountController@show');
Route::post('/accounts', 'AccountController@store');
Route::get('/accounts/{id}/edit', 'AccountController@edit');
Route::post('/accounts/{id}/update', 'AccountController@update');
Route::get('/accounts/{id}/destroy', 'AccountController@destroy');


Route::get('/billing', 'BillingController@index');
Route::post('/billing' , 'ProjectDocumentsController@storeFromBilling');



Route::post('/api/tasks', 'ApiController@storeTask');
Route::post('/api/tasks/update', 'ApiController@updateTask');
Route::post('/api/tasks/next_status', 'ApiController@nextStatusTask');
Route::post('/api/tasks/nextDay', 'ApiController@nextDayTask');


//Customers

Route::get( '/customers', 'CustomerController@index')->name('customers');
Route::get( '/customers/create', 'CustomerController@create');
Route::post('/customers', 'CustomerController@store');
Route::get( '/customers/{customer}/edit', 'CustomerController@edit');
Route::post('/customers/{customer}/update', 'CustomerController@update');
Route::get( '/customers/{customer}/show', 'CustomerController@show');
Route::get('/customers/{customer}/destroy', 'CustomerController@destroy');
//Route::post('/customers/{customer}/action/store', function(Request $request){dd($request);});
Route::post('/customers/{customer}/action/store', 'CustomerController@storeAction');
Route::post('/customers/{customer}/action/save', 'CustomerController@saveAction');
Route::post('/customers/{customer}/action/mail', 'CustomerController@storeMail');
Route::get( '/customers/{customer}/assignMe', 'CustomerController@assignMe');


Route::get( '/customers/phase/{pid}', 'CustomerController@indexPhase');


Route::get( '/leads', 'CustomerController@leads')->name('leads');
Route::get( '/leads/excel', 'CustomerController@excel');
Route::get('/customer_statuses', 'CustomerStatusController@index');
Route::get('/customer_statuses/create', 'CustomerStatusController@create');
Route::get('/customer_statuses/{customer_status}', 'CustomerStatusController@show');
Route::post('/customer_statuses', 'CustomerStatusController@store');
Route::post('/customer_statuses/{customer_status}/edit', 'CustomerStatusController@edit');
Route::get('/customer_statuses/{customer_status}/edit', 'CustomerStatusController@edit');
Route::post('/customer_statuses/{customer_status}/update', 'CustomerStatusController@update');
Route::post('/customer_statuses/{customer_status}/updateStatus', 'CustomerStatusController@updateStatus');
Route::get('/customer_statuses/{customer_status}/destroy', 'CustomerStatusController@destroy');
Route::get('/customers/{customer}/email/1', 'CustomerController@mail');
Route::get('/customers/{customer}/actions/createMail/{email}', 'ActionController@trackEmail');
Route::get('/customers/{customer}/actions/trackEmail/{email}', 'ActionController@trackEmail');

// file
Route::post('/customer_files', 'CustomerFileController@store');
Route::get('/customer_files/{file}/delete', 'CustomerFileController@delete');





// Mail
Route::get('/testMail', 'SiteController@testMail');
Route::get('/mail/send', 'MailController@send');
Route::get('/emails/store', 'EmailController@store');
Route::get('/emails/{email}/store', 'EmailController@store');
Route::get('/emails/send', 'EmailController@send');
Route::get('/emails/getCustomersZeroActions', 'EmailController@getCustomersZeroActions'); 


//Action
Route::get('/actions', 'ActionController@index')->name('actions');
Route::get('/actions/{action}/show', 'ActionController@show');
Route::get('/actions/{action}/edit', 'ActionController@edit');
Route::get('/actions/{action}/update', 'ActionController@update');
Route::get('/actions/{action}/destroy', 'ActionController@destroy');

//Action Types
Route::get('/action_type', 'ActionTypeController@index');
Route::post('/action_type/create', 'ActionTypeController@store');
Route::get('/action_type/{id}/destroy', 'ActionTypeController@destroy');
Route::get('/action_type/{id}/edit', 'ActionTypeController@edit');
Route::post('/action_type/{id}/update', 'ActionTypeController@update');
Route::get('/action_type/{id}/show', 'ActionTypeController@show');


// task types
Route::get('/task_types', 'TaskTypeController@index');
Route::post('/task_types', 'TaskTypeController@store');
Route::get('/task_types/{id}/edit', 'TaskTypeController@edit');
Route::post('/task_types/{id}/update', 'TaskTypeController@update');

// Timer
Route::get('/timer', 'TimerController@index');
Route::post('/timer', 'TimerController@store');
Route::post('/timer/stop', 'TimerController@stop');


// Planner
Route::get('/planner', 'PlannerController@index');
Route::post('/planner', 'PlannerController@store');
Route::post('/planner/stop', 'PlannerController@stop');





Route::get('/task_from_calendar', 'ApiController@storeTaskFromCalendar');
Route::get('/task_from_calendar/{id}', 'ApiController@destroyTaskFromCalendar');
Route::post('/task_from_calendar/{id}', 'ApiController@updateTaskFromCalendar');
Route::post('/task_from_calendar/{task}/update', 'ApiController@updateTaskFromIndex');


Route::get('/role_modules', 'RoleModuleController@index');
Route::get('/change_permission', 'ApiController@RolModuleChangePermission');
Route::get('/get_modules/{rol}', 'ApiController@getModules');
Route::get('/save_role_module/{rol}/{module}', 'ApiController@saveRoleModule');

/*onbording*/
Route::get('/metadata/{project}/create/{audience}', 'MetadataController@createProject');
Route::get('/metadata/{project}/show/{audience}', 'MetadataController@showProject');
Route::post('/metadata/{project}/save', 'MetadataController@saveProjectCampaing');

/*Test Branding*/
Route::get('/metadata/{project}/create/2', 'MetadataController@createBranding');
Route::post('/metadata/{project}/save/2', 'MetadataController@saveProjectCampaing');
Route::get('/metadata/{project}/show/2', 'MetadataController@showBranding');

Route::get('/metadata/create', 'MetadataController@createProjectNew');
Route::post('/metadata/{id}/save/project', 'MetadataController@saveProjectCampaing');


/*Pieces*/
Route::get('/pieces', 'TaskController@pieces');
Route::get('/pieces-show', 'TaskController@piecesOnlyShow');
Route::get('/pieces/{tid}', 'TaskController@piecesShow');
Route::get('/pieces/setUser/{user}/task/{task}','TaskController@setPieceUser');
Route::get('/pieces/setStatus/{status}/task/{task}','TaskController@setPieceStatus');
Route::post('/pieces/set_description','TaskController@setDescription');
Route::post('/pieces/set_caption','TaskController@setCaption');
Route::post('/pieces/set_copy','TaskController@setCopy');
Route::post('/pieces/set_url_finished','TaskController@setUrlFinished');



Route::get('/charge_account', 'TaskController@chargeAccount');


/*Knowledge Management*/
Route::get('/knowledge_management', 'KnowledgeManagementController@index');




Route::get('/tasks_import', 'TaskController@import');
Route::post('/tasks_import/bulk_store', 'TaskController@bulkStore');



/*Brief*/
Route::get('/brief/create', 'MetadataController@createProjectBrief');
Route::post('/metadata/{id}/save/project', 'MetadataController@saveProjectCampaing');


/*CronJob*/
Route::get('tasks_due_date', 'ApiController@changeDueDate');


Route::get('/api/customers/save', 'APIController@saveApi');
Route::get('/api/customers/saveform', 'APIController@saveAPIForm');
