<?php
namespace app\admin\controller;
use think\Controller;
use \app\api\model\Technician as UserModel;

class Techniciandetail extends Controller{

    public function Techniciandetail($jbnb){
        $ctrl =new \app\api\controller\Technician();
        $technician = $ctrl->get_technician($jbnb);
        $count = $ctrl->count_all();
        $this->assign("technician",$technician);
        return $this->fetch();
    }
}