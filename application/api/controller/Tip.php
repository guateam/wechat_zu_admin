<?php 
namespace app\api\controller;
use think\Controller;
use think\Db;

class Tip extends Controller{
    public function gettiplist(){
        $tip=Db::query("select * from tip");
        for($i=0;$i<count($tip);$i++){
            $openid = $tip[$i]['user_id'];
            $name = Db::query("select name from customer where openid = '$openid'");
            $tip[$i]['date'] = date("Y-m-d H:i:s", $tip[$i]['date']);
            if($name){
                $tip[$i]['username'] = $name[0]['name'];
            }else{
                $tip[$i]['username'] = "用户不存在";
            }
        }
        return $tip;
    }

} 