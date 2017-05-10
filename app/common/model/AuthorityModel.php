<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/26
 * Time: 20:58
 */

namespace app\common\model;


use think\Db;
use think\Model;

class AuthorityModel extends Model
{
    protected $name = 'role_admin';
    protected $role_authority_db;

    protected function initialize()
    {
        parent::initialize();
        $this->role_authority_db = Db::name('role_authority');
    }

    /**
     * 权限角色
     * @param $uid string
     * @return bool
     */
    public function check_role($uid)
    {
        $role_admin = $this->field('role_id')->where(['admin_id' => $uid])->find();
        if ($role_admin['role_id'] != 1) {
            return false;
        }
        return true;
    }

    /**
     * 获取角色ID
     * @param $uid string
     * @return string
     */
    public function by_uid_get_role_id($uid)
    {
        $role_admin = $this->field('role_id')->where(['admin_id' => $uid])->find();
        return $role_admin['role_id'];
    }

    /**
     * 用于操作权限列表
     * @param $uid string
     * @return array
     */
    public function operation_authority($uid)
    {
        $list = $this->alias('a')
            ->field('c.rule_path')
            ->join('__ROLE__ b', 'b.id = a.role_id')
            ->join('__ROLE_AUTHORITY__ c', 'c.role_id = b.id')
            ->where('a.admin_id', $uid)
            ->select();
        $data = [];
        foreach ($list as $key => $val) {
            $data[] = $val['rule_path'];
        }
        return $data;
    }

    /**
     * 用于访问权限列表
     * @param $uid string
     * @return array
     */
    public function browse_authority($uid)
    {
        $list = $this->alias('a')
            ->field('c.menu_id')
            ->join('__ROLE__ b', 'b.id = a.role_id')
            ->join('__ROLE_AUTHORITY__ c', 'c.role_id = b.id')
            ->where('a.admin_id', $uid)
            ->select();
        $data = [];
        foreach ($list as $key => $val) {
            $data[] = $val['menu_id'];
        }
        return $data;
    }

    /**
     * 用于访问权限列表
     * @param $role_id string
     * @return array
     */
    public function by_role_id($role_id)
    {
        $list = $this->role_authority_db->field('menu_id')->where('role_id', $role_id)->select();
        $data = [];
        foreach ($list as $key => $val) {
            $data[] = $val['menu_id'];
        }
        return $data;
    }

    /**
     * 添加角色权限
     * @param $data array
     * @param $role_id string
     * @return bool
     */
    public function add_role_authority($data, $role_id)
    {
        $count = $this->role_authority_db->where(['role_id' => $role_id])->count();
        if ($count > 0) {
            // 启动事务
            $this->role_authority_db->startTrans();
            try {
                $this->role_authority_db->where('role_id', $role_id)->delete();
                $this->role_authority_db->insertAll($data);
                // 提交事务
                $this->role_authority_db->commit();
                return true;
            } catch (\Exception $e) {
                // 回滚事务
                $this->role_authority_db->rollback();
                return false;
            }
        } else {
            $add = $this->role_authority_db->insertAll($data);
            if ($add) {
                return true;
            }
        }
        return false;
    }

    /**
     * 删除角色权限
     * @param $role_id string
     * @return bool
     */
    public function del_role_authority($role_id)
    {
        $count = $this->role_authority_db->where(['role_id' => $role_id])->count();
        //如果该角色权限就删除他的所有权限，没有就直接返回true;
        if ($count > 0) {
            $del = $this->role_authority_db->where('role_id', $role_id)->delete();
            if ($del) {
                return true;
            }
            return false;
        }
        return true;
    }
}