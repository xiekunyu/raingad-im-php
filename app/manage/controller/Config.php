<?php
/**
 * Created by PhpStorm
 * User Julyssn
 * Date 2022/12/14 17:24
 */


namespace app\manage\controller;


use app\BaseController;
use app\manage\model\{Config as Conf};
use think\facade\Cache;
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
            updateEnv('own',$value['preview']);
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
        }else{
            // 更新系统缓存
            Conf::getSystemInfo(true);
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
        $code=\utils\Str::random(8);
        Cache::set($code,$uid,172800);
        $url=request()->domain().'/index.html/#/register?inviteCode='.$code;
        return success('',$url);
    }

    // 发送测试邮件
    public function sendTestEmail(){
        $email=$this->request->param('email');
        if(!$email || !(\utils\Regular::is_email($email))){
            return warning('请输入正确的邮箱');
        }
        $conf=Conf::where(['name'=>'smtp'])->value('value');
        $mail=new \mail\Mail($conf);
        $mail->sendEmail([$email],'测试邮件','这是一封测试邮件，当您收到之后表明您的所有配置都是正确的！');
        return success('发送成功');

    } 
}