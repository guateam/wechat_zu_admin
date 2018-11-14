<?php 
    namespace app\api\controller;
    use think\Controller;
    use \app\api\model\Technician as UserModel;
    use think\Db;
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
                if($mobile!=""){
                    $repeat = UserModel::get(['phone_number'=>$mobile]);
                    if($repeat){
                        if($repeat->job_number !=$ori_job_number){
                            return 0;
                        }
                    }
                }
                if($job_number!=""){
                    $repeat = UserModel::get(['job_number'=>$job_number]);
                    if($repeat){
                        if($repeat->job_number !=$ori_job_number){
                            return -1;
                        }
                    }
                }
                if($idcard!=""){
                    $repeat = UserModel::get(['id_number'=>$idcard]);
                    if($repeat){
                        if($repeat->job_number !=$ori_job_number){
                            return -3;
                        }
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
        public function add_technician($name,$idcard,$birthday,$gender,$mobile,$job_number,$skill='',$describe='',$level,$invite,$type){
            if($skill=='' && $type == 1)return json(['status'=>-4]);

            $is_repeat = self::check_repeat($name,$mobile,$job_number,$idcard,"");
            if($is_repeat!=1)return json(["status"=>$is_repeat]);

            $data = new UserModel(['name'=>$name,'gender'=>$gender,'phone_number'=>$mobile,
                                    'job_number'=>$job_number,'birthday'=>$birthday,"id_number"=>$idcard,
                                    'entry_date'=>time(),'description'=>$describe,'in_job'=>1,'type'=>$type]);
            $data->save();
            if($skill != ''){
                for($i=0;$i<count($skill);$i++){
                    $skill_info = new \app\api\model\Skill(['job_number'=>$job_number,'level'=>$level,'service_id'=>$skill[$i],'extra_income'=>0]);
                    $skill_info->save();
                }
            }
            if($invite != ''){
                self::set_inviter($job_number,$invite);
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
        public function update_technician($ori_job_number,$name,$idcard,$birthday,$gender,
                                            $mobile,$job_number,$skill='',$describe='',$level='',$invite='',$type=1){

            $is_repeat = self::check_repeat($name,$mobile,$job_number,$idcard,$ori_job_number);
            if($is_repeat!=1)return json(["status"=>$is_repeat]);

            $data = UserModel::get(["job_number"=>$ori_job_number]);

            $data->type = $type;
            $data->name = $name;
            $data->gender = $gender;
            $data->phone_number=$mobile;
            $data->job_number=$job_number;
            $data->id_number = $idcard;
            $data->birthday=$birthday;
            $data->description = $describe;


            $skill_info = \app\api\model\Skill::all(['job_number'=>$ori_job_number]);
            foreach($skill_info as $it){
                $it->delete();
            }

            if($type == 1){
                foreach($skill as $sk){
                    $new_skill = new \app\api\model\Skill(['job_number'=>$job_number,'level'=>$level,'service_id'=>$sk,'extra_income'=>0]);
                    $new_skill->save();
                }
                $data->level = $level;
            }else{
                $data->level = "";
            }

            $data->save();
            self::set_inviter($job_number,$invite);
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
                $old_data = \app\api\model\Inviteship::get(['freshman_job_number'=>$job_number]);
                if(!$old_data){
                    $new_data = new \app\api\model\Inviteship();
                    $new_data->data([
                        'inviter_job_number'=>$inviter_job_number,
                        'freshman_job_number'=>$job_number,
                        'persentage'=>0
                    ]);
                    $new_data->save();
                }else{
                    $old_data->inviter_job_number = $inviter_job_number;
                    $old_data->save();
                }
                
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
            //该技师邀请的人
            $invited = \app\api\model\Inviteship::all(['inviter_job_number'=>$job_number]);
            //邀请该技师的人
            $self_p =  \app\api\model\Inviteship::get(['freshman_job_number'=>$job_number]);
            //收入
            $price = 0;
            //点钟数量
            $dian = 0;
            //排钟数量
            $pai = 0;
            //来自邀请的人的收入
            $come_frome_other = 0;
            //付给邀请自己的人的钱
            $lost = 0;

            if($so){
                foreach($so as $svod){
                    if($svod->clock_type == 1)$pai++;
                    else if($svod->clock_type == 2)$dian++;
                    $item=\app\api\model\Servicetype::get(['ID'=>$svod->item_id]);
                    $per = 0;
                    if($self_p)$per = $self_p->persentage/100;
                    $lost+= ($item->commission/100)*$per;
                    $price+=$item->commission/100;
                }
            }
            if($invited){
                foreach($invited as $inv){
                    $data = self::get_yeji($inv->freshman_job_number);
                    $come_frome_other += $data['lost'];
                }
            }
            return [
                'job_number'=>$job_number,
                'status'=>1,
                'pai'=>$pai,
                'dian'=>$dian,
                'earn'=>$price,
                'come_from_other'=>$come_frome_other,
                'lost'=>$lost,
                'final_salary'=>$price+$come_frome_other-$lost
                ];
        }

        public function get_inviter($job_number){
            $inviter = Db::query("select inviter_job_number from inviteship where freshman_job_number='".$job_number."'");
            if($inviter)
                return $inviter[0]['inviter_job_number'];
            else return "";
        }

        public function get_skill($job_number){
            $skill = Db::query("select B.name from skill A, skill_level B where A.job_number='$job_number' and A.level > 0 and B.ID=A.level limit 1");
            if($skill)
                return $skill[0]['name'];
            else return "";
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
        public function upload(){
            $dir = $_SERVER['DOCUMENT_ROOT']."/photo/";
            $save_dir = "/photo/";
            $job_number=$_POST['job_number'];
            $allowedExts = array("gif", "jpeg", "jpg", "png","PNG");
            $temp = explode(".", $_FILES["file"]["name"]);
            $extension = end($temp);        // 获取文件后缀名

            $dict=['a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u',
                    'v','w','x','y','z','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P',
                    'Q','R','S','T','U','V','W','X','Y','Z','1','2','3','4','5','6','7','8','9','0'];
            $rnd_str = "";
            for($i = 0;$i<7;$i++)
            {
                $idx = rand(0,count($dict)-1);
                $rnd_str.=$dict[$idx];    
            }

            if ((
                ($_FILES["file"]["type"] == "image/jpeg") 
                ||  ($_FILES["file"]["type"] == "image/jpg")
                || ($_FILES["file"]["type"] == "image/x-png")
                || ($_FILES["file"]["type"] == "image/png"))   
            )
            {
                if ($_FILES["file"]["error"] > 0)
                {
                    return json_encode(["state"=>$_FILES["file"]["error"]]);
                }
                else
                {
                    // 判断当期目录下的 upload 目录是否存在该文件
                    // 如果没有 upload 目录，你需要创建它，upload 目录权限为 777
                        $tm = date("ymdhis",time());
                        $sv = $save_dir.$rnd_str.$tm.$_FILES["file"]["name"];
                        $tm=$dir.$rnd_str.$tm.$_FILES["file"]["name"];
                        $tc = UserModel::get(["job_number"=>$job_number]);
                        // 如果 upload 目录不存在该文件则将文件上传到 upload 目录下
                        if($tc)
                        {
                            if($tc->photo){
                                unlink($_SERVER['DOCUMENT_ROOT'].$tc->photo);
                            }
                            move_uploaded_file($_FILES["file"]["tmp_name"],$tm );
                            //上传头像不需要视为技师的上传项目
                            // $tf = new \app\api\model\Technicianphoto();
                            // $tf->job_number = $job_number;
                            // $tf->img = $sv;
                            // $tf->save();
                            $tc->photo = $sv;
                            $tc->save();
                           return json_encode(["state"=>1,'url'=>$sv]);
                        }
                        else
                        {
                           return json_encode(["state"=>0]);
                        }
                }

            }
            else
            {
                return json_encode(["state"=>"格式错误:".$_FILES["file"]["type"]]);
            }
        }
    }