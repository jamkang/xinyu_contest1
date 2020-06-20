<?php
//食品分类后台控制器
namespace app\admin\controller;

use app\common\controller\BaseController;
use think\Request;

class FdpkCon extends BaseController
{
    //添加食品分类
    public function Add()
    {
        $data=input('post.');
        $re=model('Fdpk')->Add($data);
        //成功返回1，失败返回0
        if($re){
            return json(['return'=>'1','sucess'=>'添加成功']);
        }else{
            return json(['return'=>'0','error'=>'添加失败']);
        }
    }
    //删除
    public function Delete(Request $req)
    {
        $data=$req->get('id');
        $re=model('Fdpk')->FdDelete($data,'id');
        //成功返回1，失败返回0
        if($re){
            //先找到图片路径地址
            //$link=model('Fd')->OneSeekImg($data,'kid');
            //删除食物
            //model('Fd')->FdDelete($data,'kid');
            //foreach ($link as $value)
            //{
               // unlink(realpath(ROOT_PATH.'public').$value);
            //}
            return json(['return'=>'1','sucess'=>'删除成功']);
        }else{
            return json(['return'=>'0','error'=>'删除失败']);
        }
    }
    //查看
    public function Seek()
    {
        $re=model('Fdpk')->Seek();
        if($re!==false){
            return json(['return'=>'1','content'=>$re]);
        }else{
            return json(['return'=>'0','error'=>'查找失败']);
        }
    }
}
