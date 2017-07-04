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
use think\Db;
use think\Request;

class AdminBase extends Controller
{
    protected $authority;      //权限model
    protected $admin;            //登录用户信息
    protected $uid;            //登录用户ID
    protected $sys_theme;      //
    protected $size;
    private $allow_visit_act = [
        'index' => [
            'index', 'welcome',
        ],
        'user' => [
            'login', 'register', 'memory', 'login_out'
        ],
    ];

    public function _initialize()
    {
        parent::_initialize();
        $this->size = 10;
        $this->sys_theme = Config::get('template.default_theme');
        $sys_admin = $this->request->session('sys_admin/a');
        if (!empty($sys_admin)) {
            $this->uid = $sys_admin['uid'];
            $this->admin = $sys_admin;
        }
        $this->check_login();
        //判断有没有权限
        $this->authority = new AuthorityModel();
        if (!$this->authority->check_role($this->uid)) {
            $this->check_authority();
        }
        //用户信息
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
        // dump($template);
        return parent::fetch($template, $vars, $replace, $config);
    }

    /**
     * 检查用户登录
     */
    protected function check_login()
    {
        if (empty($this->uid) && !($this->request->module() == 'admin' && in_array($this->request->action(), $this->allow_visit_act['user']))) {
            if ($this->request->isAjax()) {
                return $this->redirect(url('user/login'));
            } else {
                $this->redirect(url('user/login'));
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
        if (!$flag) {
            if (!($this->request->module() == 'admin' && in_array($this->request->action(), $this->allow_visit_act['user']))) {
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
     * @param $name
     */
    public function _empty($name)
    {
        echo $this->fetch($name);
        exit;
    }

    /**
     * 返回json
     * @param $code int 消息码
     * @param $msg string 提示信息
     * @param $list array 数据
     * @param $pages int 分页
     * @param $url string 跳转路径
     * @return boolean
     */
    protected static function json($code, $msg, $list = [], $pages = 0, $url = '')
    {
        $data['status'] = $code;
        $data['msg'] = $msg;
        $data['data'] = $list;
        $data['pages'] = $pages;
        $data['url'] = $url;
        return json($data);
    }

    /**
     * 获取一张表数据列表
     * @param $table_name string 表名称
     * @param $where array 条件
     * @param $order array 排序
     * @param $p string 分页数
     * @param $size string 每页条数
     * @param $group string 分组字段
     * @return array
     */
    public function get_list($table_name, $where = [], $order = [], $p, $size, $group = '')
    {
        if ($size === false) {
            $limit = false;
        } else {
            $start = ($p - 1) * $size;
            $limit = "$start, $size";
            $count = Db::name($table_name)->where($where)->count();
            $page = ($count / $size);
        }
        $list = Db::name($table_name)->where($where)->order($order)->limit($limit)->group($group)->select();
        $data = [];
        foreach ($list as $index => $item) {
            $data[] = $item;
        }
        $lists['list'] = $data;
        $lists['page'] = $page ?: 1;
        $lists['total'] = $count;
        return $lists;
    }


    /**
     * 添加数据
     * @param $table_name array
     * @param $data array
     * @return boolean
     */

    public function add_post($table_name, $data)
    {
        if (empty($data)) {
            return false;
        }
        $insert = Db::name($table_name)->insert($data);
        if ($insert == false) {
            return false;
        }
        return true;
    }

    /**
     * 编辑数据
     * @param $table_name array
     * @param $data array
     * @param $tel string
     * @return bool
     */
    public function edit_post($table_name, $data, $tel = '')
    {
        if ($tel) { //只限于修改用户修改根据手机最为条件
            $map['user_mobile'] = $tel;
        } else {
            $map['id'] = $data['id'];
            unset($data['id']);
        }
        $save = Db::name($table_name)->save($data, $map);
        if ($save === false) {
            return false;
        }
        return true;
    }

}