<?php

namespace app\enterprise\controller;

use app\BaseController;

use app\enterprise\model\{File,User,Message};
use think\facade\View;
class Files extends BaseController
{
    // 文件列表
    public function index()
    {
        $param = $this->request->param();
        $is_all = $param['is_all'] ?? 0;
        $map = [];
        $data=[];
        // 如果是查询全部，就查询file表，否则查询message表
        if ($is_all) {
            if ($param['cate'] ?? 0) {
                $map[] = ['cate', '=', $param['cate']];
            }
            $model = new File();
            if ($param['keywords'] ?? '') {
                $model = $model->where('name', 'like', '%' . $param['keywords'] . '%');
            }
            $list = $this->paginate($model->where($map)->order('file_id desc'));
            
            if ($list) {
                $data = $list->toArray()['data'];
                $userList = User::matchUser($data, true, 'user_id', 120);
                foreach ($data as $k => $v) {
                    $url=getFileUrl($v['src']);
                    $data[$k]['src'] =$url;
                    $data[$k]['preview'] = previewUrl($url);
                    $data[$k]['extUrl'] = getExtUrl($v['src']);
                    $data[$k]['name'] = $v['name'].'.'.$v['ext'];
                    $data[$k]['msg_type'] = getFileType($v['ext'],true);
                    $data[$k]['user_id_info'] = $userList[$v['user_id']] ?? [];
                    $data[$k]['download'] = getMainHost().'/filedown/'.encryptIds($v['file_id']);
                }
                
            }
        } else {
            $map = [
               ['file_id', '>', 0],
               ['type', '<>', 'voice'],
               ['is_group', '=', 0],
               ['is_undo', '=', 0],
            ];
            if ($param['cate'] ?? 0) {
                $map[] = ['file_cate', '=', $param['cate']];
            }
            $user_id = $this->uid;
            $model = new Message();
            if ($param['keywords'] ?? '') {
               $map[] = ['file_name', 'like', '%' . $param['keywords'] . '%'];
            }
            $role = $param['role'] ?? 0;
            $where=[];
            if($role==1){
               $map[] = ['from_user', '=', $user_id];
            }elseif($role==2){
               $map[] = ['to_user', '=', $user_id];
            }else{
                $where='(from_user='.$user_id.' or to_user='.$user_id.')';
            }

            $list = $this->paginate($model->where($map)->where($where)->order('create_time desc'));
            if ($list) {
                $data = $list->toArray()['data'];
                $userList = User::matchUser($data, true, 'from_user', 120);
                foreach ($data as $k => $v) {
                    $content=str_encipher($v['content'],false);
                    $url=getFileUrl($content);
                    $data[$k]['src'] = $url;
                    $data[$k]['preview'] = previewUrl($url);
                    $data[$k]['extUrl'] = getExtUrl($content);
                    $data[$k]['cate'] = $v['file_cate'];
                    $data[$k]['name'] = $v['file_name'];
                    $data[$k]['size'] = $v['file_size'];
                    $data[$k]['msg_type'] = $v['type'];
                    $ext=explode('.',$content);
                    $data[$k]['ext'] = end($ext);
                    $data[$k]['user_id_info'] = $userList[$v['from_user']] ?? [];
                    $data[$k]['download'] = getMainHost().'/filedown/'.encryptIds($v['file_id']);
                }
            }
        }
        return success('', $data, $list->total(), $list->currentPage());
    }
}
