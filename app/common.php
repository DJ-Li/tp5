<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------
// 应用公共文件


/**
 * 冒泡排序小到大
 * @param $arr array
 * @return array
 * */
function min_bubble($arr)
{
    $len = count($arr);
    for ($i = 1; $i < $len; $i++) {// 进行第一层遍历,最多做n-1趟排序
        $flag = false;    //本趟排序开始前，交换标志应为假
        for ($j = $len - 1; $j >= $i; $j--) {
            // 进行第二层遍历 将数组中每一个元素都与外层元素比较
            // 这里的i+1意思是外层遍历当前元素往后的
            if ($arr[$j] < $arr[$j - 1]) {//交换记录
                $temp = $arr[$j];
                $arr[$j] = $arr[$j - 1];
                $arr[$j - 1] = $temp;
                $flag = true;//发生了交换，故将交换标志置为真
            }
        }
        if (!$flag)//本趟排序未发生交换，提前终止算法
            return $arr;
    }
}

/**
 * 冒泡排序大到小
 * @param $arr array
 * @return array
 */
function max_bubble($arr)
{
    $len = count($arr);
    for ($i = 1; $i < $len; $i++) {// // 进行第一层遍历,最多做n-1趟排序
        $flag = false;    //本趟排序开始前，交换标志应为假
        // 进行第二层遍历 将数组中每一个元素都与外层元素比较
        // 这里的i+1意思是外层遍历当前元素往后的
        for ($j = $len - 1; $j >= $i; $j--) {
            if ($arr[$j] > $arr[$j - 1]) {//交换记录
                $temp = $arr[$j];
                $arr[$j] = $arr[$j - 1];
                $arr[$j - 1] = $temp;
                $flag = true;//发生了交换，故将交换标志置为真
            }
        }
        if (!$flag)//本趟排序未发生交换，提前终止算法
            return $arr;
    }
}

/**
 * 快速排序
 * @param $arr array
 * @return array
 */
function quick_sort($arr)
{
    $len = count($arr);
    if ($len <= 1) {
        return $arr;
    }
    $key = $arr[0];
    $left_arr = array();
    $right_arr = array();
    for ($i = 1; $i < $len; $i++) {
        if ($arr[$i] <= $key) {
            $left_arr[] = $arr[$i];
        } else {
            $right_arr[] = $arr[$i];
        }
    }
    $left_arr = quick_sort($left_arr);
    $right_arr = quick_sort($right_arr);
    return array_merge($left_arr, array($key), $right_arr);
}

function partition(&$arr, $low, $high)
{
    $pivotkey = $arr[$low];
    while ($low < $high) {
        while ($low < $high && $arr[$high] >= $pivotkey) {
            $high--;
        }
        $temp = $arr[$low];
        $arr[$low] = $arr[$high];
        $arr[$high] = $temp;
        while ($low < $high && $arr[$low] <= $pivotkey) {
            $low++;
        }
        $temp = $arr[$low];
        $arr[$low] = $arr[$high];
        $arr[$high] = $temp;
    }
    return $low;
}


//function quick_sort(&$arr, $low, $high)
//{
//    if ($low < $high) {
//        $pivot = partition($arr, $low, $high);
//        quick_sort($arr, $low, $pivot - 1);
//        quick_sort($arr, $pivot + 1, $high);
//    }
//}

/**
 * 二维数组排序，$arr是数据，$keys是排序的健值，$order是排序规则，1是降序，0是升序
 */
function array_sort($arr, $keys, $order = 0)
{
    if (!is_array($arr)) {
        return false;
    }
    $keysvalue = array();
    foreach ($arr as $key => $val) {
        $keysvalue[$key] = $val[$keys];
    }
    if ($order == 0) {
        asort($keysvalue);
    } else {
        arsort($keysvalue);
    }
    reset($keysvalue);
    foreach ($keysvalue as $key => $vals) {
        $keysort[$key] = $key;
    }
    $new_array = array();
    foreach ($keysort as $key => $val) {
        $new_array[$key] = $arr[$val];
    }
    return $new_array;
}

