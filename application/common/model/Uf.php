<?php
/**
 * 用户营养模块
 * User: wangkang200006a
 * Date: 2020/5/9
 * Time: 16:13
 *
 */
namespace app\common\model;

class Uf extends Basemodel
{
    //查看是否数据存在
    public function Label($data)
    {
        $where=[
            'id'=>['=',$data['id']],
            'date'=>['=',$data['date']]
        ];
        $re=$this->where($where)->find();
        if($re!=null){
            return true;
        }else{
            return false;
        }
    }
    public function La($data)
    {
        $where=[
            'uid'=>['=',$data['uid']],
            'fid'=>['=',$data['fid']],
            'time'=>['=',$data['time']],
            'ck'=>['=',$data['ck']]
        ];
        $re=$this->where($where)->find();
        if($re!=null){
            $this->where($where)->update($data);
            return true;
        }else{
            return false;
        }
    }

    //获取一天的所有食物
    public function Day($data)
    {
        $where=[
            'uid'=>['=',$data['uid']],
            'time'=>['=',$data['time']]
        ];
        $req=$this->where($where)->select();
        if($req!=null){
            foreach ($req as $value)
            {
                $value->UF;
            }
            return $req;
        }else{
            return false;
        }
    }
    //获取一个星期的食物
    public function Week($data)
    {
        $where=[
            'uid'=>['=',$data['uid']],
            'time'=>['>=',$data['time']]
        ];
        $req=$this->where($where)->select();
        if($req!=null){
            foreach ($req as $value)
            {
                $value->UF;
            }
            return $req;
        }else{
            return false;
        }
    }
    //与食品表进行多对多
    function UF()
    {
        return $this->belongsTo("Fd",'fid',"id");
    }

}