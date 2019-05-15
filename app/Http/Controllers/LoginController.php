<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class LoginController extends Controller
{
   public function regdo(Request $request){
       $method='AES-256-CBC';
       $key='xxyyzz';
       $option=OPENSSL_RAW_DATA;
       $iv='qwertyuiopasdfgh';
       $res=file_get_contents("php://input");
       $enc_str=base64_decode($res);
       $arr=openssl_decrypt($enc_str,$method,$key,$option,$iv);
       $data=json_decode($arr,true);
       //检测密码是否一致
       if($data['pwd'] != $data['repwd']){
           $reponse=[
               'errno'=>40001,
               'msg'=>'两次密码输入不一致',
           ];
           die(json_encode($reponse,JSON_UNESCAPED_UNICODE));
       }
       $email=DB::table('api_users')->where(['email'=>$data['email']])->first();
       if($email){
           $reponse=[
               'errno'=>40003,
               'msg'=>'邮箱已存在',
           ];
           die(json_encode($reponse,JSON_UNESCAPED_UNICODE));
       }
       //密码加密处理
       $hash_pwd=password_hash($data['pwd'],PASSWORD_DEFAULT);
       $data=[
           'name'=>$data['name'],
           'email'=>$data['email'],
           'pwd'=>$hash_pwd,
       ];
       $res=DB::table('api_users')->insert($data);
       if($res){
           //TODO
           $reponse=[
               'errno'=>0,
               'msg'=>'ok',
           ];
           die(json_encode($reponse,JSON_UNESCAPED_UNICODE));
       }else{
           //TODO
           $reponse=[
               'errno'=>40002,
               'msg'=>'添加失败',
           ];
           die(json_encode($reponse,JSON_UNESCAPED_UNICODE));
       }
   }
   public function logindo(Request $request){
       $method='AES-256-CBC';
       $key='xxyyzz';
       $option=OPENSSL_RAW_DATA;
       $iv='qwertyuiopasdfgh';
       $res=file_get_contents("php://input");
       $enc_str=base64_decode($res);
       $arr=openssl_decrypt($enc_str,$method,$key,$option,$iv);
       $data=json_decode($arr,true);
       $email=$data['email'];
       $pwd=$data['pwd'];
       $res= DB::table('api_users')->where(['email'=>$email])->first();
       if($res){//账号存在
           if(password_verify($pwd,$res->pwd)){//密码正确
               $token=$this->user_token($res->id);
               $redis_token_key='login_token:id'.$res->id;
               Redis::set($redis_token_key,$token);
               Redis::expire($redis_token_key,604800);
               $reponse=[
                   'errno'=>0,
                   'msg'=>'ok',
                   'data'=>[
                       'token'=>$token,
                       'id'=>$res->id
                   ]
               ];
           }else{//密码错误
               $reponse=[
                   'errno'=>50002,
                   'msg'=>'账号或密码错误',
               ];
           }
       }else{//账号不存在
           $reponse=[
               'errno'=>50001,
               'msg'=>'账号或密码错误',
           ];
       }
       die(json_encode($reponse,JSON_UNESCAPED_UNICODE));
   }
    public function user_token($id){
        return $token=substr(md5($id.time().Str::random(10)),5,20);
    }
}
