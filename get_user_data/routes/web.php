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
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use Ixudra\Curl\Facades\Curl;
use Laravel\Lumen\Http\Redirector;

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(
    ['middleware' => 'jwt.auth'], 
    function() use ($router) {
        $router->get('users', function(Request $request) {
            $token = $request->get('token');
            $decryptToken = JWT::decode($token, env('JWT_SECRET'), ['HS256']);
            $username = $decryptToken->username;
            $gloid = $decryptToken->glo_id;
            $status = true;
            $userdata = \App\Authentication::where('username', $username)
                        ->get();
            foreach (json_decode(json_encode($userdata),true) as $users)
			{
 				// return $users; // this is user data from database
                print_r($users).'<br>';
			}

            // $sendToken = Curl::to('')
            //             ->withHeader('token:', $token)
            //             ->get();
        });
    }
);


