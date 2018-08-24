<?php
namespace app\admin\controller;
use think\Controller;
use \app\api\model\admin as UserModel;

class Editprivateroom extends Controller{

    public function index($id){
        //if(isset($_COOKIE['login'])){
        //    $user=new \app\api\controller\admin();
        //    if($user->checkuser($_COOKIE['userid'])){
                $room = new \app\api\controller\Privateroom();
                $data = $room->getroom($id);
                if($data){
                    $servicelist=\app\api\controller\Servicetype::getservicelist();
                    $this->assign('servicelist',$servicelist);
                    $this->assign('data',$data);
                    return $this->fetch('bianjifangjian');
                }
                return $this->error('404未知的房间');
        //    }
        //}
        //return $this->error('请先登录','index/index/index');
    }
    public function edit($id,$name,$roomid,$capacity,$servicetype){
        $room=new \app\api\controller\Privateroom();
        $data=$room->edit($id,$name,$roomid,$capacity,$servicetype);
        return json(['status'=>$data]);
    }
}