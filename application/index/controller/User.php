<?php
namespace app\index\controller;
use think\Request;

class User extends Common{
    public function regist(){
        return $this->fetch();
    }

    public function doregist(Request $request)
	{
		if(!$request->isAjax()){
			return json(['code'=>'0','msg'=>'非法请求IP已被记录']);
		}
		$model = model('User');
		// 对比验证码是否正确
		$session_data=session('captcha');
		if(!$session_data || $session_data['code'] != input('captcha')){
			return json(['code'=>0,'msg'=>'验证码错误']);
		}
		// 对比是否过期
		if($session_data['time']+300<time()){
			session('captcha',null);
			return json(['code'=>0,'msg'=>'验证码过期']);
		}
		// 调用模型方法注册
		$res = $model->regist(input('username'),input('password'),input('tel'));
		if($res === false){
			return json(['code'=>0,'msg'=>$model->getError()]);
		}
		return json(['code'=>1,'msg'=>'ok']);
	}
	
	public function login(Request $request){
		if($request->isGet()){
			return $this->fetch();
		}
		$model=model('User');
		$res=$model->login(input('username'),input('password'));
		if($res===false){
			$this->error($model->getError());
		}
		$this->success('登录完成','index/index/index');
	}

	public function logout(){
		session('user_info',null);
		$this->redirect('login');
	}

	public function test(){
		dump(send_email('phpyerihua@163.com','hello'));
	}

	public function sendSms(){
		// 获取手机号
		$tel=input('tel');
		// 生成验证码
		$code=rand(1000,9999);
		// 发送短信验证码
		$res=send_sms($tel,[$code,5]);
		if(!$res){
			return json(['code'=>0,'msg'=>'网络异常']);
		}
		// 保存验证码到session中
		session('captcha',['code'=>$code,'time'=>time()]);
		return json(['code'=>1,'msg'=>'ok','captcha'=>$code]);
	}

	// 邮箱注册
	public function email(Request $request){
		if($request->isGet()){
			return $this->fetch();
		}
		$model=model('User');
		$res=$model->email(input('username'),input('password'),input('email'));
		if($res===false){
			$this->error($model->getError());
		}
		$this->success('注册成功,请前往邮箱激活账号','login');
	}

	// 邮箱注册激活
	public function active(){
		// 获取标识
		$key=input('key');
		// 查询用户信息
		$user_info=db('user')->where('active_code',$key)->find();
		if(!$user_info){
			$this->redirect('index/index');
		}
		if($user_info['status']==1){
			$this->error('账户已经激活','login');
		}
		db('user')->where('active_code',$key)->setField('status',1);
		$this->success('激活成功,请前往登录页面');
	}
}