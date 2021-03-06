<?php
/**
 * 微信静默授权获取用户openId
 * @author 王晓阳 <[cjdsnwxy@gmail.com]>
 *
 * 引导用户打开网页授权网页，get获得code
 * new wxClass($appId,$appSecret,$code);
 */
class getOpenIdClass{
    private $appId;
    private $appSecret;
    private $code;
    public function __construct($appId,$appSecret,$code){
        $this->appId = $appId;
        $this->appSecret = $appSecret;
        $this->code = $code;
    }
    public function getOpenId(){
        $res = $this->getToken();
        return $res->openid;
    }
    private function getToken(){
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$this->appId."&secret=".$this->appSecret."&code=".$this->code."&grant_type=authorization_code";
        return json_decode($this->httpGet($url));
    }
    private function httpGet($url){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        curl_setopt($curl, CURLOPT_URL, $url);
        $res = curl_exec($curl);
        curl_close($curl);
        return $res;
    }
}
