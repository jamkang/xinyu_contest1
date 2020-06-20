<?php

//食品分类后台控制器
namespace app\admin\controller;

use app\common\controller\BaseController;
use think\Request;

class FdtcpkCon extends BaseController
{
    //添加食品分类
    public function Add(Request $req)
    {
        $data = input('post.');
        $img= $req->file('img');
        $data['img']=$this->Image($img);
        if($data['img']==false){
            return json(['return' => '0','error'=>'图片出现错误']);
        }
        $re = model('Fdtcpk')->Add($data);
        //成功返回1，失败返回0
        if ($re) {
            return json(['return' => '1','success'=>'添加成功']);
        } else {
            return json(['return' => '0','error'=>'添加错误']);
        }
    }

    //删除
    public function Delete(Request $req)
    {
        $data = $req->get('id');
        $link=model('Fdtcpk')->OneSeekImg($data);
        $re = model('Fdtcpk')->FdDelete($data);
        //成功返回1，失败返回0
        if ($re) {
            //删除套餐的图片
            unlink(realpath(ROOT_PATH.'public').$link);
            //先查找出套餐种类中所有套餐
            $re1=model('fdtc')->Seek($data,'kind');
            //删除其中的套餐
            model('Fdtc')->FdDelete($data,'kind');
            //循环删除里面的套餐中fdfood和收藏表里面的数据
            foreach ($re1 as $value)
            {
                model('Collect')->FdDelete($value['id'],'tid');//删除所用用户所收藏的这个套餐
                model('fdfood')->FdDelete($value['id'],'tid');
            }
            return json(['return' => '1','success'=>'删除成功']);
        } else {
            return json(['return' => '0','error'=>'删除失败']);
        }
    }

    //查看
    public function Seek()
    {
        $re = model('Fdtcpk')->Seek();
        if ($re !== false) {
            return json(['return' => '1', 'content' => $re]);
        } else {
            return json(['return' => '0','error'=>'系统忙，请稍后加载']);
        }
    }
}
