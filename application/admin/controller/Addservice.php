<?php
namespace app\admin\controller;
use think\Controller;
use \app\api\model\ServiceType as UserModel;

class Addservice extends Controller{

    public function Addservice(){
        $ctrl = new \app\api\controller\Servicetype();
        return $this->fetch('Addservice/addservice');
    }
}
