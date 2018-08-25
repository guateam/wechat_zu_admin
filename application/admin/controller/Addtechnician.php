<?php
namespace app\admin\controller;
use think\Controller;
use \app\api\model\Technician as UserModel;

class Addtechnician extends Controller{

    public function Addtechnician(){
        $ctrl = new \app\api\controller\Servicetype();
        $service_list = $ctrl->getservicelist();
        $this->assign("servicelist",$service_list);
        return $this->fetch();
    }
}