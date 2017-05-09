<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/8
 * Time: 11:48
 */

namespace app\common\Tools;


class LogUtils
{
    public static function log($msg, $_path = '',$logname='')
    {
        $dir = substr(dirname(__FILE__),0,-16);
        $path = $dir.'runtime/log/';
        if ($_path) {
            $path .= $_path . '/';
        }
        //判断路径是否存在
        $res = create_dir($path);
        //echo $dir;
        $fp = fopen($path . $logname.date('Ymd') . ".txt", "a");
        flock($fp, LOCK_EX);
        fwrite($fp, "执行日期：" . strftime("%Y%m%d%H%M%S", time()) . "\n".'$path='.$path."\n" . $msg . "\n"."\n");
        flock($fp, LOCK_UN);
        fclose($fp);
    }
}