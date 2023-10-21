<?php

namespace app\enterprise\controller;

use app\BaseController;

use app\enterprise\model\{Friend as FriendModel,User};

class Friend extends BaseController
{
    // 好友申请列表
    public function index()
    {
        $param = $this->request->param();
        $map = [];
        $map[]=['is_invite','=',1];
        $isMine=$param['is_mine'] ?? 0;
        if($isMine){
            // 我发起的
            $map[]=['create_user','=',$this->uid];
        }else{
            // 我收到的
            $map[]=['friend_user_id','=',$this->uid];
        }
        $data=[];
        $model = new FriendModel();
        $list = $this->paginate($model->where($map)->order('friend_id desc'));
        if ($list) {
            $data = $list->toArray()['data'];
            $userList = User::matchUser($data, true, ['create_user','friend_user_id'], 120);
            foreach ($data as $k => $v) {
                $data[$k]['create_user_info'] = $userList[$v['create_user']] ?? [];
                $data[$k]['user_id_info'] = $userList[$v['friend_user_id']] ?? [];
                $data[$k]['is_group'] = 0;
            }
        }
        return success('', $data,$list->total(),$list->currentPage());
    }

    // 添加好友
    public function add()
    {
        $param = $this->request->param();
        $user_id=$param['user_id'] ?? 0;
        if($user_id==$this->uid){
            return warning('不能添加自己为好友');
        }
        $friend=FriendModel::where(['friend_user_id'=>$user_id,'create_user'=>$this->uid])->find();
        if($friend){
            if($friend->status==1){
                return warning('你们已经是好友了');
            }elseif($friend->status==2){
                return warning('你已经申请过了，请等待对方同意');
            }
        }
        $status=2;
        $otherFriend=FriendModel::where(['friend_user_id'=>$this->uid,'create_user'=>$user_id])->find();
        if($otherFriend){
            if($otherFriend->status>0){
                $status=1;
            }
        }
        $model = new FriendModel();
        $data=[
            'friend_user_id'=>$user_id,
            'status'=>$status,
            'create_user'=>$this->uid,
            'remark'=>$param['remark'],
            'is_invite'=>1 // 是否为发起方
        ];
        $model->save($data);
        $msg=[
            'fromUser'=>[
                'id'=>'system',
                'displayName'=>'新朋友',
                'avatar'=>'',
            ],
            'toContactId'=>'system',
            'id'=>uniqid(),
            'is_group'=>2,
            'content'=>"添加您为好友",
            'status'=>'succeed',
            'sendTime'=>time()*1000,
            'type'=>'event',
            'fileSize'=>0,
            'fileName'=>'',
        ];
        // 发送好友申请
        wsSendMsg($user_id,'simple',$msg);
        return success('添加成功');
    }

    // 接受或者拒绝好友申请
    public function update()
    {
        $param = $this->request->param();
        $friend=FriendModel::find($param['friend_id']);
        if(!$friend){
            return warning('申请不存在');
        }
        $map=[
            'friend_id'=>$param['friend_id']
        ];
        FriendModel::where($map)->update(['status'=>$param['status']]);
        // 如果是接收，就添加到好友列表
        if($param['status']){
            $data=[
                'friend_user_id'=>$friend->create_user,
                'create_user'=>$this->uid,
            ];
            $newFriend=FriendModel::where($data)->find();
            if($newFriend){
                FriendModel::where($data)->update(['status'=>1]);
                return success('你们已经是好友了');
            }else{
                $data['status']=1;
                FriendModel::create($data);
            }
            // 将对方的信息发送给我，把我的信息发送对方
            $user=User::setContact($friend->create_user);
            if($user){
                wsSendMsg($this->uid,'appendContact',$user);
            }
            $myInfo=User::setContact($this->uid);
            if($myInfo){
                wsSendMsg($friend->create_user,'appendContact',$myInfo);
            }
            
        }
        return success('操作成功');
    }


    // 删除好友
    public function del()
    {
        $param = $this->request->param();
        $map=['friend_user_id'=>$param['id'],'create_user'=>$this->uid];
        $friend=FriendModel::where($map)->find();
        if(!$friend){
            return warning('好友不存在');
        }
        // 需要删除双方的好友关系
        FriendModel::where($map)->delete();
        FriendModel::where(['friend_user_id'=>$this->uid,'create_user'=>$param['id']])->delete();
        // 性质和删除群聊一样
        wsSendMsg($param['id'],'removeGroup',['group_id'=>$this->uid]);
        return success('删除成功');
    }

    // 设置好友备注
    public function setNickname()
    {
        $param = $this->request->param();
        if(!$param['nickname']){
            return warning('备注不能为空');
        }
        FriendModel::update(['nickname'=>$param['nickname']],['friend_id'=>$param['friend_id']]);
        return success('设置成功');
    }

    // 获取最新的一条和申请的总数
    public function getApplyMsg(){
        $model = new FriendModel();
        $map[]=['friend_user_id','=',$this->uid];
        $map[]=['status','=',2];
        $count=$model->where($map)->count();
        return success('', $count);
    }

}
