<?php
namespace app\api\controller;
//用户控制器

use app\common\controller\BaseController;
use think\Config;
use think\Request;
use think\Session;

class UserCon extends BaseController
{
    public function GetTime(){
        $uid=1;
        $re=model('User')->Seek($uid,'id');
        $content['jtime']=$re[0]['jtime'];
        $content['jgrade']=$re[0]['jgrade'];
        $content['jnotice']=$re[0]['jnotice'];

        if($content['jtime']==null){
            return json(['return'=>0,'error'=>'还未开始进行检测哦']);
        }
        return json(['return'=>1,'content'=>$content]);
    }
    //分页返回用户营养的健康记录
    public function Getjk(Request $req)
    {
        $uid=Session::get('usda.uid');
        if(empty($uid)){
            return json(['return'=>'0','error'=>'请登录']);
        }
        //获取页码，默认第一页
        $page=$req->get('page');
        $page=empty($page)?1:$page;
        //获取总页数
        $pages=model("Udu")->Pages($uid,"uid");
        if($page<1||$page>$pages){
            return json(['return'=>'0','error'=>"页码错误"]);
        }
        $re=model("Udu")->Seeksx($uid,$page);
        if($re!==false){
            return json(['return'=>'1','content'=>$re]);
        }else{
            return json(['return'=>'0','error'=>"错误"]);
        }
    }
    //用户登录处理
    public function Login(Request $req)
    {
        $login=Config::get("login");
        // https://api.weixin.qq.com/sns/jscode2session?appid=APPID&secret=SECRET&js_code=JSCODE&grant_type=authorization_code
        $appid=$login['appid'];
        $secret=$login["secret"];
        $js_code=$req->get('code');
        //获取openid
        $url='https://api.weixin.qq.com/sns/jscode2session?appid=';
        $url .=$appid.'&secret='.$secret."&js_code=".$js_code."&grant_type=authorization_code";
        $user=file_get_contents($url);
        $user=json_decode($user);
        //判断是否成功
        if($user->errcode!=0){
            return json(['return'=>0,'error'=>'获取用户id失败']);
        }
        Session::set('sesion_key',$user->sesion_key);
        $openid=$user->openid;
        $usda=model("User")->La($openid);
        if($usda==false){
            $ure=model("User")->Add($user['openid']);
            if($ure==false){
                return json(['return'=>0,'error'=>'写入数据库失败']);
            }
            $usda=model("User")->La($openid);
        }
        Session::set('usda',$usda);
    }
    public function Index(){
        $uid=Session::get('usda.uid');
        print_r($uid);
    }
}