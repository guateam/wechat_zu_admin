<?php
namespace app\admin\controller;
use think\Controller;
use \app\api\model\Rate as RateModel;

class Comment extends Controller{
    public function index($edit){
        $att = \app\api\model\Rate::all();
        $svs = new \app\api\controller\Servicetype();
        $itp = new \app\api\controller\Itemtype();
        $svod = new \app\api\controller\Serviceorder();
        foreach($att as $rt){
            $so = $svod->get_order($rt->order_id);
            $name = "未知";
            if($so != 0){
                if($so[0]->service_type == 1){
                    $name = $svs->getservicename($rt->service_id);
                }else if($so[0]->service_type == 3){
                    $name = $itp->get_name($rt->service_id);
                }
                $rt->service_id = $name;
                $rt->job_number = $so[0]->job_number;
            }else{
                $rt->service_id = "无";
                $rt->job_number = "无";
            }
        }
        $tech = \app\api\model\Technician::all();
        $this->assign("comment",$att);
        $this->assign("count",count($att));
        $this->assign("tech",$tech);
        $this->assign("edit",$edit);
        return $this->fetch('Comment/comment');
    }
}