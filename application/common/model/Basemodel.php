<?php
//模型公共类
namespace app\common\model;

use think\Config;
use think\Exception;
use think\Model;

class Basemodel extends Model
{
    //添加
    public function Add($data)
    {
        try {
            $re=$this->allowField(true)->save($data);
            if($re!==false)
            {
                return true;
            }else{
                return false;
            }
        }catch (Exception $e){
            return false;
        }
    }
    //一次添加多条数据
    public function Adds($data)
    {
        try {
            $re=$this->saveAll($data);
            if($re!==false)
            {
                return true;
            }else{
                return false;
            }
        }catch (Exception $e){
            return false;
        }
    }
    //默认根据id号进行删除
    public function FdDelete($data,$key='id')
    {
        $where=[
            $key=>['=',$data],
        ];
        if($this->where($where)->delete())
        {
            return true;
        }else{
            return false;
        }
    }
    //更新
    public function Updates($data,$key,$value){
        try{
            $where=[
                $key=>['=',$value]
            ];
            $re=$this->where($where)->update($data);
            if($re!==false){
                return true;
            }else{
                return false;
            }
        }catch (Exception $e){//有错误页返回false
            echo 1;
            return false;
        }

    }
    //全部查找
    public function Seek($value='%%',$key='id')
    {
        $where=[
            $key=>['like',$value],
        ];
        if($re=$this->where($where)->select()){
            return $re;
        }else{
            return false;
        }
    }
    //获取总页数
    public function Pages($kid,$key)
    {
        $datanum=Config::get('pages.pd');
        $where = [
            $key => ['like', $kid]
        ];
        $re = $this->where($where)->count();
        if ($re % $datanum==0) {
            return $re / $datanum;
        }else{
            return (int)($re/$datanum)+1;
        }
    }
}