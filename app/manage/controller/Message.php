<?php
/**
 * User raingad
 * Date 2024/11/17 17:24
 */


namespace app\manage\controller;


use app\BaseController;
use app\enterprise\model\{Message as MessageModel,User,Friend,Group};
use think\facade\Db;
class Message extends BaseController
{
    protected $fileType = ['file', 'image','video','voice'];
    // 获取聊天记录
    public function index()
    {
        $param = $this->request->param();
        $user_id=$param['user_id'] ?? 0;
        $toContactId=$param['toContactId'] ?? 0;
        $is_group=($param['is_group'] ?? 0) ? $param['is_group']-1 : -1;
        $map = [ 'status' => 1];
        if($user_id){
            if(!$toContactId){
                return warning(lang('system.parameterError'));
            }
            $chat_identify=chat_identify($param['user_id'],$param['toContactId']);
            $map['chat_identify'] = $chat_identify;
        }
        if($is_group>=0){
            $map['is_group']=$is_group;
        }
        $type = isset($param['type']) ? $param['type'] : '';
        $where = [];
        if ($type && $type != "all") {
            $map['type'] = $type;
        } else {
            $where[] = ['type', 'not in', ['event','admin_notice','webrtc']];
        }
        $keywords = isset($param['keywords']) ? $param['keywords'] : '';
        if ($keywords && in_array($type, ['text', 'all'])) {
            $where[] = ['content', 'like', '%' . $keywords . '%'];
            $where[] = ['type', '=', 'text'];
        }
        $listRows = $param['limit'] ?: 20;
        $pageSize = $param['page'] ?: 1;
        $last_id = $param['last_id'] ?? 0;
        if($last_id){
            $where[]=['msg_id','<',$last_id];
        }
        $list = MessageModel::getList($map, $where, 'msg_id desc', $listRows, $pageSize);
        $data = $this->recombileMsg($list);
        return success('', $data, $list->total(),$list->currentPage());
    }

    protected function recombileMsg($list,$isPagination=true)
    {
        $data = [];
        if ($list) {
            $listData = $isPagination ? $list->toArray()['data'] : $list;
            $userList = User::matchUser($listData, true, 'from_user', 120);
            foreach ($listData as $k => $v) {
                $content = str_encipher($v['content'],false);
                $preview = '';
                $ext='';
                if (in_array($v['type'], $this->fileType)) {
                    $content = getFileUrl($content);
                    $preview = previewUrl($content);
                    $ext=getExtUrl($content);
                }
                $fromUser = $userList[$v['from_user']];
                $toContactId=$v['is_group'] ==1 ?  'group-'.$v['to_user'] : $v['to_user'];
                $atList=($v['at'] ?? null) ? explode(',',$v['at']): [];
                if($v['is_group']==0){
                    $toUser=User::where(['user_id'=>$v['to_user']])->field(User::$defaultField)->find() ?? [];
                    if($toUser){
                        $toUser=[
                            'name'=>$toUser['realname']
                        ];
                    }
                    
                }else{
                    $toUser=Group::where(['group_id'=>$v['to_user']])->find();
                    if($toUser){
                        $toUser=[
                            'name'=>$toUser['name']
                        ];
                    }
                }
                $data[] = [
                    'msg_id' => $v['msg_id'],
                    'id' => $v['id'],
                    'status' => "succeed",
                    'type' => $v['type'],
                    'sendTime' => $v['create_time'] * 1000,
                    'create_time' => is_string($v['create_time']) ? $v['create_time'] : date('Y-m-d H:i:s',$v['create_time']),
                    'content' => $content,
                    'preview' => $preview,
                    'download' => $v['file_id'] ? getMainHost().'/filedown/'.encryptIds($v['file_id']) : '',
                    'is_read' => $v['is_read'],
                    'is_group' => $v['is_group'],
                    'at' => $atList,
                    'toContactId' => $toContactId,
                    'from_user' => $v['from_user'],
                    'file_id' => $v['file_id'],
                    'file_cate' => $v['file_cate'],
                    'fileName' => $v['file_name'],
                    'fileSize' => $v['file_size'],
                    'fromUser' => $fromUser,
                    'toUser' => $toUser,
                    'extUrl'=>$ext,
                    'extends'=>is_string($v['extends'])?json_decode($v['extends'],true) : $v['extends']
                ];
            }
        }
        return $data;
    }

