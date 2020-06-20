<?php
//收藏模块表
namespace app\common\model;

use think\Config;

class Collect extends Basemodel
{
    //判断是否收藏了
    public function Label($data)
    {
        $where=[
            'uid'=>['=',$data['uid']],
            'tid'=>['=',$data['tid']]
        ];
        $re=$this->where($where)->find();
        if($re!=null)
        {
            return true;
        }
        return false;
    }
    //取消收藏
    public function Qx($data)
    {
        $where=[
            'uid'=>['=',$data['uid']],
            'tid'=>['=',$data['tid']]
        ];
        $re=$this->where($where)->delete();
        if($re!==false)
        {
            return true;
        }
        return false;
    }
    public function FT()
    {
        return $this->belongsTo('fdtc','tid','id');
    }
}