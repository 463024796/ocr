<?php
/**
 * Created by PhpStorm.
 * User: as463
 * Date: 2017/10/26
 * Time: 19:15
 */
class GetImage
{

    public function curlGetGif($name, $path)
    {

        $headers = array();
        $headers[] = 'Accept:image/webp,image/*,*/*;q=0.8';
        $headers[] = 'Accept-Encoding:gzip, deflate, sdch';
        $headers[] = 'Accept-Language:zh-CN,zh;q=0.8';
        $headers[] = 'Cache-Control:no-cache';
        $headers[] = 'Connection:keep-alive';
        $headers[] = 'Host:211.66.88.71';
        $headers[] = 'Pragma:no-cache';
        $headers[] = 'Referer:http://211.66.88.71/jwweb/ZNPK/KBFB_ClassSel.aspx';
        $headers[] = 'User-Agent:Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $path);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);

        $downloaded_file = fopen("./captcha/".$name.".jpg", 'w');
        fwrite($downloaded_file, $result);
        fclose($downloaded_file);
        return $result;
    }
}