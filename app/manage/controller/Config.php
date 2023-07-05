<?php
/**
 * Created by PhpStorm
 * User Julyssn
 * Date 2022/12/14 17:24
 */


namespace app\manage\controller;


use app\BaseController;
use app\manage\model\{Config as Conf};
use think\facade\Env;
class Config extends BaseController
{
    /**
     * 获取单个配置
     * @return \think\response\Json
     */
    public function getInfo()
    {
        $name=$this->request->param('name');
        $data = Conf::where(['name'=>$name])->value('value');
        return success('', $data);
    }

    /**
     * 获取配置
     * @return \think\response\Json
     */
    public function getAllConfig()
    {
        $name=['sysInfo','chatInfo','smtp','fileUpload'];
        $list = Conf::where(['name'=>$name])->select();
        return success('', $list);
    }

    /**
     * 修改配置
     * @return \think\response\Json
     */
    public function setConfig()
    {
        $name = $this->request->param('name');
        $value = $this->request->param('value');
        if(Conf::where(['name'=>$name])->find()){
            Conf::where(['name'=>$name])->update(['value'=>$value]);
        }else{
            Conf::create(['name'=>$name,'value'=>$value]);
        }
        if($name=='fileUpload'){
            updateEnv('driver',$value['disk']);
            foreach ($value['aliyun'] as $k=>$v){
                if($v){
                    updateEnv('aliyun_'.$k,$v);
                }
            }
            foreach ($value['qiniu'] as $k=>$v){
                if($v){
                    updateEnv('qiniu_'.$k,$v);
                }
            }
            foreach ($value['qcloud'] as $k=>$v){
                if($v){
                    updateEnv('qcloud_'.$k,$v);
                }
            }
        }
        return success('保存成功');
    }

    /**
     * 获取邀请链接
     * @return \think\response\Json
     */
    public function getInviteLink(){
        $uid=$this->userInfo['user_id'];
        // 邀请码仅两天有效
        $code=\utils\Str::authcode($uid,'ENCODE','imchat',48*3600);
        $url=request()->domain().'/index.html/#/register?code='.$code;
        return success('',$url);
    }
}