<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/26
 * Time: 13:24
 */

namespace app\admin\controller;


use app\admin\model\MenuModel;
use app\common\controller\AdminBase;
use app\common\Tools\AjaxCode;

class Index extends AdminBase
{
    protected $menu_model;

    public function _initialize()
    {
        parent::_initialize(); // TODO: Change the autogenerated stub
        $this->menu_model = new MenuModel();
    }

    /**
     * 后台首页
     */
    public function index()
    {
        return $this->fetch('index');
    }
    /**
     * 获取菜单
     */
    public function get_menu()
    {
        $data['list'] = $this->menu_model->get_menu($this->uid, 0);
        if (!empty($data['list'])) {
            return self::json(AjaxCode::SUCCESS, '获取成功', $data);
        } else {
            return self::json(AjaxCode::FAIL, '获取失败', $data);
        }
    }
    /**
     * 获取首页信息
     */
    public function welcome()
    {
        return $this->fetch('welcome');
    }

}