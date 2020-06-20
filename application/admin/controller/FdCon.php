<?php
//食品后台控制器
namespace app\admin\controller;

use app\common\controller\BaseController;
use think\Request;

class FdCon extends BaseController
{
    //添加
    public function Add(Request $req)
    {
        $data=$req->post();
        $file=$req->file('img');
        //调用图片处理函数，进行图片处理
        $data['img']=$this->Image($file);
        if($data['img']==false){
            return json(['return'=>'0','error'=>"图片出现错误"]);
        }
        $fd=$this->Yysf($data);
        $re=model('Fd')->Add($fd);
        if($re!==false){
            return json(['return'=>'1','success'=>'添加成功']);
        }else{
            return json(['return'=>'0','error'=>"添加错误"]);
        }
    }

    //添加食品营养算法
    public function Yysf($data)
    {
        //分析营养默认为100
        $data['anyy']=empty($data['anyy'])?100:$data['anyy'];
        //分析食物比重
        $scale=$data['awgt']/$data['anyy'];
        $yy=["protein","lip","zn","ca","mg","fe","p","k","wa","wb","wc","we","ffb","sugar","kll"];
        //添加名字，介绍，种类，每份量，份量
        $qda=["name","introduce","kid","acpt","cpt","img"];
        foreach ($data as $key=>$value){
            if(in_array($key,$yy)){
                $fd[$key]=(float)($value)*(float)($scale);
            }elseif (in_array($key,$qda)){
                $fd[$key]=$value;
            }
        }
        return $fd;
    }
    //删除
    public function Delete(Request $req)
    {
        $id=$req->get('id');
        //先找到图片路径地址
        $link=model('Fd')->OneSeekImg($id);
        $re=model('fd')->FdDelete($id);
        if($re!==false){
            //如果成功则删除食品套餐表中的数据
            model('fdfood')->FdDelete($id,'fid');
            if($link!==false){
                $link=$link[0];
                unlink(realpath(ROOT_PATH.'public').$link);
            }
            return json(['return'=>'1','success'=>'删除成功']);
        }else{
            return json(['return'=>'0','error'=>"删除错误"]);
        }
    }
    //修改食物信息
    public function AlterFd(Request $req){
        $data=$req->post();
        $value=$data['id'];
        $link=model('Fd')->OneSeekImg($value);//找出原先的图片路径
        $file=$req->file('img');
        //调用图片处理函数，进行图片处理
        $data['img']=$this->Image($file);
        if($data['img']==false){
            return json(['return'=>'0','error'=>"图片出现错误"]);
        }
        $fd=$this->Yysf($data);
        $re=model('Fd')->Updates($fd,'id',$value);
        if($re!==false){
            $link=$link[0];
            unlink(realpath(ROOT_PATH.'public').$link);
            return json(['return'=>'1','success'=>'修改成功']);
        }
        return json(['return'=>'0','error'=>'修改失败']);
    }
}