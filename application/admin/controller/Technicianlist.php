<?php
namespace app\admin\controller;
use think\Controller;
use \app\api\model\Technician as UserModel;

class Technicianlist extends Controller{

    public function Technicianlist(){
        $ctrl =new \app\api\controller\Technician();
        $technicians = $ctrl->get_all_technician();
        $count = $ctrl->count_all();
        $this->assign("technicians",$technicians);
        $this->assign("count",$count);
        return $this->fetch();
    }
}