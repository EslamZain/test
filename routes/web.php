<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes();
// Auth::routes(['register' => false]);

Route::group(['middleware' => 'auth'], function () {

    Route::get('/', function () {
        return view('auth.login');
    });

    Route::get('/index', function () {
        return view('index');
    });

    Route::get('/home', 'HomeController@index')->name('home');

############# Start Invoices #################

    Route::get('/invoices', 'InvoicesController@index')->name('invoices');
    Route::get('/invoices/create', 'InvoicesController@create')->name('invoices.create');
    Route::post('/invoices/store', 'InvoicesController@store')->name('invoices.store');
    Route::get('/invoices/details/{id}', 'InvoicesController@details')->name('invoices.details');
    Route::get('/edit_invoice/{id}', 'InvoicesController@edit_invoice');
    Route::put('/invoices.update', 'InvoicesController@invoicesUpdate')->name('invoices.update');

############# End Invoices #################

############# Start sections #################

    Route::get('/sections', 'SectionsController@index')->name('sections');
    Route::post('/sections/save', 'SectionsController@store')->name('sections.store');
    Route::put('/sections/update', 'SectionsController@update')->name('sections.update');
    Route::get('/sections/delete', 'SectionsController@destroy')->name('sections.delete');

############# End sections #################

############# Start Products #################
    Route::get('/products', 'ProductController@index')->name('products');
    Route::post('/products/save', 'ProductController@store')->name('products.store');
    Route::put('/products/update', 'ProductController@update')->name('products.update');
    Route::get('/products/delete', 'ProductController@destroy')->name('products.delete');

############# End Products #################

    Route::get('/Section/{id}', 'InvoicesController@getProducts');

############# Start File ###############

    Route::get('/View_file/{invoice_number}/{file_name}', 'InvoicesController@openFile');
    Route::get('/download/{invoice_number}/{file_name}', 'InvoicesController@get_file');
    Route::get('/delete_file', 'InvoicesController@delete_file');
// Route::post('delete_file', 'InvoicesDetailsController@destroy')->name('delete_file');

############# Start InvoicesAttachments #################
    Route::post('/InvoicesAttachments/store', 'InvoicesAttachmentsController@store');

############# End InvoicesAttachments #################

    Route::get('/{page}', 'AdminController@index');
});
