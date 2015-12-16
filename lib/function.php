<?php
function checkDoctorLogin()
{
    if (!isset($_SESSION["isLogin"]) || $_SESSION["isLogin"] != true || $_SESSION["loginType"] != 2){
        echo "您尚未登录!";
        header('location:index.php');
        exit;
    }
}

function checkHospitalAdminLogin()
{
    if (!isset($_SESSION["isLogin"]) || $_SESSION["isLogin"] != true || $_SESSION["loginType"] != 1){
        echo "您尚未登录!";
        header('location:index.php');
        exit;
    }
}

function include_head($title)
{
    echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
            <html xmlns="http://www.w3.org/1999/xhtml">
            <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
    echo "<title>$title</title>";
    echo '</head>';
}

function include_js_file()
{
    echo '<script type="text/javascript" src="' . URL_ROOT . 'js/jquery-1.7.1.min.js"></script>';
    echo '<script type="text/javascript" src="' . URL_ROOT . 'js/common.js"></script>';
}

function get_rows_by_resolution($height, $webView, $noticeRow = 0)
{
    if (1 == $webView) {
        switch ($height) {
            case 768:
                $rows = 6 - $noticeRow;
                break;
            case 900:
                $rows = 8 - $noticeRow;
                break;
            case 1024:
                $rows = 10 - $noticeRow;
                break;
            case 1050:
                $rows = 10 - $noticeRow;
                break;
            case 1080:
                $rows = 11 - $noticeRow;
                break;
            default:
                $rows = null;
        }
    }
    if (2 == $webView) {
        switch ($height) {
            case 768:
                $rows = 5;
                break;
            case 900:
                $rows = 6;
                break;
            case 1024:
                $rows = 8;
                break;
            case 1050:
                $rows = 8;
                break;
            case 1080:
                $rows = 9;
                break;
            default:
                $rows = null;
        }
    }
    if (3 == $webView) {
    
    }
    return $rows;
}

/**
 * @param string $message message of notice information.
 * @param integer $gotoFlag only 3 values are allowed
 *      GOTO_FLAG_EXIT | GOTO_FLAG_BACK | GOTO_FLAG_URL) 
 * @param string $url if $gotoFlag is given, $url is requried. if same folder, file name is ok.
 */
function user_goto($message, $gotoFlag, $url = null)
{
    if (GOTO_FLAG_URL === $gotoFlag && null == $url) {
        echo MESSAGE_OTHER_ERROR;
        exit;
    }
    if (GOTO_FLAG_EXIT === $gotoFlag) {
        echo $message;
        exit;
    }
    if (GOTO_FLAG_BACK === $gotoFlag) {
        if (null == $message) {
            echo '<script language="javascript">history.back();</script>';
        } else {
            echo '<script language="javascript">alert("' . $message . '");history.back();</script>';
        }
        exit;
    }
    if (GOTO_FLAG_URL === $gotoFlag) {
        if (null == $message) {
            echo '<script language="javascript">window.location.href="' . $url . '";</script>';
        } else {
            echo '<script language="javascript">alert("' . $message . '");window.location.href="' . $url . '";</script>';
        }
        exit;
    }
    echo MESSAGE_OTHER_ERROR;
    exit;
}

function user_back_after_delay($message, $delayTime, $url = null)
{
    echo $message;
    if (null == $url) {
        echo '<script language="javascript">setTimeout("history.back()", ' . $delayTime . ');</script>';
    } else {
        echo "<script language='javascript'>setTimeout('window.location.href=\"$url\"',$delayTime);</script>";
    }
    exit;
}

function check_user_existed($isExisted)
{
    if (VALUE_DB_ERROR === $isExisted) {
        user_goto(MESSAGE_DB_ERROR, GOTO_FLAG_BACK);
    }
    if (true === $isExisted) {
        user_goto('该账号已被他人使用。', GOTO_FLAG_BACK);
    }
}

/**
 * get paging(offset and navigation).
 * 
 * 1.http://domain
 * 2.http://domain?other_param=value
 * 3.http://domain?page=int
 * 4.http://domain?page=int&other_param=value
 * 5.http://domain?other_param=value&page=int
 * support:1,2,3,5
 * 
 * @param int $total
 * @param int $rows
 * @param string $url
 * @param int $currentPage
 * @return array
 */
function getPaging($total, $rows, $url, $currentPage) {
    $result = array();
    $result['offset'] = 0;
    $result['navigation'] = "共 $total 条记录";
    
    $url = trim($url, '?');
    if (false === stripos($url, '?')) {
        $url .= '?page';
    } elseif (is_null($currentPage)) {
        $url .= '&page';
    } else {
        $url = str_replace('?page=' . $currentPage, '?page', $url);
        $url = str_replace('&page=' . $currentPage, '&page', $url);
    }
    
    $lastPage = ceil($total / $rows);
    if ($lastPage <= 1) {
        return $result;
    }
    $currentPage = min($lastPage, $currentPage);
    if ($currentPage < 1) {
        $currentPage = 1;
    }
    $prePage = $currentPage -1;
    $nextPage = $currentPage == $lastPage ? 0 : $currentPage + 1;
    $offset = ($currentPage -1) * $rows;
    
    $navigation = " <a href='$url=1'>首页</a> ";
    if ($prePage == 0) {
        $navigation .= '  前页  ';
    } else {
        $navigation .= " <a href='$url=$prePage'> 前页 </a> ";
    }
    if ($nextPage == 0) {
        $navigation .= '  后页  ';
    } else {
        $navigation .= " <a href='$url=$nextPage'> 后页 </a> ";
    }
    $navigation .= " <a href='$url=$lastPage'> 尾页 </a> ";
    
    $result['offset'] = $offset;
    $result['navigation'] .= $navigation;
    return $result;
}

// $typeList = ['pdf'];
// function checkFileType($file, $typeList)
// {
//     $file = trim($file);
//     if ($file == '') {
//         return false;
//     }
//     $extension = strtolower(substr(strrchr($file, '.'), 1));
//     foreach ($typeList as $type) {
//         if ($type != $extension) {
//             return false;
//         }
//     }
//     return true;
// }
