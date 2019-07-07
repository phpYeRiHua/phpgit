<?php
namespace app\admin\model;
use think\Db;
use think\Model;

class Admin extends Model{
    public function login($data){
        // 检查验证码
        $obj=new \think\captcha\Captcha();
        if(!$obj->check($data['captcha'])){
            $this->error='验证码错误';
            return false;
        }
        // 检查账号和密码
        $user_info=$this->get(['username'=>$data['username'],'password'=>md5($data['password'])]);
        if(!$user_info){
            $this->error='用户名或者密码错误';
            return false;
        }

        // 保存用户的状态
        $expire=0;
        if(isset($data['remenber'])){
            // 保存登录状态
            $expire=3600*24*3;
        }
        cookie('admin_info',$user_info->toArray(),$expire);
    }
    public function addUser($data){
        if($this->get(['username'=>$data['username']])){
            $this->error='该用户已存在';
            return false;
        }
        $data['password']=md5($data['password']);
        $this->save($data);
    }
    public function getList(){
        return $this->alias('a')->field('a.*,b.role_name')->join('shop_role b','a.role_id=b.id','left')->select();
    }
    public function editUser($data){
        $map=[
            'username'=>$data['username'],
            'id'=>['neq',$data['id']]
        ];
        if($this->get($map)){
            $this->error='用户名已存在';
            return false;
        }
        if($data['password']){
            $data['password']=md5($data['password']);
        }else{
            unset($data['password']);
        }
        $this->isUpdate(true)->save($data);
    }
}