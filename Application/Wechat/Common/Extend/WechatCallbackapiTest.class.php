<?php
/**
 * Created by PhpStorm.
 * User: gaoyang
 * Date: 15/10/6
 * Time: 下午1:00
 */

namespace Wechat\Common\Extend;

define("TOKEN", "weixin");
define("appid","wxcec023a0d89b6b96");
define("appsecret","ed06183c78714989c4a8c2ee4ca84724");
define("teacherURL","http://112.124.101.41/mike_server_v01/index.php/Wechat/Index/testForTeacher");
define("studentTestURL","http://112.124.101.41/mike_server_v01/index.php/Wechat/Index/testForStudent");
define("studentCountURL","http://112.124.101.41/mike_server_v01/index.php/Wechat/Index/countForStudent");
define("teacherCountURL","http://112.124.101.41/mike_server_v01/index.php/Wechat/Index/countForTeacher");
define("bindURL","http://112.124.101.41/mike_server_v01/index.php/Wechat/Index/bind");
define("indexURL","http://mp.weixin.qq.com/s?__biz=MzIzMDA2OTU5OQ==&mid=211605137&idx=1&sn=54c0a1014cf78c14ee1a3f00cebcb846#rd");
define("announceTeacherURL","http://112.124.101.41/mike_server_v01/index.php/Wechat/Index/announceForTeacher");
define("announceStudentURL","http://112.124.101.41/mike_server_v01/index.php/Wechat/Index/announceForStudent");
define('announceStudentPushURL','http://112.124.101.41/mike_server_v01/index.php/Wechat/Index/announceSingleForStudent');
$wechatObj = new wechatCallbackapiTest();

if(isset($_GET["echostr"])){
    $wechatObj->valid();
}else{
    $wechatObj->responseMsg();
}

class wechatCallbackapiTest
{

    private function getAccessToken()
    {
        $token = new \Wechat\Controller\WechatDesktopController();
        $access_token = $token->getToken();
        if(!$access_token){
            $access_token = $this->setAccessToken();
        }
        return $access_token;
    }

    private function setAccessToken(){
        $sql = new \Wechat\Controller\WechatDesktopController();
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".appid."&secret=".appsecret;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        $jsoninfo = json_decode($output, true);
        $access_token = $jsoninfo["access_token"];
        $sql->setToken($access_token,time());
        return $access_token;
    }

