<?php

use Illuminate\Http\Request;
use App\Http\Controllers\API\AuthController;
use PhpOffice\PhpWord\IOFactory;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::group(['namespace' => 'API', 'prefix' => 'admin', 'as' => 'admin.'], function () {
  Route::get('/getratinglink/{id}', 'InterviewScheduleController@getratinglink');
  Route::get('jobapplication/simple/data', 'JobApplicationController@exportJobApplication');
  Route::post('/sendPasswordResetLink', 'ResetPasswordController@sendEmail');
  Route::post('/resetPassword', 'ResetPasswordController@submitResetPasswordForm');
  Route::post('/read-docx', function (Request $request) {

    $file = $request->file('resume');

    $striped_content = '';
    $content = '';

    $zip = zip_open($file);

    if (!$zip || is_numeric($zip))
      return false;

    while ($zip_entry = zip_read($zip)) {

      if (zip_entry_open($zip, $zip_entry) == FALSE)
        continue;

      if (zip_entry_name($zip_entry) != "word/document.xml")
        continue;

      $content .= zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));

      zip_entry_close($zip_entry);
    }// end while

    zip_close($zip);

    $content = str_replace('</w:r></w:p></w:tc><w:tc>', " ", $content);
    $content = str_replace('</w:r></w:p>', "\r\n", $content);
    $content1 = strip_tags($content);



    $email = preg_match('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $content, $matches) ? $matches[0] : '';
    $phone = preg_match('/(\+?\d[-\.\s]?)?(\(\d{3}\)|\d{3})[-\.\s]?(\d{3})[-\.\s]?(\d{4})/', $content, $matches) ? $matches[0] : '';


    $data = ['email' => $email, 'mobile' => $phone];

    return response()->json($data);
  });

  Route::post('/login', 'DashboardController@login');
  Route::post('/register', 'DashboardController@store');
  
  Route::get('/testmail', 'ErfgeneraterController@mail');
  Route::get('lead/assignlink/{id}', 'LeadMangementController@linkget');
  Route::get('/jobcategory', 'JobCategoryController@index1');
  Route::get('/certifications', 'NotificationController@index1');
  Route::get('/skills/search/{id}', 'SkillController@search1');
  Route::get('/qualifications', 'QualificationController@index1');
  Route::post('/subqualifications/search', 'SubqualificationController@search1');

  Route::get('salaryverification/{id}', 'ErfgeneraterController@Salarygeturl');

  Route::post('salaryupdate/{id}', 'ErfgeneraterController@SalaryUpdate');
  Route::post('lead/approved/{id}', 'LeadMangementController@approved_by');

  Route::get('/exam/job-applications/{id}', 'ExamController@onlinexamlistbyapplication');
  Route::post('/exam/job-applications/submit', 'ExamController@examresult_save');


  //pcc upload
  Route::post('pccupload/{id}', 'JobApplicationController@uploadpcc');
  Route::post('documentsupload/{id}', 'JobApplicationController@uploadDocument');
  Route::get('job-offer/{id}', 'AdminJobOnboardController@view');
  Route::post('saveOffer', 'AdminJobOnboardController@saveOffer');
  Route::post('/job-applications/rating/{id}', 'JobApplicationController@ratingSave');

});

