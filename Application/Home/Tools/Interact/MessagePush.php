<?php

/**
 * Created by PhpStorm.
 * User: kisstheraik
 * Date: 15/10/7
 * Time: 下午7:22
 */

namespace Home\Controller\Tools;
require_once(dirname(__FILE__) . '/' . 'notification/android/AndroidBroadcast.php');
class MessagePush
{
    public static $instance;
    protected $appkey           = NULL;
    protected $appMasterSecret     = NULL;
    protected $timestamp        = NULL;


    private function __construct($appKey,$appMaster){

        $this->appkey=$appKey;
        $this->appMasterSecret=$appMaster;

    }

    public static function  getInstance(){

        if(self::$instance==null){
            self::$instance=new MessagePush("5614fe4d67e58e36e50024e3","ix0knkfgknn2cl4r1rozci0d5oqf7ol0");
            return self::$instance;
        }

        else{

            return self::$instance;
        }

    }
    /*
     * description:向所有的android设备推送消息
     * para:消息类型（notification，message），消息题目，消息内容，如果类型是notification这个就是消息框的标题
     */
    public function sendAndroidBroadcast($type,$title,$content,$notificationTitle="课间通知"){

        try {
            $brocast = new AndroidBroadcast();
            $brocast->setAppMasterSecret($this->appMasterSecret);
            $brocast->setPredefinedKeyValue("appkey",           $this->appkey);
            $brocast->setPredefinedKeyValue("timestamp",        $this->timestamp);
            $brocast->setPredefinedKeyValue("ticker",           $notificationTitle);
            $brocast->setPredefinedKeyValue("title",            $title);
            $brocast->setPredefinedKeyValue("text",             $content);
            $brocast->setPredefinedKeyValue("after_open",       "go_app");
            $brocast->setPredefinedKeyValue("display_type",       $type);
            // Set 'production_mode' to 'false' if it's a test device.
            // For how to register a test device, please see the developer doc.
            $brocast->setPredefinedKeyValue("production_mode", "true");
            // [optional]Set extra fields
            $brocast->setExtraField("test", "helloworld");
            print("Sending broadcast notification, please wait...\r\n");
            $brocast->send();
            print("Sent SUCCESS\r\n");
        } catch (Exception $e) {
            print("Caught exception: " . $e->getMessage());
        }

    }



    public function sendAndroidUnicast($type,$title,$content,$userToken,$notificationTitle="课间通知"){

        try {
            $unicast = new AndroidUnicast();
            $unicast->setAppMasterSecret($this->appMasterSecret);
            $unicast->setPredefinedKeyValue("appkey",           $this->appkey);
            $unicast->setPredefinedKeyValue("timestamp",        $this->timestamp);
            // Set your device tokens here
            $unicast->setPredefinedKeyValue("display_type",       $type);
            $unicast->setPredefinedKeyValue("device_tokens",    $userToken);
            $unicast->setPredefinedKeyValue("ticker",           $notificationTitle);
            $unicast->setPredefinedKeyValue("title",            $title);
            $unicast->setPredefinedKeyValue("text",             $content);
            $unicast->setPredefinedKeyValue("after_open",       "go_app");
            // Set 'production_mode' to 'false' if it's a test device.
            // For how to register a test device, please see the developer doc.
            $unicast->setPredefinedKeyValue("production_mode", "true");
            // Set extra fields
            $unicast->setExtraField("test", "helloworld");
            print("Sending unicast notification, please wait...\r\n");
            $unicast->send();
            print("Sent SUCCESS\r\n");
        } catch (Exception $e) {
            print("Caught exception: " . $e->getMessage());
        }


    }

    public function sendAndroidListcast($type,$title,$content,$userTokens,$notificationTitle="课间通知"){

        try {
            $unicast = new AndroidUnicast();
            $unicast->setAppMasterSecret($this->appMasterSecret);
            $unicast->setPredefinedKeyValue("appkey",           $this->appkey);
            $unicast->setPredefinedKeyValue("timestamp",        $this->timestamp);
            // Set your device tokens here
            $unicast->setPredefinedKeyValue("device_tokens",    $userTokens);
            $unicast->setPredefinedKeyValue("ticker",           $notificationTitle);
            $unicast->setPredefinedKeyValue("title",            $title);
            $unicast->setPredefinedKeyValue("text",             $content);
            $unicast->setPredefinedKeyValue("after_open",       "go_app");
            $unicast->setPredefinedKeyValue("display_type",       $type);
            // Set 'production_mode' to 'false' if it's a test device.
            // For how to register a test device, please see the developer doc.
            $unicast->setPredefinedKeyValue("production_mode", "true");

            // Set extra fields
            $unicast->setExtraField("test", "helloworld");
            print("Sending unicast notification, please wait...\r\n");
            $unicast->send();
            print("Sent SUCCESS\r\n");
        } catch (Exception $e) {
            print("Caught exception: " . $e->getMessage());
        }



    }
    function sendAndroidGroupcast($type,$title,$content,$tags,$notificationTitle="课间通知") {
        try {
            /*
              *  Construct the filter condition:
              *  "where":
              *	{
              *		"and":
              *		[
                *			{"tag":"test"},
                *			{"tag":"Test"}
              *		]
              *	}
              */
            $filter = 	array(
                "where" => 	array(
                    "and" 	=>  array(
                        $tags
                    )
                )
            );

            $groupcast = new AndroidGroupcast();
            $groupcast->setAppMasterSecret($this->appMasterSecret);
            $groupcast->setPredefinedKeyValue("appkey",           $this->appkey);
            $groupcast->setPredefinedKeyValue("timestamp",        $this->timestamp);
            // Set the filter condition
            $groupcast->setPredefinedKeyValue("filter",           $filter);
            $groupcast->setPredefinedKeyValue("ticker",           $notificationTitle);
            $groupcast->setPredefinedKeyValue("title",            $title);
            $groupcast->setPredefinedKeyValue("text",             $content);
            $groupcast->setPredefinedKeyValue("after_open",       "go_app");
            $groupcast->setPredefinedKeyValue("production_mode", "true");
            // Set 'production_mode' to 'false' if it's a test device.
            // For how to register a test device, please see the developer doc.
            $groupcast->setPredefinedKeyValue("display_type",       $type);
            print("Sending groupcast notification, please wait...\r\n");
            $groupcast->send();
            print("Sent SUCCESS\r\n");
        } catch (Exception $e) {
            print("Caught exception: " . $e->getMessage());
        }
    }




}