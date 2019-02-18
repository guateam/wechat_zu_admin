<?php 
    namespace app\api\controller;
    use think\Controller;
    use \app\api\model\Servicetype as Service;
    class Servicetype extends Controller{
        /**
         * 获取服务总数
         * 2018-8-27    创建   赖品钊
         * 
         */
        public function count_all(){
            $data = Service::all();
            return count($data);
        }
        /**
         * 获取服务种类
         * 2018-8-24 创建 张煜
         * @param int $id 服务所属id
         */
        public static function getservicename($id){
            $service= Service::get(['ID'=>$id]);
            if($service){
                return $service->name;
            }
            return "未知";
        }
        /**
         * 获取服务列表
         * 2018-8-24 创建 张煜
         */
        public static function getservicelist(){
            $list = Service::all();
            $data=[];
            foreach($list as $value){
                $item=[
                    'id'=>$value->ID,
                    'name'=>$value->name,
                    'check'=>false,
                ];
                array_push($data,$item);
            }
            return $data;
        }
        /**
         * 获取服务基础价格
         * 2018-8-26 创建 袁宜照
         * @param int $id 服务id
         */
        public static function getserviceprice($id){
            $service= Service::get(['ID'=>$id]);
            if($service){
                return $service->price;
            }
            return 0;
        }
        /**
         * 获取服务折扣情况
         * 2018-8-26 创建 袁宜照
         * @param int $id 服务id
         */
        public static function getservicediscount($id){
            $service= Service::get(['ID'=>$id]);
            if($service){
                return $service->discount;
            }
            return "未知";
        }
        /**
         * 获取所有服务信息
         * 2018-8-27 创建 袁宜照
         * @param int $id 服务id
         */
        public static function get_all(){
            $service= Service::all();
            return $service;
        }
        /**
         * 修改特定服务的价格
         * 2018-8-27 创建 袁宜照
         * @param int $id       服务id
         * @param int $price    服务价格，单位：分
         * 
         */
        public static function update_price($id,$value){
            $service= Service::get(['ID'=>$id]);
            if($service){
                $service->price = $value;
                $service->save();
                return json(['status'=>1]);
            }
            return json(['status'=>0]);
        }
        /**
         * 修改特定服务的折扣
         * 2018-8-27 创建 袁宜照
         * @param int $id       服务id
         * @param int $ratio    折扣比例，单位：百分比
         * 
         */
        public static function update_discount($id,$value){
            $service= Service::get(['ID'=>$id]);
            if($service){
                $service->discount = $value;
                $service->save();
                return json(['status'=>1]);
            }
            return json(['status'=>0]);
        }
        /**
         * 修改特定服务的服务时长
         * 2018-8-27 创建 袁宜照
         * @param int $id       服务id
         * @param int $dura     时长，单位：分钟
         * 
         */
        public static function update_duration($id,$value){
            $service= Service::get(['ID'=>$id]);
            if($service){
                $service->duration = $value;
                $service->save();
                return json(['status'=>1]);
            }
            return json(['status'=>0]);
        }
        public static function update_dir($id,$value){
            $service= Service::get(['ID'=>$id]);
            //未填写服务名，默认设置为空
            if($value == ""){
                $service->dir = $value;
                $service->save();
                return json(['status'=>1]);
            }

            $to_service = Service::get(['name'=>$value]);
            $new_dir = "";
            //根据填写的服务名设置跳转链接
            if($to_service){
                $new_dir="xiangmuxiangqing.html?id="+$to_service->ID;
            }else{
                return json(['status'=>0]);
            }
            if($service){
                $service->dir = $new_dir;
                $service->save();
                return json(['status'=>1]);
            }
            return json(['status'=>0]);
        }
        public function update_have_level($id){
            $service= Service::get(['ID'=>$id]);
            if($service){
                if($service->have_level == 0)
                {
                    $service->have_level = 1;
                }
                else
                {
                    $service->have_level = 0;
                }
                $service->save();
                return json(['status'=>1]);
            }
            return json(['status'=>0]);
        }
        /**
         * 修改特定服务的提成比例
         * 2018-8-27 创建 袁宜照
         * @param int $id       服务id
         * @param int $ratio    提成比例，单位：百分比
         * 
         */
        public static function update_commission($id,$value){
            $service= Service::get(['ID'=>$id]);
            if($service){
                $service->commission = $value;
                $service->save();
                return json(['status'=>1]);
            }
            return json(['status'=>0]);
        }
        /**
         * 为服务上传缩略图
         */
        public function upload($name,$duration,$price,$commission,$commission2,$info,$invite_income){
            $dir = $_SERVER['DOCUMENT_ROOT']."/photo/";
            $save_dir = "/photo/";
            $bg = false;
            if(isset($_POST['bg']))$bg = $_POST['bg'];
            $allowedExts = array("gif", "jpeg", "jpg", "png","PNG");
            $temp = explode(".", $_FILES["image"]["name"]);
            $extension = end($temp);        // 获取文件后缀名

            $dict=['a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u',
                    'v','w','x','y','z','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P',
                    'Q','R','S','T','U','V','W','X','Y','Z','1','2','3','4','5','6','7','8','9','0'];
            $rnd_str = "";
            for($i = 0;$i<7;$i++){
                $idx = rand(0,count($dict)-1);
                $rnd_str.=$dict[$idx];    
            }

            if ((
                ($_FILES["image"]["type"] == "image/jpeg") 
                ||  ($_FILES["image"]["type"] == "image/jpg")
                || ($_FILES["image"]["type"] == "image/x-png")
                || ($_FILES["image"]["type"] == "image/png"))   
            ){
                if ($_FILES["image"]["error"] > 0)
                {
                    return json_encode(["state"=>$_FILES["image"]["error"]]);
                }
                else
                {
                    // 判断当期目录下的 upload 目录是否存在该文件
                    // 如果没有 upload 目录，你需要创建它，upload 目录权限为 777
                    if (file_exists( $dir . $_FILES["image"]["name"]))
                    {
                        return json_encode(["state"=>'文件已经存在']);
                    }
                    else
                    {
                        $tm = date("ymdhis",time());
                        $sv = $save_dir.$rnd_str.$tm.'.'.$extension;
                        $tm=$dir.$rnd_str.$tm.'.'.$extension;;

                        // 如果 upload 目录不存在该文件则将文件上传到 upload 目录下
                        move_uploaded_file($_FILES["image"]["tmp_name"],$tm );
                        $svtp = self::add_service($name,$duration,$price,$commission,$commission2,$info,$invite_income,$sv);

                        return json_encode(["state"=>1,'url'=>$sv]);
                    }
                }

            }else{
               return json_encode(["state"=>"格式错误:".$_FILES["image"]["type"]]);
            }
        }
        /**
         * 删除指定id的服务
         * 2018-8-27    创建   赖品钊
         * @param array|string $id ID列表
         * 
         */
        public function delete_service($id=[]){
            $single_number;
            if(count($id)==0)return json(["status"=>1]);
            
            for($i=0;$i<count($id);$i++){
                $single_number=$id[$i];

                $data = Service::get(["id"=>$single_number]);
                if($data){
                    $data->delete();
                }else{
                    return json(["status"=>0]);
                }
            }
            foreach($id as $i){
                $dt =  \app\api\model\Skill::all(["service_id"=>$i]);
                foreach($dt as $sk){
                    $sk->delete();
                }
            }

            return json(["status"=>1]);
        }
        /**
         * 添加服务
         * 2018-8-27    创建   赖品钊
         * @param string $name                  服务名称
         * @param string $duration              基础时间
         * @param string $price                 价格
         * @param string $discount              折扣
         * @param string $commission            提成金额
         * @param string $info                  服务介绍
         * @param string $image                 服务介绍图
         * 
         */
        public static function add_service($name,$duration,$price,$commission,$commission2,$info,$invite_income,$image){

            $data = new Service(['name'=>$name,'duration'=>$duration,'price'=>$price,'market_price'=>$price,
                                    'invite_income'=>$invite_income,'commission'=>((int)$commission),
                                    'commission2'=>((int)$commission2),
                                    'info'=>$info,'image'=>$image]);
            $data->save();
            $data = Service::get(['name'=>$name]);
            if($data){
                $data->dir = "xiangmuxiangqing.html?id=".$data->ID;
                $data->save();
            }
            return json(['status'=>1]);
        }

        /**
         * 更新服务的详细信息
         */
        public function  update_info($id,$name,$procedure,$attention,$benefit,$price,$market_price,
        $duration,$commission,$commission2,$have_level,$index_show,$invite_income,$belong_to,$pai_commission,$pai_commission2){
            $data = \app\api\model\Servicetype::get(['ID'=>$id]);
            $have_new_img = true;
            if($data){

                if ($_FILES["image"]["error"] > 0){
                    $have_new_img = false;
                }
                if ((
                            ($_FILES["image"]["type"] != "image/jpeg") 
                            &&  ($_FILES["image"]["type"] != "image/jpg")
                            && ($_FILES["image"]["type"] != "image/x-png")
                            && ($_FILES["image"]["type"] != "image/png"))   
                ){
                    $have_new_img = false;
                }
                if($have_new_img){
                    $dir = $_SERVER['DOCUMENT_ROOT']."/photo/";
                    $save_dir = "/photo/";
                    $temp = explode(".", $_FILES["image"]["name"]);
                    $extension = end($temp);        // 获取文件后缀名
        
                    $dict=['a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u',
                            'v','w','x','y','z','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P',
                            'Q','R','S','T','U','V','W','X','Y','Z','1','2','3','4','5','6','7','8','9','0'];
                    $rnd_str = "";
                    for($i = 0;$i<7;$i++){
                        $idx = rand(0,count($dict)-1);
                        $rnd_str.=$dict[$idx];    
                    }
                    $tm = date("ymdhis",time());
                    $sv = $save_dir.$rnd_str.$tm.'.'.$extension;
                    $tm=$dir.$rnd_str.$tm.'.'.$extension;;

                    // 如果 upload 目录不存在该文件则将文件上传到 upload 目录下
                    move_uploaded_file($_FILES["image"]["tmp_name"],$tm );
                    $data->image = $sv;
                }

                $data->name = $name;
                $data->procedure = $procedure;
                $data->attention = $attention;
                $data->benefit = $benefit;
                $data->price = $price;
                $data->market_price = $market_price;
                $data->duration = $duration;
                $data->commission = $commission;
                $data->commission2 = $commission2;
                $data->have_level = $have_level;
                $data->index_show = $index_show;
                $data->invite_income = $invite_income;
                $data->belong_to = $belong_to;
                $data->pai_commission = $pai_commission;
                $data->pai_commission2 = $pai_commission2;
                $data->save();
                return json(['status'=>1]);
            }
            return json(['status'=>0]);
        }
    }