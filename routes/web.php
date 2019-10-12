<?php  

// Route::get('/db-to-res', 'IndexController@dbToRes');
// Route::get('/pdf-to-text-2', 'IndexController@extractPDF2');
// Route::get('/pdf-to-text', 'IndexController@extractPDF');
// Route::get('/text-to-res', 'IndexController@extractString');
// Route::get('/text-to-name', 'IndexController@extractNameFromString');
// Route::get('/text-to-alias', 'IndexController@extractAliasFromString');
// Route::get('/pdf-to-array', 'IndexController@extractPdfToArray');
// Route::get('/cut-text', 'IndexController@potongString');



Route::get('/', 'IndexController@index');
Route::get('/coba', 'IndexController@coba');
Route::get('page-not-found',['as' => 'pagenotfound','uses' => 'IndexController@pageNotFound']);
Route::get('server-error',['as' => 'servererror','uses' => 'IndexController@serverError']);
Route::get('/forgot-password', 'IndexController@forgotPassword');

// change language
Route::get('lang/{locale}', 'HomeController@lang');


Auth::routes();
Route::get('/home', 'HomeController@index')->name('home');
Route::resource('/users', 'UserController');
Route::get('/users/listuser', 'UserController@listUsers');
Route::get('/user/get-profile', 'UserController@getProfile');
Route::post('/user/change-password', 'UserController@changePassword');
Route::get('/user/get-super-admin', 'UserController@getSuperAdmin');
Route::get('/user/get-ops', 'UserController@getOps');
Route::get('/user/get-hrd', 'UserController@getHrd');
Route::get('/user/get-ukk', 'UserController@getUkk');
Route::delete('/user/delete-all/{id}', 'UserController@deleteAll'); 


Route::resource('/roles', 'RoleController');
Route::delete('/role/delete-all/{id}', 'RoleController@deleteAll');
Route::get('/get-role', 'RoleController@getRole');


Route::resource('/perorangan', 'PeroranganController');  
Route::resource('/non-perorangan', 'NonPeroranganController');


Route::get('/hrd-karyawan', 'KaryawanController@index');
Route::get('/print-karyawan', 'KaryawanController@print');
Route::post('/download-excel-karyawan', 'KaryawanController@downloadFile');
Route::get('/hrd-karyawan/by-jabatan', 'KaryawanController@chartByJabatan');
Route::get('/hrd-karyawan/by-divisi', 'KaryawanController@chartByDivisi');
Route::post('/hrd-karyawan', 'KaryawanController@store');
Route::delete('/hrd-karyawan/{id}', 'KaryawanController@destroy');
Route::post('/hrd-karyawan-update/{id}', 'KaryawanController@updateKaryawan');
Route::post('/hrd-download-import-themplate', 'KaryawanController@downloadThemplate');
Route::post('/hrd-import-data-karyawan', 'KaryawanController@importData');
Route::resource('/jabatan', 'JabatanController');
Route::get('/jabatan-dropdown', 'JabatanController@dropDown');
Route::delete('/jabatan/delete-all/{id}', 'JabatanController@deleteAll');
Route::get('/print-jabatan', 'JabatanController@print');
Route::post('/download-excel-jabatan', 'JabatanController@downloadFile');
Route::resource('/divisi', 'DivisiController');
Route::get('/divisi-dropdown', 'DivisiController@dropDown');
Route::delete('/divisi/delete-all/{id}', 'DivisiController@deleteAll');
Route::get('/print-divisi', 'DivisiController@print');
Route::post('/download-excel-divisi', 'DivisiController@downloadFile');
 
Route::resource('/ukk-peps', 'PepsController');
Route::post('/ukk-peps-update/{id}', 'PepsController@updatePeps');
Route::post('/ukk-peps-download-file-pdf', 'PepsController@downloadFilePeps');

 
Route::get('/coba-non-perorangan', 'DttotController@cobaDttotNonPerorangan');
Route::resource('/ukk-dttot', 'DttotController');
Route::post('/ukk-dttot-update/{id}', 'DttotController@updateDttot');
Route::get('/ukk-dttot-extract-file/{id}/{filename}', 'DttotController@extractPdfToArray');
Route::post('/ukk-dttot-download-excel', 'DttotController@downloadDttot');
Route::post('/ukk-dttot-download-excel-non-perorangan', 'DttotController@downloadDttotNonPerorangan');
Route::post('/ukk-dttot-download-excel-perorangan', 'DttotController@downloadDttotPerorangan');
Route::post('/ukk-dttot-download-excel-karyawan', 'DttotController@downloadDttotKaryawan');
Route::post('/ukk-dttot-download-file-pdf', 'DttotController@downloadFileDttot');
Route::get('/ukk-dttot-with-karyawan', 'DttotController@withDttotKaryawan');
Route::post('/ukk-dttot-excel-karyawan', 'DttotController@excelDttotKaryawan');

Route::get('/users-view', 'ViewController@index');
Route::get('/roles-view', 'ViewController@index');
Route::get('/operasional-view', 'ViewController@operasional'); 
Route::get('/ukk-view', 'ViewController@ukk'); 
Route::get('/hrd-view', 'ViewController@hrd'); 
