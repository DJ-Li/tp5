<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/26
 * Time: 13:14
 */

namespace app\common\controller;


use app\common\model\Authority;
use app\common\Tools\AjaxCode;
use think\Config;
use think\Controller;
use think\Request;

class AdminBase extends Controller
{
    protected $authority;      //权限model
    protected $uid;            //登录用户ID
    protected $sys_theme;      //

    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function _initialize()
    {
        parent::_initialize();
        //判断有没有权限
        $this->sys_theme = Config::get('template.default_theme');
        $this->check_login();
        $this->authority = new Authority();
        $this->check_authority();
    }

    protected function fetch($template = '', $vars = [], $replace = [], $config = [])
    {
        $replace = [
            '__ADMIN_PUBLIC__' => WEB_URL . 'public/admin/' . $this->sys_theme,
            '__WEB_URL__'      => WEB_URL,
        ];
        if ($this->request->module() != 'admin') {
            $template_prefix = "admin@" . $this->sys_theme . '/' . $this->request->module() . '/';
        } else {
            $template_prefix = $this->sys_theme . '/';
        }
        if (empty($template)) {
            $template = strtolower($this->request->controller() . '/' . $this->request->action());
        }
        $template = $template_prefix . $template;
        if (!($this->request->module() == 'admin' && $this->request->controller() == 'Index' && ($this->request->action() == 'index' || $this->request->action() == 'welcome'))) {
            $this->view->engine->layout('admin/' . $template_prefix . 'layout.html');
        }
        return parent::fetch($template, $vars, $replace, $config);
    }

    /**
     * 检查用户登录
     */
    protected function check_login()
    {
        if (!($this->request->module() == 'admin' && $this->request->controller() == 'User' && ($this->request->action() == 'login' || $this->request->action() == 'register'))) {
            if (empty($uid)) {
               // if($this->request->isAjax()){
                //    redirect(url('user/login'));
               // }else{
                    return $this->redirect(url('user/login'));
                //}
            }
        }

    }

    /**
     * 判断是否权限
     */
    public function check_authority()
    {
        $authority_data = $this->authority->operation_authority($this->uid);
        $flag = false;
        foreach ($authority_data as $index => $item) {
            if ($item == $this->request->module() . '/' . $this->request->controller() . '/' . $this->request->action()) {
                $flag = true;
                break;
            }
        }
        if (!(($this->request->controller() == 'Index' || $this->request->controller() == 'User') && ($this->request->action() == 'index' || $this->request->action() == 'welcome' || $this->request->action() == 'login' || $this->request->action() == 'login_post' || $this->request->action() == 'register' || $this->request->action() == 'register_post'))) {
            if (!$flag) {
                if ($this->request->isAjax()) {
                    return json(['status' => AjaxCode::NO_AUTHORITY, 'msg' => '没有权限!']);
                } else {
                    $this->_empty('authority');
                }
            }
        }
        return $flag;
    }



    /**
     * 检查访问错误
     */
    public function _empty($name)
    {
        echo $this->fetch($name);
        exit;
    }

}