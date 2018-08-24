<?php
namespace app\admin\controller;
use think\Controller;
use \app\api\model\Technician as UserModel;

class Edittechnician extends Controller{

    public function Edittechnician($name,$gender,$phonenum,$jobnum){
        $ctrl =new \app\api\controller\Technician();
        $technician = $ctrl->get_technician($jobnum);
        $this->assign("technician",$technician);
        return $this->fetch();
    }
}