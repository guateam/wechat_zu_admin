<?php 
    namespace app\api\controller;
    use think\Controller;
    use \app\api\model\Privateroom as Room;
    class Privateroom extends Controller{
        /**
         * 获取包厢列表
         * 2018-8-24 创建 张煜
         */
        public function getroomlist(){
            $roomlist=Room::all();
            $data=[];
            foreach($roomlist as $value){
                $item=[
                    'ID'=>$value->ID,
                    'name'=>$value->name,
                    'roomid'=>$value->room_id,
                    'capacity'=>$value->capacity,
                    'servicetype'=>$value->service_type,
                    'servicename'=>\app\api\controller\Servicetype::getservicename($value->service_type)
                ];
                array_push($data,$item);
            }
            return $data;
        }
        /**
         * 添加包厢
         * 2018-8-24 创建 张煜
         * @param mixed $name 房间名称
         * @param mixed $roomid 房间号
         * @param int $capacity 房间容量
         * @param mixed $servicetype 房间可提供的服务类型
         */
        public function add($name,$roomid,$capacity,$servicetype){
            $room=new Room();
            $room->data([
                'name'=>$name,
                'room_id'=>$roomid,
                'capacity'=>$capacity,
                'service_type'=>$servicetype
            ]);
            $room->save();
            return 1;
        }
        /**
         * 清除包厢
         * 2018-8-24 创建 张煜
         * @param int $id 房间内部ID
         */
        public function delete($id){
            $room = Room::get(['ID'=>$id]);
            if($room){
                $room->delete();
                return 1;
            }
            return 0;
        }
        /**
         * 批量清除包厢
         * 2018-8-24 创建 张煜
         * @param array $idlist 房间内部ID数组
         */
        public function deletelist($idlist){
            foreach($idlist as $id){
                $this->delete($id);
            }
            return 1;
        }
        /**
         * 编辑房间信息
         * 2018-8-24 创建 张煜
         * @param int $id 房间内部id
         * @param mixed $name 房间名称
         * @param mixed $roomid 房间号
         * @param int $capacity 房间容量
         * @param mixed $servicetype 房间可提供的服务类型
         */
        public function edit($id,$name,$roomid,$capacity,$servicetype){
            $room = Room::get(['ID'=>$id]);
            if($room){
                $room->data([
                    'name'=>$name,
                    'room_id'=>$roomid,
                    'capacity'=>$capacity,
                    'service_type'=>$servicetype
                ]);
                $room->save();
                return 1;
            }
            return 0;
        }
        /**
         * 获取单个房间信息
         * 2018-8-24 创建 张煜
         * @param int $id 房间内部id
         */
        public function getroom($id){
            $room=Room::get(['ID'=>$id]);
            $data=[];
            if($room){
                $data=[
                    'ID'=>$room->ID,
                    'name'=>$room->name,
                    'roomid'=>$room->room_id,
                    'capacity'=>$room->capacity,
                    'servicetype'=>$room->service_type
                ];
            }
            return $data;
        }
    }