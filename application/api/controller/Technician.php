<?php 
    namespace app\api\controller;
    use think\Controller;
    use \app\api\model\Technician as UserModel;
    class Technician extends Controller{
        public function get_all_technician(){
            $data = UserModel::all();
            return $data;
        }
        public function count_all(){
            $data = UserModel::all();
            return count($data);
        }
        public function get_technician($job_number){
            $data = UserModel::get(["job_number"=>$job_number]);
            if($data){
                return $data;
            }else{
                return json(["status"=>0]);
            }
        }
        public function delete_technician($job_number=[]){
            $single_number;
            if(count($job_number)==0)return json(["status"=>1]);
            
            for($i=0;$i<count($job_number);$i++){
                $single_number=$job_number[$i];

                $skill = \app\api\model\Skill::all(["job_number"=>$single_number]);
                foreach($skill as $sk)$sk->delete();

                $data = UserModel::get(["job_number"=>$single_number]);
                if($data){
                    $data->delete();
                }else{
                    return json(["status"=>0]);
                }
            }
            return json(["status"=>1]);
        }
        public function delete_all(){

        }
        private function check_repeat($mobile,$job_number,$ori_job_number=""){
            
                $repeat = UserModel::get(['phone_number'=>$mobile]);
                if($repeat->job_number !=$ori_job_number)return 0;

                $repeat = UserModel::get(['job_number'=>$job_number]);
                if($repeat->job_number !=$ori_job_number)return -1;
            return 1;
        }
        public function add_technician($name,$gender,$mobile,$job_number,$skill){
            $repeat = UserModel::get(['phone_number'=>$mobile]);
            if($repeat)return json(['status'=>0]);
            $data = new UserModel(['name'=>$name,'gender'=>$gender,'phone_number'=>$mobile,'job_number'=>$job_number]);
            $data->save();
            for($i=0;$i<count($skill);$i++){
                $skill_info = new \app\api\model\Skill(['job_number'=>$job_number,'service_id'=>$skill[$i],'extra_income'=>0]);
                $skill_info->save();
            }
            return json(['status'=>1]);
        }
        public function update_technician($ori_job_number,$name,$gender,$mobile,$job_number,$skill){

            $is_repeat = self::check_repeat($mobile,$job_number,$ori_job_number);
            if($is_repeat!=1)return json(["status"=>$is_repeat]);

            $data = UserModel::get(["job_number"=>$ori_job_number]);
            $data->name = $name;
            $data->gender = $gender;
            $data->phone_number=$mobile;
            $data->job_number=$job_number;
            $data->save();
            $skill_info = \app\api\model\Skill::all(['job_number'=>$ori_job_number]);
            foreach($skill_info as $it){
                $it->delete();
            }
            foreach($skill as $sk){
                $new_skill = new \app\api\model\Skill(['job_number'=>$job_number,'service_id'=>$sk,'extra_income'=>0]);
                $new_skill->save();
            }
            return json(["status"=>1]);
        }

        public function set_inviter($job_number,$inviter_job_number){
            $data = UserModel::get(["job_number"=>$job_number]);
            $data->inviter=$inviter_job_number;
            $data->save();
            return json(["status"=>1]);
        }
    }