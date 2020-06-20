<?php
//食品套餐后台接口
namespace app\admin\controller;

use app\common\controller\BaseController;
use think\Exception;
use think\Request;

class FdtcCon extends BaseController
{
    //添加套餐
    public function Add(Request $req)
    {
        try{
            $data=$req->post();
            if(!empty($data['grade'])&&($data['grade']>100||$data['grade']<0)){
                $data['grade']=50;
            }
            $id=model('fdtc')->TcAdd($data);
            if($id==false){
                return json(['return'=>['0'],'error'=>'添加套餐第一步出错']);
            }
            //添加数据到fdfood里面去
            $re = model('Fdfood')->XhAdd($id,$data['fd']);
            if($re!==true){
                model('Fdfood')->FdDelete($id,'tid');
                model('Fdtc')->FdDelete($id);
                return json(['return'=>'0','error'=>'添加错误']);
            }
            return json(['return'=>'1','sucess'=>'添加成功']);
        }catch (Exception $e){
            return json(['return'=>'0','error'=>'添加错误，套餐名已存在']);
        }



        //添加数据到fdfood里面去
        //这个注释掉的时出现错误的，因为只能添加到第一个数据，后面的数据就添加不进去了
        //$data1['tid']=$id;
//        foreach($data['fd'] as $value)
//        {
//            $data1['fid']=$value;
//            $re=model('Fdfood')->Add($data1);
//            //如果中间出现错误，就立马停止添加，并且删除这次添加的数据
//            if($re!==true){
//                model('Fdfood')->TfDelete($data['tid']);
//                model('Fdtc')->FdDelete($data['tid']);
//                return json(['return'=>'0','error'=>'添加时出现小错误']);
//            }{
//                var_dump($re);
//                $re=false;
//        }
//         }
    }
    //删除套餐
    public function Delete(Request $req)
    {
        $id=$req->get('id');
        $re=model('Fdfood')->FdDelete($id,'tid');
        if($re==false){
            return json(['return'=>'0','sucess'=>'没有该套餐']);
        }
        model('Fdtc')->FdDelete($id,'id');
        model('Collect')->FdDelete($id,'tid');//删除所用用户所收藏的这个套餐
        return json(['return'=>'1','sucess'=>'删除成功']);
    }


    //分页按照种类全部返回
    public function Allseek(Request $req)
    {
        $data = $req->get();
        $data['page']=empty($data['page'])?1:$data['page'];//默认为第一页
        //获取总页数
        $pages=model('Fdtc')->Pages($data['kind'],'kind');
        if($data['page']<1||$data['page']>$pages){
        return json(['return'=>'0','error'=>'没有数据哦']);
    }
        //分页进行返回
        $re=model('Fdtc')->KindAllSeek($data,'kind');
        if($re!==false)
        {
            return json(['return'=>'1','pages'=>$pages,'content'=>$re]);
        }else{
            return json(['return'=>'0','error'=>'出现错误']);
        }
    }
}