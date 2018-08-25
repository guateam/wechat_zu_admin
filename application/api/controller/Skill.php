<?php 
    namespace app\api\controller;
    use think\Controller;
    use \app\api\model\Skill as UserModel;
    class Skill extends Controller{
        public function get_skill($job_number){
            $skill_list = UserModel::all(['job_number'=>$job_number]);
            return $skill_list;
        }

        public function get_skill_service_id($job_number){
            $skill_list = UserModel::all(['job_number'=>$job_number]);
            $data=[];
            foreach($skill_list as $sk)
            {
                array_push($data,$sk->service_id);
            }
            return $data;
        }
    }