<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
if(!function_exists('get_tree')) {
    function get_tree($data,$id=0,$lev=0,$is_clear=false){
        static $list=[];
        if($is_clear){
            $list=[];
        }
        foreach($data as $value){
            if($value['parent_id']==$id){
                $value['lev']=$lev;
                $list[]=$value;
                get_tree($data,$value['id'],$lev+1,false);
            }
        }
        return $list;
    }
}
if(!function_exists('md6')){
    function md6($password,$salt){
        return md5(md5($password).$salt);
    }
}
if(!function_exists('send_sms')){
    function send_sms($to,$datas,$tempId=1){
        // 主账号
        $accountSid='8aaf07086b8862cb016ba7f60fa0115a';
        // 主账号Token
        $accountToken='1b0dce420a04406fb56dc865bd3ed1fb';
        // 应用ID
        $appId='8aaf07086b8862cb016ba7f8a5c41164';
        // 请求地址
        $serverIp='app.cloopen.com';
        // 请求端口
        $serverPort='8883';
        // REST版本
        $softVersion='2013-12-26';
        $rest=new \REST($serverIp,$serverPort,$softVersion);
        $rest->setAccount($accountSid,$accountToken);
        $rest->setAppId($appId);
        // 发送模板信息
        $result=$rest->sendTemplateSMS($to,$datas,$tempId);
        if($result == NULL ) {
            return false;
        }
        if($result->statusCode!=0) {
            return false;
        }
        return true;
    }
}
if(!function_exists('send_email')){
    function send_email($to,$msg,$Subject='账号注册邮件激活'){
        require '../extend/PHPMailer/class.phpmailer.php';
        $mail             = new \PHPMailer();
        // 读取配置信息
        $server = config('email_server');
        // halt($server);
        /*服务器相关信息*/
        $mail->IsSMTP();   //启用smtp服务发送邮件                     
        $mail->SMTPAuth   = true;  //设置开启认证             
        $mail->Host       = $server['host'];      //指定发件箱smtp邮件服务器地址  
        $mail->Username   = $server['username'];     //指定用户名 
        $mail->Password   = $server['password'];     //邮箱的第三方客户端的授权密码
        /*内容信息*/
        $mail->IsHTML(true);
        $mail->CharSet    ="UTF-8";         
        $mail->From       = $server['from'];         
        $mail->FromName   ="商城管理员";   //发件人昵称
        $mail->Subject    = $Subject; //发件主题
        $mail->MsgHTML($msg);  //邮件内容 支持HTML代码
        $mail->AddAddress($to);  
        return $mail->Send();          //发送邮箱
    }
}