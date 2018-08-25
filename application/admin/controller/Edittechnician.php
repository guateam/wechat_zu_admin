<?php
namespace app\admin\controller;
use think\Controller;
use \app\api\model\Technician as UserModel;

class Edittechnician extends Controller{

    public function Edittechnician($name,$gender,$phonenum,$jobnum){
        $ctrl =new \app\api\controller\Technician();
        $serve_ctrl = new \app\api\controller\Servicetype();
        $skill_ctrl = new \app\api\controller\Skill();
        $skill_list = $skill_ctrl->get_skill_service_id($jobnum);
        $service_list = $serve_ctrl->getservicelist();
        $technician = $ctrl->get_technician($jobnum);
        $this->assign("servicelist",$service_list);
        $this->assign("technician",$technician);
        $this->assign("skill_serveid",$skill_list);
        return $this->fetch();
    }
}