/**
 * 顺序查找
 * @param  array $arr 数组
 * @param   $k   要查找的元素
 * @return   mixed  成功返回数组下标，失败返回-1
 */
function seq_sch($arr, $k)
{
    for ($i = 0, $n = count($arr); $i < $n; $i++) {
        if ($arr[$i] == $k) {
            break;
        }
    }
    if ($i < $n) {
        return $i;
    } else {
        return -1;
    }
}


/**
 * 二分查找，要求数组已经排好顺序
 * @param  array $array 数组
 * @param  int $low 数组起始元素下标
 * @param  int $high 数组末尾元素下标
 * @param   $k     要查找的元素
 * @return mixed        成功时返回数组下标，失败返回-1
 */
function bin_sch($array, $low, $high, $k)
{
    if ($low <= $high) {
        $mid = intval(($low + $high) / 2);
        if ($array[$mid] == $k) {
            return $mid;
        } elseif ($k < $array[$mid]) {
            return bin_sch($array, $low, $mid - 1, $k);
        } else {
            return bin_sch($array, $mid + 1, $high, $k);
        }
    }
    return -1;
}

/**
 * 创建多级目录
 * @param $path string 要创建的目录
 * @param $mode int 创建目录的模式，在windows下可忽略
 * @return string
 */
function create_dir($path, $mode = 0777)
{
    if (is_dir($path)) {
        # 如果目录已经存在，则不创建
        return "该目录已经存在";
    } else {
        # 不存在，创建
        if (mkdir($path, $mode, true)) {
            return "创建目录成功";
        } else {
            return "创建目录失败";
        }
    }
}

/**
 * 遍历一个文件夹下的所有文件和子文件夹
 * @param $dir
 * @return array
 */
function my_scandir($dir)
{
    $files = array();
    if (is_dir($dir)) {//先判断是不是
        if ($handle = opendir($dir)) {// 打开目录，然后读取其内容
            while (($file = readdir($handle)) !== false) {//函数返回目录中下一个文件的文件名
                if ($file != "." && $file != "..") {
                    if (is_dir($dir . "/" . $file)) {//如果$file下还用目录将进行递归继续
                        $files[$file] = my_scandir($dir . "/" . $file);
                    } else {
                        $files[] = $dir . "/" . $file;
                    }
                }
            }
            closedir($handle);//函数关闭目录句柄
            return $files;
        }
    }
}

/**
 * 确保多个进程同时写入同一个文件成功
 * 核心思路：加锁
 * @param $path string
 * @param $data string
 * @return string
 */
function my_lock($path, $data)
{
    $fp = fopen($path, "w+");
    if (flock($fp, LOCK_EX)) {
        //获得写锁，写入数据文件（可安全用于二进制文件）
        fwrite($fp, $data);

        // 解除锁定
        flock($fp, LOCK_UN);
    } else {
        return "文件锁定!";
    }
    //函数关闭一个打开文件
    fclose($fp);
}

/**
 * 递归遍历，实现无限分类
 * @param $arr array
 * @param $pid int
 * @param $level int
 * @return array
 */
function my_tree($arr, $pid = 0, $level = 0)
{
    static $list = array();
    foreach ($arr as $v) {
        //如果是顶级分类，则将其存到$list中，并以此节点为根节点，遍历其子节点
        if ($v['parent_id'] == $pid) {
            $v['level'] = $level;
            $list[] = $v;
            my_tree($arr, $v['cat_id'], $level + 1);
        }
    }
    return $list;
}

/**
 * 算出两个文件的相对路径
 * @param $path1 string
 * @param $path2 string
 * @return string
 */

