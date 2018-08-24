<?php
namespace app\admin\controller;
use think\Controller;
use \app\api\model\Technician as UserModel;

class Addtechnician extends Controller{

    public function Addtechnician(){
        return $this->fetch();
    }
}