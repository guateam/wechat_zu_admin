<?php 
namespace app\api\controller;
use think\Controller;
use think\Db;
class Promotion extends Controller{
    public function delete($id){
        $data = \app\api\model\Promotion::get(["ID"=>$id]);
        $data->delete();
        return json(['status'=>1]);
    }
    public function get($id){
        $data = Db::query("select ID,content,need,back,FROM_UNIXTIME(A.start,'%Y-%m-%d') as _start,FROM_UNIXTIME(A.end,'%Y-%m-%d') as _end from promotion A where ID='$id'");
        //$data = \app\api\model\Promotion::get(["ID"=>$id]);
        return $data;
    }
    public function get_all(){
        $data = Db::query("select ID,content,need,back,FROM_UNIXTIME(A.start,'%Y-%m-%d') as _start,FROM_UNIXTIME(A.end,'%Y-%m-%d') as _end from promotion A");
        return $data;
    }
    public function add($content,$need,$back,$start,$end){
        $data = new \app\api\model\Promotion();
        $data->content = $content;
        $data->need = $need;
        $data->back = $back;
        $data->start = strtotime($start);
        $data->end = strtotime($end);
        $data->save();
        return json(['status'=>1]);
    }
    public function update($id,$content,$need,$back,$start,$end){
        $data = \app\api\model\Promotion::get(["ID"=>$id]);
        //将字符串日期转换成时间戳
        if(gettype($start) == "string"){
            $start = strtotime($start);

        }
        if(gettype($end) == "string"){
            $end = strtotime($end);
        }
        $data->content = $content;
        $data->need = $need;
        $data->back = $back;
        $data->start = $start;
        $data->end = $end;
        $data->save();
        return json(['status'=>1]);

    }
}