<?php
namespace app\admin\controller;
use think\Controller;
use think\Request;
use think\Db;

class Login extends Controller
{
    public function login(Request $request)
    {
        if ($request->isGet()) {
            return $this->fetch();
        }
        $res = model('Admin')->login(input());
        if($res===false){
            $this->error(model('Admin')->getError());
        }
        $this->success('ok','admin/index/index');
    }


    // 生成验证码
    public function captcha(){
        $obj=new \think\captcha\Captcha(['length'=>3,'codeSet'=>'123456789']);
        return $obj->entry();
    }

    // 退出登录方法
    public function logout(){
        cookie('admin_info',null);
        $this->success('ok','login');
    }
}
