<?php

namespace app\common\model;

use think\Config;
use think\Exception;

class Fd extends Basemodel
{
    //根据id查询单个数据图片路径
    public function OneSeekImg($id,$key='id')
    {
        try {
            $where=[
                $key=>['=',$id]
            ];
            $re=$this->where($where)->column('img');
            if($re!==false){
                return $re;
            }else{
                return false;
            }
        }catch (Exception $e){
            return false;
        }
    }
    public function YySeek($yy){
        $where=[
            $yy=>['>',0]
        ];
        if($re=$this->where($where)->select()){
            return $re;
        }else{
            return false;
        }
    }
    //和食品种类表进行关联
    public function Fdpk()
    {
        return $this->belongsTo('Fdpk','id');
    }
    //相关联到套餐表上去
    public function Pk()
    {
        return $this->belongsToMany('fdtc','Fdfood','fid','id');
    }
}