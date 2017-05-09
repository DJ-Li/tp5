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

class Authority extends Model
{
    protected $name = 'role_admin';

    protected function initialize()
    {
        parent::initialize();
    }

    /**
     * 权限角色
     */
    public function check_role($uid)
    {
        $role_admin = $this->field('role_id')->where(['admin_id'=>$uid])->find();
        if ($role_admin['role_id'] != 1){
            return false;
        }
        return true;
    }
    /**
     * 用于操作权限列表
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
}