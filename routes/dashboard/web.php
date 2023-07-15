<?php

use App\Http\Controllers\Dashboard\SectionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Dashboard\UserController ;

Route::group(['prefix' => LaravelLocalization::setLocale(), 'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']],
    function () {

            Route::prefix('dashboard')->name('dashboard.')->middleware(['auth'])->group(function () {

            Route::get('/', 'WelcomeController@index')->name('welcome');

            //category routes
            Route::resource('categories', 'CategoryController')->except(['show']);

            //product routes
            Route::get('productf/{product}', 'ProductController@f')->name('productf');

            Route::get('productc/{product}', 'ProductController@c')->name('productc');



            Route::get('printedit/{id}', 'PrinterController@edit')->name('printedit');
            
             Route::put('printupdate/{id?}', 'PrinterController@update')->name('printupdate');


            Route::get('productindex/{product}', 'ProductController@indexf')->name('productindex');

            Route::get('productedit/{id}', 'ProductController@editf')->name('productedit');

                Route::delete('productdestroy/{id}','ProductController@productdestroy')->name('productdestroy');

            Route::post('productupdate/{product?}', 'ProductController@updatef')->name('productupdate');

            Route::resource('products', 'ProductController')->except(['show']);

            //client routes
                Route::resource('takeaway', 'Client\TakeawayController')->except(['show']);
                Route::resource('place', 'Client\PlaceController')->except(['show']);
            Route::resource('clients', 'ClientController')->except(['show']);
            Route::get('clientstype','ClientController@type')->name('type');
         //       Route::get('clients.place','ClientController@place')->name('clients.place');
            Route::get('clients.orders.add/{client}/order/{order}', 'Client\OrderController@add')->name('clients.orders.add');
            Route::post('clients.orders.storeadd/{client}', 'Client\OrderController@storeadd')->name('clients.orders.storeadd');
            Route::resource('clients.orders', 'Client\OrderController')->except(['show']);
            Route::resource('clients.total', 'Client\TotalController')->except(['show']);


            Route::get('kitchen_table','KitchenController@index')->name('kitchen_table');

            Route::get('kitchen_done/{id}','KitchenController@done')->name('kitchen-done');
            Route::get('kitchen_notdone/{id}','KitchenController@notdone')->name('kitchen-notdone');

            Route::post('kitchen_finsh/{id?}/{client?}','KitchenController@finsh')->name('kitchen-finsh');

            Route::get('finshorder/{id?}/{client?}','KitchenController@traceupdate')->name('finshorder');

        //    Route::resource('place', 'Place\OrderController');
            //order routes
            Route::resource('orders', 'OrderController');
                Route::get('/orders/{order}/products', 'OrderController@products')->name('orders.products');
                Route::get('/orders/{order}/printproducts', 'OrderController@printproducts')->name('orders.printproducts');

//                printproducts
            //user routes
            Route::resource('users', 'UserController')->except(['show' , '__construct']);

            //report routes
            Route::get('report','ReportController@index')->name('report');
            Route::get('report/{client_id}','ReportController@order')->name('report-client_id');
            Route::get('report-products/{date1?}/{date2?}','ReportController@products')->name('report-products');

            Route::get('section/{id}', [SectionController::class, 'section'])->name('section_one');

        });//end of dashboard routes


    });


