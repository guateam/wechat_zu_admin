<?php
namespace app\admin\controller;
use think\Controller;
use \app\api\model\Customer as UserModel;

class Customer extends Controller{

    public function index(){
        $ctrl =new \app\api\controller\Rechargerecord();
        $recharge = $ctrl->get_all();
        $customer = UserModel::all();
        $chargelist = [];
        foreach($customer as $cus){
            $money = 0;
            foreach($recharge as $rec){
                if($rec->user_id == $cus->ID){
                    $money+=$rec->charge;
                }
            }
            $money/=100;
            array_push($chargelist,$money);
        }
        $this->assign("customer",$customer);
        $this->assign("count",count($customer));
        $this->assign("chargelist",$chargelist);
        return $this->fetch('customer');

    }
}