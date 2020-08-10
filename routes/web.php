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

//Auth
$router->post("/user-login", "AuthController@userLogin");
$router->post("/user-create", "AuthController@userCreate");
$router->post("/user-change-password", ['middleware' => 'if_userlogin', 'uses' => "AuthController@userChangePassword"]);
$router->post("/member-login", "AuthController@memberLogin");
$router->post("/member-create", "AuthController@memberCreate");
$router->post("/member-change-password", ['middleware' => 'if_userlogin', 'uses' => "AuthController@memberChangePassword"]);

//Users
$router->post("/users", ['middleware' => 'if_userlogin', 'uses' => "UserController@users"]);
$router->post("/user/show/{id}", ['middleware' => 'if_userlogin', 'uses' => "UserController@userShow"]);
$router->post("/user/create", ['middleware' => 'if_userlogin', 'uses' => "UserController@userCreate"]);
$router->post("/user/update/{id}", ['middleware' => 'if_userlogin', 'uses' => "UserController@userUpdate"]);
$router->post("/user/delete/{id}", ['middleware' => 'if_userlogin', 'uses' => "UserController@userDelete"]);

//Members
$router->post("/members", ['middleware' => 'if_userlogin', 'uses' => "MemberController@members"]);
$router->post("/member/show/{id}", ['middleware' => 'if_userlogin', 'uses' => "MemberController@memberShow"]);
$router->post("/member/create", ['middleware' => 'if_userlogin', 'uses' => "MemberController@memberCreate"]);
$router->post("/member/update/{id}", ['middleware' => 'if_userlogin', 'uses' => "MemberController@memberUpdate"]);
$router->post("/member/delete/{id}", ['middleware' => 'if_userlogin', 'uses' => "MemberController@memberDelete"]);

//Games
$router->post("/categories", ['middleware' => 'if_userlogin', 'uses' => "GameController@activeCategories"]);
$router->post("/games", ['middleware' => 'if_userlogin', 'uses' => "GameController@games"]);
$router->post("/game/show/{id}", ['middleware' => 'if_userlogin', 'uses' => "GameController@gameShow"]);
$router->post("/game/create", ['middleware' => 'if_userlogin', 'uses' => "GameController@gameCreate"]);
$router->post("/game/update/{id}", ['middleware' => 'if_userlogin', 'uses' => "GameController@gameUpdate"]);
$router->post("/game/delete/{id}", ['middleware' => 'if_userlogin', 'uses' => "GameController@gameDelete"]);

//Payment Instruments
$router->post("/payment-instruments", ['middleware' => 'if_userlogin', 'uses' => "PaymentInstrumentController@paymentInstruments"]);
$router->post("/payment-instrument/show/{id}", ['middleware' => 'if_userlogin', 'uses' => "PaymentInstrumentController@paymentInstrumentShow"]);
$router->post("/payment-instruments/showby-gamecode/{gamecode}/showby-payment-instrument/{payment_instrument}", ['middleware' => 'if_userlogin', 'uses' => "PaymentInstrumentController@paymentInstrumentShowByGameCodeAndPaymentInstrument"]);
$router->post("/payment-instrument/using/{amount}/{payment_instrument}/to/{gamecode}", ['middleware' => 'if_userlogin', 'uses' => "PaymentInstrumentController@paymentInstrumentUse"]);
$router->post("/payment-instrument/add/{amount}/{payment_instrument}/to/{gamecode}", ['middleware' => 'if_userlogin', 'uses' => "PaymentInstrumentController@paymentInstrumentAdd"]);
$router->post("/payment-instrument/create", ['middleware' => 'if_userlogin', 'uses' => "PaymentInstrumentController@paymentInstrumentCreate"]);
$router->post("/payment-instrument/update/{id}", ['middleware' => 'if_userlogin', 'uses' => "PaymentInstrumentController@paymentInstrumentUpdate"]);
$router->post("/payment-instrument/delete/{id}", ['middleware' => 'if_userlogin', 'uses' => "PaymentInstrumentController@paymentInstrumentDelete"]);
