<?php
/**
 * Created by PhpStorm.
 * User: wangkang200006a
 * Date: 2020/5/19
 * Time: 16:59
 * 用户表
 */
namespace app\common\model;

class User extends Basemodel
{
    //判断用户是否存在
    public function La($openid)
    {
        $where=[
            "openid"    =>  $openid
        ];
        $re=$this->where($where)->find();
        if($re!==false){
            return $re;
        }else{
            return false;
        }
    }
}