    // 获取某个联系人的好友列表
    public function getContacts(){
        $param = $this->request->param();
        $user_id=$param['user_id'] ?? 0;
        if(!$user_id){
            return warning(lang('system.parameterError'));
        }
        $config=$this->globalConfig;
        $listRows = $param['limit'] ?: 20;
        $pageSize = $param['page'] ?: 1;
        $keywords = $param['keywords'] ?: '';
        $where=[['status','=',1]];
        if($keywords){
            $where[] = ['realname', 'like', '%' . $keywords . '%'];
        }
        $hasConvo=$param['hasConvo'] ?? 0;
        if($hasConvo){
            // 查询最近的联系人
            $map1 = [['to_user', '=', $user_id], ['is_last', '=', 1], ['is_group', '=', 0]];
            $map2 = [['from_user', '=', $user_id], ['is_last', '=', 1], ['is_group', '=', 0]];
            $msgField = 'from_user,to_user,content as lastContent,create_time as lastSendTime,chat_identify,type,del_user';
            $lasMsgList = Db::name('message')
                ->field($msgField)
                ->whereOr([$map1, $map2])
                ->order('create_time desc')
                ->select();
            $ids1=\utils\Arr::arrayToString($lasMsgList,'from_user',false);
            $ids2=\utils\Arr::arrayToString($lasMsgList,'to_user',false);
            $ids=array_merge($ids1,$ids2);
            $userList = array_diff($ids, [$user_id]);
            $where[]=['user_id','in',$userList];
        }else{
             // 如果是社区模式，就只查询的好友，如果是企业模式，就查询所有用户
            if($config['sysInfo']['runMode']==1){
                $where[]=['user_id','<>',$user_id];
            }else{
                $friendList = Friend::getFriend(['create_user' => $user_id,'status'=>1]);
                $userList = array_keys($friendList);
                $where[]=['user_id','in',$userList];
            }
        }
        
        $list = User::where($where)->field(User::$defaultField)->paginate(['list_rows'=>$listRows,'page'=>$pageSize]);
        $data=[];
        if($list){
            $data=$list->toArray()['data'];
            foreach ($data as $k => $v) {
                $data[$k]['avatar'] = avatarUrl($v['avatar'], $v['realname'], $v['user_id'], 120);
                $data[$k]['id'] = $v['user_id'];
            }
        }
        return success('',$data,$list->total(),$list->currentPage());
    }

    // 消息处理
    public function dealMsg(){
        $param = $this->request->param();
        $id = $param['id'];
        $message = MessageModel::where(['id' => $id])->find();
        if ($message) {
            $dealType=$param['dealType'] ?? 0;
            $content=$message['content'] ?? '';
            if($dealType==1){
                MessageModel::where(['id' => $id])->delete();
                 // 如果是最后一条消息，需要将上一条设置为最后一条
                if($message['is_last']){
                    MessageModel::where(['chat_identify'=>$message['chat_identify']])->order('msg_id desc')->limit(1)->update(['is_last'=>1]);
                }
                $action='delMessage';
            }else{
                $content=str_encipher(lang('im.forbidMsg'),true);
                MessageModel::where(['id' => $id])->update(['content'=>$content,'type'=>'text']);
                $action='updateMessage';
            }
            $toContactId = $message['to_user'];
            if ($message['is_group'] == 1) {
                $toContactId = explode('-', $message['chat_identify'])[1];
            }
            $data=[
                'id'=>$message['id'],
                'type'=>"text",
                'content'=>str_encipher($content,false),
            ];
            wsSendMsg($toContactId, $action, $data, $message['is_group']); 
            return success('');
        } else {
            return warning(lang('im.exist'));
        }
    }
}