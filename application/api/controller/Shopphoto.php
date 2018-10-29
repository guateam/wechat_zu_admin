<?php 
    namespace app\api\controller;
    use think\Controller;
    use \app\api\model\Shopphoto as UserModel;
    class Shopphoto extends Controller{

        public function get_all(){
            $all = UserModel::all();
            for($i=0;$i<count($all);$i++){
                $all[$i]->time = date("Y-m-d H:i:s",$all[$i]->time);
            }
            return $all;
        }

        public function get_by_id($id){
            $all = UserModel::all(['shop_id'=>$id]);
            $result=[];
            for($i=0;$i<count($all);$i++){
                $all[$i]->time = date("Y-m-d H:i:s",$all[$i]->time);
                $name = \app\api\model\Servicetype::get(['ID'=>$all[$i]->theme]);
                if(!$name)$name = '主页';
                array_push($result,[
                    'dir'=>$all[$i]->dir,
                    'time'=>$all[$i]->time,
                    'theme'=>$all[$i]->theme,
                    'theme_name'=>$name
                ]);
            }
            return $result;
        }
        public function change_theme($url,$id){
            $pic = UserModel::get(['dir'=>$url]);
            if($pic){
                $pic->theme = $id;
                $pic->save();
            }
            return json(['status'=>1]);
        }


        public function unlink_p($dir,$shop_id){
            $pic = \app\api\model\Shopphoto::get(['shop_id'=>$shop_id,'dir'=>$dir]);
            $pic->delete();
            unlink($_SERVER['DOCUMENT_ROOT'].$dir);
            return json(['status'=>1]);
        }


        public function upload($shop_id){
            $dir = $_SERVER['DOCUMENT_ROOT']."/photo/";
            $save_dir = "/photo/";
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
                    if (file_exists( $dir . $_FILES["file"]["name"]))
                    {
                        return json_encode(["state"=>'文件已经存在']);
                    }
                    else
                    {
                        $tm = date("ymdhis",time());
                        $sv = $save_dir.$rnd_str.$tm.$_FILES["file"]["name"];
                        $tm=$dir.$rnd_str.$tm.$_FILES["file"]["name"];
                        //$tc = UserModel::get(["job_number"=>$job_number]);
                        // 如果 upload 目录不存在该文件则将文件上传到 upload 目录下

                        move_uploaded_file($_FILES["file"]["tmp_name"],$tm );
                        $photo = new \app\api\model\Shopphoto(['shop_id'=>$shop_id,'dir'=>$sv,'time'=>time()]);
                        $photo->save();
                        return json(["state"=>1,'url'=>$sv]);
                        
                    }
                }

            }
            else
            {
                return json(["state"=>"格式错误:".$_FILES["file"]["type"]]);
            }
        }
    }