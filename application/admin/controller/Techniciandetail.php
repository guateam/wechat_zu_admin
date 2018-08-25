<?php
namespace app\admin\controller;
use think\Controller;
use \app\api\model\Technician as UserModel;

class Techniciandetail extends Controller{

    public function Techniciandetail($jbnb){
        $ctrl =new \app\api\controller\Technician();
        $skill_ctrl = new \app\api\controller\Skill();
        $serve_ctrl = new \app\api\controller\Servicetype();
        $technician = $ctrl->get_technician($jbnb);
        $count = $ctrl->count_all();
        $skill = $skill_ctrl->get_skill($jbnb);
        $skill_name = [];
        foreach($skill as $sk){
           array_push($skill_name,$serve_ctrl->getservicename($sk->service_id));
        }
        $this->assign("technician",$technician);
        $this->assign("skill",$skill_name);
        return $this->fetch();
    }
}