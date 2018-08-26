<?php 
    namespace app\api\controller;
    use think\Controller;
    use \app\api\model\Itemtype as Item;
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

    }