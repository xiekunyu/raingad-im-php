<?php
 
namespace app\common\middleware;
use think\facade\Lang; 
class Locale
{
    public function handle($request, \Closure $next)
    {
        $lang = $request->header('Accept-Language'); // 从HTTP头获取语言设置
        $config=lang::getConfig();
        if ($lang) {
            $extLang=$config['extend_list'];
            if(!isset($extLang[$lang])){
                $lang=$config['default_lang'];
            }
            $accept_lang=$config['accept_language'];
            // 检测替换包
            if(isset($accept_lang[$lang])){
                $lang=$accept_lang[$lang];
            }
            // 根据Accept-Language头设置语言
            Lang::setLangSet($lang); // 例如 'zh-cn' 或 'en'
        } else {
            // 如果没有指定语言，可以设置默认语言
            Lang::setLangSet($config['default_lang']); // 默认语言设置为中文
        }
        return $next($request);
    }
}