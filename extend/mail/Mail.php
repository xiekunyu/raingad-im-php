<?php
/**
 * Created by PhpStorm
 * User Julyssn
 * Date 2021/8/3 13:50
 */


namespace mail;


use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;
use think\facade\View;

class Mail
{
    public $Config = [
        'driver' => 'smtp',                     // 邮件驱动, 支持 smtp|sendmail|mail 三种驱动
        'host' => 'smtp.qq.com',                // SMTP服务器地址
        'port' => 465,                          // SMTP服务器端口号,一般为25
        'addr' => '',                           // 发件邮箱地址
        'pass' => '',                           // 发件邮箱密码
        'sign' => '',                           // 发件邮箱名称
        'content_type' => 'text/html',          // 默认文本内容 text/html|text/plain
        'charset' => 'utf-8',                   // 默认字符集
        'security' => 'ssl',                    // 加密方式 null|ssl|tls, QQ邮箱必须使用ssl
        'temp' => '',                           //邮件模板
        'logo' => '',                           //邮件logo
    ];


    public function __construct($config)
    {
        $this->Config = array_merge($this->Config, $config);
        //默认模板
        $this->Config['temp'] = $this->Config['temp'] ?: 'temp';
        $this->Config['logo'] = $this->Config['logo'] ?: 'https://im.file.raingad.com/logo/logo.png';
    }

    public function sendEmail(array $toEmails, $title, $content)
    {

        // 创建Transport对象，设置邮件服务器和端口号，并设置用户名和密码以供验证
        $transport = (new Swift_SmtpTransport($this->Config['host'], $this->Config['port'], $this->Config['security']))
            ->setUsername($this->Config['addr'])
            ->setPassword($this->Config['pass']);

        //创建mailer对象
        $mailer = new Swift_Mailer($transport);

        //创建message对象
        $message = (new Swift_Message($title));//设置邮件主题

        //用关联数组设置发件人地址，可以设置多个发件人
        $message->setFrom([$this->Config['addr'] => $this->Config['sign']]);

        //用关联数组设置收件人地址，可以设置多个收件人
        $message->setTo($toEmails);

        //设置邮件内容
        $data = [
            'logo' => $this->Config['logo'],
            'title' => $title,
            'content' => $content,
            'time' => date('Y-m-d H:i:s'),
            'name' => $this->Config['sign']
        ];
        $html = View::fetch(dirname(__FILE__) . '/' . $this->Config['temp'] . '.html', ['data' => $data]);
        $message->setBody($html, 'text/html');

//        //创建attachment对象，content-type这个参数可以省略
//        $attachment = Swift_Attachment::fromPath('image.jpg', 'image/jpeg')->setFilename('cool.jpg');
//        //添加附件
//        $message->attach($attachment);


//        //添加抄送人
//        $message->setCc(array(
//            'Cc@qq.com' => 'Cc'
//        ));

//        //添加密送人
//        $message->setBcc(array(
//            'Bcc@qq.com' => 'Bcc'
//        ));

//        //设置邮件回执
//        $message->setReadReceiptTo('receipt@163.com');

        //发送邮件
        return $mailer->send($message);
    }
}