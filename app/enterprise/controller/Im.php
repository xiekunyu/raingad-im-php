<?php

namespace app\enterprise\controller;

use app\BaseController;
use think\facade\Request;
use think\facade\Db;
use app\enterprise\model\{User, Message, GroupUser, Friend};
use Exception;

class Im extends BaseController
{
    protected $fileType = ['file', 'image','video','voice'];
    // 获取联系人列表
    public function getContacts()
    {
        $data = User::getUserList([['status', '=', 1], ['user_id', '<>', $this->userInfo['user_id']]], $this->userInfo['user_id']);
        return success('', $data);
    }


    //发送消息
    public function sendMessage()
    {
        $param = $this->request->param();
        $param['user_id'] = $this->userInfo['user_id'];
        $data = Message::sendMessage($param);
        if ($data) {
            return success('', $data);
        } else {
            return error('发送失败');
        }
    }

    // 获取聊天记录
    public function getMessageList()
    {
        $param = $this->request->param();
        $is_group = isset($param['is_group']) ? $param['is_group'] : 0;
        // 设置当前聊天消息为已读
        $chat_identify = $this->setIsRead($is_group, $param['toContactId']);
        $type = isset($param['type']) ? $param['type'] : '';
        $map = ['chat_identify' => $chat_identify, 'status' => 1, 'is_group' => $is_group];
        $where = [];
        if ($type && $type != "all") {
            $map['type'] = $type;
        } else {
            if (isset($param['type'])) {
                $where[] = ['type', '<>', 'event'];
            }
        }
        $keywords = isset($param['keywords']) ? $param['keywords'] : '';
        if ($keywords && in_array($type, ['text', 'all'])) {
            $where[] = ['content', 'like', '%' . $keywords . '%'];
        }
        $listRows = input('listRows') ?: 20;
        $pageSize = input('pageSize');
        $list = Message::getList($map, $where, 'msg_id desc', $listRows, $pageSize);
        $data = $this->recombileMsg($list);
        // 如果是消息管理器则不用倒序
        if (!isset($param['type'])) {
            $data = array_reverse($data);
        }
        return success('', $data, $list->total());
    }

    protected function recombileMsg($list)
    {
        $data = [];
        $ossUrl = config('oss.ossUrl') ?: Request::domain() . '/';
        $userInfo = $this->userInfo;
        if ($list) {
            $listData = $list->toArray()['data'];
            $userList = User::matchUser($listData, true, 'from_user', 120);
            foreach ($listData as $k => $v) {
                // 屏蔽已删除的消息
                if ($v['del_user']) {
                    $delUser = explode(',', $v['del_user']);
                    if (in_array($userInfo['user_id'], $delUser)) {
                        unset($list[$k]);
                        continue;
                        // $v['type']="event";
                        // $v['content']="删除了一条消息";
                    }
                }
                $content = $v['content'];
                $preview = '';
                if (in_array($v['type'], $this->fileType)) {
                    $content = $ossUrl . $v['content'];
                    $preview = previewUrl($content);
                }
                $fromUser = $userList[$v['from_user']];
                // 处理撤回的消息
                if ($v['type'] == "event") {
                    if ($v['from_user'] == $userInfo['user_id']) {
                        $content = "你" . $v['content'];
                    } elseif ($v['is_group'] == 1) {
                        $content = $fromUser['realname'] . $v['content'];
                    } else {
                        $content = "对方" . $v['content'];
                    }
                }
                $data[] = [
                    'msg_id' => $v['msg_id'],
                    'id' => $v['id'],
                    'status' => "successd",
                    'type' => $v['type'],
                    'sendTime' => $v['create_time'] * 1000,
                    'content' => $content,
                    'preview' => $preview,
                    'is_read' => $v['is_read'],
                    'is_group' => $v['is_group'],
                    'toContactId' => $v['to_user'],
                    'from_user' => $v['from_user'],
                    'fileName' => $v['file_name'],
                    'fileSize' => $v['file_size'],
                    'fromUser' => $fromUser,
                    'extends'=>is_string($v['extends'])?json_decode($v['extends'],true) : $v['extends']
                ];
            }
        }
        return $data;
    }

    // 设置当前窗口的消息默认为已读
    public function setMsgIsRead()
    {
        $param = $this->request->param();
        $this->setIsRead($param['is_group'], $param['toContactId']);
        if (!$param['is_group']) {
            wsSendMsg($param['fromUser'], 'isRead', $param['messages'], 0);
        }
        return success('');
    }

