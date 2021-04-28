<?php
/**
 * raingad IM [ThinkPHP6]
 * @author xiekunyu <raingad@foxmail.com>
 */
namespace app\model;

use think\Model;
use think\facade\Db;
use app\model\{User,GroupUser};

class Message extends Model
{
    protected $pk="msg_id";
    protected static $fileType=['file','image'];

    // 添加聊天记录
    public static function addData($data){
       return Db::name('message')->insert($data);
    }

    // 更新消息状态
    public static function editData($update,$map){
        return Db::name('message')->where($map)->update($update);
    }

    // 查询聊天记录
    public static function getList($map,$where,$sort,$listRows,$pageSize){
        $list= Db::name('message')
        ->where($map)
        ->where($where)
        ->order($sort)
        ->paginate(['list_rows'=>$listRows,'page'=>$pageSize]);
        $data=[];
        $oss=config('oss');
        if($list){
            $listData=$list->toArray()['data'];
            foreach($listData as $k=>$v){
                $content=$v['content'];
                $preview='';
                if(in_array($v['type'],self::$fileType)){
                    $content=$oss['ossUrl'].$v['content'];
                    if($v['type']=='image'){
                        $preview=previewUrl($content);
                    }else{
                        $preview=previewUrl($content,2);
                    }
                    
                }
                $data[]=[
                    'id'=>$v['id'],
                    'status'=>"successd",
                    'type'=>$v['type'],
                    'sendTime'=>$v['create_time']*1000,
                    'content'=>$content,
                    'preview'=>$preview,
                    'is_read'=>$v['is_read'],
                    'is_group'=>$v['is_group'],
                    'toContactId'=>$v['to_user'],
                    'from_user'=>$v['from_user'],
                    'fileName'=>$v['file_name'],
                    'fileSize'=>$v['file_size']
                ];
            }
            $data=User::matchUser($data,true,'from_user','fromUser');
        }

        return $data;
     }

         //    发送消息
    public static function sendMessage($param){
        $toContactId=$param['toContactId'];
        $is_group=$param['is_group']?:0;
        if(!$is_group){
            $chat_identify=chat_identify($param['user_id'],$toContactId);
            $is_read=0;
        }else{
            $chat_identify=$toContactId;
            $toContactIdArr=explode('-',$toContactId);
            $toContactId=$toContactIdArr[1];
            $is_read=1;
        }
        $fileSzie=isset($param['file_size'])?$param['file_size']:'';
        $fileName=isset($param['file_name'])?$param['file_name']:'';
        $data=[
            'from_user'=>$param['user_id'],
            'to_user'=>$toContactId,
            'id'=>$param['id'],
            'content'=>$param['content'],
            'chat_identify'=>$chat_identify,
            'create_time'=>time(),
            'type'=>$param['type'],
            'is_group'=>$is_group,
            'is_read'=>$is_read,
            'file_size'=>$fileSzie,
            'file_name'=>$fileName,
        ];
        $message=new self();
        $message->update(['is_last'=>0],['chat_identify'=>$chat_identify]);
        $message->save($data);
        // 拼接消息推送
        $type=$is_group?'group':'simple';
        $sendData=$param;
        $sendData['status']='succeed';
        $sendData['is_read']=0;
        $sendData['sendTime']=(int)$sendData['sendTime'];
        //这里我也不知为啥单聊要把发送对象设置为自己的ID。
        if($is_group){
            $sendData['toContactId']=$param['toContactId'];
            // 将团队所有成员的未读状态+1
            GroupUser::editGroupUser([['group_id','=',$toContactId],['user_id','<>',$param['user_id']]],['unread'=>Db::raw('unread+1')]);
        }else{
            $sendData['toContactId']=$param['user_id'];
        }
        $sendData['fromUser']['id']=(int)$sendData['fromUser']['id'];
        $sendData['fileSize']=$fileSzie;
        $sendData['fileName']=$fileName;
        $oss=config('oss');
        if(in_array($sendData['type'],self::$fileType)){
            $sendData['content']=$oss['ossUrl'].$sendData['content'];
            if($sendData['type']=='image'){
                $pre=1;
            }else{
                $pre=2;
            }
            $sendData['preview']=previewUrl($sendData['content'],$pre);
        }
        // 向发送方发送消息
        wsSendMsg($toContactId,$type,$sendData,$is_group);
        return $sendData;
    }

}