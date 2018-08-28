<?php
namespace app\admin\controller;
use think\Controller;
use \app\api\model\Customer as UserModel;

class Customer extends Controller{

    public function index(){
        $customer = UserModel::all();
        $this->assign("customer",$customer);
        $this->assign("count",count($customer));
        return $this->fetch('customer');

    }
}