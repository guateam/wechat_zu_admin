<?php
namespace app\admin\controller;
use think\Controller;
use \app\api\model\admin as UserModel;

class Privateroom extends Controller{

    public function index(){
        //if(isset($_COOKIE['login'])){
        //    $user=new \app\api\controller\admin();
        //    if($user->checkuser($_COOKIE['userid'])){
                $privateroom=new \app\api\controller\Privateroom();
                $data=$privateroom->getroomlist();
                $this->assign('data',$data);
                return $this->fetch('baoxiangliebiao');
        //    }
        //}
        //return $this->error('请先登录','index/index/index');
    }
    public function delete($id){
        $room= new \app\api\controller\Privateroom();
        $data=$room->delete($id);
        return json(['status'=>$data]);
    }
    public function deletelist($idlist){
        $room= new \app\api\controller\Privateroom();
        $data=$room->deletelist($idlist);
        return json(['status'=>$data]);
    }
}