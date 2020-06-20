<?php
// 食品套餐公用类
namespace app\common\model;

use think\Config;

class Fdtc extends Basemodel
{
    //获取数据
    public function KindAllSeek($data,$key){
        $datanum=Config::get('pages.pd');
        $star = ($data['page']-1)*$datanum;
        $where=[
            $key=>['like',$data[$key]],
        ];
        //获取食品分类名称
        $re = $this->where($where)->limit($star,$datanum)->select();
        foreach ($re as $value)
        {
            $value->pkname;
            $value->food;
        }
        if($re!==false)
        {
            return $re;
        }else{
            return false;
        }

    }
    //添加
    public function TcAdd($data)
    {
        try {
            if($this->allowField(true)->save($data))
            {
                return $this->getLastInsID();
            }else{
                return false;
            }
        }catch (Exception $e){
            return false;
        }
    }
    //随机获取相对应的数据
    public function KindSeek($kind)
    {
        $re=$this->where(['kind'=>['=',$kind]])->order('rand()')
            ->limit(1)->select();
        foreach ($re as $value)
        {
            $value->food;
        }
        if($re!==false){
            return $re;
        }else{
            return false;
        }

    }
    //相关联到食品表上去
    public function food()
    {
        return $this->belongsToMany('Fd','fdfood','fid','tid');
    }
    //连接到种类
    public function pkname()
    {
        return $this->belongsTo('Fdtcpk','kind');
    }
}