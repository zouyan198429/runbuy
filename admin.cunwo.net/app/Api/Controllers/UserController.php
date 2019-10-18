<?php
namespace App\Api\Controllers;
/**
 * https://juejin.im/post/5b544f19f265da0fac1e149d
 *
 *   $curl = curl_init();
 *
 *   curl_setopt_array($curl, array(
 *  CURLOPT_URL => "http://api.c.com/user/register",
 *  CURLOPT_RETURNTRANSFER => true,
 *  CURLOPT_ENCODING => "",
 *  CURLOPT_MAXREDIRS => 10,
 *  CURLOPT_TIMEOUT => 30,
 *  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
 *  CURLOPT_CUSTOMREQUEST => "POST",
 *   CURLOPT_POSTFIELDS => "-----011000010111000001101001\r\nContent-Disposition: form-data; name=\"tel\"\r\n\r\n18510362698\r\n-----011000010111000001101001\r\nContent-Disposition: form-data; name=\"password\"\r\n\r\nzjk1221\r\n-----011000010111000001101001--",
 *  CURLOPT_HTTPHEADER => array(
 *  "accept: application/vnd.catering.v1+json",
 *   "cache-control: no-cache",
 *  "content-type: multipart/form-data; boundary=---011000010111000001101001",
 *  "postman-token: e7cf665f-3698-217a-cd71-35c3a44f42bc"
 *  ),
 *  ));
 *
 *   $response = curl_exec($curl);
 *  $err = curl_error($curl);
 *
 *  curl_close($curl);
 *
 *  if ($err) {
 *  echo "cURL Error #:" . $err;
 *  } else {
 *  echo $response;
 *  }
 *
 *
 */

// 控制器
use App\Api\DingoController;
use App\Api\Response;
use App\Api\Services\UserService;
use Illuminate\Http\Request;

class UserController extends DingoController
{
    public $request;

    protected $userService;

    public function __construct(Request $request, UserService $userService)
    {
        $this->request = $request;

        $this->userService = $userService;
    }

    public function register()
    {
        $result = $this->userService->register ($this->request->all ());

        if ($result['status_code'] == 200) {
            return $this->response->array (Response::return (200, '注册成功', [
                'user_id' => $result['data'],
            ]));
        }

        return $this->response->error ($result['message'], 500);
    }
}