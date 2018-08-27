<?php
namespace app\admin\controller;
use think\Controller;
use \app\api\model\Technician as UserModel;

class Technicianlist extends Controller{

    public function Technicianlist(){
        $ctrl =new \app\api\controller\Technician();
        $sk = new \app\api\controller\Skill();

        $skill = [];
        $technicians = $ctrl->get_all_technician();
        foreach($technicians as $tc){
            array_push($skill,$sk->get_skill_name($tc->job_number));
        }
        $count = $ctrl->count_all();
        $this->assign("skills",$skill);
        $this->assign("technicians",$technicians);
        $this->assign("count",$count);
        return $this->fetch();
    }
}