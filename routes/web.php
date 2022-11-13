<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

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


Route::get('/', function () {

    if (Auth::user()) { 
        return redirect('/dashboard');
    } 
    return view('welcome');
});

Route::get('/login',function(){
    return redirect('/');
})->name('login');

Route::get('/register',function(){
    return redirect('/');
});

// Migration Routes

Route::get('/linkstorage', function () {
    Artisan::call('storage:link');
    $exitCode = Artisan::call('storage:link', [] );
    echo $exitCode;
});

Route::get('/migration', function () {
    $m = request()->m;
    Artisan::call('migrate'.$m);
    $exitCode = Artisan::call('migrate', [] );
    echo $exitCode;
});

// PRofile Routes

Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])
->name('dashboard');

Route::get('/profile', [App\Http\Controllers\HomeController::class, 'profile'])->name('profile');

Route::post('/profile', [App\Http\Controllers\HomeController::class, 'updateProfile'])->name('profile');

Route::post('/password', [App\Http\Controllers\HomeController::class, 'updatePassword'])->name('password');


Route::get('favourites',[ App\Http\Controllers\HomeController::class,'favourites'])->name('favourites');

Route::get('make-favourite',[ App\Http\Controllers\HomeController::class,'makeFavourite'])->name('make.favourite');

Route::post('favourite',[ App\Http\Controllers\HomeController::class,'getFavourite'])->name('get.favourite');

Route::post('favourite/{id}',[ App\Http\Controllers\HomeController::class,'updateFavourite'])->name('update.favourite');

Route::delete('favourite/{id}',[ App\Http\Controllers\HomeController::class,'deleteFavourite'])->name('delete.favourite');


// Setup Routes

Route::get('/setup', [App\Http\Controllers\HomeController::class, 'setup'])->name('setup');

Route::resource('document-types', App\Http\Controllers\DocumentTypeController::class);

Route::resource('users', App\Http\Controllers\UserController::class)->middleware('can:add_users');

Route::resource('roles', App\Http\Controllers\RoleController::class)->middleware('can:add_users');

Route::resource('project-types', App\Http\Controllers\ProjectTypeController::class);

Route::resource('categories', App\Http\Controllers\CategoryController::class);

Route::resource('trades', App\Http\Controllers\TradeController::class);

Route::resource('subcontractors', App\Http\Controllers\SubcontractorController::class);

Route::resource('vendors', App\Http\Controllers\VendorController::class);

Route::resource('setting', App\Http\Controllers\SettingController::class);

Route::resource('properties', App\Http\Controllers\PropertyTypeController::class);

Route::resource('assignees', App\Http\Controllers\AssigneeController::class);

Route::resource('ball_in_courts', App\Http\Controllers\BallInCourtController::class);

Route::resource('statuses', App\Http\Controllers\StatusController::class);

Route::resource('property-groups', App\Http\Controllers\PropertyGroupController::class);

Route::resource('inspection-types', App\Http\Controllers\InspectionTypeController::class);

Route::resource('report-companies', App\Http\Controllers\ReportCompanyController::class);

Route::resource('payment-statuses', App\Http\Controllers\PaymentStatusController::class);

Route::resource('rfi-submittal/statuses', App\Http\Controllers\RFISubmittalStatusController::class,
  ['names' => 'rfi-submittal.statuses']);

Route::resource('ffe/vendors', App\Http\Controllers\FFEVendorController::class,['names' => 'ffe.vendors']);

Route::resource('ffe/trades', App\Http\Controllers\FFETradeController::class,['names' => 'ffe.trades']);

Route::resource('ffe/categories', App\Http\Controllers\FFECategoryController::class,['names' => 'ffe.categories']);

Route::resource('ffe/categories', App\Http\Controllers\FFECategoryController::class,['names' => 'ffe.categories']);


// Document Routes



Route::get('documents/search', [App\Http\Controllers\DocumentController::class,'search'])->name('documents.search');


Route::delete('documents/{id}/file', [App\Http\Controllers\DocumentController::class,'destroyFile'])->name('documents.file.destroy');

Route::resource('documents', App\Http\Controllers\DocumentController::class);


// Reports Routes

