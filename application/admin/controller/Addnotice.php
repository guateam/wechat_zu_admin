<?php
namespace app\admin\controller;
use think\Controller;
use \app\api\model\Technician as UserModel;

class Addnotice extends Controller{

    public function index(){

        return $this->fetch("fabugonggao");
    }
}