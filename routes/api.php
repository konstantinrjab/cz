<?php

use Illuminate\Http\Request;
use MongoDB\Client as Mongo;

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

Route::middleware('auth:api')->group(function () {
    Route::post('games/{game}/move', 'API\GameController@move');
    Route::apiResources([
        'games' => 'API\GameController',
    ]);
});

Route::get('mongo', function(Request $request) {
    // Manager Class
    $manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");

    // Query Class
    $query = new MongoDB\Driver\Query(array('age' => 30));

    // Output of the executeQuery will be object of MongoDB\Driver\Cursor class
    $cursor = $manager->executeQuery('cz.game', $query);

    // Convert cursor to Array and print result
    print_r($cursor->toArray());
});

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});
