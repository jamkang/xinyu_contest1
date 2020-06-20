<?php
// 食品分类公用类
namespace app\common\model;

class Fdpk extends Basemodel
{
    public function Fd()
    {
        return $this->hasMany('Fd','kid','id');
    }
}