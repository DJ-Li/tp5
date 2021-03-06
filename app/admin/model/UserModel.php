<?php
/**
 * Created by PhpStorm.
 * User: Adminstrator
 * Date: 2017/4/15
 * Time: 23:28
 */

namespace app\admin\model;

use think\Db;
use think\Model;
use think\Request;
use think\Session;

class UserModel extends Model
{
    protected $name = 'user';

    public function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub
    }

    /**
     * 登录
     * @param $account string 账号或手机邮箱;
     * @param $pass string 密码
     * @return boolean
     */
    public function check_login($account, $pass)
    {
        if (check_tel($account)) {
            $info = $this->field('id,user_login,user_pass,login_times,user_avatar')->where(['user_mobile' => $account, 'user_type' => 1])->find();
            if ($info['user_pass'] == my_md5($pass, 2)) {
                $this->save([
                    'login_times' => $info['login_times'] + 1,
                    'last_login_ip' => Request::instance()->ip(),
                    'last_login_time' => times_format(time()),
                ], ['user_mobile' => $account]);
                //  将用户ID及电话、名称组装数组用于保存 session
                $user_info = [
                    'uid' => $info['id'],
                    'name' => $info['user_login'],
                    'head_pic' => $info['user_avatar'],
                ];
                Session::set('sys_admin', $user_info);
                return true;
            }
        } elseif (check_email($account)) {
            $info = $this->field('id,user_login,user_pass,login_times,user_avatar')->where(['user_email' => $account, 'user_type' => 1])->find();
            if ($info['user_pass'] == my_md5($pass, 2)) {
                $this->save([
                    'login_times' => $info['login_times'] + 1,
                    'last_login_ip' => Request::instance()->ip(),
                    'last_login_time' => times_format(time()),
                ], ['user_email' => $account]);
                //  将用户ID及电话、名称组装数组用于保存 session
                $user_info = [
                    'uid' => $info['id'],
                    'name' => $info['user_login'],
                    'head_pic' => $info['user_avatar'],
                ];
                Session::set('sys_admin', $user_info);
                return true;
            }
        } else {
            $info = $this->field('id,user_login,user_pass,login_times,user_avatar')->where(['user_login' => $account, 'user_type' => 1])->find();
            if ($info['user_pass'] == my_md5($pass, 2)) {
                $this->save([
                    'login_times' => $info['login_times'] + 1,
                    'last_login_ip' => Request::instance()->ip(),
                    'last_login_time' => times_format(time()),
                ], ['user_login' => $account]);
                //  将用户ID及电话、名称组装数组用于保存 session
                $user_info = [
                    'uid' => $info['id'],
                    'name' => $info['user_login'],
                    'head_pic' => $info['user_avatar'],
                ];
                Session::set('sys_admin', $user_info);
                return true;
            }
        }
        return false;
    }

    /**
     * 根据用户ID
     * @param $id string
     * @return array
     */
    public function by_id_admin($id)
    {
        $list = $this->where(['id' => $id])->find();
        return $list ?: [];
    }

    /**
     * 检查密码是否正确
     * @param $name string
     * @param $pass string
     * @return boolean
     */
    public function check_pass($name, $pass)
    {

    }

    /**
     * 检查登录名称是否存在
     * @param $name string
     * @return boolean
     */
    public function check_name($name)
    {
        $count = $this->where(['user_login' => $name, 'user_type' => 1])->count();
        if ($count > 0) {
            return false;
        }
        return true;
    }

    /**
     * 检查电话否存在
     * @param $mobile string
     * @return boolean
     */
    public function check_mobile($mobile)
    {
        $count = $this->where(['user_mobile' => $mobile])->count();
        if ($count > 0) {
            return false;
        }
        return true;
    }

    /**
     * 检查电子邮件否存在
     * @param $email string
     * @return boolean
     */
    public function check_email($email)
    {
        $count = $this->where(['user_email' => $email])->count();
        if ($count > 0) {
            return false;
        }
        return true;
    }

    /**
     * 创建角色管理员
     * @param $data array
     * @param $role_id string
     * @return string
     */
    public function create_user($data = [], $role_id)
    {
        if (empty($data)) { // 如果数据为空就重新查询一遍
            $data = $this->field('id,user_login')->where(['status' => ['egt', -1], 'user_type' => 1])->select();
        }
        //获取角色管理员表中的管理员ID
        $role_admin_ids = Db::name('role_admin')->field('admin_id')->where(['role_id' => $role_id])->select();
        //遍历为数组
        $role_admin = [];
        foreach ($role_admin_ids as $key => $val) {
            $role_admin[] = $val['admin_id'];
        }
        $tmp = "";
        foreach ($data as $index => $item) {
            if (!empty($role_admin) && in_array($item['id'], $role_admin)) { //如果该成员存在就点亮
                $tmp .= "<li><input type='checkbox' checked name='user[{$item['id']}]' value='{$item['id']}' title='{$item['user_login']}' lay-filter='role'/></li>";
            } else {
                $tmp .= "<li><input type='checkbox'  name='user[{$item['id']}]' value='{$item['id']}' title='{$item['user_login']}' lay-filter='role'/></li>";
            }
        }
        return $tmp;
    }
}