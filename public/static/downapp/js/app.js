var _0x4f06 = ['show', 'browser', 'KHTML', './app.mobileprovision', '#gosteptips', 'Safari', 'appmarket://details?id=', 'test', 'ios', 'html', '.pc-box', 'click', 'Trident', 'class', 'huawei', '#gomchelp', 'android', '#weixin_ios', 'mimarket://details?id=', 'oppomarket://details?packagename=', 'attr', 'match', 'safari', 'indexOf', '#install-btn', 'getElementById', 'hide', 'appVersion', '.app-slider', 'userAgent', 'QQ/', 'app_code', 'href', '#gomchelp-btn', 'oppo', 'Gecko', 'Adr', 'samsungapps://ProductDetail/', 'qq2', 'Presto', 'xiaomi', 'MQQBrowser', 'samsung', '#ipt_url'];
var _0x417a = function (_0x4f0689, _0x417a46) {
    _0x4f0689 = _0x4f0689 - 0x0;
    var _0x330fc9 = _0x4f06[_0x4f0689]; return _0x330fc9;
};
var package_name = 'com.yougou.mch';
var android_url = 'https://yougou999.oss-cn-hongkong.aliyuncs.com/yougou-mch-100002.apk';
var ios_appid = '6449150515';
var ios_webclip = '';
var market = ['xiaomi', 'huawei', _0x417a('0x2a')];
var Base = {};
Base[_0x417a('0x1')] = function () {
    var _0x4e510b = navigator[_0x417a('0x1d')], _0x41f47f = navigator[_0x417a('0x1b')];
    return { 'trident': _0x4e510b['indexOf'](_0x417a('0xc')) > -0x1, 'presto': _0x4e510b['indexOf'](_0x417a('0x27')) > -0x1, 'webKit': _0x4e510b['indexOf']('AppleWebKit') > -0x1, 'gecko': _0x4e510b['indexOf'](_0x417a('0x23')) > -0x1 && _0x4e510b[_0x417a('0x17')](_0x417a('0x2')) == -0x1, 'mobile': !!_0x4e510b['match'](/AppleWebKit.*Mobile.*/), 'ios': /(iPhone|iPad|iPod|iOS|Mac)/i[_0x417a('0x7')](_0x4e510b), 'android': _0x4e510b['indexOf']('Android') > -0x1 || _0x4e510b['indexOf'](_0x417a('0x24')) > -0x1, 'iPhone': _0x4e510b['indexOf']('iPhone') > -0x1, 'iPad': /(?:iPad|PlayBook)/['test'](_0x4e510b), 'webApp': _0x4e510b['indexOf'](_0x417a('0x5')) == -0x1, 'weixin': _0x4e510b['indexOf']('MicroMessenger') > -0x1, 'qq': _0x4e510b[_0x417a('0x15')](/QQ/i) == 'QQ', 'qq2': _0x4e510b['match'](/QQ/i) == 'QQ' && _0x4e510b['indexOf']('MQQBrowser') == -0x1, 'qq1': _0x4e510b['indexOf'](_0x417a('0x29')) > -0x1 && _0x4e510b[_0x417a('0x17')](_0x417a('0x1e')) > -0x1, 'safari': /Safari/['test'](_0x4e510b) && !(/Chrome/[_0x417a('0x7')](_0x4e510b) || /CriOS/[_0x417a('0x7')](_0x4e510b)) && _0x4e510b[_0x417a('0x17')]('MQQBrowser') < 0x0, 'xiaomi': /XiaoMi/i[_0x417a('0x7')](_0x4e510b) || / MI /i['test'](_0x4e510b), 'vivo': /ViVo/i['test'](_0x4e510b), 'oppo': /OPPO/i['test'](_0x4e510b), 'huawei': /HUAWEI/i['test'](_0x4e510b), 'samsung': /amsung/i[_0x417a('0x7')](_0x4e510b) || / SM-/['test'](_0x4e510b) };
}();
$(function () {
    var _0x172525 = new Swiper('.swiper-container', {}); $('#gosteptips-close-btn')['on']('click', function () {
        $(_0x417a('0x4'))[_0x417a('0x1a')]();
    });
    $('.pc-colsed')['on'](_0x417a('0xb'), function () { $('.pc-box')[_0x417a('0x1a')](); });
    $(_0x417a('0x21'))['on'](_0x417a('0xb'), function () { $(_0x417a('0xf'))[_0x417a('0x0')](); });
    $('#gomchelp-close-btn')['on']('click', function () { $(_0x417a('0xf'))[_0x417a('0x1a')](); });
    if (!/windows phone|iphone|android/ig[_0x417a('0x7')](window['navigator']['userAgent'])) {
        new QRCode(document[_0x417a('0x19')](_0x417a('0x1f')), { 'text': location['href'], 'width': 0xc8, 'height': 0xc8 });
        $(_0x417a('0xa'))['show']();
    } else if (Base['browser']['weixin'] || Base['browser'][_0x417a('0x26')]) {
        if (Base['browser']['android']) {
            $('#weixin_android')['show']();
        } else {
            $(_0x417a('0x11'))['show']();
        }
        $(_0x417a('0x9'))['attr'](_0x417a('0xd'), 'no_scroll');
    } else if (Base[_0x417a('0x1')][_0x417a('0x8')]) {
        if (Base[_0x417a('0x1')][_0x417a('0x16')]) {
            if (ios_appid != '') {
                var _0x1687ac = 'itms-apps://itunes.apple.com/app/' + ios_appid;
                $(_0x417a('0x18'))[_0x417a('0x14')](_0x417a('0x20'), _0x1687ac);
                location['href'] = _0x1687ac;
            } else if (ios_webclip != '') {
                $(_0x417a('0x1c'))['show']();
                $('#install-btn')['attr']('href', './' + ios_webclip);
                $(_0x417a('0x18'))[_0x417a('0xb')](function (_0x526b35) {
                    setTimeout(function () {
                        if (confirm) {
                            location['href'] = _0x417a('0x3');
                        }
                    }, 0xdac);
                    $('#gosteptips')[_0x417a('0x0')]();
                });
            }
        } else {
            $('body')[_0x417a('0xb')](function (_0x180351) {
                $(_0x417a('0x2b'))['html'](location['href']);
                $('.wechat-mask')['show']();
            });
        }
    } else if (Base['browser'][_0x417a('0x10')]) {
        var _0x1687ac = android_url;
        if (market[_0x417a('0x17')](_0x417a('0x28')) > -0x1 && Base['browser']['xiaomi']) {
            _0x1687ac = _0x417a('0x12') + package_name;
        } else if (market[_0x417a('0x17')]('vivo') > -0x1 && Base[_0x417a('0x1')]['vivo']) {
            _0x1687ac = 'vivomarket://details?id=' + package_name;
        } else if (market[_0x417a('0x17')](_0x417a('0x22')) > -0x1 && Base[_0x417a('0x1')][_0x417a('0x22')]) {
            _0x1687ac = _0x417a('0x13') + package_name;
        } else if (market[_0x417a('0x17')](_0x417a('0xe')) > -0x1 && Base[_0x417a('0x1')]['huawei']) {
            _0x1687ac = _0x417a('0x6') + package_name;
        } else if (market['indexOf'](_0x417a('0x2a')) > -0x1 && Base[_0x417a('0x1')][_0x417a('0x2a')]) {
            _0x1687ac = _0x417a('0x25') + package_name;
        }
        $('#install-btn')[_0x417a('0x14')]('href', _0x1687ac);
        if (_0x1687ac != android_url) {
            location['href'] = _0x1687ac;
        }
    }
});