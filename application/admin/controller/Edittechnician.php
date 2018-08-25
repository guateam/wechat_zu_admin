<?php
namespace app\admin\controller;
use think\Controller;
use \app\api\model\Technician as UserModel;

class Edittechnician extends Controller{

    public function Edittechnician($name,$gender,$phonenum,$jobnum){
        $ctrl =new \app\api\controller\Technician();
        $serve_ctrl = new \app\api\controller\Servicetype();
        $service_list = $serve_ctrl->getservicelist();
        $technician = $ctrl->get_technician($jobnum);
        $this->assign("servicelist",$service_list);
        $this->assign("technician",$technician);
        return $this->fetch();
    }
}