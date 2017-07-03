<?php
/**
 * Created by tp5.
 * User: 吴勉之
 * Date: 2017/3/7
 * Time: 11:18
 * Description:
 */

namespace app\common\Tools;


class AjaxCode
{
    const SUCCESS        = 200;  //处理成功
    const FAIL           = 400;  //处理失败
    const PARAM_ERROR    = 100;  //参数错误
    const PARAM_EMPTY    = 101;  //参数为空
    const SYSTEM_ERROR   = 300;  //系统错误
    const DATA_EXIST     = 403;  //数据存在或已处理
    const DATA_NO_EXIST  = 404;  //数据不存在
    const NO_AUTHORITY   = -999; //没有权限
}