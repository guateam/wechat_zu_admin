<?php 
    namespace app\api\controller;
    use think\Controller;
    use \app\api\model\Itemtype as Item;
    use think\Db;
    class Itemtype extends Controller{
        /**
         * 获取商品价格
         * 2018-8-26 创建 袁宜照
         * @param int $id 商品id
         */
        public static function getitemprice($id){
            $item= Item::get(['ID'=>$id]);
            if($item){
                return $item->price;
            }
            return 0;
        }

        public function get_tea(){
            $item = Db::query("select * from item_type");
            if($item){
                return $item;
            }
            return false;
        }
        
        public function get_name($id){
            $item = Db::query("select * from item_type where `ID`='$id'");
            if($item){
                return $item[0]['name'];
            }
            return false;
        }

        public function delete($ids){
            foreach( $ids as $id){
                Db::query("delete from item_type where `ID` = '$id' ");
            }
            return json(['status'=>1]);
        }

        public function add($name,$price,$commission,$info,$img){
            $result  =  Db::query("insert into item_type (`name`,`info`,`image`,`price`,`commission`) values ('$name','$info','$img','$price','$commission')");
            return json(['status'=>1]);
        }

        public function update($id,$name,$price,$commission,$info,$img){
            if($img == ''){
                Db::query("update item_type set `name`='$name',`price`='$price',`commission`='$commission',`info`='$info' where `ID`='$id' ");
            }
            else
            {
                Db::query("update item_type set `name`='$name',`price`='$price',`commission`='$commission',`info`='$info',`image`='$img'  where `ID`='$id' ");
            } 
            return json(['status'=>1]);
        }

        /**
         * 创建时为茶艺上传缩略图
         */
        public function upload($name,$price,$commission,$info,$id=0){
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


            //编辑茶艺时候，如果没有更新图片
            if($_FILES['image']['type'] == "" && $id !=0){
                self::update($id,$name,$price,$commission,$info,"");
                return json(['status'=>1]);
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
                    // echo "上传文件名: " . $_FILES["file"]["name"] . "<br>";
                    // echo "文件类型: " . $_FILES["file"]["type"] . "<br>";
                    // echo "文件大小: " . ($_FILES["file"]["size"] / 1024) . " kB<br>";
                    // echo "文件临时存储的位置: " . $_FILES["file"]["tmp_name"];

                    // 判断当期目录下的 upload 目录是否存在该文件
                    // 如果没有 upload 目录，你需要创建它，upload 目录权限为 777
                    if (file_exists( $dir . $_FILES["image"]["name"]))
                    {
                        return json_encode(["state"=>'文件已经存在']);
                    }
                    else
                    {
                        $tm = date("ymdhis",time());
                        $sv = $save_dir.$rnd_str.$tm.$_FILES["image"]["name"];
                        $tm=$dir.$rnd_str.$tm.$_FILES["image"]["name"];

                        // 如果 upload 目录不存在该文件则将文件上传到 upload 目录下
                        move_uploaded_file($_FILES["image"]["tmp_name"],$tm );

                        //如果是新增茶艺
                        $svtp = "";
                        if($id == 0){
                            $svtp = self::add($name,$price,$commission,$info,$sv);
                        }else{
                            $svtp = self::update($id,$name,$price,$commission,$info,$sv);
                        }

                        return json_encode(["state"=>1,'url'=>$sv]);
                    }
                }

            }else{
               return json_encode(["state"=>"格式错误:".$_FILES["image"]["type"]]);
            }
        }
    }