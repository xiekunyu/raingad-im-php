<?php
/**
 * raingad IM [ThinkPHP6]
 * @author xiekunyu <raingad@foxmail.com>
 */
namespace app\enterprise\model;

use app\BaseModel;
use think\facade\Db;
use think\facade\Cache;
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
        if($param['type']=='text'){
            // 接入聊天内容检测服务
            event('GreenText',['content'=>$param['content'],'service'=>"chat_detection"]);
        }
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
        $param['content']=$content;
        $atList=($param['at'] ?? null) ? array_map('intval', $param['at']): [];
        // 如果at里面有0，代表@所有人
        if($atList && in_array(0,$atList)){
            $atList=GroupUser::where([['group_id','=',$toContactId],['status','=',1],['user_id','<>',$param['user_id']]])->column('user_id');
        }
        $at=$atList ? implode(',',$atList) : null;
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
            'at'=>$at,
            'pid'=>$param['pid'] ?? 0,
            'extends'=>($param['extends'] ?? null) ? $param['extends'] : null,
        ];
        $message=new self();
        $message->update(['is_last'=>0],['chat_identify'=>$chat_identify]);
        $message->save($data);
        // 拼接消息推送
        $type=$is_group?'group':'simple';
        $sendData=$param;
        $sendData['status']='succeed';
        $sendData['at']=$atList;
        $sendData['msg_id']=$message->msg_id;
        $sendData['is_read']=0;
        $sendData['to_user']=$toContactId;
        $sendData['sendTime']=(int)$sendData['sendTime'];
        //这里单聊中发送对方的消息，对方是接受状态，自己是对方的联系人，要把发送对象设置为发送者的ID。
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
            $sendData['extUrl']=getExtUrl($sendData['content']);
            $sendData['download']= $sendData['file_id'] ? request()->domain().'/filedown/'.encryptIds($sendData['file_id']) : '';
        }
        if($is_group==0){
            $toContactId=[$toContactId,$param['user_id']];
        }
        $sendData['toUser']=$param['toContactId'];
        $user=new User();
        // 将聊天窗口的联系人信息带上，方便临时会话
        $sendData['contactInfo']=$user->setContact($sendData['toContactId'],$is_group,$sendData['type'],$sendData['content']);
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

    // 将消息中的@用户加入到atListQueue中
    public static function setAtread($messages,$user_id){
        foreach($messages as $k=>$v){
            if(!isset($v['at'])){
                continue;
            }
            if($v['at'] && in_array($user_id,$v['at'])){
               $atListQueue=Cache::get("atListQueue");
               $atListQueue[$v['msg_id']][]=$user_id;
               Cache::set("atListQueue",$atListQueue);
            }
        }
    }

}