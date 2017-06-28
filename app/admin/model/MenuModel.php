<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/6
 * Time: 20:43
 */

namespace app\admin\model;

use app\common\model\AuthorityModel;
use think\Db;
use think\Model;

class MenuModel extends Model
{
    protected $name = 'menu';
    protected $db_authority;
    protected $authority_model;
    private $option = '';

    protected function initialize()
    {
        parent::initialize();
        $this->db_authority = Db::name('role_authority');
        $this->authority_model = new AuthorityModel();
    }

    /**
     * 获取左边树形菜单
     * @param $uid
     * @param $pid int
     * @return array
     */
    public function get_menu($uid, $pid = 0)
    {
        $menu = $this->where(['menu_pid' => $pid, 'status' => 1])->order('menu_sort')->select();
        $authority_data = [];
        if (!$this->authority_model->check_role($uid)) {
            $authority_data = $this->authority_model->browse_authority($uid);
        }
        $data = [];
        foreach ($menu as $index => $item) {
            $url = $item['menu_type'] == 0 ? '' : \url("$item[menu_app] /$item[menu_control]/$item[menu_method]");
            $tmp['id'] = $item['id'];
            $tmp['name'] = $item['menu_title'];
            $tmp['iconfont'] = $item['menu_icon'];
            $tmp['url'] = $url;
            if (!empty($authority_data) && !in_array($item['id'], $authority_data)) {
                continue;
            }
            $tmp['sub'] = $this->get_menu($uid, $item['id']);
            if (empty($tmp['sub'])) {
                unset($tmp['sub']);
            }
            $data[] = $tmp;
        }
        return $data;
    }

    /**
     * 生成菜单树形结构并返回option
     * @param $id string
     * @param $data array
     * @param $level int
     * @return array
     */
    public function create_option($id, $data = [], $level = 0)
    {
        if (empty($data)) {
            $data = $this->get_tree();
        }
        if ($data) {
            foreach ($data as $index => $item) {
                $tmp = '';
                if ($level > 0) {
                    $tmp = "├";
                    for ($i = 0; $i < $level; $i++) {
                        $tmp .= "─";
                    }
                }
                $selected = '';
                if ($id == $item['id']) {
                    $selected = 'selected';
                }
                $this->option .= "<option  $selected  value='{$item['id']}'>{$tmp} {$item['name']}</option>";
                if ($level == 3) {
                    continue;
                }
                if (isset($item['sub']) && $item['sub']) {
                    $this->create_option($id, $item['sub'], $level + 1);
                }
            }
        }

        return $this->option;
    }


    /**
     * 获取左边树形菜单
     * @param $pid int
     * @return array
     */
    public function get_tree($pid = 0)
    {
        $menu = $this->where(['menu_pid' => $pid])->order('menu_sort')->select();
        $data = [];
        foreach ($menu as $index => $item) {
            $tmp['id'] = $item['id'];
            $tmp['name'] = $item['menu_title'];
            $tmp['iconfont'] = $item['menu_icon'];
            $tmp['url'] = $item['menu_app'] . '/' . $item['menu_control'] . '/' . $item['menu_method'];
            $tmp['sub'] = $this->get_tree($item['id']);
            if (empty($tmp['sub'])) {
                unset($tmp['sub']);
            }
            $data[] = $tmp;
        }
        return $data;
    }

    /**
     * 获取菜单列表
     * @param $pid int
     * @param $p string
     * @param $size string
     * @return array
     */
    public function get_menu_list($pid = 0, $p, $size)
    {
        if ($size === false) {
            $limit = false;
        } else {
            $start = ($p - 1) * $size;
            if ($pid > 0) {
                $limit = "10";
            } else {
                $limit = "$start, $size";
            }
        }
        $menu = $this->where(['menu_pid' => $pid])->limit($limit)->order('menu_sort')->select();
        $data = [];
        foreach ($menu as $index => $item) {
            $tmp = [
                'id' => $item['id'],
                'title' => $item['menu_title'],
                'ban' => $item['menu_ban'],
                'sort' => $item['menu_sort'],
                'status' => $item['status'],
                'type' => $item['menu_type'],
                'link' => $item['menu_app'] . '/' . $item['menu_control'] . '/' . $item['menu_method'],
            ];
            $tmp['sub'] = $this->get_menu_list($item['id'],$p,$size);
            if (empty($tmp['sub'])) {
                unset($tmp['sub']);
            }
            $data[] = $tmp;
        }
        return $data;
    }

    /**
     * 根据ID获取
     * @param $id string
     * @return array
     */
    public function by_id_get($id)
    {
        $list = $this->where(['id' => $id])->find();
        $data = [];
        if ($list) {
            $data = [
                'id' => $list['id'],
                'pid' => $list['menu_pid'],
                'title' => $list['menu_title'],
                'app' => $list['menu_app'],
                'control' => $list['menu_control'],
                'method' => $list['menu_method'],
                'icon' => $list['menu_icon'],
                'remark' => $list['menu_remark'],
                'type' => $list['menu_type'],
                'status' => $list['status'],
                'ban' => $list['menu_ban'],
            ];
        }
        return $data;
    }

    /**
     * 增加菜单
     * @param $data array
     * @return bool
     */
    public function add_menu($data)
    {
        $add_menu = $this->insert($data);
        if ($add_menu == false) {
            return false;
        }
        return true;
    }

    /**
     * 编辑菜单
     * @param $data array
     * @return bool
     */
    public function edit_menu($data)
    {
        $id = $data['id'];
        unset($data['id']);
        $edit_menu = $this->save($data, ['id' => $id]);
        if ($edit_menu === false) {
            return false;
        }
        return true;
    }

    /**
     * 删除菜单
     * @param $id string
     * @return bool
     */
    public function delete_menu($id)
    {
        $count = $this->where(['menu_pid' => $id])->count();
        if ($count > 0) {
            return false;
        }
        $del_menu = $this->where(['id' => $id])->delete();
        if ($del_menu == false) {
            return false;
        }
        return true;
    }

    /**
     * 检查排序是否重复
     * @param $id string
     * @param $sort string
     * @return bool
     */
    public function check_order($id, $sort)
    {
        $menu = $this->field('menu_pid')->where('id', $id)->find()->toArray();
        if (!empty($menu) || check_number($menu['menu_pid'])) {
            $data = $this->where('menu_pid', $menu['menu_pid'])->select();
            if ($data) {
                foreach ($data as $index => $item) {
                    if ($sort == $item['menu_sort']) {
                        return false;
                        break;
                    }
                }
            }
        }
        return true;
    }

}