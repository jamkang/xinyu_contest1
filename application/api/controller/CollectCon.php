<?php
namespace app\api\controller;

use app\common\controller\BaseController;
use think\Request;
use think\Session;

class CollectCon extends BaseController
{
    //添加，取消收藏
    public function Add(Request $req)
    {
        $data['tid']=$req->get('tid');
        $data['uid']=1;
        //查看改套餐是否被收藏
        $label=model('Collect')->Label($data);
        if($label!==true){
            $re=model('Collect')->Add($data);
            if($re==false) return json(['return'=>'0','error'=>'收藏失败']);
            return json(['return'=>'1','sucess'=>'收藏成功']);
        }else{
            $re=model('Collect')->Qx($data);
            if($re==false) return json(['return'=>'0','error'=>'取消收藏失败']);
            return json(['return'=>'1','sucess'=>'取消收藏成功']);
        }
    }
    //分页用户返回收藏套餐
    public function Seek(Request $req)
    {
        $data['uid']=Session::get('usda.uid');
        if(empty($data['uid'])){
            return json(['return'=>'0','error'=>'请登录']);
        }
        $re=model('collect')->Seek($data['uid'],'uid');
        foreach ($re as $value){
            $value->FT;
        }
        if($re!==false)
        {
            return json(['return'=>'1','content'=>$re]);
        }
        return json(['return'=>'0','error'=>'收藏为空']);
    }
}