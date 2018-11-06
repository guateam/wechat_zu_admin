<?php
namespace app\admin\controller;
use think\Controller;
use \app\api\model\Technician as UserModel;

class Addtechnician extends Controller{

    public function Addtechnician(){
        $ctrl = new \app\api\controller\Servicetype();
        $ctrl_lv = new \app\api\controller\Skilllevel();
        $level = $ctrl_lv->get_all();
        $service_list = $ctrl->getservicelist();
        $this->assign("servicelist",$service_list);
        $this->assign("level",$level);
        return $this->fetch('Addtechnician/addtechnician');
    }
}