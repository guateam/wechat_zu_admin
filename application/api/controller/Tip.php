<?php 
namespace app\api\controller;
use think\Controller;

class Tip extends Controller{
    public function gettiplist(){
        $tip=\app\api\model\Tip::all();
        return $tip;
    }

}