Route::resource('reports', App\Http\Controllers\ReportController::class);

 Route::get('reports/{id}/{type}/{trade?}', [App\Http\Controllers\ReportController::class,'getReport'])->name('reports.report');

 Route::post('reports/{id}/send-mail', [App\Http\Controllers\ReportController::class,'reportSendMail'])->name('reports.send-mail');



// Files Routes

Route::get('files/{directory?}',[App\Http\Controllers\FileController::class,'index'])->name('files.index');

Route::get('files/{directory}/{property_type}',[App\Http\Controllers\FileController::class,'propertyType'])->name('files.property-type');

Route::get('files/{directory}/{property_type}/{property}',[App\Http\Controllers\FileController::class,'property'])->name('files.property');

Route::get('files/{directory}/{property_type}/{property}/{doc_type}',[App\Http\Controllers\FileController::class,'docType'])->name('files.doc_type');


Route::get('files/{directory}/{property_type}/{property}/{doc_type}/{doc}',[App\Http\Controllers\FileController::class,'doc'])->name('files.doc');

Route::delete('files', [App\Http\Controllers\FileController::class,'destroy'])->name('files.destroy');


// Project Routes

Route::resource('projects', App\Http\Controllers\ProjectController::class);

Route::get('projects/{id}/documents',[App\Http\Controllers\DocumentController::class,'create'])
->name('projects.documents');

Route::get('projects/{id}/documents/{document}',[App\Http\Controllers\DocumentController::class,'show'])->name('projects.documents.show');

Route::post('projects/{id}/documents',[App\Http\Controllers\DocumentController::class,'store'])
->name('projects.documents');


Route::get('projects/{id}/trades',[App\Http\Controllers\TradeController::class,'createProjectTrade'])->name('projects.trades');

Route::post('projects/{id}/trades',[App\Http\Controllers\TradeController::class,'storeProjectTrade'])->name('projects.trades');

Route::post('projects/{id}/trades/multiple',[App\Http\Controllers\TradeController::class,'storeMultipleProjectTrade'])->name('projects.trades.multiple');

 Route::delete('projects/{project_id}/trades/{id}', [App\Http\Controllers\TradeController::class,'destroyProjectTrade'])->name('projects.trades.destroy');

 Route::get('projects/{id}/proposals/{trade}',[App\Http\Controllers\ProposalController::class,'create'])->name('projects.proposals');

Route::post('projects/{id}/proposals/{trade}',[App\Http\Controllers\ProposalController::class,'store'])->name('projects.proposals');

Route::get('projects/proposals/{id}',[App\Http\Controllers\ProposalController::class,'show'])->name('projects.proposals.edit');

Route::get('projects/proposals/award/{id}/{status}',[App\Http\Controllers\ProposalController::class,'award'])->name('projects.proposals.award');

Route::post('projects/proposals/{id}',[App\Http\Controllers\ProposalController::class,'update'])->name('projects.proposals.update');

Route::post('projects/proposals/{id}/upload',[App\Http\Controllers\ProposalController::class,'upload'])->name('projects.proposals.upload');

 Route::delete('projects/proposals/{id}', [App\Http\Controllers\ProposalController::class,'destroy'])->name('projects.proposals.destroy');

 Route::delete('projects/proposals/{id}/file', [App\Http\Controllers\ProposalController::class,'destroyFile'])->name('projects.proposals.file.destroy');

Route::get('projects/{id}/payments',[App\Http\Controllers\PaymentController::class,'create'])->name('projects.payments');

Route::post('projects/{id?}/payments',[App\Http\Controllers\PaymentController::class,'store'])->name('projects.payments');

Route::get('projects/payments/{id}',[App\Http\Controllers\PaymentController::class,'show'])->name('projects.payments.edit');

Route::post('projects/payments/{id}',[App\Http\Controllers\PaymentController::class,'update'])->name('projects.payments.update');

 Route::delete('projects/payments/{id}', [App\Http\Controllers\PaymentController::class,'destroy'])->name('projects.payments.destroy');

 Route::delete('projects/payments/{id}/file', [App\Http\Controllers\PaymentController::class,'destroyFile'])->name('projects.payments.file.destroy');

 Route::get('projects/{id}/download', [App\Http\Controllers\PaymentController::class,'downloadPDF'])->name('projects.download');

 Route::post('projects/{id}/send-mail', [App\Http\Controllers\PaymentController::class,'sendMail'])->name('projects.send.mail');


