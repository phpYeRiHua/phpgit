<?php
namespace app\index\controller;
use think\Controller;
use think\Db;

class Common extends Controller{
    public function __construct(){
        parent::__construct();
        $category=Db::name('category')->select();
        $this->assign('category',$category);
    }
    

    public function checkLogin(){
        $user_info=session('user_info');
        if(!$user_info){
            $this->error('先登录');
        }
    }
}