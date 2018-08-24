<?php
namespace app\admin\controller;
use think\Controller;
use \app\api\model\Technician as UserModel;

class Addinviter extends Controller{

    public function Addinviter(){
        $ctrl =new \app\api\controller\Technician();
        $technicians = $ctrl->get_all_technician();
        $count = $ctrl->count_all();
        $this->assign("technicians",$technicians);
        return $this->fetch();
    }
}