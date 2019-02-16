<?php
namespace app\admin\controller;
use think\Controller;
use \app\api\model\Consumedorder as UserModel;

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
            $info = $cs->get_customer($od['user_id']);
            $info2 = $cs->get_customer($od['payment_user_id']);
            $so = \app\api\model\Serviceorder::all(['order_id'=>$od['order_id']]);
            $str = "";
            $exist = [];
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
                array_push($user,"找不到该顾客");
                $order[$index]['username'] = "找不到该顾客";
            }
            if($info2){
                array_push($payer,$info2->phone_number);
            }else{
                array_push($payer,"暂无");
            }
        }
        $this->assign("order",$order);
        $this->assign("tech",$tech);
        $this->assign("phone",$user);
        $this->assign("phone2",$payer);
        $this->assign("count",count($order));
        return $this->fetch('Consumedorder/consumedorder');
    }
}