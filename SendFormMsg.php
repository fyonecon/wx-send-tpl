<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/19
 * Time: 14:35
 */

namespace app\api\controller;

use think\Controller;
use think\Request;
use think\Db;

class SendFormMsg extends Controller{

    /*
     * 0. 收集用户的form_id(建议把用户的操作都录入form_id)
     *
        <form bindsubmit="submit" report-submit='true' >
            <button form-type="submit" type="default" size="mini">提交</button>
        </form>

        submit: function (e) {
          console.log(e.detail.formId);
        }

     * 1. 小程序后台配置或者选取消息模板
     * 2. 配置模板ID、appid等
     * 3. 批量发送模板消息
     *
     * */
    // 批量推送小程序模板消息， 接口 https://xxxxx.com/public/?s=/api/send_form_msg/send
    public function send(){
        $time = time() - 60*60*24*7; // 最近7天的时间戳
        $res = Db::name('formid')->field("id, form_id, openid")->where('create_time', ">", $time)->where('send', 1)->order('id desc')->group('openid')->limit(1000)->select(); // 符合七天条件的用户

        for ($i=0; $i < count($res); $i++){

            $openid = $res[$i]['openid'];
            $form_id = $res[$i]['form_id'];

            $this->send_tpl($openid, $form_id); // 批量发送

        }
        //print_r($res);
    }
    // 推送小程序模板消息
    public function send_tpl($openid, $form_id){

        $app = array(
            "appid" => "wx90xxxxxxx6450cc", // 小程序appid
            "appsecret" => "a13a9a2xxxxxxxxxxxxxxxad4e513d60f" // 小程序appsecret
        );

        $accessToken = getAccessToken($app); // 换取access_token
        // 配置模板
        $postData = array(
            "touser"        =>$openid,      //用户openid
            "form_id"       =>$form_id,      //表单提交场景下，事件带上的 formId；支付场景下，为本次支付的 prepay_id
            "template_id"   =>'HTJu4-mwcxxxxxxxxxxxxxxxxxpfWRsFwE-lR9k8LJeSII',  //模板消息ID
            "page"          =>'pages/index/index',
            "data"          =>array(
                'keyword1'  => array('value'=>'签到提醒','color'=>'#000000'),
                'keyword2'  => array('value'=>'签到可以赚金币哦','color'=>'#000000')
            ),
            "emphasis_keyword" => "keyword1.DATA" // 超大字体该字段
        );

        $postData =  json_encode($postData,JSON_UNESCAPED_UNICODE);
        $url = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token={$accessToken}";
        $http = json_decode(http_request($url, $postData), 1); // 发送模板消息

        if($http['errcode']){ // 抛出错误

            dump($http['errmsg']);
            echo "<hr>";
            print_r($http['errcode']);
            echo "<hr>";

            file_put_contents(ROOT_PATH . "runtime/send_tpl.log", '错误号：' .$http['errcode']."=错误信息：".$http['errmsg']."=openid：".$openid. PHP_EOL, FILE_APPEND);

            return;

        }else{ // 成功

            $data['send'] = 2;

            $keep = Db::name('formid')->where('openid',  $openid)->update($data); // 已发送则改变状态

            print_r($keep);
            echo "<hr>";
        }

    }




        // 模板
        public function sendtpl(){

            $app = array(
                "appid" => "wx906xxxxxxxxxxxxx450cc", //
                "appsecret" => "a13a9axxxxxxxxxxxxxxxxxxxxxxxxxe513d60f"
            );

            $accessToken = getAccessToken($app); // 换取access_token

            $postData = array(
                "touser"        =>'oCJvb4hdxEwgXGQw3X6hx8Mx2Fwg',      //用户openid
                "template_id"   =>'HTJu4-mwcxxxxxxxxxxxxxxxxxxxxxE-lR9k8LJeSII',  //模板消息ID
                "page"          =>'pages/index/index',
                "form_id"       =>'ce2ca99xxxxxxxxxxxxxxxxxxxxxxf24a690b',      //表单提交场景下，事件带上的 formId；支付场景下，为本次支付的 prepay_id
                "data"          =>array(
                    'keyword1'  => array('value'=>'签到提醒','color'=>'#000000'),
                    'keyword2'  => array('value'=>'签到可以赚金币哦','color'=>'#000000')
                ),
                "emphasis_keyword" => "keyword1.DATA"
            );
            $postData =  json_encode($postData,JSON_UNESCAPED_UNICODE);
            print_r($postData);
            $url = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token={$accessToken}";
            print_r($url);
            $http = json_decode(http_request($url, $postData), 1);

            if($http['errcode']){ // 抛出错误
                dump($http['errmsg']);
                print_r($http['errcode']);
            }

            print_r($http);

            //$rtn = request($url,true,'POST',$postData);

            //return $rtn;
        }


}