Route::prefix('projects')->group(function(){

     // Bills Route

     Route::get('{id}/bills',[App\Http\Controllers\BillController::class,'create'])
     ->name('projects.bills');

    Route::post('{id?}/bills',[App\Http\Controllers\BillController::class,'store'])
    ->name('projects.bills');
    
    Route::get('bills/{id}',[App\Http\Controllers\BillController::class,'show'])
    ->name('projects.bills.edit');
   
     Route::get('bills/{id}/bill-stattus', [App\Http\Controllers\BillController::class,'billStatus'])->name('projects.bills.status');

    Route::post('bills/{id}',[App\Http\Controllers\BillController::class,'update'])->name('projects.bills.update');

     Route::delete('bills/{id}', [App\Http\Controllers\BillController::class,'destroy'])->name('projects.bills.destroy');

     Route::delete('bills/{id}/file', [App\Http\Controllers\BillController::class,'destroyFile'])->name('projects.bills.file.destroy');

    // Total Construction Cost

    Route::get('{id}/total/download', [App\Http\Controllers\PaymentController::class,'totalDownloadPDF'])->name('projects.total.download');

    Route::post('{id}/total/send-mail', [App\Http\Controllers\PaymentController::class,'totalSendMail'])->name('projects.total.send.mail');


   Route::get('{id}/get-project-lines', [App\Http\Controllers\ProjectLineController::class,'index'])->name('projects.get-project-lines');

    Route::get('{id}/aia-pay-app', [App\Http\Controllers\ProjectLineController::class,'create'])->name('projects.aia-pay-app');

    Route::post('{id}/add-project-lines', [App\Http\Controllers\ProjectLineController::class,'store'])->name('projects.add-project-lines.store'); 

     Route::delete('{id}/project-lines', [App\Http\Controllers\ProjectLineController::class,'destroy'])->name('projects.project-lines.destroy');

     Route::match(['get','post'],'delete/{id}/project-lines', [App\Http\Controllers\ProjectLineController::class,'deleteLines'])->name('projects.project-lines.delete');

     Route::get('{id}/applications', [App\Http\Controllers\ProjectApplicationController::class,'create'])->name('projects.applications'); 

    Route::get('{id}/applications/edit', [App\Http\Controllers\ProjectApplicationController::class,'edit'])->name('projects.applications.edit'); 
    
     Route::get('{id}/get-project-applications', [App\Http\Controllers\ProjectApplicationController::class,'index'])->name('projects.applications.index'); 

     Route::post('{id}/applications', [App\Http\Controllers\ProjectApplicationController::class,'store'])->name('projects.applications.store');

      Route::get('{id}/get-applications-summary', [App\Http\Controllers\ProjectApplicationController::class,'summary'])->name('projects.applications.summary'); 

      Route::get('{id}/get-all-applications', [App\Http\Controllers\ProjectApplicationController::class,'allApplications'])->name('projects.all.applications');

      Route::get('{id}/{to}/{app_id}', [App\Http\Controllers\ProjectApplicationController::class,'generatePDF'])->name('projects.application.pdf'); 


      Route::get('{id}/change-orders', [App\Http\Controllers\ChangeOrderController::class,'create'])->name('projects.change-orders.create'); 

      Route::post('{id}/change-orders', [App\Http\Controllers\ChangeOrderController::class,'store'])->name('projects.change-orders.store'); 

      Route::get('{id}/get-change-orders', [App\Http\Controllers\ChangeOrderController::class,'index'])->name('projects.change-orders.index'); 

      Route::delete('{id}/change-orders', [App\Http\Controllers\ChangeOrderController::class,'destroy'])->name('projects.change-orders.destroy');

      Route::get('{id}/close-project', [App\Http\Controllers\CloseProjectController::class,'create'])->name('projects.close-project.create'); 

      Route::post('{id}/close-project', [App\Http\Controllers\CloseProjectController::class,'store'])->name('projects.close-project.store'); 

    

      // RFI Routes 
    Route::get('{id}/rfi',[App\Http\Controllers\RFIController::class,'create'])->name('projects.rfi');

    Route::post('{id?}/rfi',[App\Http\Controllers\RFIController::class,'store'])->name('projects.rfi');

    Route::get('rfi/{id}',[App\Http\Controllers\RFIController::class,'show'])->name('projects.rfi.edit');

    Route::post('rfi/{id}',[App\Http\Controllers\RFIController::class,'update'])->name('projects.rfi.update');

     Route::delete('rfi/{id}', [App\Http\Controllers\RFIController::class,'destroy'])->name('projects.rfi.destroy');

     Route::delete('rfi/{id}/file', [App\Http\Controllers\RFIController::class,'destroyFile'])->name('projects.rfi.file.destroy');

    Route::post('rfi/{id}/send-mail', [App\Http\Controllers\RFIController::class,'sendMail'])->
           name('rfi.send.mail');


 // Submittal Routes 
    Route::get('{id}/submittal',[App\Http\Controllers\SubmittalController::class,'create'])->name('projects.submittal');

    Route::post('{id?}/submittal',[App\Http\Controllers\SubmittalController::class,'store'])->name('projects.submittal');

    Route::get('submittal/{id}',[App\Http\Controllers\SubmittalController::class,'show'])->name('projects.submittal.edit');

    Route::post('submittal/{id}',[App\Http\Controllers\SubmittalController::class,'update'])->name('projects.submittal.update');

     Route::delete('submittal/{id}', [App\Http\Controllers\SubmittalController::class,'destroy'])->name('projects.submittal.destroy');

     Route::delete('submittal/{id}/file', [App\Http\Controllers\SubmittalController::class,'destroyFile'])->name('projects.submittal.file.destroy');

     Route::post('submittal/{id}/send-mail', [App\Http\Controllers\SubmittalController::class,'sendMail'])->name('submittal.send.mail');


   //Attachment Routes

   Route::get('{id}/attachment',[App\Http\Controllers\ProjectController::class,'getAttachment'])->name('projects.attachment');

    Route::post('{id}/attachment',[App\Http\Controllers\ProjectController::class,'uploadAttachment'])->name('projects.attachment.update');    

     // Logs Route 

    Route::get('{id}/logs',[App\Http\Controllers\ProcurementLogController::class,'create'])->name('projects.logs');

    Route::post('{id}/logs',[App\Http\Controllers\ProcurementLogController::class,'store'])->name('projects.logs.store');

    Route::get('logs/{id}',[App\Http\Controllers\ProcurementLogController::class,'show'])->name('projects.logs.edit');

    Route::post('logs/{id}',[App\Http\Controllers\ProcurementLogController::class,'update'])->name('projects.logs.update');

     Route::delete('logs/{id}', [App\Http\Controllers\ProcurementLogController::class,'destroy'])->name('projects.logs.destroy');

     Route::delete('logs/{id}/file', [App\Http\Controllers\ProcurementLogController::class,'destroyFile'])->name('projects.logs.file.destroy');

     Route::get('{id}/download-logs', [App\Http\Controllers\ProcurementLogController::class,'downloadPDF'])->name('projects.logs.download');

     Route::post('{id}/send-mail-logs', [App\Http\Controllers\ProcurementLogController::class,'sendMail'])->name('projects.logs.send.mail');     

   
      //FFE Routes
      Route::group(['prefix' => '{project}/ffe'], function ($project) { 
             Route::get('/',[App\Http\Controllers\FFEController::class,'index'])->name('ffe.index');

            Route::get('proposals/{trade}',[App\Http\Controllers\FFEProposalController::class,'create'])->name('projects.ffe.proposals');

            Route::post('proposals/{trade}',[App\Http\Controllers\FFEProposalController::class,'store'])->name('projects.ffe.proposals');

            Route::get('proposals/{id}/edit',[App\Http\Controllers\FFEProposalController::class,'show'])->name('projects.ffe.proposals.edit');

            Route::get('proposals/award/{id}/{status}',[App\Http\Controllers\FFEProposalController::class,'award'])->name('projects.ffe.proposals.award');

            Route::post('proposals/{id}/update',[App\Http\Controllers\FFEProposalController::class,'update'])->name('projects.ffe.proposals.update');

            Route::post('proposals/{id}/upload',[App\Http\Controllers\FFEProposalController::class,'upload'])->name('projects.ffe.proposals.upload');

             Route::delete('proposals/{id}', [App\Http\Controllers\FFEProposalController::class,'destroy'])->name('projects.ffe.proposals.destroy');

             Route::delete('proposals/{id}/file', [App\Http\Controllers\FFEProposalController::class,'destroyFile'])->name('projects.ffe.proposals.file.destroy');


             Route::get('trades/add',[App\Http\Controllers\FFETradeController::class,'createProjectTrade'])->name('projects.ffe.trades');

            Route::post('trades/store',[App\Http\Controllers\FFETradeController::class,'storeProjectTrade'])->name('projects.ffe.trades.store');

            Route::post('trades/multiple',[App\Http\Controllers\FFETradeController::class,'storeMultipleProjectTrade'])->name('projects.ffe.trades.multiple');

             Route::delete('trades/{id}', [App\Http\Controllers\FFETradeController::class,'destroyProjectTrade'])->name('projects.ffe.trades.destroy');
              
            // Payments Route  
             
            Route::get('payments/create',[App\Http\Controllers\FFEPaymentController::class,'create'])->name('projects.ffe.payments');

            Route::post('{id?}/payments',[App\Http\Controllers\FFEPaymentController::class,'store'])->name('projects.ffe.payments.store');

            Route::get('payments/{id}',[App\Http\Controllers\FFEPaymentController::class,'show'])->name('projects.ffe.payments.edit');

            Route::post('payments/{id}',[App\Http\Controllers\FFEPaymentController::class,'update'])->name('projects.ffe.payments.update');

             Route::delete('payments/{id}', [App\Http\Controllers\FFEPaymentController::class,'destroy'])->name('projects.ffe.payments.destroy');

             Route::delete('payments/{id}/file', [App\Http\Controllers\FFEPaymentController::class,'destroyFile'])->name('projects.ffe.payments.file.destroy');

             Route::get('budget/download', [App\Http\Controllers\FFEPaymentController::class,'downloadPDF'])->name('projects.ffe.download');

             Route::post('send-mail', [App\Http\Controllers\FFEPaymentController::class,'sendMail'])->name('projects.ffe.send.mail');

            // Logs Route 

            Route::get('logs/create',[App\Http\Controllers\FFEProcurementLogController::class,'create'])->name('projects.ffe.logs');

            Route::post('{id?}/logs',[App\Http\Controllers\FFEProcurementLogController::class,'store'])->name('projects.ffe.logs.store');

            Route::get('logs/{id}',[App\Http\Controllers\FFEProcurementLogController::class,'show'])->name('projects.ffe.logs.edit');

            Route::post('logs/{id}',[App\Http\Controllers\FFEProcurementLogController::class,'update'])->name('projects.ffe.logs.update');

             Route::delete('logs/{id}', [App\Http\Controllers\FFEProcurementLogController::class,'destroy'])->name('projects.ffe.logs.destroy');

             Route::delete('logs/{id}/file', [App\Http\Controllers\FFEProcurementLogController::class,'destroyFile'])->name('projects.ffe.logs.file.destroy');

             Route::get('download/logs', [App\Http\Controllers\FFEProcurementLogController::class,'downloadPDF'])->name('projects.ffe.logs.download');

             Route::post('send-mail-logs', [App\Http\Controllers\FFEProcurementLogController::class,'sendMail'])->name('projects.ffe.logs.send.mail');


      });



});


// Calendar Route

Route::resource('calendar', App\Http\Controllers\CalendarController::class);
Route::get('get-projects', [App\Http\Controllers\CalendarController::class,'getProjects'])
     ->name('calendar.projects');
Route::resource('itb-tracker', App\Http\Controllers\ITBTrackerController::class);

Route::post('send-mail', [App\Http\Controllers\ITBTrackerController::class,'sendMail'])->
           name('send.mail');

Route::post('send-mail-pdf', [App\Http\Controllers\ITBTrackerController::class,'sendMailWithPdf'])->
           name('send.mail.pdf');
Route::post('bid-recieved', [App\Http\Controllers\ITBTrackerController::class,'bidRecieved'])->
           name('bid.recieved');
Route::post('contract-signed', [App\Http\Controllers\ITBTrackerController::class,'contractSigned'])->
           name('contract.signed');

//Vendor 

 Route::get('get-materials', [App\Http\Controllers\VendorController::class,'getMaterials'])
     ->name('vendor.materials');          