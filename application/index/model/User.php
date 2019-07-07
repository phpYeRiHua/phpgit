<?php
namespace app\index\model;
use think\Model;
class User extends Model{
    protected $resultSetType='think\Collection';
    public function regist($username,$password,$tel)
	{
		// 检查重名
		if($this->get(['username'=>$username])){
			$this->error = '用户名重复';
			return false;
		}
		// 检查手机号
		if($this->get(['tel'=>$tel])){
			$this->error='手机号重复';
			return false;
		}
		// 生成盐
		$salt = rand(100000,999999);
		// 生成密码
		$password = md6($password,$salt);
		// 入库
		$data = [
			'username'=>$username,
			'password'=>$password,
			'salt'=>$salt,
			'tel'=>$tel,
			'status'=>1
		];
		$this->save($data);
		// 销毁session中的验证码
		session('captcha',null);
	}
	public function login($username,$password){
		$user_info=$this->get(['username'=>$username]);
		if(!$user_info){
			$this->error='账号不存在';
			return false;
		}
		$user_info=$user_info->toArray();
		// 用户状态验证
		if($user_info['status']==0){
			$this->error='该账号还没激活';
			return false;
		}
		// 密码验证
		if($user_info['password'] !=md6($password,$user_info['salt'])){
			$this->error='密码错误';
			return false;
		}
		session('user_info',$user_info);
		model('Cart')->cookie2db();
	}
	public function email($username,$password,$email){
		// 检查重名
		if($this->get(['username'=>$username])){
			$this->error = '用户名重复';
			return false;
		}
		// 检查邮箱
		if($this->get(['email'=>$email])){
			$this->error = '邮箱重复';
			return false;
		}
		// 计算激活码
		$active_code=uniqid();
		// 生成盐
		$salt=rand(100000,999999);
		// 生成密码
		$password=md6($password,$salt);
		// 入库
		$data=[
			'active_code'=>$active_code,
			'username'=>$username,
			'password'=>$password,
			'salt'=>$salt,
			'email'=>$email
		];
		$this->save($data);
		// 发送邮件
		$link=url('index/user/active','',true,true).'?key='.$active_code;
		$content="<a href='$link' style='font-size:18px;color:red'>点击激活</a>";
		send_email($email,$content);
	}
}