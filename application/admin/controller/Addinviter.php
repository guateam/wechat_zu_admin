<?php
namespace app\admin\controller;
use think\Controller;
use \app\api\model\Technician as UserModel;

class Addinviter extends Controller{

    public function Addinviter($jbnb){
        $ctrl =new \app\api\controller\Technician();
        $technicians = $ctrl->get_all_technician();
        $count = $ctrl->count_all();
        $this->assign("technicians",$technicians);
        $this->assign("self_job_number",$jbnb);
        return $this->fetch("Addinviter/addinviter");
    }
}