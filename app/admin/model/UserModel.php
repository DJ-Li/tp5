<?php
/**
 * Created by PhpStorm.
 * User: Adminstrator
 * Date: 2017/4/15
 * Time: 23:28
 */

namespace app\admin\model;


use think\Model;
use think\Request;

class UserModel extends Model
{
    protected $name = 'admin';

    public function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub
    }

    /**
     * 获取管理列表
     */
    public function get_admin_list()
    {

    }

    /**
     * 根据ID获取管理员信息
     */
    public function by_id_get_admin($id)
    {
        if (empty($id) || !check_id($id)) {
            return false;
        }
        $admin = $this->where(['id' => $id])->find();
        if (!$admin) {
            return false;
        }
        return $admin->toArray();
    }

    /**
     * 检查管理员登录
     */
    public function login($name, $pass)
    {
        if (empty($name)) {
            return false;
        }
        $admin = $this->field('admin_name,admin_pass')->where(['admin_name' => $name])->find();
        if ($admin['admin_pass'] != md5(md5($pass))) {
            return false;
        }
        //  更改登录IP、时间
        $this->save([
            'login_num' => $admin['login_num'] + 1,
            'last_login_ip' => Request::instance()->ip(),
            'last_login_tiem' => time(),
        ], ['admin_name' => $name]);
        //  将用户ID及电话、名称组装数组用于保存 session
        $admin_info = [
            'uid' => $admin['id'],
            'name' => $admin['admin_name'],
            'nickname' => $admin['admin_nickname'] ?: $admin['admin_nickname'],
        ];
        //  保存session
        Session::set('sys_admin', $admin_info);
        return true;
    }

    /**
     * 检查是否登录昵称是否已注册过
     */
    public function check_name($name)
    {
        if (empty($name)) {
            return false;
        }
        $admin_name = $this->field('admin_name')->where(['admin_name' => $name])->find();
        if ($admin_name) {
            return false;
        }
        return true;
    }
}