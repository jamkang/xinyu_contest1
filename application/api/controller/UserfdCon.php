<?php
/**
 * Created by PhpStorm.
 * User: wangkang200006a
 * Date: 2020/5/9
 * Time: 14:59
 * 分析用户营养的接口
 */
namespace app\api\controller;

use app\common\controller\BaseController;
use think\Config;
use think\Exception;
use think\Request;
use think\response\Json;
use think\Session;

class UserfdCon extends BaseController
{
    public function index()//测试时间
    {
        echo date('H:i:s');
    }

    //输入食品分析其中的营养
    public function Uf(Request $req)
    {
        $data['uid']=Session::get('usda.uid');
        if(empty($data['uid'])){
            return json(['return'=>'0','error'=>'请登录']);
        }
     // $data['uid']=1;
        //接收食物和吃饭的时间
        $wdata=$req->post();
        if(empty($wdata['fd'])){
            return json(['return'=>1,'success'=>"请输入您今天的食物"]);
        }
        $data['time']=date('Y-m-d');
        foreach ($wdata['fd'] as $value){
            if($value["cpt"]<=0){
                continue;
            }
            $data['ck']=empty($value['ck'])?0:$value['ck'];
            $data['fid']=$value["id"];
            $data['cpt']=$value["cpt"];
            $re=model('Uf')->La($data);
            if($re!=true){
                model('Uf')->isUpdate(false)->Add($data);
            }
        }
        return json(['return'=>1,'success'=>"添加今日食品成功"]);
    }

