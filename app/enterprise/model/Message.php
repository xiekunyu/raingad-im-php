<?php
/**
 * raingad IM [ThinkPHP6]
 * @author xiekunyu <raingad@foxmail.com>
 */
namespace app\enterprise\model;

use app\BaseModel;
use think\facade\Db;
use think\facade\Request;
class Message extends BaseModel
{
    protected $pk="msg_id";
    protected $json      = ["extends"];
    protected $jsonAssoc = true;
    protected static $fileType=['file','image','video','voice'];

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
        return $list;
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
            if(!self::nospeak($toContactId,$param['user_id'])){
                return shutdown("群聊已禁言！");
            }
        }
        $fileSzie=isset($param['file_size'])?$param['file_size']:'';
        $fileName=isset($param['file_name'])?$param['file_name']:'';
        $ossUrl=getDiskUrl();
        // 如果是转发图片文件的消息，必须把域名去除掉
        $content=$param['content'];
        if(in_array($param['type'],self::$fileType)){
            if(strpos($param['content'],$ossUrl)!==false){
                $content=str_replace($ossUrl,'',$param['content']);
            }
        }
        $data=[
            'from_user'=>$param['user_id'],
            'to_user'=>$toContactId,
            'id'=>$param['id'],
            'content'=>str_encipher($content,true),
            'chat_identify'=>$chat_identify,
            'create_time'=>time(),
            'type'=>$param['type'],
            'is_group'=>$is_group,
            'is_read'=>$is_read,
            'file_id'=>$param['file_id'] ?? 0,
            "file_cate"=>$param['file_cate'] ?? 0,
            'file_size'=>$fileSzie,
            'file_name'=>$fileName,
            'extends'=>($param['extends'] ?? null) ? $param['extends'] : null,
        ];
        $message=new self();
        $message->update(['is_last'=>0],['chat_identify'=>$chat_identify]);
        $message->save($data);
        // 拼接消息推送
        $type=$is_group?'group':'simple';
        $sendData=$param;
        $sendData['status']='succeed';
        $sendData['msg_id']=$message->msg_id;
        $sendData['is_read']=0;
        $sendData['to_user']=$toContactId;
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
        
        if(in_array($sendData['type'],self::$fileType)){
            $sendData['content']=getFileUrl($sendData['content']);
            if($sendData['type']=='image'){
                $pre=1;
            }else{
                $pre=2;
            }
            $sendData['preview']=previewUrl($sendData['content'],$pre);
        }
        if($is_group==0){
            $toContactId=[$toContactId,$param['user_id']];
        }
        $sendData['toUser']=$param['toContactId'];
        // 向发送方发送消息
        wsSendMsg($toContactId,$type,$sendData,$is_group);
        
        $sendData['toContactId']=$param['toContactId'];
        return $sendData;
    }

    // 群禁言
    public static function nospeak($group_id,$user_id){
        $group=Group::find($group_id);
        if($group->owner_id==$user_id){
            return true;
        }
        if($group->setting){
            $setting=json_decode($group->setting,true);
            $nospeak=isset($setting['nospeak'])?$setting['nospeak']:0;
            $role=GroupUser::where(['group_id'=>$group_id,'user_id'=>$user_id])->value('role');
            if($nospeak==1 && $role>2){
                return false;
            }elseif($nospeak==2 && $role!=1){
                return false;
            }
        }
        return true;
    }

    public function matchData(){

    }

}