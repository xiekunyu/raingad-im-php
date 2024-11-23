<?php

namespace app\enterprise\controller;

use app\BaseController;

use app\enterprise\model\{Emoji as EmojiModel,File};
use think\facade\Filesystem;
use think\facade\View;
class Emoji extends BaseController
{
    // 表情列表
    public function index()
    {
        $param = $this->request->param();
        $listRows = $param['limit'] ?: 20;
        $pageSize = $param['page'] ?: 1;
        $map=['status'=>1,'user_id'=>$this->uid,'type'=>2];
        $list = EmojiModel::where($map)->field('id,name,src')->limit('update_time desc')->paginate(['list_rows'=>$listRows,'page'=>$pageSize]);
        $data=[];
        if($list){
            $data=$list->toArray()['data'];
            foreach ($data as $k => $v) {
                $url=getFileUrl($v['src']);
                $data[$k]['src'] =$url;
            }
        }
        return success('', $data, $list->total(), $list->currentPage());
    }

    // 添加表情
    public function add(){
        $param = $this->request->param();
        $file_id=$param['file_id'];
        $fileInfo=File::find($file_id);
        if(!$fileInfo){
            return warning(lang('system.exits'));
        }
        $exist=EmojiModel::where(['user_id'=>$this->uid,'file_id'=>$file_id])->find();
        // 判断是否已经有了当前表情，有了就更新
        if($exist){
            EmojiModel::where(['id'=>$exist['id']])->update(['update_time'=>time()]);

        }else{
            $info=[
                'user_id'=>$this->uid,
                'type'=>2,
                'file_id'=>$file_id,
                'name'=>$fileInfo->name,
                'src'=>$fileInfo->src,
            ];
            EmojiModel::create($info);
        }
        return success('');
    }

    // 删除表情
    public function del(){
        $param = $this->request->param();
        $id=$param['id'];
        $emoji=EmojiModel::find($id);
        if(!$emoji){
            return warning(lang('system.exits'));
        }
        $res=EmojiModel::where(['id'=>$id])->delete();
        if($res){
            $exist=EmojiModel::where(['file_id'=>$emoji->file_id])->find();
            // 如果文件没有引用了，就删除掉源文件
            if(!$exist){
                $disk=env('filesystem.driver','local');
                $file=File::find($emoji->file_id);
                Filesystem::disk($disk)->delete($file->src);
            }
        }
        return success(lang('system.delOk'));
        
    }


}