    // 设置消息已读
    protected function setIsRead($is_group, $to_user)
    {
        if ($is_group) {
            $chat_identify = $to_user;
            $toContactId = explode('-', $to_user)[1];
            // 更新群里面我的所有未读消息为0
            GroupUser::editGroupUser(['user_id' => $this->userInfo['user_id'], 'group_id' => $toContactId], ['unread' => 0]);
        } else {
            $chat_identify = chat_identify($this->userInfo['user_id'], $to_user);
            // 更新我的未读消息为0
            Message::update(['is_read' => 1], [['chat_identify', '=', $chat_identify], ['to_user', '=', $this->userInfo['user_id']]]);
        }
        return $chat_identify;
    }

    // 聊天设置
    public function setting()
    {
        $param = $this->request->param();
        if ($param) {
            User::where(['user_id' => $this->userInfo['user_id']])->update(['setting' => json_encode($param)]);
            return success('');
        }
        return warning('设置失败');
    }

    // 撤回消息
    public function undoMessage()
    {
        $param = $this->request->param();
        $id = $param['id'];
        $message = Message::where(['id' => $id])->find();
        if ($message) {
            $text = "撤回了一条消息";
            $fromUserName = "对方";
            $toContactId = $message['to_user'];
            if ($message['is_group'] == 1) {
                $fromUserName = $this->userInfo['realname'];
                $toContactId = $message['chat_identify'];
            }
            $message->content = $text;
            $message->type = 'event';
            $message->is_undo = 1;
            $message->create_time = time();
            $message->save();
            $data = $message->toArray();
            $data['content'] = $fromUserName . $text;
            wsSendMsg($toContactId, 'undoMessage', $data, $data['is_group']);
            return success('');
        } else {
            return warning();
        }
    }

    // 删除消息
    public function removeMessage()
    {
        $param = $this->request->param();
        $id = $param['id'];
        $map = ['id' => $id];
        $message = Message::where($map)->find();
        if ($message) {
            $message->del_user = $this->userInfo['user_id'];
            if ($message['is_group'] == 1) {
                if ($message['del_user']) {
                    $message->del_user .= ',' . $this->userInfo['user_id'];
                }
            } else {
                if ($message['del_user'] > 0) {
                    $message->where($map)->delete();
                    return success('删除成功！');
                }
            }
            $message->save();
            return success('');
        } else {
            return warning('');
        }
    }

    // 消息免打扰
    public function isNotice()
    {
        $param = $this->request->param();
        $user_id = $this->userInfo['user_id'];
        $id = $param['id'];
        if ($param['is_group'] == 1) {
            $group_id = explode('-', $param['id'])[1];
            GroupUser::update(['is_notice' => $param['is_notice']], ['user_id' => $user_id, 'group_id' => $group_id]);
        } else {
            $map = ['create_user' => $user_id, 'friend_user_id' => $id, 'is_group' => 0];
            $friend = Friend::where($map)->find();
            try {
                if ($friend) {
                    $friend->is_notice = $param['is_notice'];
                    $friend->save();
                } else {
                    $info = [
                        'create_user' => $user_id,
                        'friend_user_id' => $id,
                        'is_group' => 0,
                        'is_notice' => $param['is_notice']
                    ];
                    Friend::create($info);
                }
                return success('');
            } catch (Exception $e) {
                return error($e->getMessage());
            }
        }

        return success('');
    }

    // 设置聊天置顶
    public function setChatTop()
    {
        $param = $this->request->param();
        $user_id = $this->userInfo['user_id'];
        $is_group = $param['is_group'] ?: 0;
        $id = $param['id'];
        $map = ['create_user' => $user_id, 'friend_user_id' => $id, 'is_group' => $is_group];
        $friend = Friend::where($map)->find();
        try {
            if ($friend) {
                $friend->is_top = $param['is_top'];
                $friend->save();
            } else {
                $info = [
                    'create_user' => $user_id,
                    'friend_user_id' => $id,
                    'is_group' => $is_group,
                    'is_top' => $param['is_top']
                ];
                Friend::create($info);
            }
            return success('');
        } catch (Exception $e) {
            return error($e->getMessage());
        }
    }
    
    // 删除聊天
    public function delChat()
    {
        $param = $this->request->param();
        $user_id = $this->userInfo['user_id'];
        $is_group = $param['is_group'] ?: 0;
        $id = $param['id'];
        if(!$is_group){
            $chat_identify=chat_identify($user_id,$id);
        }else{
            return success('');
        }
        Message::where(['chat_identify' => $chat_identify])->update(['is_last' => 0]);
        return success('');
    }
}
