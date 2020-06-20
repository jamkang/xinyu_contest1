<?php
/**
 * Created by PhpStorm.
 * User: wangkang200006a
 * Date: 2020/5/21
 * Time: 12:21
 * 用于存放用户每日营养记录的
 */
namespace app\common\model;

use think\Config;
use think\Exception;

class Udu extends Basemodel
{
    public function Up($data){
        try{
            $where=[
                'uid'=>['=',$data['uid']],
                'time'=>['=',$data['time']]
            ];
            $re=$this->where($where)->update($data);
            if($re!==false){
                return true;
            }else{
                return false;
            }
        }catch (Exception $e){
            return false;
        }
    }
    //分页顺序获取
    public function Seeksx($uid,$page)
    {
        $tiaos=Config::get("pages.udu");
        $where=[
            "uid"=>['like',$uid],
        ];
        $star=($page-1)*$tiaos;
        if($re=$this->where($where)->limit($star,$tiaos)->order("time","desc")->select()){
            return $re;
        }else{
            return false;
        }
    }
}