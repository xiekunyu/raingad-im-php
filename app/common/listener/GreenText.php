<?php
namespace app\common\listener;

use think\api\Client;

// 检测敏感词
class GreenText
{
    protected $contentTypes = [  
        'ad' => '广告引流',  
        'political_content' => '涉政内容',  
        'profanity' => '辱骂内容',  
        'contraband' => '违禁内容',  
        'sexual_content' => '色情内容',  
        'violence' => '暴恐内容',  
        'nonsense' => '无意义内容',  
        'negative_content' => '不良内容',  
        'religion' => '宗教内容',  
        'cyberbullying' => '网络暴力',  
        'ad_compliance' => '广告法合规'  
    ];

    public function handle($data){
        $token = config('app.thinkapi_token');
        if(!$token){
            return true;
        }
        $client = new Client($token);
        $pattern = '/<[^>]*>/';
        $content = preg_replace($pattern, '', $data['content']);
        $result = $client->greenText()
            ->withService($data['service'])
            ->withContent(\utils\Str::msubstr($content,0,500))
            ->request();
        
        if($result && $result['code']==0){
            $data=$result['data'] ?? '';
            $labels=$data['labels'] ?? '';
            if(!$labels){
                return true;
            }
            $labels=explode(',',$labels);
            $keywords=[];
            if($labels){
                foreach($labels as $v){
                    $keywords[]=$this->contentTypes[$v] ?? $v;
                }
                $msg="您发布的内容包含".implode('、',$keywords)."等违规内容，系统已自动清除！";
                return shutdown($msg,500);
            }
        }
        return true;
    }
}