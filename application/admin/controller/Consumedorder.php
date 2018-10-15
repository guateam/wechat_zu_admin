<?php
namespace app\admin\controller;
use think\Controller;
use \app\api\model\Consumedorder as UserModel;

class Consumedorder extends Controller{

    public function index(){
        $ctrl =new \app\api\controller\Consumedorder();
        $cs = new \app\api\controller\Customer();
        $order = $ctrl->get_all();
        $tech = [];
        $user = [];
        $payer = [];
        foreach($order as $od){
            $info = $cs->get_customer($od->user_id);
            $info2 = $cs->get_customer($od->payment_user_id);
            $so = \app\api\model\Serviceorder::all(['order_id'=>$od->order_id]);
            $str = "";
            foreach($so as  $s){
                $str = $str.$s->job_number.',';
            }
            array_push($tech,$str);
            if($info){
                array_push($user,$info->phone_number);
            }
            else{
                array_push($user,"找不到该顾客");
            }
            if($info2){
                array_push($payer,$info2->phone_number);
            }else{
                array_push($payer,"找不到该顾客");
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