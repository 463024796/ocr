<?php

require_once "files.php";
require_once "GetImage.php";
class OCR
{
    public $imagePath;
    private $DataArray;
    private $ImageSize;
    public $maxfontwith = 50;
    public $data;
    private $Keys;
    private $ImagePath;
    private $imageName;
    private $getImage;

    public function __construct($imgpath)
    {
        $this->imagePath = $imgpath;
        $keysfiles = new files;
        $this->Keys = $keysfiles->funserialize();
        if($this->Keys == false)
            $this->Keys = array();
        unset($keysfiles);
        $this->getImage = new GetImage();
        $this->imageName = rand(999,9999);
    }
    public function setImagePath($img)
    {
        $this->ImagePath = $img;
    }

    public function getCaptcha()
    {

        $this->getImage->curlGetGif($this->imageName, $this->imagePath);
//        $this->imageName = "FYPC";
        $this->ImagePath = "./captcha/".$this->imageName.".jpg";

        $this->getHec();
        $this->filterInfo();
        //显示库中的
        foreach ($this->DataArray as $key => $items) {
            foreach ($items as $item) {
                echo $item;
            }echo "<br>";
        }

        foreach ($this->data as $key => $items) {
            foreach ($items as $k => $ss) {
                foreach ($ss as $s) {
                    echo $s;
                }echo "<br>";

            }
        }
        $this->run();
    }


    public function getHec()
    {
        $res = imagecreatefromjpeg($this->ImagePath);
        $size = getimagesize($this->ImagePath);
        $data = array();
        for($i=0; $i < $size[1]; ++$i)
        {
//			echo "$i  R  G  B\n";
            for($j=0; $j < $size[0]; ++$j)
            {
                $rgb = imagecolorat($res,$j,$i);
                $rgbarray = imagecolorsforindex($res, $rgb);
//				echo "  ".$rgbarray['red']." ";
//				echo $rgbarray['green']." ";
//				echo $rgbarray['blue']." \n";
//				/*
                if(($rgbarray['red'] > 120 &&( $rgbarray['green']< 155 || $rgbarray['blue'] < 155)) ) // 不知道什么颜色
                {
                    $data[$i][$j]=1;
                }else if (( $rgbarray['red'] < 120 && ( $rgbarray['green'] > 155 || $rgbarray['blue'] > 155))) {
                    $data[$i][$j]=1;
                } else if (( $rgbarray['red'] < 120 && ( $rgbarray['green'] > 100 || $rgbarray['blue'] > 155))) {
                    $data[$i][$j]=1;
                } else if (( $rgbarray['red'] < 180 && ( $rgbarray['green'] < 80 || $rgbarray['blue'] >188))) {
                    $data[$i][$j]=1;
                } else if (( $rgbarray['red'] > 220 &&  $rgbarray['green'] > 220 && $rgbarray['blue'] >220)){
                    $data[$i][$j]=0;
                }else {
                    $data[$i][$j]=0;

                }
//				*/
            }
        }
        // 如果1的周围数字不为1，修改为了0
        for($i=0; $i < $size[1]; ++$i)
        {
            for($j=0; $j < $size[0]; ++$j)
            {
                $num = 0;
                if($data[$i][$j] == 1)
                {
                    // 上
                    if(isset($data[$i-1][$j])){
                        $num = $num + $data[$i-1][$j];
                    }
                    // 下
                    if(isset($data[$i+1][$j])){
                        $num = $num + $data[$i+1][$j];
                    }
                    // 左
                    if(isset($data[$i][$j-1])){
                        $num = $num + $data[$i][$j-1];
                    }
                    // 右
                    if(isset($data[$i][$j+1])){
                        $num = $num + $data[$i][$j+1];
                    }
                    // 上左
                    if(isset($data[$i-1][$j-1])){
                        $num = $num + $data[$i-1][$j-1];
                    }
                    // 上右
                    if(isset($data[$i-1][$j+1])){
                        $num = $num + $data[$i-1][$j+1];
                    }
                    // 下左
                    if(isset($data[$i+1][$j-1])){
                        $num = $num + $data[$i+1][$j-1];
                    }
                    // 下右
                    if(isset($data[$i+1][$j+1])){
                        $num = $num + $data[$i+1][$j+1];
                    }
                }
                if($num < 3){
                    $data[$i][$j] = 0;
                }
                //前10行gg
                if ($i <10){
                    $data[$i][$j] = 0;
                }
                //第一列gg
                if ($j == 0) {
                    $data[$i][0] = 0;
                }
                //最后2行gg
                if ($i == $size[1]-1 || $i == $size[1] -2) {
                    $data[$i][$j] = 0;
                }
            }
        }
        $this->DataArray = $data;
        $this->ImageSize = $size;
    }

