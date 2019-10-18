<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\WorksController;

class UserController extends WorksController
{


    public function show($id)
    {
        // $user = User::findOrFail($id);
        // return $this->response->array($user->toArray());

        // 响应一个数组
        $user = [
            'aaa' => 1,
            'bbbb' => 'bbb',
        ];
         return $this->response->array($user);

        // 响应一个元素
        // return $this->response->item($user, new UserTransformer);

        // 响应一个元素集合

        // $users = User::all();

        // return $this->response->collection($users, new UserTransformer);

        // 分页响应
        // $users = User::paginate(25);

        // return $this->response->paginator($users, new UserTransformer);

        // 无内容响应
        // return $this->response->noContent();

        // 创建了资源的响应
        // return $this->response->created();

        // 你可以在第一个参数的位置，提供创建的资源的位置。
        // return $this->response->created($location);

        // 错误响应
        // 这有很多不同的方式创建错误响应，你可以快速的生成一个错误响应。
        // 一个自定义消息和状态码的普通错误。
        // return $this->response->error('This is an error.', 404);

        // 一个没有找到资源的错误，第一个参数可以传递自定义消息。
        // return $this->response->errorNotFound();

        // 一个 bad request 错误，第一个参数可以传递自定义消息。
        // return $this->response->errorBadRequest();

        // 一个服务器拒绝错误，第一个参数可以传递自定义消息。
        // return $this->response->errorForbidden();

        // 一个内部错误，第一个参数可以传递自定义消息。
        // return $this->response->errorInternal();

        // 一个未认证错误，第一个参数可以传递自定义消息。
        // return $this->response->errorUnauthorized();

    }

}