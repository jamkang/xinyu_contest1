<?php


namespace app\common\model;

use think\Exception;

class Fdfood extends Basemodel
{
    //循环添加
    public function XhAdd($tid,$fids)
    {
        try{
            $i=0;
            foreach ($fids as $fid)
            {
                $where[$i]['tid']=$tid;
                $where[$i]['fid']=$fid;
                $i++;
            }
            $re =  $this->saveAll($where);
            if($re!==false){
                return true;
            }else{
                return false;
            }
        }catch (Exception $e){
            return false;
        }

    }
}