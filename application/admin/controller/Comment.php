<?php
namespace app\admin\controller;
use think\Controller;
use \app\api\model\Rate as RateModel;

class Comment extends Controller{
    public function index(){
        $att = \app\api\model\Rate::all();
        $svs = new \app\api\controller\Servicetype();
        foreach($att as $rt){
            $name = $svs->getservicename($rt->service_id);
            if($name){
                $rt->service_id = $name;
            }
        }
        $this->assign("comment",$att);
        $this->assign("count",count($att));
        return $this->fetch('comment');
    }
}