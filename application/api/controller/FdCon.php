<?php
//前端api的接口--食品
namespace app\api\controller;

use app\common\controller\BaseController;
use think\Request;

class FdCon extends BaseController
{
    //根据分类进行查找，并且分页返回
    public function KindSeek(Request $req)
    {
        $kid=$req->get("kid");
        $re=model('Fd')->Seek($kid,'kid');
        if($re!==false){
            return json(['return'=>'1','content'=>$re]);
        }else{
            return json(['return'=>'0','error'=>'没有数据']);
        }
    }
    //根据名称进行查找，并且分页返回
    public function NameSeek(Request $req)
    {
        $name = $req->get("name");
        $name='%'. $name.'%';
        $re=model('Fd')->Seek($name,'name');
        if($re!==false){
            return json(['return'=>'1','content'=>$re]);
        }else{
            return json(['return'=>'0','error'=>'未知错误']);
        }
    }
    //根据营养物质进行食品查找
    public function YySeek(Request $req)
    {
        $yy = $req->get("yy");
        $ta=['protein','lip','zn','ca','mg','fe','i','wa','wb','wc','ffb',"sugar"];
        $la=in_array($yy,$ta);
        if($la==false){
           dump($la);
            return json(['return'=>'0','error'=>'未知营养物质']);
        }
        $re=model('Fd')->YySeek($yy);
        if($re!==false){
            return json(['return'=>'1','content'=>$re]);
        }else{
            return json(['return'=>'0','error'=>'没有数据']);
        }
    }
}