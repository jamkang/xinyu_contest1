<?php
//食品套餐接口
namespace app\api\controller;

use app\common\controller\BaseController;
use think\Request;

class FdtcCon extends BaseController
{
    public function KindSeek(Request $req)
    {
        //通过分类来给出随机的套餐数量
        $kind=$req->get('kind');
        //随机获取相对应的数据
        $re=model('Fdtc')->KindSeek($kind);
        if($re!==false)
        {
            return json(['return'=>'1','content'=>$re]);
        }else{
            return json(['return'=>'0','error'=>'出现未知错误']);
        }

    }
}