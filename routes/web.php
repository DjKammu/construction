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

Route::get('/linkstorage', function () {
    Artisan::call('storage:link');
    $exitCode = Artisan::call('storage:link', [] );
    echo $exitCode;
});

Route::get('/migration', function () {
    Artisan::call('migrate');
    $exitCode = Artisan::call('migrate', [] );
    echo $exitCode;
});

Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])
->name('dashboard');

Route::get('/profile', [App\Http\Controllers\HomeController::class, 'profile'])->name('profile');

Route::post('/profile', [App\Http\Controllers\HomeController::class, 'updateProfile'])->name('profile');

Route::post('/password', [App\Http\Controllers\HomeController::class, 'updatePassword'])->name('password');

Route::get('/setup', [App\Http\Controllers\HomeController::class, 'setup'])->name('setup');

Route::resource('project-types', App\Http\Controllers\ProjectTypeController::class);

Route::resource('properties', App\Http\Controllers\PropertyController::class);

Route::resource('roles', App\Http\Controllers\RoleController::class)->middleware('can:add_users');

Route::resource('users', App\Http\Controllers\UserController::class)->middleware('can:add_users');

Route::resource('document-types', App\Http\Controllers\DocumentTypeController::class);
Route::resource('categories', App\Http\Controllers\CategoryController::class);


Route::get('documents/search', [App\Http\Controllers\DocumentController::class,'search'])->name('documents.search');

Route::resource('documents', App\Http\Controllers\DocumentController::class);

Route::get('properties/{id}/documents',[App\Http\Controllers\DocumentController::class,'create'])
->name('properties.documents');

Route::get('properties/{id}/documents/{document}',[App\Http\Controllers\DocumentController::class,'show'])->name('properties.documents.show');

Route::post('properties/{id}/documents',[App\Http\Controllers\DocumentController::class,'store'])
->name('properties.documents');

Route::delete('documents/{id}/file', [App\Http\Controllers\DocumentController::class,'destroyFile'])->name('documents.file.destroy');



Route::get('files/{directory?}',[App\Http\Controllers\FileController::class,'index'])->name('files.index');

Route::get('files/{directory}/{property_type}',[App\Http\Controllers\FileController::class,'propertyType'])->name('files.property-type');

Route::get('files/{directory}/{property_type}/{property}',[App\Http\Controllers\FileController::class,'property'])->name('files.property');

Route::get('files/{directory}/{property_type}/{property}/{doc_type}',[App\Http\Controllers\FileController::class,'docType'])->name('files.doc_type');


Route::get('files/{directory}/{property_type}/{property}/{doc_type}/{doc}',[App\Http\Controllers\FileController::class,'doc'])->name('files.doc');

Route::delete('files', [App\Http\Controllers\FileController::class,'destroy'])->name('files.destroy');

Route::resource('trades', App\Http\Controllers\TradeController::class);
Route::resource('subcontractors', App\Http\Controllers\SubcontractorController::class);