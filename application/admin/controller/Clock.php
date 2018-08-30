<?php
namespace app\admin\controller;
use think\Controller;

class Clock extends Controller{
    public function index(){
        $technician = \app\api\model\Technician::all();
        $date = date("Y-m-d");
        $date2 = date("Y-m-d");
        $date.=" 00:00:00";
        $date2.=" 23:59:59";
        $clock = [];
        foreach($technician as $tc){
            $service_order = \app\api\model\Serviceorder::all(["job_number"=>$tc->job_number]);
            if(!$service_order)$service_order=[];
            $consumed_order = [];
            $service_type = \app\api\model\Servicetype::all();
            if(!$service_type)$service_type = [];
            $last_soid = "";
            $key_info = [];
            //提成
            $ticheng = 0;
            //业绩
            $yeji = 0;
            $all_time = false;
            if($date=="")$all_time=true;
            
            foreach($service_order as $so){
                $one_consumed_order = \app\api\model\Consumedorder::get(["order_id"=>$so['order_id']]);
                $time = $one_consumed_order['generated_time'];
                if($all_time){
                    $date = $time;
                    $date2 = $time;
                }
                if($so['service_type']==1 && ($time >= $date && $time <= $date2) ){
                    foreach($service_type as $tp){
                        if($tp['ID']== $so['item_id']){
                            $so['price']=$tp['price']*$tp['discount']/100.0;
                            $so['price']/=100;
                            $ticheng+=$tp['commission']/100;
                            $yeji+=$so['price'];
                            array_push($key_info,["job_number"=>$tc->job_number,"service_name"=>$tp['name'],"room_number"=>$so['private_room_number'],"bonus"=>(int)$tp['commission']/100,
                            "time"=>$one_consumed_order['generated_time'],"order_id"=>$so['order_id'],"income"=>$so['price'],"clock_type"=>$so['clock_type']]);
                        }
                    }
                }
                if($so['order_id']!=$last_soid){
                    array_push($consumed_order,$one_consumed_order);
                };
                $last_soid = $so['order_id'];
            }
            array_push($clock,[
                'total_clock'=>count($key_info),
                'bonus'=>$ticheng,
                'total_income'=>$ticheng+$yeji,
                'key_info'=>$key_info
            ]);
        }
        $this->assign("clock",$clock);
        $this->assign("count",count($clock));
        return $this->fetch("clock");
    }
}