function releative_path($path1, $path2)
{
    $arr1 = explode("/", dirname($path1));
    $arr2 = explode("/", dirname($path2));
    for ($i = 0, $len = count($arr2); $i < $len; $i++) {
        if ($arr1[$i] != $arr2[$i]) {
            break;
        }
    }
    // 不在同一个根目录下
    if ($i == 1) {
        $return_path = array();
    }
    // 在同一个根目录下
    if ($i != 1 && $i < $len) {
        $return_path = array_fill(0, $len - $i, "..");
    }
    // 在同一个目录下
    if ($i == $len) {
        $return_path = array('./');
    }
    $return_path = array_merge($return_path, array_slice($arr1, $i));
    return implode('/', $return_path);
}

/**
 * 高效从一个标准url里取出文件的扩展名
 */

/**
 * 方案二
 * @param $url string
 * @return string
 */
function get_ext1($url)
{
    $arr = parse_url($url);
    $file = basename($arr['path']);
    $ext = explode('.', $file);
    return $ext[count($ext) - 1];
}

/**
 * 方案二
 * @param $url string
 * @return string
 */

function get_ext2($url)
{
    $url = basename($url);
    $pos1 = strpos($url, '.');
    $pos2 = strpos($url, '?');
    if (strstr($url, '?')) {
        return substr($url, $pos1 + 1, $pos2 - $pos1 - 1);
    } else {
        return substr($url, $pos1);
    }
}



/**
 * 正则检查ID
 * @param $id string
 * @return boolean
 */

function check_id($id)
{
    if (preg_match('/^\+?[1-9][0-9]*$/', $id)) {
        return true;
    }
    return false;
}

/**
 * 正则检查是数字
 * @param $number string
 * @return boolean
 */
function check_number($number)
{
    if (preg_match('/^[-\\+]?[\\d]*$/', $number)) {
        return true;
    }
    return false;
}

/**
 * 正则检查电话号码是否正确
 * @param $tel string 电话号码
 * @param $type string 电话类型 sj=>手机 Tel=>座机 400=> 400电话
 * @return boolean
 */
function check_tel($tel, $type = '')
{
    $regx_arr = array(
        'sj' => '/^(\+?86-?)?(13|15|18|17|14)[0-9]{9}$/',
        'tel' => '/^(010|02\d{1}|0[3-9]\d{2})-\d{7,9}(-\d+)?$/',
        '400' => '/^400(-\d{3,4}){2}$/',
    );
    if ($type && isset($regx_arr[$type])) {
        return preg_match($regx_arr[$type], $tel) ? true : false;
    }
    foreach ($regx_arr as $regx) {
        if (preg_match($regx, $tel)) {
            return true;
        }
    }
    return false;
}

/**
 * 判断邮箱格式
 * @param $email string
 * @return boolean
 */
function check_email($email)
{
    if (preg_match('/^[\w\-\.]+@[\w\-]+(\.\w+)+$/', $email)) {
        return true;
    }
    return false;
}


/**
 * 判断一个字符串是否是合法的日期模式
 * @param $data string 时间戳
 * @param $type string 默认 :"Y-M-d H:i:s"
 * @return boolean
 */
function check_date_time($data,$type = 'Y-m-d H:i:s')
{
    if (date($type, strtotime($data)) == $data) {
        return true;
    } else {
        return false;
    }
}
/**
 * 时间格式
 * @param $data string
 * @param $type string
 * @return  string
 */
function times_format($data,$type = 'Y-m-d H:i:s')
{
    $data = $data?:time();
    return date($type,$data);
}
/**
 * 获取给定月份的上一月最后一天
 * @param $date string 给定日期
 * @return string 上一月最后一天
 */
function get_last_month_last_day($date = '')
{
    date_default_timezone_set('PRC');
    if ($date != '') {
        $time = strtotime($date);
    } else {
        $time = time();
    }
    $day = date('j', $time);//获取该日期是当前月的第几天
    return date('Y-m-d', strtotime("-{$day} days", $time));
}
/**
 * MD5加密
 * @param $str string
 * @param $times int 加密次数
 * @return string
 */
function my_md5($str, $times = 1)
{
    for ($i = 1; $i <= $times; $i++) {
        $str = md5($str);
    }
    return $str;
}


