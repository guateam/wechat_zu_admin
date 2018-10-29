<?php 
namespace app\api\controller;
use think\Controller;

class Tip extends Controller{
    public function gettiplist(){
        $tip=\app\api\model\Tip::all();
        for($i=0;$i<count($tip);$i++){
            $tip[$i]->date = date("Y-m-d H:i:s", $tip[$i]->date);
        }
        return $tip;
    }

}