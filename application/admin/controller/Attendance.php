<?php
namespace app\admin\controller;
use think\Controller;
use \app\api\model\ServiceType as UserModel;

class Attendance extends Controller{
    public function index(){
        $att = \app\api\model\Attendance::all();
        $this->assign("attendance",$att);
        $this->assign("count",count($att));
        return $this->fetch('attendance');
    }
}