<?php 
    namespace app\api\controller;
    use think\Controller;
    use \app\api\model\Technician as UserModel;
    class Technician extends Controller{
        /**
         * 获取技师列表
         * 2018-8-24    创建   袁宜照
         * 
         */
        public function get_all_technician(){
            $data = UserModel::all();
            return $data;
        }
        /**
         * 获取技师总数
         * 2018-8-24    创建   袁宜照
         * 
         */
        public function count_all(){
            $data = UserModel::all();
            return count($data);
        }
        /**
         * 获取指定工号的技师信息
         * 2018-8-24    创建   袁宜照
         * @param string $job_number 工号
         * 
         */
        public function get_technician($job_number){
            $data = UserModel::get(["job_number"=>$job_number]);
            if($data){
                return $data;
            }else{
                return json(["status"=>0]);
            }
        }
        /**
         * 删除指定工号的技师
         * 2018-8-24    创建   袁宜照
         * @param array|string $job_number 工号列表
         * 
         */
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
        /**
         * 删除所有技师
         * 2018-8-24    创建   袁宜照
         * 未完成
         */
        public function delete_all(){

        }
        /**
         * 检测技师录入是否有重复信息
         * 2018-8-24    创建   袁宜照
         * @param string $name              录入的姓名
         * @param string $mobile            录入的手机号
         * @param string $job_number        录入的工号
         * @param string $idcard            录入的身份证号
         * @param string $ori_job_number    修改前的工号(添加新技师时不提供该参数)
         * 
         * 
         */
        public function check_repeat($name="",$mobile="",$job_number="",$idcard="",$ori_job_number=""){
                	    
                if($name!=""){
                    $repeat = UserModel::get(['name'=>$name]);
                    if($repeat){
                        if($repeat->job_number !=$ori_job_number){
                            return -2;
                        }
                    }
                }

                $repeat = UserModel::get(['phone_number'=>$mobile]);
                if($repeat){
                    if($repeat->job_number !=$ori_job_number){
                        return 0;
                    }
                }
                $repeat = UserModel::get(['job_number'=>$job_number]);
                if($repeat){
                    if($repeat->job_number !=$ori_job_number){
                        return -1;
                    }
                }
                $repeat = UserModel::get(['id_number'=>$idcard]);
                if($repeat){
                    if($repeat->job_number !=$ori_job_number){
                        return -3;
                    }
                }
            return 1;
        }
        
        public function check_name($name,$ori_number=""){
            $repeat = UserModel::get(['name'=>$name]);
            if($repeat){
                if($repeat->job_number != $ori_number)
                    return false;
            }
            return true;
        }
        public function check_phone_number($num,$ori_number=""){
            $repeat = UserModel::get(['phone_number'=>$num]);
            if($repeat){
                if($repeat->job_number != $ori_number)
                    return false;
            }
            return true;
        }
        public function check_job_number($num,$ori_number=""){
            $repeat = UserModel::get(['job_number'=>$num]);
            if($repeat){
                if($repeat->job_number != $ori_number)
                    return false;
            }
            return true;
        }
        public function check_idnumber($num,$ori_number=""){
            $repeat = UserModel::get(['id_number'=>$num]);
            if($repeat){
                if($repeat->job_number != $ori_number)
                    return false;
            }
            return true;
        }
        /**
         * 添加技师
         * 2018-8-24    创建   袁宜照
         * @param string $name       技师姓名
         * @param string $gender     技师性别
         * @param string $mobile     技师手机号
         * @param string $job_number 技师工号
         * @param string $skill      技师的技能
         * 
         */
        public function add_technician($name,$idcard,$birthday,$gender,$mobile,$job_number,$skill=''){
            if($skill=='')return json(['status'=>-4]);

            $is_repeat = self::check_repeat($name,$mobile,$job_number,$idcard,"");
            if($is_repeat!=1)return json(["status"=>$is_repeat]);

            $data = new UserModel(['name'=>$name,'gender'=>$gender,'phone_number'=>$mobile,
                                    'job_number'=>$job_number,'birthday'=>$birthday,"id_number"=>$idcard]);
            $data->save();
            for($i=0;$i<count($skill);$i++){
                $skill_info = new \app\api\model\Skill(['job_number'=>$job_number,'service_id'=>$skill[$i],'extra_income'=>0]);
                $skill_info->save();
            }
            return json(['status'=>1]);
        }
        /**
         * 修改技师信息
         * 2018-8-24    创建    袁宜照
         * 2018-8-25    修改    袁宜照
         * @param string $ori_job_number    原始工号
         * @param string $name              修改后的姓名
         * @param string $gender            修改后的性别
         * @param string $mobile            修改后的手机号
         * @param string $job_number        修改后的工号
         * @param string $skill             修改后的技能
         * 
         */
        public function update_technician($ori_job_number,$name,$idcard,$birthday,$gender,$mobile,$job_number,$skill,$level){

            $is_repeat = self::check_repeat($name,$mobile,$job_number,$idcard,$ori_job_number);
            if($is_repeat!=1)return json(["status"=>$is_repeat]);

            $data = UserModel::get(["job_number"=>$ori_job_number]);
            $data->name = $name;
            $data->gender = $gender;
            $data->phone_number=$mobile;
            $data->job_number=$job_number;
            $data->id_number = $idcard;
            $data->birthday=$birthday;
            $data->level = $level;
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
        /**
         * 指定技师的邀请人
         * 2018-8-25    创建    袁宜照
         * @param string $job_number            技师工号
         * @param string $inviter_job_number    邀请人工号
         * 
         */
        public function set_inviter($job_number,$inviter_job_number){
            $data = UserModel::get(["job_number"=>$job_number]);
            if($data){
                $data->inviter=$inviter_job_number;
                $data->save();
                return json(["status"=>1]);
            }
            // $ctrl = new \app\api\controller\Inviteship();
            // $result = $ctrl->add($inviter_job_number,$job_number,0);
            // $result = $result->getdata();
            // if($result['status']!=1)
            // {
            //     return json(['status'=>0]);
            // }
            return json(["status"=>0]);
        }
        /**
         * 改变技师在职状态
         * 2018-8-25    创建    袁宜照
         * @param string $job_number            技师工号
         *
         */
        public function change_in_job($job_number){
            $data = UserModel::get(["job_number"=>$job_number]);
            if($data){
                $data->in_job=abs($data->in_job-1);
                $data->save();
                return json(["status"=>1]);
            }
            return json(['status'=>0]);
        }
        /**
         * 获取技师的业绩
         */
        public static function get_yeji($job_number){
            $so = \app\api\model\Serviceorder::all(['job_number'=>$job_number]);
            $invited = \app\api\model\Technician::all(['inviter'=>$job_number]);
            $price = 0;
            $come_frome_other = 0;
            $lost = 0;
            if($so){
                foreach($so as $svod){
                    $item=\app\api\model\Servicetype::get(['ID'=>$svod->item_id]);
                    $skill = \app\api\model\Skill::get(['service_id'=>$svod->item_id,'job_number'=>$job_number]);
                    $lost+=$skill->lost/100;
                    $price+=$item->commission/100;
                }
            }
            if($invited){
                foreach($invited as $inv){
                    $data = self::get_yeji($inv->job_number);
                    $come_frome_other += $data['lost'];
                }
            }
            return [
                'job_number'=>$job_number,
                'status'=>1,
                'earn'=>$price,
                'come_from_other'=>$come_frome_other,
                'lost'=>$lost,
                'final_salary'=>$price+$come_frome_other-$lost
                ];
        }
        /**
         * 获取所有技师的业绩
         */
        public function get_all_yeji(){
            $data = [];
            $technicians = \app\api\model\Technician::all();
            foreach($technicians as $tech){
                array_push($data,self::get_yeji($tech->job_number));
            }
            return $data;
        }
    }