Route::middleware('auth:api')->group(function () {
  Route::group(['namespace' => 'API', 'prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::post('/dashboard', 'DashboardController@dashboard')->name('dashboard');
    Route::post('/logout', 'DashboardController@logout');
    //jobcategories
    Route::get('/jobcategories', 'JobCategoryController@index');
    Route::get('/jobcategories/edit/{id}', 'JobCategoryController@edit');
    Route::post('/jobcategories/update/{id}', 'JobCategoryController@update');
    Route::post('/jobcategories/store', 'JobCategoryController@add');
    Route::post('/jobcategories/delete/{id}', 'JobCategoryController@destroy');
    //eduction
    Route::get('/qualification', 'QualificationController@index');
    Route::post('/qualification/update/{id}', 'QualificationController@update');
    Route::post('/qualification/store', 'QualificationController@add');
    Route::post('/qualification/delete/{id}', 'QualificationController@destroy');


    //sub eduction
    Route::get('/subqualification', 'SubqualificationController@index');
    Route::post('/subqualification/update/{id}', 'SubqualificationController@update');
    Route::post('/subqualification/store', 'SubqualificationController@add');
    Route::post('/subqualification/delete/{id}', 'SubqualificationController@destroy');
    Route::post('/subqualification/search', 'SubqualificationController@search');
    //Departments

    Route::get('/department', 'DepartmentController@index');
    Route::post('/department/update/{id}', 'DepartmentController@update');
    Route::post('/department/store', 'DepartmentController@add');
    Route::post('/department/delete/{id}', 'DepartmentController@destroy');
    //skill
    Route::get('/skill', 'SkillController@index')->name('skill');
    Route::post('/skill/add', 'SkillController@add');
    Route::get('/skill/edit/{id}', 'SkillController@edit');
    Route::post('/skill/delete/{id}', 'SkillController@destroy');
    Route::get('/skill/search/{id}', 'SkillController@search');
    Route::post('/skill/update/{id}', 'SkillController@update');
    //
    Route::get('questions', 'OnlinequestionController@index');
    Route::post('questions/store', 'OnlinequestionController@Store');
    Route::get('/questions/edit/{id}', 'OnlinequestionController@edit');
    Route::post('/questions/update/{id}', 'OnlinequestionController@update');
    Route::get('questions/delete/{id}', 'OnlinequestionController@destroy');
    //Exam
    Route::get('/exam', 'ExamController@index');
    Route::post('/exam/store', 'ExamController@store');
    Route::get('/exam/delete/{id}', 'ExamController@destroy');
    Route::post('/exam/update/{id}', 'ExamController@update');
    Route::get('/exam/edit/{id}', 'ExamController@edit');
    //Question Assign
    Route::Post('/exam/assign', 'ExamController@Assignquestion');
    Route::get('/exam/getexamquestion/{exam_id}', 'ExamController@getExamQuestion');
    Route::get('/exam/removeexamquestion/{exam_id}/{question_id}', 'ExamController@RemoveExamQuestion');
    //assign student exam
    Route::post('/exam/assign/job-applications', 'ExamController@exmaassign');
    Route::Post('/exam/assign/job-applications-remove', 'ExamController@exmaassignRemove');
    Route::get('/exam/examresult/{id}', 'ExamController@results');
    //location
    Route::get('/location', 'LocationController@index');
    //ERF
    Route::get('/erf', 'ErfgeneraterController@index');
    Route::get('/erf/view/{id}', 'ErfgeneraterController@view');
    // Route::get('/leadstest/{id}','ErfgeneraterController@leaddata');
    Route::match(['GET', 'POST'], '/leadstest/{id?}', 'ErfgeneraterController@leaddata');
    Route::get('/jobs/approveal/{id}', 'ErfgeneraterController@approveallink');
    //ERF STORE

    Route::post('/jobs/erf/{id}', 'ErfgeneraterController@Leads');
    Route::post('/jobs/lead/{id}/{leadid}', 'ErfgeneraterController@Leadsupdate');
    //Create Team
    Route::get('/role', 'TeamController@index');
    Route::get('team/delete/{id}', 'TeamController@destroy');
    Route::post('team/change-role', 'TeamController@changeRole');

    Route::post('/team/store', 'TeamController@Store');
    Route::get('/team', 'TeamController@get');
    Route::get('/team/{name}', 'TeamController@filter');
    Route::post('/team/update/{id}', 'TeamController@update');
    Route::post('/team/changepassword/{id}', 'TeamController@changepassword');
    //Permission
    Route::get('/role-permission', 'RolePermission@get');
    Route::post('/role-permission/allpermission', 'RolePermission@assignAllPermission');
    Route::post('/role-permission/assign', 'R  olePermission@store');
    Route::post('/role-permission/permissionid', 'RolePermission@permisioncheck');


    //role
    Route::post('/role/store', 'RolePermission@storeRole');
    Route::post('/role/update/{id}', 'RolePermission@Update');
    Route::post('/jobs/store', 'ErfgeneraterController@store');

    //add jobaplication

    Route::post('/job-applications/create', 'JobApplicationController@store');
    Route::match(['GET', 'POST'], '/job-applications/{id?}', 'JobApplicationController@get');
    Route::get('/job-applications/edit/{id}', 'JobApplicationController@edit');
    Route::post('/job-applications/update/{id}', 'JobApplicationController@update');
    Route::get('/job-applications/delete/{id}', 'JobApplicationController@destroy');
    //Route::post('/job-applications/rating/{id}','JobApplicationController@ratingSave');
    Route::get('/job-applications/show/{id}', 'JobApplicationController@show');
    Route::post('/job-applications/archiveJobApplication/{application}', 'JobApplicationController@archiveJobApplication');
    Route::post('/job-applications/reject/{application}', 'JobApplicationController@reject');
    Route::post('/job-applications/selected-application/{application}', 'JobApplicationController@selectionupdate');
    Route::post('/job-applications/unarchive-job-application/{application}', 'JobApplicationController@unarchiveJobApplication');
    Route::post('/upload-result/{id}', 'JobApplicationController@uploadresult');
    //lead assign
    Route::post('lead/assignlead', 'LeadMangementController@assign');
    Route::post('/lead/transfer/{id}', 'LeadMangementController@transfer');
    //interview

    Route::get('/interview/getdata', 'InterviewScheduleController@view');

    Route::post('interview/store', 'InterviewScheduleController@store');
    Route::post('interview/storeurl', 'InterviewScheduleController@storeurl');
    Route::get('interview', 'InterviewScheduleController@data');
    Route::get('interview/delete/{id}', 'InterviewScheduleController@destroy');
    Route::post('interview/update/{id}', 'InterviewScheduleController@update');
    Route::get('interview-schedule/showdata/{id}', 'InterviewScheduleController@show');
    Route::post('interview-schedule/change-status/{id?}', 'InterviewScheduleController@changeStatus');


    //certification
    Route::get('notification', 'NotificationController@unread');
    Route::get('/certification', 'NotificationController@index');
    Route::post('/certification/update/{id}', 'NotificationController@update');
    Route::post('/certification/store', 'NotificationController@add');
    Route::post('/certification/delete/{id}', 'NotificationController@destroy');
    //condidate database
    Route::get('/applications-archive', 'AdminApplicationArchiveController@data');


    Route::post('jobapplication/import', 'JobApplicationController@JobApplicationimport');

    Route::get('/lead/details/{id}', 'LeadMangementController@leadDetails');

    Route::post('/salarycreate/{id?}', 'ErfgeneraterController@Salarycreated');
    #Route::post('/salaryupdate/{id?}','ErfgeneraterController@SalaryUpdate');
    Route::get('/salaryget/{id}', 'ErfgeneraterController@Salaryget');

    Route::post('jobapplication/statusupdate/{id}', 'JobApplicationController@statusJobApplication');
    Route::get('documents/uploadlink/{id}', 'JobApplicationController@documentuploadlink');
    //send pcc documents
    Route::get('pccdocumentslink/{id}', 'JobApplicationController@PCClink');
    Route::post('/create-offer/{id}', 'AdminJobOnboardController@store');

    Route::get('onboard-list', 'AdminJobOnboardController@onboadList');
    Route::post('onboard-statuschange/{id}', 'AdminJobOnboardController@updateStatus');
    Route::post('job/{id}', 'ErfgeneraterController@updatejobStatus');
    Route::match(['GET', 'POST'], '/leaddatasearch/{id?}', 'ErfgeneraterController@leaddatasearch');
    Route::get('/exam/examresultlist', 'ExamController@resultslist');
    Route::get('/candidate', 'JobApplicationController@getlist');
    Route::post('/jdstore', 'JdController@store');
    Route::get('/jd/{id?}', 'JdController@index');
    Route::post('/jd/update/{id}', 'JdController@update');
    Route::get('/jd/delete/{id}', 'JdController@delete');

    Route::get('/candidatelist', 'JobApplicationController@getlistbyid');

    Route::get('/ratinglistround/{type?}', 'JobApplicationController@ratinginterviewoneold');
    //Route::get('/ratinglistroundnew/{type?}','JobApplicationController@ratinginterviewoneold');
    Route::get('/candidatefilter/{id}', 'JobApplicationController@getlistbyidfilter');
    Route::post('/offerletter', 'OfferletterController@store');
    Route::get('/offerletter', 'OfferletterController@index');
    Route::post('/offerletterupdate/{id}', 'OfferletterController@update');

    Route::get('/offerletter/delete/{id}', 'OfferletterController@delete');

    Route::post('/sendratinglink/{id}/{employeeid}', 'InterviewScheduleController@sendratinglink');
    Route::post('/hired/{id}', 'JobApplicationController@hired');
  });





});
