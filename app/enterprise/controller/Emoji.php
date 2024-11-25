<?php

namespace app\enterprise\controller;

use app\BaseController;

use app\enterprise\model\{Emoji as EmojiModel,File,Message};
use think\facade\Filesystem;
use think\facade\View;
class Emoji extends BaseController
{
    // 表情列表
    public function index()
    {
        $map=['status'=>1,'user_id'=>$this->uid,'type'=>2];
        $list = EmojiModel::where($map)->field('id,name,src,file_id')->order('update_time desc')->select();
        $data=[];
        if($list){
            $data=$list->toArray();
            foreach ($data as $k => $v) {
                $url=getFileUrl($v['src']);
                $data[$k]['src'] =$url;
                $data[$k]['title'] =$v['name'];
            }
        }
        return success('', $data, count($data));
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
        return success(lang('system.addOk'));
    }

    // 删除表情
    public function del(){
        $ids = $this->request->param('ids',[]);
        if(!is_array($ids) || $ids==[]){
            return warning(lang('system.parameterError'));
        }
        foreach($ids as $id){
            $emoji=EmojiModel::where(['id'=>$id,'user_id'=>$this->uid])->find();
            if(!$emoji){
                continue;
            }
            $res=EmojiModel::where(['id'=>$id])->delete();
            if($res){
                $exist=EmojiModel::where(['file_id'=>$emoji['file_id']])->find();
                $exist2=Message::where(['file_id'=>$emoji['file_id']])->find();
                // 如果文件没有引用了，就删除掉源文件
                if(!$exist || !$exist2){
                    $disk=env('filesystem.driver','local');
                    $file=File::find($emoji['file_id']);
                    Filesystem::disk($disk)->delete($file->src);
                }
            }
        }
        return success(lang('system.delOk'));
        
    }

    // 移动表情
    public function move(){
        $ids = $this->request->param('ids',[]);
        if(!is_array($ids) || $ids==[]){
            return warning(lang('system.parameterError'));
        }
        foreach($ids as $id){
            $emoji=EmojiModel::where(['id'=>$id,'user_id'=>$this->uid])->find();
            if(!$emoji){
                continue;
            }
            EmojiModel::where(['id'=>$id])->update(['update_time'=>time()]);
        }
        return success(lang('system.success'));
        
    }


}
