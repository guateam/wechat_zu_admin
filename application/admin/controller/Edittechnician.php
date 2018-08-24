<?php
namespace app\admin\controller;
use think\Controller;
use \app\api\model\Technician as UserModel;

class Edittechnician extends Controller{

    public function Edittechnician(){
        return $this->fetch();
    }
}