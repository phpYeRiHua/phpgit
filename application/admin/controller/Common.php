<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;

class Common extends Controller
{
    // 用于保存用户数据
    public $_user = [];
    public function __construct()
    {
        // 防止用户直接访问
        parent::__construct();
        // 判断用户是否登录
        $user_info = cookie('admin_info');
        if (!$user_info) {
            $this->error('请先登录', 'login/login');
        }
        // 读取缓存中的内容
        // 设置名称需要使用用户的唯一标识确保缓存的标识各不相同
        $this->_user = cache('user_info_id_' . $user_info['id']);
        if (!$this->_user) {
            // 没有从缓存中读取到数据，执行数据库的查询
            // 保存用户数据
            $this->_user = $user_info;

            // 根据角色不用获取到对应的权限数据
            if ($this->_user['role_id'] == 1) {
                // 超级管理员获取到所有权限
                $rule_list = Db::name('rule')->select();
            } else {
                // 根据ID获取权限ID
                $role_info = Db::name('role')->where('id', $this->_user['role_id'])->find();
                // 根据ID获取完整的权限信息 
                $role_list = Db::name('rule')->where('id', 'in', $role_info['rule_id'])->select();
            }
            // 格式化数据方便判断
            foreach ($rule_list as $key => $value) {
                // 将用户的权限信息转换为一维数组保存_user属性中
                // rules每个元素的内容为控制器与方法名称所组合的字符串
                $this->_user['rules'][] = strtolower($value['controller_name'] . '/' . $value['action_name']);
                if ($value['is_show'] == 1) {
                    $this->_user['menus'][] = $value;
                }
            }
            // 将数据写入缓存中
            cache('user_info_id_' . $user_info['id'], $this->_user, 3600 * 24);
        }

        // 判断是否有权访问
        if ($this->_user['role_id'] != 1) {
            // 增加固定权限
            $this->_user['rules'][] = 'index/index';
            $this->_user['rules'][] = 'index/top';
            $this->_user['rules'][] = 'index/menu';
            $this->_user['rules'][] = 'index/main';

            // 非管理员角色下的用户才需要验证权限
            // 获取当前用户访问的控制器、方法名称
            $action = strtolower(request()->controller() . '/' . request()->action());
            if (!in_array($action, $this->_user['rules'])) {
                if (request()->isAjax()) {
                    echo json_encode(['code' => 0, 'msg' => '无权访问']);
                    exit();
                } else {
                    $this->error('无权访问');
                }
            }
        }
    }
}
