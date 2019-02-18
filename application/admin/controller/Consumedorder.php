<?php
namespace app\admin\controller;
use think\Controller;
use \app\api\model\Consumedorder as UserModel;
use think\Db;

class Consumedorder extends Controller{

    public function index($edit){
        $ctrl =new \app\api\controller\Consumedorder();
        $cs = new \app\api\controller\Customer();

        $order = $ctrl->get_all_origin();
        $tech = [];
        $user = [];
        $payer = [];
        foreach($order as $index => $od){
            //时间戳显示为日期字符串
            $order[$index]['generated_time'] = date('Y-m-d H:i:s', $order[$index]['generated_time']);
            $order[$index]['appoint_time'] = date('Y-m-d H:i:s', intval($order[$index]['appoint_time']));
            $info = $cs->get_customer($od['user_id']);
            $info2 = $cs->get_customer($od['payment_user_id']);
            $so = \app\api\model\Serviceorder::all(['order_id'=>$od['order_id']]);
            $str = "";
            $exist = [];
            if($so){
                $order[$index]['clock_type'] = $so[0]['clock_type'];
            }
            foreach($so as  $s){
                $repeat = false;
                foreach($exist as $ex){
                    if($ex == $s->job_number){
                        $repeat = true;
                        break;
                    }
                }
                if(!$repeat){
                    $str = $str.$s->job_number.',';
                    array_push($exist,$s->job_number);
                }
               
            }
            $str = substr($str,0,strlen($str)-1);
            array_push($tech,$str);
            if($info){
                array_push($user,$info->phone_number);
                $order[$index]['username'] = $info->name;
            }
            else{
                array_push($user,$od['contact_phone']);
                $order[$index]['username'] = $od['user_id'];
            }
            if($info2){
                array_push($payer,$info2->phone_number);
            }else{
                array_push($payer,$od['contact_phone']);
            }
        }
        $this->assign("order",$order);
        $this->assign("tech",$tech);
        $this->assign("phone",$user);
        $this->assign("phone2",$payer);
        $this->assign("edit",$edit);
        $this->assign("count",count($order));
        return $this->fetch('Consumedorder/consumedorder');
    }

    public function change($order_id){
        $order = Db::query("select * from consumed_order where order_id='$order_id'");
        $svod = Db::query("select * from service_order where order_id = '$order_id'");
        $room = Db::query("select ID,name from private_room");

        $order[0]['appoint_time'] = date("Y-m-d H:i:s",$order[0]['appoint_time']);
        foreach($svod as $idx => $sv){
            $service_id = $sv['item_id'];
            $name = Db::query("select name from service_type where ID='$service_id'");
            if($name){
                $svod[$idx]['name'] = $name[0]['name'];
            }
        }
        $technicians = Db::query("select * from technician");
        $service = Db::query("select ID,name,price from service_type");
        $room = Db::query("select ID,name from private_room");
        $spare_tech = [];
        //去除有约的技师
        foreach($technicians as $idx => $tc){
            $job_number = $tc['job_number'];
            $select_time = time();
            //获取刷钟情况
            $clock = Db::query("select state from clock where job_number = '$job_number' order by `time` limit 1");
            //获取预约情况
            $appoint_tech = Db::query("select * from service_order where job_number = '$job_number' and appoint_time > ($select_time - (select Sum(duration)*60 from service_order A,service_type B where A.item_id = B.ID and A.order_id =(select order_id from service_order where job_number = '$job_number' and appoint_time < $select_time order by appoint_time desc limit 1)  ))");
            //是否在上钟
            $up_clock = false;
            //是否被预约
            $already_appoint = false;
            //若有刷钟记录
            if($clock){
                //若最近的刷钟记录为上钟，则上钟情况为true
                if($clock[0]['state'] == 1)
                    $up_clock = true;
            }
            //若有预约
            if($appoint_tech){
                //预约情况为true
                $already_appoint = true;
            }

            if(!$already_appoint && !$up_clock){
                array_push($spare_tech,$tc);
            }
        }
        for($i=0;$i<count($svod);$i++){
            $ctrl = new \app\api\controller\Skill();
            $skills = $ctrl->get_skill($svod[$i]['job_number']);
            $svod[$i]['skill'] = $skills;
        }

        $this->assign('technicians',$spare_tech);
        $this->assign('order',$order[0]);
        $this->assign('service_orders',$svod);
        $this->assign('service_num',count($svod));
        $this->assign('room',$room);

        return $this->fetch('Consumedorder/editorder');
    }
}