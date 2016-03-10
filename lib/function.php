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
//only for admin folder because using path:tpl/***.
function user_back_after_delay($message, $delayTime = 2000, $url = null)
{
    echo '<font color="#eb9316" size="5px">' . $message . '<br>' . ($delayTime / 1000) . '秒后自动跳转页面。</font>';
    if (null == $url) {
        echo '<script language="javascript">setTimeout("history.back()", ' . $delayTime . ');</script>';
    } else {
        echo "<script language='javascript'>setTimeout('window.location.href=\"$url\"',$delayTime);</script>";
    }
    include 'tpl/footer.tpl';
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

function getPaging($page, $lastPage) {
    $paging = '<li><a href="?page=1">首页</a></li>';
    if ($page == 1) {
        $paging .= '<li class="disabled"><a href="?page=1">前页</a></li>';
    } else {
        $paging .= '<li><a href="?page=' . ($page - 1) . '">前页</a></li>';
    }
    for ($i = 1; $i <= $lastPage; $i++) {
        $paging .= '<li';
        if ($page == $i) {
            $paging .= ' class="active"';
        }
        $paging .= '><a href="?page=' . $i . '">' . $i . '</a></li>';
    }
    if ($page == $lastPage) {
        $paging .= '<li  class="disabled"><a href="?page=' . $lastPage . '">后页</a></li>';
    } else {
        $paging .= '<li><a href="?page=' . ($page + 1) . '">后页</a></li>';
    }
    $paging .= '<li><a href="?page=' . $lastPage . '">尾页</a></li>';
    return $paging;
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
