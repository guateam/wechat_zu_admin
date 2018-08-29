<?php
namespace app\admin\controller;
use think\Controller;
use \app\api\model\admin as UserModel;

class Addprivateroom extends Controller{

    public function index(){
        //if(isset($_COOKIE['login'])){
        //    $user=new \app\api\controller\admin();
        //    if($user->checkuser($_COOKIE['userid'])){
                $servicelist=\app\api\controller\Servicetype::getservicelist();
                $this->assign('servicelist',$servicelist);
                return $this->fetch('tianjiafangjian');
        //    }
        //}
        //return $this->error('请先登录','index/index/index');
    }
    public function add($name,$roomid,$capacity,$servicetype){
        $room=new \app\api\controller\Privateroom();
        $data=$room->add($name,$roomid,$capacity,$servicetype);
        return json(['status'=>$data]);
    }
    public function checkname($name){
        $room=new \app\api\controller\Privateroom();
        $data=$room->checkname($name);
        return json(['status'=>$data]);
    }
    public function checkroomid($id){
        $room=new \app\api\controller\Privateroom();
        $data=$room->checkroomid($id);
        return json(['status'=>$data]);
    }
}