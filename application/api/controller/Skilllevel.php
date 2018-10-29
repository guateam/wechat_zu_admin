<?php 
    namespace app\api\controller;
    use think\Controller;
    use \app\api\model\Skilllevel as UserModel;
    class Skilllevel extends Controller{

        public function get_all(){
            $all = UserModel::all();
            return $all;
        }

        /**
         * 根据等级名称ID修改等级名称
         */
        public function change_name($id,$name){
            $skill_name = \app\api\model\Skilllevel::get(['ID'=>$id]);
            $skill_name->name = $name;
            $skill_name->save();
            return json(['status'=>1]);
        }

        public function add($name){
            $skill = new \app\api\model\Skilllevel(['name'=>$name]);
            $skill->save();
            return json(['status'=>1]);
        }
    }