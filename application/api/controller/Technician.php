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
        public function delete_technician($job_number){
            $single_number;
            if(count($job_number)==0)return json(["status"=>1]);
            
            for($i=0;$i<count($job_number);$i++){
                $single_number=$job_number[$i];
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
        public function add_technician($name,$gender,$mobile,$job_number){
            $data = new UserModel(['name'=>$name,'gender'=>$gender,'phone_number'=>$mobile,'job_number'=>$job_number]);
            $data->save();
            return json(['status'=>1]);
        }
        public function update_technician($ori_job_number,$name,$gender,$mobile,$job_number){
            $data = UserModel::get(["job_number"=>$ori_job_number]);
            $data->name = $name;
            $data->gender = $gender;
            $data->phone_number=$mobile;
            $data->job_number=$job_number;
            $data->save();
            return json(["status"=>1]);
        }

        public function set_inviter($job_number,$inviter_job_number){
            $data = UserModel::get(["job_number"=>$job_number]);
            $data->inviter=$inviter_job_number;
            $data->save();
            return json(["status"=>1]);
        }
    }