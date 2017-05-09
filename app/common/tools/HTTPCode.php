<?php
/**
 * Created by tp5.
 * User: 吴勉之
 * Date: 2017/3/7
 * Time: 11:30
 * Description:
 */

namespace app\common\Tools;


class HTTPCode
{
    const  SUCCESS               = 200; // 成功
    const  BAD_REQUEST           = 400; // 服务器不理解请求的语法。
    const  UNAUTHORIZED          = 401; // 请求要求身份验证。 对于需要登录的网页，服务器可能返回此响应。
    const  FORBIDDEN             = 403; // 服务器拒绝请求。//
    const  NOT_FOUND             = 404; // 服务器找不到请求的网页。
    const  NOT_AUTHORIZED        = 777; // 没有权限。
    const  INTERNAL_SERVER_ERROR = 500; // 内部服务器错误
    const  SERVICE_UNAVAILABLE   = 503; // 服务不可用
}