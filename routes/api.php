<?php

use Illuminate\Http\Request;

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

use App\Tenant;
use Hyn\Tenancy\Environment;

/* HACK */
$posible_hosts = [
    env('TENANT_URL_BASE'),
    env('TENANT_URL_BASE_LOCAL')
];

$origin  = request()->header('origin');
$_origin = '';

foreach ($posible_hosts AS $host) {

    if (!empty($_origin)) {
        continue;
    }

    $_origin_arr = explode($host, $origin);
    
    if ((is_array($_origin_arr)) && (count($_origin_arr) > 1)) {
        $_origin = $_origin_arr[0];
        
        $_origin = str_replace('http://', '', $_origin);
        $_origin = str_replace('https://', '', $_origin);
        $_origin = substr($_origin, 0, -1);
        
        $_origin = trim($_origin);
    }
  
}

$origin = $_origin;

if (!empty($origin)) {

    $tenant  = Tenant::getByFQDN($origin);
    app(Environment::class)->tenant($tenant->website);

}
/* END HACK */

/*
Route::group([

    'middleware' => ['api'],
    'prefix' => 'auth'

], function ($router) {

    Route::post('login', 'Auth\AuthController@login');
    Route::post('logout', 'Auth\AuthController@logout');
    Route::post('refresh', 'Auth\AuthController@refresh');
    Route::post('me', 'Auth\AuthController@me');

});*/

Route::group([

    'middleware' => ['tenant.confirm'],

], function ($router) {
   
    Route::post('graphql', 'GraphController@query');  

});
