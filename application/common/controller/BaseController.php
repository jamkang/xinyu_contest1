<?php
//控制器公共类

namespace app\common\controller;

use think\Controller;
use think\Image;

class BaseController extends Controller
{
    //图片处理函数，进行图片处理，返回路径
    public function Image($file)
    {
        //简单验证一下看是否为图片
        if(true!==$this->validate(['image'=>$file],['image'=>'require|image'])){
            return false;
        }
        $image=Image::open($file);
        $image->thumb(200,200,6);
        $src = DS.'static'.DS.'upload'.DS.time().".png";
            if($image->save(realpath(ROOT_PATH.'public').$src)){
                return $src;
        }else{
            return false;
        }
    }
}