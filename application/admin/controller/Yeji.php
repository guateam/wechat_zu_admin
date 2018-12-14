<?php
namespace app\admin\controller;

use think\Controller;

class Yeji extends Controller
{

    public function index($edit)
    {
        $ctrl = new \app\api\controller\Technician();
        //本月第一天
        $first_day = strtotime(date("Y-m-01"));
        //本月最后一天
        $last_day = strtotime(date("Y-m-t"));
        $yeji = $ctrl->get_all_yeji($first_day,$last_day);
        $this->assign("yeji", $yeji);
        $this->assign("count", count($yeji));
        return $this->fetch('Yeji/Yeji');
    }
}
