<?php
/**
 * Created by PhpStorm.
 * User: Adminstrator
 * Date: 2017/4/15
 * Time: 22:37
 */

namespace app\admin\controller;


use app\admin\model\MenuModel;
use app\admin\model\RoleModel;
use app\admin\model\UserModel;
use app\common\controller\AdminBase;
use app\common\model\Authority;
use app\common\model\AuthorityModel;
use app\common\Tools\AjaxCode;

class Role extends AdminBase
{
    protected $role_model, $menu_model, $authority_model,$user_model;

    public function _initialize()
    {
        parent::_initialize(); // TODO: Change the autogenerated stub
        $this->role_model = new RoleModel();
        $this->user_model = new UserModel();
        $this->menu_model = new MenuModel();
        $this->authority_model = new AuthorityModel();
    }

    /**
     * 权限组
     */
    public function index()
    {
        return $this->fetch();
    }

    /**
     * 权限组列表
     */
    public function lists()
    {
        $p = $this->request->param('p');

        if ($p < 1 || !is_numeric($p)) {
            $p = 1;
        }
        $size = 10;
        $list = $this->role_model->get_role_list($p, $size);
        $data = [
            'status' => 200,
            'msg' => '获取成功',
            'data' => ['list' => $list['list']],
            'pages' => $list['page'],
        ];
        return json($data);
    }

    /**
     * 增加角色
     */
    public function add()
    {
        if ($this->request->isPost()){//提交数据
            $post = $this->request->post();
            $data = [];
            array_walk($post, function (&$val, $key) use (&$data) {
                $data['add_time'] = time();
                $data[$key] = $val;
            });
            if (empty($data['name'])) {
                return json(['status' => AjaxCode::PARAM_EMPTY, 'msg' => '参数为空']);
            }
            $add = $this->role_model->add_role($data);
            if (!$add) {
                return json(['status' => AjaxCode::FAIL, 'msg' => '添加失败！', 'url' => '',]);
            }
            return json(['status' => AjaxCode::SUCCESS, 'msg' => '添加成功', 'url' => 'reload']);
        }
        return $this->fetch();
    }

    /**
     * 编辑角色
     */
    public function edit()
    {
        if ($this->request->isPost()){//提交数据
            $post = $this->request->post();
            $id = $post['id'];
            if (empty($id) || !check_id($id)) {
                return json(['status' => AjaxCode::PARAM_EMPTY, 'msg' => '参数为空']);
            }
            array_walk($post, function (&$val, $key) use (&$data) {
                $data['update_time'] = time();
                $data[$key] = $val;
            });
            unset($data['id']);
            $update = $this->role_model->edit_role($id, $data);
            if (!$update) {
                return json(['status' => AjaxCode::FAIL, 'msg' => '编辑失败！', 'url' => '',]);
            }
            return json(['status' => AjaxCode::SUCCESS, 'msg' => '编辑成功', 'url' => 'reload']);
        }
        $id = request()->get('id');
        if (empty($id) || !check_id($id)) {
            return json(['status' => AjaxCode::PARAM_EMPTY, 'msg' => '参数为空']);
        }
        $data = $this->role_model->by_id_role($id);
        $this->assign('list', $data);
        return $this->fetch();
    }

    /**
     * 编辑权限
     */
    public function edit_role()
    {
        if ($this->request->isPost()){
            $post = $this->request->post();
            $role_id = $post['role_id'];
            if (empty($role_id) || !check_id($role_id)) {
                return json(['status' => AjaxCode::PARAM_VALID, 'msg' => '参数错误！', 'url' => '',]);
            }
            if (!empty($post['role'])) {
                $data = [];
                //组装数组
                foreach ($post['role'] as $index => $item) {
                    $tmp['role_id'] = $role_id;
                    $tmp['menu_id'] = $index;
                    $tmp['rule_path'] = $item;
                    $data[] = $tmp;
                }
                $add = $this->authority_model->add_role_authority($data, $role_id);
                if (!$add) {
                    return json(['status' => AjaxCode::FAIL, 'msg' => '处理失败！', 'url' => '',]);
                }
                return json(['status' => AjaxCode::SUCCESS, 'msg' => '处理成功', 'url' => 'reload']);
            } else {
                $del = $this->authority_model->del_role_authority($role_id);
                if (!$del) {
                    return json(['status' => AjaxCode::FAIL, 'msg' => '处理失败！', 'url' => '',]);
                }
                return json(['status' => AjaxCode::SUCCESS, 'msg' => '处理成功', 'url' => 'reload']);
            }
        }
        $role_id = $this->request->get('id');
        if (empty($role_id) || !check_id($role_id)) {
            return json(['status' => AjaxCode::PARAM_VALID, 'msg' => '参数错误！', 'url' => '',]);
        }
        $list = $this->menu_model->get_tree(0);
        $data = $this->role_model->create_role($list, $role_id, 0);
        $role = "<ul><input type='hidden' name='role_id' value='{$role_id}'>" . $data . "</ul>";
        $this->assign('data', $role);
        return $this->fetch();
    }

