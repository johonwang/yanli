<?php

class indexController extends Controller
{
    public function actionIndex(){
        $this->display('Page/index');
    }

    public function actionMsg(){
        if(!empty($_GET['state'])){
            $msgId = $_GET['state'];
            $message = new Message();
            $megInfo = $message->getMsgInfo($msgId);
            if($megInfo){
                //更改消息查询状态
                $redis = parent::redis();
                $hash = 'msg'.$msgId;
                $key = $this->openId;
                $val = '1';
                $redis->hSet($hash,$key,$val);
                //显示消息页面
                $this->display('Page/message',$megInfo);
            }
        }else{
        
        }
    }
}