    /**
     * 分割字符串
     */
    public function filterInfo()
    {
        $data=array();
        $num = 0;
        $b = false;
        $Continue = 0;
        $XStart = 0;
        // X 坐标
        for($i=0; $i<$this->ImageSize[0]; ++$i)
        {
            // Y 坐标
            for($j=0; $j<$this->ImageSize[1]; ++$j)
            {
                if($this->DataArray[$j][$i] == "1")
                {
                    $b = true;
                    ++$Continue;
                    break;
                }else{
                    $b = false;
                }
            }
            if($b == true)
            {
                for($jj = 0; $jj < $this->ImageSize[1]; ++$jj)
                {
                    $data[$num][$jj][$XStart] = $this->DataArray[$jj][$i];
                }
                ++$XStart;

            }else{
                if($Continue > 0){
                    $XStart = 0;
                    $Continue = 0;
                    ++$num;
                }
            }
        }
//        echo "sdfsdf:<br>";
//        foreach($data as $key => $val) {
//            foreach ($val as $k => $v) {
//                foreach ($v as $vv){
//                    echo $vv;
//                }echo "<br>";
//            }
//        }
//        var_dump($data);
        // 粘连字符分割
        $inum = 0;
        for($num =0; $num < count($data); ++$num)
        {
            $itemp = 5;
            $str = implode("",$data[$num][$itemp]);
            // 超过标准长度
            if(strlen($str) > $this->maxfontwith)
            {
                $len = (strlen($str)+1)/2;
                $flen = strlen($str);
                $ih = 0;
//				$iih = 0;
                foreach($data[$num] as $key => $value)
                {
                    $ix = 0;
                    $ixx = 0;
                    foreach($value as $skey=>$svalue)
                    {
                        if($skey < $len)
                        {
                            $this->data[$inum][$ih][$ix] = $svalue;
                            ++$ix;
                        }
                        if($skey > ($flen-$len-1))
                        {
                            $this->data[$inum+1][$ih][$ixx] = $svalue;
                            ++$ixx;
                        }
                    }
                    ++$ih;
                }
                ++$inum;
            }else{
                $i = 0;
                foreach($data[$num] as $key => $value){
                    $this->data[$inum][$i] = $value;
                    ++$i;
                }
            }
            ++$inum;
        }
//        foreach($this->data as $key => $val) {
//            foreach ($val as $k => $v) {
//                foreach ($v as $vv){
//                    echo $vv;
//                }echo "<br>";
//            }
//        }

        // 去掉0数据        这里容易出错。干扰点太多。或者是两个字母重叠在一起的时候。这样会让人奔溃的。所以当出现这样的问题。直接换一个验证码！
        for($num = 0; $num < count($this->data); ++$num)
        {
            if(count($this->data[$num]) != $this->ImageSize[1])
            {
//                echo $num;
//                echo count($this->data[$num]).">>>".$this->ImageSize[1];
                echo "分割字符错误";
                continue;
            }

            for($i=0; $i < $this->ImageSize[1]; ++$i)
            {
                $str = implode("",$this->data[$num][$i]);
                $pos = strpos($str, "1");
                if($pos === false)
                {
                    unset($this->data[$num][$i]);
                }
            }
//            var_dump($this->data);
//            foreach ($this->data as $key => $val) {
//                foreach ($val as $k => $vv) {
//                    foreach ($vv as $vvv) {
//                        echo $vvv;
//                    }echo "<br>";
//                }echo "<br>";
//            }
        }

    }

    public function run()
    {
//        $this->changeKeys();
        $result = '';
        $data = array();
        $i = 0;
        foreach($this->data as $key => $value)
        {
            $data[$i] = "";
            foreach($value as $skey => $svalue)
            {
                $data[$i] .= implode("",$svalue);
            }
            if(strlen($data[$i]) > $this->maxfontwith)
                ++$i;
        }
        // 进行关键字匹配
        foreach($data as $numKey => $numString)
        {
            $max=0.0;
            $num = 0;
            foreach($this->Keys as $key => $value)
            {
                $FindOk = false;
                foreach($value as $skey => $svalue)
                {
//					print_r($svalue);exit;
                    $percent=0.0;
                    $maxx = similar_text($svalue, $numString,$percent);

//					print_r(intval($percent));
//					echo " ";
                    if(intval($percent) > $max)
                    {
                        $max = $percent;
                        $num = $skey;
                        if(intval($percent) > 96){
                            $FindOk = true;
                            break;
                        }
                    }
                }
                if($FindOk)
                    break;
            }
//			echo "\n max=";
//			echo $max;
//			echo "\n";
//			echo $num."\n";exit;
            $result.=$num;
        }
        echo $result;
        // 查找最佳匹配数字
        return $result;
    }

    public function changeKeys()
    {
        $array = [];
        foreach ($this->Keys as $key => $val) {
            $array[$val][] = $key;
        }
        $this->Keys = $array;
    }



    public function study($info)
    {
        // 做成字符串
        $data = array();
        $i = 0;
        foreach($this->data as $key => $value)
        {
            $data[$i] = "";
            foreach($value as $skey => $svalue)
            {
                $data[$i] .= implode("",$svalue);
            }
            if(strlen($data[$i]) > $this->maxfontwith)
                ++$i;
        }
        if(count($data) != count($info))
        {

//			echo count($data)."\n";
//			print_r($data);
//			echo count($info)."\n";
            echo "设置数据库数据出错";
            // print_r($data);
            echo "<pre>";
            var_dump($data);
            return false;
        }

        // 设置N级匹配模式
        foreach($info as $key => $value)
        {
            if(isset($this->Keys[0][$value])){
//				print_r($value);
                $percent=0.0;
                similar_text($this->Keys[0][$value], $data[$key],$percent);
//				print_r($percent);
//				print_r(" \n");
                if(intval($percent) < 96)
                {
                    $i=1;
                    $OK = false;
                    while(isset($this->Keys[$i][$value]))
                    {
                        $percent=0.0;
//						print_r($value);
                        similar_text($this->Keys[$i][$value], $data[$key],$percent);
//						print_r($percent);
//						print_r(" \n");
                        if(intval($percent) > 96){
                            $OK = true;
                            break;
                        }
                        ++$i;
                    }
                    if(!$OK){
//						while(!isset($this->Keys[$i++][$value])){
//							print_r($i);
//						print_r($i);
//						print_r(" \n");
                        $this->Keys[$i][$value] = $data[$key];
//							$i++;
//						}
                    }
                }

            }else{
                $this->Keys[0][$value] = $data[$key];
            }
        }
        return true;
    }

    public function getKeys()
    {
        var_dump($this->Keys);
    }

}