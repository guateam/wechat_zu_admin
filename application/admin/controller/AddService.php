<?php
namespace app\admin\controller;
use think\Controller;
use \app\api\model\ServiceType as UserModel;

class AddService extends Controller{

    public function AddService(){
        $ctrl = new \app\api\controller\Servicetype();
        return $this->fetch('addservice');
    }
}