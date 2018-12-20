<?php
//docker run --name auth_docker -d -p 8080:80 -v C:/xampp/htdocs/laravel/auth_docker:/var/www/html nimmis/apache-php7
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
$status = false;

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->post(
    'auth/login', 
    [
       'uses' => 'AuthController@authenticate'
    ]
);

$router->group(
    ['middleware' => 'jwt.auth'], 
    function() use ($router) {
        $router->get('users', function(Request $request) {
            $token = $request->get('token');
            $decryptToken = JWT::decode($token, env('JWT_SECRET'), ['HS256']);
            $username = $decryptToken->username;
            $gloid = $decryptToken->glo_id;
            $status = true;
            // echo $username.'<br>';
            // $userdata = \App\User::all();
            $userdata = \App\User::where('username', $username)
                        ->get();
            foreach (json_decode(json_encode($userdata),true) as $users)
			{
 				print_r($users).'<br>'; // this is user data from database
			}

            // $sendToken = Curl::to('http://localhost:8080/users')
            //             ->withHeader('token:', $token)
            //             ->get();
        });
    }
);

$router->get('/check-connection', function () use ($router) {
    try {
        DB::connection()->getPdo();
        if(DB::connection()->getDatabaseName()){
            echo "Yes! Successfully connected to the DB: " . DB::connection()->getDatabaseName();
        }else{
            die("Could not find the database. Please check your configuration.");
        }
    } catch (\Exception $e) {
        die("Could not open connection to database server.  Please check your configuration.");
    }
});

$router->get('/account/update', function () use ($router) {
    if ($status = false) {
        return redirect()->route('auth/login');
    } else {
        // $sendToken = Curl::to('')
        //             ->withHeader('token:', $token)
        //             ->get();
    }
    
});



