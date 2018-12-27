<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return 'Hello world from lumen API';
});

$router->get('/version', function () use ($router) {
    return $router->app->version();
});

// $router->group(['prefix' => 'page'], function () use ($router) {
//     $router->get('index', function ()    {
//         echo 'index';
//         die;
//     });

//     $router->get('about', function ()    {
//         echo 'about';
//         die;
//     });
// });

// Route::options( '/{any:.*}', [ 'middleware' => ['CorsMiddleware'], function (){ return response(['status' => 'success']); } ] );

// $router->any('members/{action}', 'MemberController@all')->where('action', '(.*)');
$router->post('createuser', 'UserController@create');
$router->post('createuser', 'UserController@create');
$router->post('createquestion', 'QuestionController@create');
$router->post('submitanswer', 'AnswerController@submit');
$router->post('userlogin', 'UserController@login');
$router->post('createcategory', 'CategoryController@create');

$router->get('categories', 'CategoryController@get');
$router->get('getmyquestions/{user_id}', 'UserController@getQuestions');
$router->get('questions', 'QuestionController@getAll');
$router->get('questions/{category_id}', 'QuestionController@getByCategory');

// ----------------------------------
// other routes for sample database
// localhost/lumina/api/v1/product
$router->group(['prefix'=>'api/v1/'], function() use($router){
    $router->post('product', 'ProductController@create');
    $router->get('product', 'ProductController@index');
    $router->get('product/{id}', 'ProductController@show');
    $router->put('product/{id}', 'ProductController@update');
    $router->delete('product/{id}', 'ProductController@destroy');
});