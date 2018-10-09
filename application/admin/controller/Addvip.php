<?php
namespace app\admin\controller;
use think\Controller;
use \app\api\model\Vipinformation as VipModel;

class Addvip extends Controller{
public function index(){
        return $this->fetch("Addvip/addvip");
    }
}