    /**
     * 为角色添加用户
     */
    public function add_user()
    {
        if ($this->request->isPost()) {
            $post = $this->request->post();
            if (empty($post['role_id']) || !check_id($post['role_id'])) {
                return json(['status' => AjaxCode::PARAM_VALID, 'msg' => '参数错误！', 'url' => '',]);
            }
            if (!empty($post['user'])) {
                $data = [];
                //组装数组
                foreach ($post['user'] as $index => $item) {
                    $tmp['role_id'] = $post['role_id'];
                    $tmp['admin_id'] = $index;
                    $data[] = $tmp;
                }
                $add = $this->role_model->add_role_admin($data, $post['role_id']);
                if (!$add) {
                    return json(['status' => AjaxCode::FAIL, 'msg' => '处理失败！', 'url' => '',]);
                }
                return json(['status' => AjaxCode::SUCCESS, 'msg' => '处理成功', 'url' => 'reload']);
            } else {
                $del = $this->role_model->del_role_admin($post['role_id']);
                if (!$del) {
                    return json(['status' => AjaxCode::FAIL, 'msg' => '处理失败！', 'url' => '',]);
                }
                return json(['status' => AjaxCode::SUCCESS, 'msg' => '处理成功', 'url' => 'reload']);
            }
        }
        $role_id = $this->request->get('id');
        if (empty($role_id) || !check_id($role_id)) {
            return json(['status' => AjaxCode::PARAM_VALID, 'msg' => '参数错误！', 'url' => '',]);
        }
        $list= $this->user_model->field('id,user_login')->where(['status' => ['egt',-1],'user_type' => 1])->select();
        $data = [];
        foreach ($list as $index => $item){
            $data[] = $item->toArray();
        }
        $arr = $this->user_model->create_user($data,$role_id);
        $user = "<ul><input type='hidden' name='role_id' value='{$role_id}'" . $arr . "</ul>";
        $this->assign('data', $user);
        return $this->fetch();
    }

    /**
     * 排序
     */
    public function set_sort()
    {
        $id = $this->request->param('id');
        $sort = $this->request->param('sort');
        if (empty($id) || !check_id($id)) {
            return json(['status' => AjaxCode::PARAM_VALID, 'msg' => '参数错误！']);
        }
        if (!check_number($sort)) {
            return json(['status' => AjaxCode::PARAM_VALID, 'msg' => '排序参数错误！']);
        }
        $check_sort = $this->role_model->check_order($id, $sort);
        //检查pid同级下的排序是否冲突
        if (!$check_sort) {
            return json(['status' => AjaxCode::DATA_EXIST, 'msg' => '排序起冲突！']);
        }
        $edit_sort = $this->role_model->where('id', $id)->setField('sort', $sort);
        if ($edit_sort === false) {
            return json(['status' => AjaxCode::FAIL, 'msg' => '处理失败！']);
        } else {
            return json(['status' => AjaxCode::SUCCESS, 'msg' => '处理成功！']);
        }
    }


    /**
     * 设置状态
     */
    public function set_status()
    {
        $id = $this->request->param('id');
        $state = $this->request->param('state');
        if (empty($id) || !check_id($id)) {
            return json(['status' => AjaxCode::PARAM_VALID, 'msg' => '参数错误！']);
        }
        switch ($state) {
            case -1:
                $status = 1;
                break;
            case 1:
                $status = -1;
                break;
            default:
                return json(['status' => AjaxCode::PARAM_VALID, 'msg' => '状态参数错误！']);
                break;
        }
        $edit_state =  $this->role_model->where('id', $id)->setField('status', $status);
        if ($edit_state === false) {
            return json(['status' => AjaxCode::FAIL, 'msg' => '处理失败！']);
        } else {
            return json(['status' => AjaxCode::SUCCESS, 'msg' => '处理成功！']);
        }
    }

    /**
     * 删除
     */
    public function del()
    {

    }
}
