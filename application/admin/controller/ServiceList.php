<?php
namespace app\admin\controller;
use think\Controller;
use \app\api\model\Servicetype as UserModel;

class ServiceList extends Controller{

    public function ServiceList(){
        $ctrl =new \app\api\controller\Servicetype();
        $serviceList = $ctrl->getservicelist();
        $count = $ctrl->count_all();
        $this->assign("serviceList",$serviceList);
        $this->assign("count",$count);
        return $this->fetch();
    }
}