<?php
namespace app\admin\controller;
use think\Controller;
use \app\api\model\Rate as RateModel;

class Comment extends Controller{
    public function index(){
        $att = \app\api\model\Rate::all();
        $svs = new \app\api\controller\Servicetype();
        $svod = new \app\api\controller\Serviceorder();
        foreach($att as $rt){
            $name = $svs->getservicename($rt->service_id);

            if($name){
                $so = $svod->get_order($rt->order_id);
                $rt->service_id = $name;
                $rt->job_number = $so[0]->job_number;
            }
        }
        $tech = \app\api\model\Technician::all();
        $this->assign("comment",$att);
        $this->assign("count",count($att));
        $this->assign("tech",$tech);
        return $this->fetch('comment');
    }
}