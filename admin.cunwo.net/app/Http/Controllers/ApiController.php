<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterAuthRequest;
// use App\User;
use App\Admin as User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use JWTAuth;
use JWTFactory;use Tymon\JWTAuth\Exceptions\JWTException;

class ApiController extends Controller
{

    public $loginAfterSignUp = true;

    public function register(RegisterAuthRequest $request)
    {
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();

        if ($this->loginAfterSignUp) {
            return $this->login($request);
        }

        return response()->json([
            'success' => true,
            'data' => $user
        ], 200);
    }

    /**
     * {
     *      "success": true,
     *       "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9ydW5idXkuYWRtaW4uY3Vud28ubmV0XC9hcGlcL3JlZ2lzdGVyIiwiaWF0IjoxNTcxMTk3Njc4LCJleHAiOjE1NzEyMDEyNzgsIm5iZiI6MTU3MTE5NzY3OCwianRpIjoiMk1vUGRGNkcyRGlIZTJlaiIsInN1YiI6MSwicHJ2IjoiODdlMGFmMWVmOWZkMTU4MTJmZGVjOTcxNTNhMTRlMGIwNDc1NDZhYSJ9._XTTUbeDvIqguL82O62L_TBrOFng7VYdJyqERO2UtxM"
     *  }
     *
     */
    public function login(Request $request)
    {
        $input = $request->only('email', 'password');
        $jwt_token = null;

        $tokenData = $input;

        //if (!$jwt_token = JWTAuth::attempt($input)) {
        if (!$jwt_token = JWTAuth::attempt($tokenData)) {
        // if (!$jwt_token = JWTAuth::fromSubject($tokenData)) {

            return response()->json([
                'success' => false,
                'message' => 'Invalid Email or Password',
            ], 401);
        }

        return response()->json([
            'success' => true,
            'token' => $jwt_token,
        ]);
    }

    public function logout(Request $request)
    {
        $this->validate($request, [
            'token' => 'required'
        ]);

        try {
            JWTAuth::invalidate($request->token);

            return response()->json([
                'success' => true,
                'message' => 'User logged out successfully'
            ]);
        } catch (JWTException $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, the user cannot be logged out'
            ], 500);
        }
    }

    // 刷新
    //    // 如果你不用 Facade，你可以这么写
    //    auth('api')->refresh();
    //    // 用 JWTAuth Facade
    //    JWTAuth::parseToken()->refresh();

    public function getAuthUser(Request $request)
    {

        $this->validate($request, [
            'token' => 'required'
        ]);

        // 获得token中的用户数据
//        $tokenData = JWTAuth::getPayload()->get('sub');//->get('userData');
//        pr($tokenData);
        $user = JWTAuth::authenticate($request->token);

        return response()->json(['user' => $user]);
    }

    public function testaa(Request $request){
//        $customClaims = ['foo' => 'bar', 'baz' => 'bob'];
//        $payload = JWTFactory::make($customClaims);
//        $token = JWTAuth::encode($payload);
//        pr($token);


        //token生成
        $customClaims = [
            'sub' => [
                'foo' => 'bar'
            ],
            'testaa' => 'dfsfsdfsdfd',
        ];
        $payload = JWTFactory::customClaims($customClaims)->make();
        $token = JWTAuth::encode($payload);
        pr($token);

//        $payload = JWTFactory::customClaims($customClaims)->make();
//        pr($payload);
//        $token = Auth::guard('api')->attempt($payload);
//        pr($token);

        // 当然如果需要的话你还可以手动设置token：
        // JWTAuth::setToken('foo.bar.baz');


//        $customClaims = ['sub'=> 'aaabbb', 'foo' => 'bar', 'baz' => 'bob'];
//        $payload = JWTFactory::make($customClaims);
//        $token = JWTAuth::encode($payload);
//        pr($token);
//        // Facade - 3
//        $payload = JWTFactory::sub(123)->aud('foo')->foo(['bar' => 'baz'])->make();
//        $token = JWTAuth::encode($payload);
//        pr($token);
    }

    public function testbb(Request $request){

        // 获取 token
        // 如果 token 被设置则会返回，否则会尝试使用方法从请求中解析 token ，如果 token 未被设置或不能解析最终返回 false。
        // header中的 Authorization Bearer {jwt} 或 post/get 参数 token  都能获取到
        // 辅助函数
        // $token = auth('api')->getToken();
        // Facade
        // $token = JWTAuth::parseToken()->getToken();// 从 request 中解析 token 到对象中，以便进行下一步操作。
        //  $token = JWTAuth::getToken();// // 这个不用 parseToken ，因为方法内部会自动执行一次
        // pr($token);
        // 更新 token。
        // $newToken = JWTAuth::parseToken()->refresh()

        // 让一个 token 无效。
       // JWTAuth::parseToken()->invalidate(JWTAuth::getToken());
       //  auth('api')->invalidate(auth('api')->getToken());
       // refresh
        // 更新 token。
        // $newToken = JWTAuth::parseToken()->refresh();
//        $newToken = auth('api')->refresh();
//        echo $newToken;
//        echo '<br/><br/>';

        // 检验 token 的有效性。
        if(JWTAuth::parseToken()->check()) {
        // if(Auth::guard('api')->check()) {// 不能用-只能检验: $credentials = request(['email', 'password']); auth('api')->attempt($credentials)
            dd("token是有效的");
        }else{
            dd("token是无效的");
        }


        // 获得 payload 数据
        // $payload = auth('api')->payload();
//        $payload = JWTAuth::parseToken()->payload();
//        pr($payload);
        // 获得 payload 数据
        // $sub = auth('api')->payload()->get('sub');
        //$sub = JWTAuth::parseToken()->payload()->get('sub');


        // $json = auth('api')->payload()->toJson();
//        $array = JWTAuth::parseToken()->payload()->jsonSerialize();
//        //  pr($array);
//        $sub = $array['sub'];
//        pr($sub);

        //获取载荷中指定的一个元素。
        // $sub = JWTAuth::parseToken()->getClaim('sub');
        //  pr($sub);

    }
}