    public function valid()
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
            header('content-type:text');
            echo $echoStr;
            exit;
        }
    }

    private function checkSignature()
    {
        // you must define TOKEN by yourself
        if (!defined("TOKEN")) {
            throw new Exception('TOKEN is not defined!');
        }

        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }

    public function responseMsg()
    {
        //get post data, May be due to the different environments
        $postStr =  file_get_contents("php://input");

        //extract post data
        if (!empty($postStr)){
            /* libxml_disable_entity_loader is to prevent XML eXternal Entity Injection,
               the best way is to check the validity of xml by yourself */
            libxml_disable_entity_loader(true);
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $fromUsername = $postObj->FromUserName;
            $toUsername = $postObj->ToUserName;
            $keyword = trim($postObj->Content);
            $Event = $postObj->Event;
            $receiveMsgType = $postObj->MsgType;
            $time = time();
            $EventKey = $postObj->EventKey;
            $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";

//            if($receiveMsgType=="image" || $receiveMsgType=="voice"){
//                $MsgType="text";
//                $contentstr = $fromUsername;
//                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time,$MsgType,$contentstr);
//                echo $resultStr;
//            }
            if($Event=='CLICK'){
                $sql = new \Wechat\Controller\WechatDesktopController();

                if($sql->getRole($fromUsername)==-1){
                    $MsgType="text";
                    $contentstr = "该功能需绑定后才能生效";
                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time,$MsgType,$contentstr);
                    echo $resultStr;
                }

                else{
                    if($EventKey == 'V1'){
                        $this->userTest($postObj);
                    }
                    elseif($EventKey == 'V2'){
                        $this->userCount($postObj);
                    }
                    elseif($EventKey == 'V3'){
                        $this->userBind($postObj);
                    }
                    elseif($EventKey == 'V4'){
                        $this->userMessages($postObj);
                    }
                }

            }

            if($Event=="subscribe" || !empty( $keyword ) || $receiveMsgType=="image" || $receiveMsgType=="voice"){
                $textTpl="<xml>
                                <ToUserName><![CDATA[%s]]></ToUserName>
                                <FromUserName><![CDATA[%s]]></FromUserName>
                                <CreateTime>%s</CreateTime>
                                <MsgType><![CDATA[news]]></MsgType>
                                <ArticleCount>1</ArticleCount>
                                <Articles>
                                <item>
                                <Title><![CDATA[欢迎关注echome]]></Title>
                                <Description><![CDATA[团队纳新]]></Description>
                                <PicUrl><![CDATA[http://i2.tietuku.com/aa3187fadfcf0863s.jpg]]></PicUrl>
                                <Url><![CDATA[%s]]></Url>
                                </item>
                                </Articles>
                                </xml>";
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time,indexURL);
                echo $resultStr;
            }

//            if(!empty( $keyword ))
//            {
//
//                $msgType = "text";
//                // $result = $sim->talk($keyword);
//                $contentStr = "请点击下面三个菜单哟~";
//                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
//                echo $resultStr;
//            }

        }else {
            echo "";
            exit;
        }
    }

    private function userTest($postObj){
        $sql = new \Wechat\Controller\WechatDesktopController();
        $fromUsername = $postObj->FromUserName;
        $toUsername = $postObj->ToUserName;
        if($sql->getRole($fromUsername)==1){
            $textTpl = "<xml>
                                <ToUserName><![CDATA[%s]]></ToUserName>
                                <FromUserName><![CDATA[%s]]></FromUserName>
                                <CreateTime>%s</CreateTime>
                                <MsgType><![CDATA[%s]]></MsgType>
                                <ArticleCount>1</ArticleCount>
                                <Articles>
                                <item>
                                <Title><![CDATA[%s]]></Title>
                                <Description><![CDATA[%s]]></Description>
                                <Url><![CDATA[%s]]></Url>
                                </item>
                                </Articles>
                                </xml>";
            $msgType = "news";
            $title = "小测";
            $description = "点击进行小测";
            $randomCode = $this->random();
            $url = teacherURL."?openID=$fromUsername&random=$randomCode";
            $resultStr = sprintf($textTpl, $fromUsername, $toUsername, time(), $msgType, $title, $description,$url);
            echo $resultStr;
        }

        elseif($sql->getRole($fromUsername)==0){
            if($sql->isTestExist($fromUsername)) {
                $this->studentEvent(0,$postObj);
            }
            else{
                $msgType = "text";
                $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";
                $content = "暂时无相关小测";
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, time(), $msgType,$content);
                echo $resultStr;
            }
        }
    }

    private function  userCount($postObj){
        $sql = new \Wechat\Controller\WechatDesktopController();
        $fromUsername = $postObj->FromUserName;
        $toUsername = $postObj->ToUserName;
        if($sql->getRole($fromUsername)==1){
            $textTpl = "<xml>
                                <ToUserName><![CDATA[%s]]></ToUserName>
                                <FromUserName><![CDATA[%s]]></FromUserName>
                                <CreateTime>%s</CreateTime>
                                <MsgType><![CDATA[%s]]></MsgType>
                                <ArticleCount>1</ArticleCount>
                                <Articles>
                                <item>
                                <Title><![CDATA[%s]]></Title>
                                <Description><![CDATA[%s]]></Description>
                                <Url><![CDATA[%s]]></Url>
                                </item>
                                </Articles>
                                </xml>";
            $msgType = "news";
            // $result = $sim->talk($keyword);
            $title = "点名";
            $description = "点击进行点名";
            $randomCode = $this->random();
            $url = teacherCountURL."?openID=$fromUsername&random=$randomCode";
            $resultStr = sprintf($textTpl, $fromUsername, $toUsername, time(), $msgType, $title, $description,$url);
            echo $resultStr;
        }
        elseif($sql->getRole($fromUsername)==0){
            if($sql->isCountExist($fromUsername)) {
                $this->studentEvent(1,$postObj);
            }
            else{
                $msgType = "text";
                $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";
                $content = "暂时无相关点名";
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, time(), $msgType,$content);
                echo $resultStr;
            }
        }
    }

    private function userBind($postObj){
        $fromUsername = $postObj->FromUserName;
        $toUsername = $postObj->ToUserName;
        $textTpl = "<xml>
                                <ToUserName><![CDATA[%s]]></ToUserName>
                                <FromUserName><![CDATA[%s]]></FromUserName>
                                <CreateTime>%s</CreateTime>
                                <MsgType><![CDATA[%s]]></MsgType>
                                <ArticleCount>1</ArticleCount>
                                <Articles>
                                <item>
                                <Title><![CDATA[%s]]></Title>
                                <Description><![CDATA[%s]]></Description>
                                <Url><![CDATA[%s]]></Url>
                                </item>
                                </Articles>
                                </xml>";
        $msgType = "news";
        $title = "绑定";
        $description = "点击进行绑定";
        $url = bindURL."?openID=$fromUsername";
        $resultStr = sprintf($textTpl, $fromUsername, $toUsername, time(), $msgType, $title, $description,$url);
        echo $resultStr;
    }

    public function userMessages($postObj){
        $sql = new \Wechat\Controller\WechatDesktopController();
        $fromUsername = $postObj->FromUserName;
        $toUsername = $postObj->ToUserName;
        $time = time();

        $textTpl = "<xml>
                                <ToUserName><![CDATA[%s]]></ToUserName>
                                <FromUserName><![CDATA[%s]]></FromUserName>
                                <CreateTime>%s</CreateTime>
                                <MsgType><![CDATA[%s]]></MsgType>
                                <ArticleCount>1</ArticleCount>
                                <Articles>
                                <item>
                                <Title><![CDATA[%s]]></Title>
                                <Description><![CDATA[%s]]></Description>
                                <Url><![CDATA[%s]]></Url>
                                </item>
                                </Articles>
                                </xml>";
        $msgType = "news";
        $title = "课程公告";
        $description = "";
        if($sql->getRole($fromUsername)==1){
            $randomCode = $this->random();
            $url = announceTeacherURL."?openID=$fromUsername&random=$randomCode";
            $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $title,$description,$url);
            echo $resultStr;
        }
        elseif($sql->getRole($fromUsername)==0){
            $url = announceStudentURL."?openID=$fromUsername";
            $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $title,$description,$url);
            $this->sendMessages('sadas');
            echo $resultStr;
        }
    }

    private function  studentEvent($i,$postObj){
        $sql = new \Wechat\Controller\WechatDesktopController();
        $fromUsername = $postObj->FromUserName;
        $toUsername = $postObj->ToUserName;
        $itemTpl = "    <item>
                                <Title><![CDATA[%s]]></Title>
                                <Url><![CDATA[%s]]></Url>
                                </item>
                            ";
        $item_str = "";
        $countNews = 0;
        if($i==0){
            $arr = $sql->getTest($fromUsername);
            foreach ($arr as $k=>$v){
                $courseID = $v['course_id'];
                $quizID = $v['quiz_id'];
                $url = studentTestURL."?openID=$fromUsername&course_id=$courseID&quiz_id=$quizID";
                $item_str .= sprintf($itemTpl, $v['title'],$url);
                $countNews++;
                if($countNews==8){
                    break;
                }
            }
        }
        elseif($i==1){
            $arr = $sql->getCountTest($fromUsername);
            foreach ($arr as $k=>$v){
                $quizID = $v['quiz_id'];
                $url = studentCountURL."?openID=$fromUsername&quiz_id=$quizID";
                $item_str .= sprintf($itemTpl, $v['title'],$url);
                $countNews++;
                if($countNews==8){
                    break;
                }
            }
        }

        $xmlTpl = "<xml>
                            <ToUserName><![CDATA[%s]]></ToUserName>
                            <FromUserName><![CDATA[%s]]></FromUserName>
                            <CreateTime>%s</CreateTime>
                            <MsgType><![CDATA[news]]></MsgType>
                            <ArticleCount>%s</ArticleCount>
                            <Articles>
                            $item_str</Articles>
                            </xml>";

        $result = sprintf($xmlTpl, $fromUsername, $toUsername,time(),$countNews);
        echo $result;
    }

    private function random(){
        $rand = '';
        $c= "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        srand((double)microtime()*1000000);
        for($i=0; $i<5; $i++) {
            $rand.= $c[rand()%strlen($c)];
        }
        $rand.= microtime()*100000000;
        $rand.= time();
        return $rand;
    }

    public function sendMessages($openIDs,$course_name='',$teacher_name='',$content=''){
        $posttime=date('Y-m-d H:i:s',time());
        $token = $this->getAccessToken();
        for($i=0;$i<count($openIDs);$i++) {
            $open_id = $openIDs[$i];
            $url = announceStudentPushURL."?openID=$open_id&course_name=$course_name&teacher_name=$teacher_name&content=$content&posttime=$posttime";
            $news = '{
                        "touser":"'.$open_id.'",
                        "msgtype":"news",
                        "news":{
                            "articles": [
                             {
                                 "title":"'.$course_name.'课程公告",
                                 "url":"'.$url.'"
                             }
                             ]
                        }
                    }';
            $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=$token";
            $result = $this->https_post($url, $news);
            var_dump($result);
        }
    }

    private function https_post($url,$data){
        $curl = curl_init();
        curl_setopt($curl,CURLOPT_URL,$url);
        curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,false);
        curl_setopt($curl,CURLOPT_POST,1);
        curl_setopt($curl,CURLOPT_POSTFIELDS,$data);
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
        $result = curl_exec($curl);
        if(curl_errno($curl)){
            return 'Errno'.curl_error($curl);
        }
        curl_close($curl);
        return $result;
    }
}
