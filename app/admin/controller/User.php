<?php
/**
 * Created by PhpStorm.
 * User: Adminstrator
 * Date: 2017/4/15
 * Time: 18:14
 */

namespace app\admin\controller;

use app\admin\model\UserModel;
use app\common\controller\AdminBase;
use app\common\Tools\AjaxCode;
use think\Session;

class User extends AdminBase
{
    protected $user_model;

    public function _initialize()
    {
        parent::_initialize(); // TODO: Change the autogenerated stub
        $this->user_model = new UserModel();
    }

    /**
     * 管理用户
     */
    public function index()
    {
        return $this->fetch();
    }

    /**
     * 管理用户列表
     */
    public function lists()
    {
        $p = $this->request->param('p');
        if ($p < 1 || !is_numeric($p)) {
            $p = 1;
        }
        $tmp = [];
        $order = [
            'id' => 'desc',
        ];
        $size = 3;
        $list = $this->get_list('user', $tmp, $order, $p, $size);
        if (!empty($list['list'])){
            $data = [
                'status' => AjaxCode::SUCCESS,
                'msg' => '获取成功',
                'data' => ['list' => $list['list']],
                'pages' => $list['page'],
            ];
        }else{
            $data = [
                'status' => AjaxCode::FAIL,
                'msg' => '获取失败',
                'data' => ['list' => $list['list']],
                'pages' => $list['page'],
            ];
        }
        return json($data);
    }

    /**
     * 添加管理员
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $post = $this->request->post();
            $data = [];
            array_walk($post, function (&$val, $key) use (&$data) {
                $key = 'user_' . $key;
                $data[$key] = trim($val);
            });
            $data['create_ip'] = $this->request->ip();
            $data['create_time'] = times_format(time());
            if (empty($data['user_login'])) {
                return json(['status' => AjaxCode::PARAM_EMPTY, 'msg' => '请填写登录昵称']);
            }
            //检查登录昵称是否存在
            $name = $this->user_model->check_name($data['user_login']);
            if (!$name) {
                return json(['status' => AjaxCode::PARAM_EMPTY, 'msg' => '该昵称已存在请重新输入']);
            }
            if (empty($data['user_pass'])) {
                return json(['status' => AjaxCode::PARAM_EMPTY, 'msg' => '请填写6-16位以数字或字母的密码']);
            }
            $data['user_pass'] = my_md5($data['user_pass'], 2);
            if (empty($data['user_mobile']) || !check_tel($data['user_mobile'], 'sj')) {
                return json(['status' => AjaxCode::PARAM_ERROR, 'msg' => '请填写正确的手机格式！']);
            }
            if (!empty($data['user_email'])) {
                if (!check_email($data['user_mobile'])) {
                    return json(['status' => AjaxCode::PARAM_ERROR, 'msg' => '请填写正确的邮箱格式！']);
                }
            }
            $data['user_type'] = 1;
            //检查电话
            $tel = $this->user_model->check_mobile($data['user_mobile']);
            if (!$tel) { //如果存在就修改用户类型
                $save = $this->edit_post('user', $data, $data['user_mobile']);
                if (!$save) {
                    return json(['status' => AjaxCode::FAIL, 'msg' => '添加失败！']);
                }
                return json(['status' => AjaxCode::SUCCESS, 'msg' => '添加成功！']);
            } else {
                $add = $this->add_post('user', $data);
                if (!$add) {
                    return json(['status' => AjaxCode::FAIL, 'msg' => '添加失败！']);
                }
                return json(['status' => AjaxCode::SUCCESS, 'msg' => '添加成功！']);
            }
        }
        return $this->fetch();
    }

    /**
     * 管理员信息
     */
    public function user_info()
    {
        $id = request()->param('id');
        if (empty($id) || !check_id($id)) {
            return json(['status' => AjaxCode::PARAM_EMPTY, 'msg' => '参数错误!']);
        }
        $admin_info = $this->user_model->by_id_admin($id);
        $this->assign('info', $admin_info);
        return $this->fetch();
    }

    /**
     * 登录
     */
    public function login()
    {
        if ($this->request->isPost()) {
            $post = $this->request->post();
            $data = [];
            array_walk($post, function (&$val, $key) use (&$data) {
                $key = "user_" . $key;
                $data[$key] = trim($val);
            });
            if (!captcha_check($data['user_verify']) || empty($data['user_verify'])) {
                return json(['status' => AjaxCode::PARAM_ERROR, 'msg' => '验证码不正确!']);
            }
            $bool = $this->user_model->check_login($data['user_login'], $data['user_pass']);

            if (!$bool) {
                return json(['status' => AjaxCode::PARAM_ERROR, 'msg' => '用户账户或密码错误!']);
            }
            return json(['status' => AjaxCode::SUCCESS, 'msg' => '登录成功!','url' => url('index/')]);
        }
        return $this->fetch('/login');
    }

    /**
     * 退出登录
     */
    public function login_out()
    {
        Session::delete('sys_admin');
        $this->redirect(url('user/login'));
    }

    /**
     * 检查验证码
     */
    public function check_verify()
    {
        $verify = $this->request->post('verify');
        if (!captcha_check($verify) || $verify == '') {
            return json(['status' => AjaxCode::PARAM_ERROR, 'msg' => '验证码不正确!']);
        } else {
            return json(['status' => AjaxCode::SUCCESS, 'msg' => '验证码成功!']);
        }
    }

    /**
     * 登录
     */
    public function exit_login()
    {
        Session::delete('sys_admin');
        $this->redirect(url('admin/login'));
    }
}