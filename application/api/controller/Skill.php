<?php 
    namespace app\api\controller;
    use think\Controller;
    use \app\api\model\Skill as UserModel;
    use think\Db;

    class Skill extends Controller{
        /**
         * 获取指定技师的技能列表
         * 2018-8-24    创建   袁宜照
         * @param string $job_number 工号
         */
        public function get_skill($job_number){
            $skill_list = Db::query("select * from skill where job_number='$job_number'");
            $tech = Db::query("select busy from technician where job_number='$job_number'");

            $ctrl = new \app\api\controller\Servicetype();
            foreach($skill_list as $idx => $sk){
                $name = $ctrl->getservicename($sk['service_id']);
                $price = $ctrl->getserviceprice($sk['service_id']);
                if($name){
                    $skill_list[$idx]['name'] = $name;
                    $skill_list[$idx]['price'] = $price;
                    $skill_list[$idx]['busy'] = $tech[0]['busy'];
                }
            }
            return $skill_list;
        }
        public function get_skill_name($job_number){
            $skill_list = UserModel::all(['job_number'=>$job_number]);
            $ctrl = new \app\api\controller\Servicetype();
            $skillname = [];
            foreach($skill_list as $sk){
                $name = $ctrl->getservicename($sk->service_id);
                if($name){
                    array_push($skillname,$name);
                }
            }
            return $skillname;
        }
        /**
         * 获取指定技师的技能ID列表
         * 2018-8-24    创建   袁宜照
         * @param string $job_number 工号
         */
        public function get_skill_service_id($job_number){
            $skill_list = UserModel::all(['job_number'=>$job_number]);
            $data=[];
            foreach($skill_list as $sk)
            {
                array_push($data,$sk->service_id);
            }
            return $data;
        }

        public function get_all(){
            $skill = UserModel::all();
            $result = [];
            for($i=0;$i<count($skill);$i++){
                $name = \app\api\model\Skilllevel::get(['ID'=>$skill[$i]->level]);
                $sk_name = \app\api\model\Servicetype::get(['ID'=>$skill[$i]->service_id]);
                if($name){
                    array_push($result,[
                        'job_number'=>$skill[$i]->job_number,
                        'id'=>$skill[$i]->service_id,
                        'name'=>$name->name,
                        'sk_name'=>$sk_name->name,
                        'can_change'=>$sk_name->have_level,
                    ]);
                }else{
                    array_push($result,[
                        'job_number'=>$skill[$i]->job_number,
                        'id'=>$skill[$i]->service_id,
                        'name'=>"",
                        'sk_name'=>$sk_name->name,
                        'can_change'=>$sk_name->have_level,
                    ]);
                }
                
            }
            return $result;
        }

        public function change_lv($job_number,$service_id,$new_lv){
            $skill = UserModel::get(['job_number'=>$job_number,'service_id'=>$service_id]);
            $skill->level = $new_lv;
            $skill->save();
        }


    }