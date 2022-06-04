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
 
   Route::get('{id}/get-project-lines', [App\Http\Controllers\ProjectLineController::class,'index'])->name('projects.get-project-lines');

    Route::get('{id}/aia-pay-app', [App\Http\Controllers\ProjectLineController::class,'create'])->name('projects.aia-pay-app');

    Route::post('{id}/add-project-lines', [App\Http\Controllers\ProjectLineController::class,'store'])->name('projects.add-project-lines.store'); 

     Route::delete('{id}/project-lines', [App\Http\Controllers\ProjectLineController::class,'destroy'])->name('projects.project-lines.destroy');

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