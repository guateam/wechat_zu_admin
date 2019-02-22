<?php
namespace app\admin\controller;
use think\Controller;
use \app\api\model\Rate as RateModel;
use think\Db;

class Comment extends Controller
{
    public function index($edit)
	{
        $data = Db::query("select * from rate");
		for($i=0;$i<count($data);$i++)
		{
			$openid = $data[$i]['customer_id'];
			$service_id = $data[$i]['service_id'];

			$name = Db::query("select name from customer where openid='$openid'");
			$service_name = Db::query("select name from service_type where ID='$service_id'");

			if($name){
				$data[$i]['username'] = $name[0]['name'];
			}else{
				$data[$i]['username'] = "用户不存在";
			}
			if($service_name){
				$data[$i]['service_name'] = $service_name[0]['name'];
			}else{
				$data[$i]['service_name'] = "服务不存在";
			}
			$data[$i]['time'] = date("Y-m-d H:i:s",$data[$i]['time']);
		}
        $att = $data;
        $svs = new \app\api\controller\Servicetype();
        $itp = new \app\api\controller\Itemtype();
        $svod = new \app\api\controller\Serviceorder();
        foreach($att as $rt){
            $so = $svod->get_order($rt['order_id']);
            $name = "未知";
            if($so != 0){
                if($so[0]->service_type == 1){
                    $name = $svs->getservicename($rt['service_id']);
                }else if($so[0]->service_type == 3){
                    $name = $itp->get_name($rt['service_id']);
                }
                $rt['service_id'] = $name;
                $rt['job_number'] = $so[0]->job_number;
            }else{
                $rt['service_id'] = "无";
                $rt['job_number'] = "无";
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