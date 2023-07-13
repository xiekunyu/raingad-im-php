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
            $userList = User::matchUser($data, true, 'create_user', 120);
            foreach ($data as $k => $v) {
                $data[$k]['create_user_info'] = $userList[$v['create_user']] ?? [];
            }
        }
        return success('', $data);
    }

    // 添加好友
    public function add()
    {
        $param = $this->request->param();
        $user_id=$param['user_id'] ?? 0;
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
        ];
        $model->save($data);
        // 发送好友申请
        wsSendMsg($user_id,'friendApply',[]);
        return success('添加成功');
    }

    // 接受或者拒绝好友申请
    public function update()
    {
        $param = $this->request->param();
        $model = new FriendModel();
        $friend=FriendModel::find($param['friend_id']);
        if(!$friend){
            return warning('申请不存在');
        }
        $data=[
            'status'=>$param['status'],
            'friend_id'=>$param['friend_id']
        ];
        $model->save($data);
        // 如果是接收，就添加到好友列表
        if($param['status']){
            $data=[
                'friend_user_id'=>$friend->friend_user_id,
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
        }
        return success('操作成功');
    }

    // 删除好友
    public function del()
    {
        $param = $this->request->param();
        $model = new FriendModel();
        $model->where(['friend_id'=>$param['friend_id']])->delete();
        return success('删除成功');
    }

    // 设置好友备注
    public function setNickname()
    {
        $param = $this->request->param();
        $model = new FriendModel();
        $model->save(['nickname'=>$param['nickname']],['friend_id'=>$param['friend_id']]);
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
