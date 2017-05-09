<?php
/**
 * Created by PhpStorm.
 * User: Adminstrator
 * Date: 2017/4/15
 * Time: 22:37
 */

namespace app\admin\controller;


use app\admin\model\RoleModel;
use app\common\controller\AdminBase;

class Role extends AdminBase
{
    protected $role_model;

    public function _initialize()
    {
        parent::_initialize(); // TODO: Change the autogenerated stub
        $this->role_model = new RoleModel();
    }

    public function index()
    {
        return $this->fetch();
    }

    public function lists()
    {
        $list = $this->role_model->get_role_list();
        $data = [
            'status' => 200,
            'msg' => '获取成功',
            'data' => ['list' => $list],
            'pages' => 2,
        ];

        return json($data);
    }

    public function add()
    {
        return $this->fetch();
    }

    public function edit()
    {
        return $this->fetch();
    }

    public function add_post()
    {

    }

    public function edit_post()
    {

    }
}
