# wx-send-tpl
小程序给用户发送模板消息

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
