<?php
namespace app\admin\controller;
use think\Controller;
use \app\api\model\Customer as UserModel;

class Customer extends Controller{

    public function index($edit){
        $ctrl =new \app\api\controller\Rechargerecord();

        $recharge = $ctrl->get_all_by_user();
        $customer = UserModel::all();
        $chargelist = [];
        // foreach($customer as $index => $cus){
        //     $customer[$index]->registration_date = date("Y-m-d H:i:s",$customer[$index]->registration_date);
        // }
        $this->assign("customer",$recharge);
        $this->assign("count",count($customer));
        //$this->assign("chargelist",$chargelist);
        $this->assign("edit",$edit);
        return $this->fetch('Customer/customer');

    }
}