    //返回今天一天的营养状况
    public function Today()
    {
            $uid=Session::get('usda.uid');
            if(empty($uid)){
                return json(['return'=>'0','error'=>'请登录']);
            }
            //$uid=1;
            $data['uid']=$uid;
            $data['time']=date("Y-m-d");
            $re=model("Uf")->Day($data);
            if($re==null){
                return json(['return'=>0,'sum'=>0,'error'=>'还没有输入要分析的食物哦']);
            }

            //计算营养总量
            $mu=$this->MuCount($re);
            $yy=$mu['yy'];  //营养总量
            $mufd=$mu['fd'];    //每种营养的食物
            //总分和提示语句
            $ns=$this->MuNotice($yy,'day');
            //返回营养比例
            $pre=$this->MuPre($yy,'day');

            //更新用户表中上次检测的数据
            $up['jtime']=date("Y-m-d H:i:s");
            $up['jgrade']=$ns['sum'];
            $up['jnotice']=$ns['notice'];
            model("User")->Updates($up,'id',$data['uid']);

            //更新用户每日统计表
            $udu['uid']=$data['uid'];
            $udu['time']= $data['time'];
            $udu['sug']=$ns['notice'];
            $udu['grade']=$ns['sum'];
            $up=model("Udu")->Add($udu);
            if($up==false){
                $up=model("Udu")->Up($udu);
            }

            return json(['return'=>1,'sum'=>$ns['sum'],'notice'=>$ns['notice'],'pre'=>$pre,'fd'=> $mufd]);

    }
    //返回这一星期的营养分析
    public function Week(){
            $uid=Session::get('usda.uid');
            if(empty($uid)){
                return json(['return'=>'0','error'=>'请登录']);
            }
            //$uid=1;
            $data['uid']=$uid;
            $time=time()-604800;
            $data['time']=date("Y-m-d",$time);
            $re=model("Uf")->Week($data);
            if($re==null){
                return json(['return'=>0,'sum'=>0,'error'=>'这七天还未输入要分析的食物哦']);
            }
        //计算营养总量
        $mu=$this->MuCount($re);
        $yy=$mu['yy'];  //营养总量
        $mufd=$mu['fd'];    //每种营养的食物
        //总分和提示语句
        $ns=$this->MuNotice($yy,'day');
        //返回营养比例
        $pre=$this->MuPre($yy,'day');

        return json(['return'=>1,'sum'=>$ns['sum'],'notice'=>$ns['notice'],'pre'=>$pre,'fd'=> $mufd]);

    }
    //计算营养总量//
    public function MuCount($re)
    {
        $muda=["protein","lip","zn","ca","mg","fe","p","k","wa","wb","wc","we","ffb","sugar","kll"];
        // 初始化营养
        foreach ($muda as $value){
            $mufd[$value]='';
            $yy[$value]=0;
        }
        //营养含量的叠加
        foreach($re as $value) {
            if($value['UF']===null){
                continue;
            }
            $food=json_decode($value['UF']);    //用来化解$value['UF']不能用foreach来遍历的问题
            foreach($food as $key2 => $value2) {
                $mufd[$key2]=array();
                if(in_array($key2,$muda)&&$value2!=0){
                    $mufd[$key2][]= $food->name;
                    $yy[$key2]+=$value2*$value['cpt'];
                }
            }
        }
        //合并数组
        $mufd["misa"]=array_merge($mufd['zn'],$mufd['p'],$mufd['mg'],$mufd['ca'],$mufd['fe'],$mufd['k']);
        $mufd['w']=array_merge($mufd['wa'],$mufd['wb'],$mufd['wc'],$mufd['we']);
        //去除数组重复值
        $mufd["misa"]=array_unique($mufd["misa"]);
        $mufd["w"]=array_unique($mufd["w"]);

        //转换为字符串
        $content['protein'] =  implode('、',$mufd['protein']);
        $content['lip']     =  implode('、',$mufd['lip']);
        $content['misa']    =  implode('、',$mufd['misa']);
        $content['w']       =  implode('、',$mufd['w']);
        $content['ffb']     =  implode('、',$mufd['ffb']);
        $content['sugar']     =  implode('、',$mufd['sugar']);
        $content['kll']     =  implode('、',$mufd['kll']);

        $mu['yy']=$yy;
        $mu['fd']=$content;

        return $mu;
    }
    //给出提示语
    public function MuNotice($yy,$kind)
    {
        $notice='';
        $sum=0;
        $re['notice']='';
        $re['sum']=0;
        $out='out'.$kind;
        $tdayy=Config::get('yy');
        //营养提示
        foreach ($yy as $key => $value){
            if($value==0){
                //没有吃
                $wsr[]=$tdayy['name'][$key];
            }elseif($value<$tdayy[$kind][$key]){
                //吃少了；
                $sum+=4;
                $scl[]=$tdayy['name'][$key];
            }elseif($value>$tdayy[$out][$key]){
                //吃多了
                $sum+=4;
                $dcl[]=$tdayy['name'][$key];
            }else{
                //正常的
                $sum+=6.5;
            }
        }
         if(isset($wsr)){
            $strwsr=implode('、',$wsr);
             $notice.="还未摄入的：".$strwsr."。";
        }
        if(isset($dcl)){
            $strdcl=implode('、',$dcl);
            $notice.="摄入过多的：".$strdcl."。";
        }
        if(isset($scl)){
            $strscl=implode('、',$scl);
            $notice.="摄入过少的：".$strscl."。";
        }
        $re['sum']=$sum>96?100:$sum;
        $re['notice']=empty($notice)?"营养均衡,请继续保持":$notice;
        return $re;
    }
    //百分比重
    public function MuPre($yy,$kind)
    {
        $tdayy=Config::get('yy');
        $out='out'.$kind;
        foreach ($yy as $key => $value){
            if($value==0){
                $fd[$key]=array();
                $pre[$key]=0;
                continue;
            }
           if($value<$tdayy[$kind][$key]){
                //吃少了；
               $pre[$key]=($value/$tdayy[$kind][$key])/3;
            }elseif($value>$tdayy[$out][$key]){
                //吃多了
               $pre[$key]=(2*($value/$tdayy[$out][$key]))/3;
               if($pre[$key]>1){
                   $pre[$key]=1;
               }
            }else{
                //正常的
               //{（营养值-合格量）/[(最大值-合格量）*3]}+1/3;
               $pre[$key]=(($value-$tdayy[$kind][$key])/(($tdayy[$out][$key]-$tdayy[$kind][$key])*3))+0.3333;
            }
        }

        //统计百分比
        $content['protein'] =   $pre['protein'];
        $content['lip']     =   $pre['lip'];
        $content['misa']    =   (($pre['zn']+$pre['p']+$pre['mg']+$pre['ca']+$pre['fe']+$pre['k'])/6.00);
        $content['w']       =   (($pre['wa']+$pre['wb']+$pre['wc']+$pre['we'])/4.00);
        $content['ffb']     =   $pre['ffb'];
        $content['sugar']     =   $pre['sugar'];
        $content['kll']     = $pre['kll'];

        return $content;
    }
}
