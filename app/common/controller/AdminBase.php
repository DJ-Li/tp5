<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/26
 * Time: 13:14
 */

namespace app\common\controller;


use app\common\model\AuthorityModel;
use app\common\Tools\AjaxCode;
use think\Config;
use think\Controller;
use think\Request;

class AdminBase extends Controller
{
    protected $authority;      //权限model
    protected $admin;            //登录用户信息
    protected $uid;            //登录用户ID
    protected $sys_theme;      //
    private $allow_visit_act = [
        'Index' => [
            'index', 'welcome',
        ],
        'Admin' => [
            'login', 'register', 'login_post', 'register_post'
        ],
    ];

    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function _initialize()
    {
        parent::_initialize();
        $this->sys_theme = Config::get('template.default_theme');
        $sys_admin = $this->request->session('sys_admin/a');
        $sys_admin['uid'] = 1;
        if (!empty($sys_admin)) {
            $this->admin = $sys_admin;
            $this->uid = $sys_admin['uid'];
        }
        $this->uid = 1;
        $this->check_login();
        //判断有没有权限
        $this->authority = new AuthorityModel();
        if (!$this->authority->check_role($this->uid)) {
            $this->check_authority();
        }
        $this->assign('admin', $this->admin);
    }

    protected function fetch($template = '', $vars = [], $replace = [], $config = [])
    {
        $replace = [
            '__ADMIN_PUBLIC__' => WEB_URL . 'public/admin/' . $this->sys_theme,
            '__WEB_URL__' => WEB_URL,
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
        if (empty($this->uid) && !($this->request->module() == 'admin' && in_array($this->request->action(), $this->allow_visit_act['Admin']))) {
            if ($this->request->isAjax()) {
                return $this->redirect(url('admin/login'));
            } else {
                $this->redirect(url('admin/login'));
            }
        }
    }


    /**
     * 判断是否权限
     */
    protected function check_authority()
    {
        $authority_data = $this->authority->operation_authority($this->uid);
        $flag = false;
        foreach ($authority_data as $index => $item) {
            if ($item == $this->request->module() . '/' . $this->request->controller() . '/' . $this->request->action()) {
                $flag = true;
                break;
            }
        }
        if (!($this->request->module() == 'admin' && in_array($this->request->action(), $this->allow_visit_act[$this->request->controller()]))) {
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
     * 访问错误跳转页面
     */
    public function _empty($name)
    {
        echo $this->fetch($name);
        exit;
    }

}