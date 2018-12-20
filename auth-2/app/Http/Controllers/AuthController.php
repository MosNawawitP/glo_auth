<?php
namespace App\Http\Controllers;
use Validator;
use Illuminate\Http\RedirectResponse;
use App\Authentication;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Firebase\JWT\ExpiredException;
use Illuminate\Support\Facades\Hash;
use Ixudra\Curl\Facades\Curl;
use Stevebauman\Location\Position;
use Laravel\Lumen\Routing\Controller as BaseController;
class AuthController extends BaseController 
{
    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    private $request;
    /**
     * Create a new controller instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct(Request $request) {
        $this->request = $request;
    }
    /**
     * Create a new token.
     * 
     * @param  \App\User   $user
     * @return string
     */
    protected function jwt(Authentication $user) {
        $payload = [
            'iss' => "glo-jwt-token", // Issuer of the token
            'glo_id' => $user->glo_id, // Subject of the token
            'username' => $user->username, // User's username
            'iat' => time(), // Time when JWT was issued. 
            'exp' => time() + 60*60 // Expiration time
        ];
        
        // As you can see we are passing `JWT_SECRET` as the second parameter that will 
        // be used to decode the token in the future.
        return JWT::encode($payload, env('JWT_SECRET'));
    } 
    /**
     * Authenticate a user and return the token if the provided credentials are correct.
     * 
     * @param  \App\User   $user 
     * @return mixed
     */
    public function authenticate(Authentication $user, Request $request) {
        $this->validate($this->request, [
            'username'    => 'required',
            'password'  => 'required',
        ]);
        // Find the user by username
        $user = Authentication::where('username', $this->request->input('username'))->first();
        $username = $this->request->get('username');
        $decodeData = json_decode($user);

        if (!$user) {
            // You wil probably have some sort of helpers or whatever
            // to make sure that you have the same response format for
            // differents kind of responses. But let's return the 
            // below respose for now.
            return response()->json([
                'error' => 'username does not exist.'
            ], 400);
        }
        // Verify the password and generate the token
        if (Hash::check($this->request->input('password'), $user->password)) {
            $token = $this->jwt($user);
            // $this->updateTimeAndToken($username, $token);

            //update last_login and token
            date_default_timezone_set("Asia/Bangkok");
            $nowtime = date("Y-m-d H:i:s");
            $ipaddress = $this->getIp();
            // $ipaddress = '128.0.0.0';
            Authentication::where('username', $username)->update(array(
                'last_login'    =>  $nowtime,
                'token' =>  $token,
                'updated_at' =>  $nowtime,
                'ip_address' => $ipaddress,
            ));
            // For a route with the following URI: users/{token}
            // return redirect()->route('users', ['token' => $token]);

            return Curl::to('http://localhost:8080/users')
                        // ->withHeader('token', $token)
                        ->withData( array( 'token' => $token ) )
                        ->get();

            // return response()
            //     ->json(['token' => $this->jwt($user), 'username' => $username], 200);
        }
        // Bad Request response
        return response()->json([
            'error' => 'username or password is wrong.'
        ], 400);
    }

    public function getIp(){
        foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key){
            if (array_key_exists($key, $_SERVER) === true){
                foreach (explode(',', $_SERVER[$key]) as $ip){
                    $ip = trim($ip); // just to be safe
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false){
                        return $ip;
                    }
                }
            }
        }
    }

    // public function updateTimeAndToken($inputusername, $inputToken) {
    //     date_default_timezone_set("Asia/Bangkok");
    //     $token = $inputToken;
    //     $username = $inputusername;
    //     // $nowtime = strtotime("now");
    //     $nowtime = date("Y-m-d H:i:s");

    //     Authentication::where('username', $username)->update(array(
    //         'last_login'    =>  $nowtime,
    //         'token' =>  $token,
    //         'updated_at' =>  $nowtime,
    //     ));
    // }   
}