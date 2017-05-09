<?php
/**
 * Created by PhpStorm.
 * User: Adminstrator
 * Date: 2017/4/15
 * Time: 18:14
 */

namespace app\admin\controller;


use app\common\controller\AdminBase;
use app\common\Tools\AjaxCode;

class User extends AdminBase
{
    public function _initialize()
    {
        parent::_initialize(); // TODO: Change the autogenerated stub
    }

    public function login()
    {
        return $this->fetch('/login');
    }
    public function login_post()
    {
        $post = $this->request->post();
        if(!captcha_check($post['verify']) || empty($post['verify'])){
            $json['status'] = AjaxCode::PARAM_VALID;
            $json['msg'] = '验证码不正确！';
            return json($json);
        }

    }
    public function check_verify()
    {
        $verify = $this->request->post('verify');
        if (!captcha_check($verify) || $verify == '') {
            $json['status'] = AjaxCode::PARAM_VALID;
            $json['msg'] = '验证码不正确！';
            return json($json);
        } else {
            $json['status'] = AjaxCode::SUCCESS;
            $json['msg'] = '验证码正确！';
            return json($json);
        }
    }
}