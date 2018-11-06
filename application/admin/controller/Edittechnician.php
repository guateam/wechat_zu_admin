<?php
namespace app\admin\controller;
use think\Controller;
use \app\api\model\Technician as UserModel;
use think\Db;

class Edittechnician extends Controller{

    public function Edittechnician($name,$gender,$phonenum,$jobnum){
        $ctrl =new \app\api\controller\Technician();
        $serve_ctrl = new \app\api\controller\Servicetype();
        $skill_ctrl = new \app\api\controller\Skill();
        $ctrl_lv = new \app\api\controller\Skilllevel();

        $level = $ctrl_lv->get_all();
        $skill_list = $skill_ctrl->get_skill_service_id($jobnum);
        $service_list = $serve_ctrl->getservicelist();
        $technician = $ctrl->get_technician($jobnum);
        $inviter = $ctrl->get_inviter($jobnum);
        $skillname = $ctrl->get_skill($jobnum);
        $techlist = Db::query("select * from technician where job_number <> '".$jobnum."'");

        $btday = $technician->birthday;
        $technician->birthday = substr($btday,0,10);
        $this->assign("servicelist",$service_list);
        $this->assign("technician",$technician);
        $this->assign("inviter",$inviter);
        $this->assign("skillname",$skillname);
        $this->assign("techlist",$techlist);
        $this->assign("level",$level);
        $this->assign("skill_serveid",$skill_list);
        return $this->fetch('Edittechnician/edittechnician');
    }
}