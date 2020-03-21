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

function getPaging($page, $lastPage, $currentPage = null) {
    if (null !== $currentPage) {
        $link = '<a href="' . $currentPage . '&page=';
    } else {
        $link = '<a href="?page=';
    }
    
    $paging = '<li>' . $link . '1">首页</a></li>';
    if ($page == 1) {
        $paging .= '<li class="disabled">' . $link . '1">前页</a></li>';
    } else {
        $paging .= '<li>' . $link . ($page - 1) . '">前页</a></li>';
    }
    for ($i = 1; $i <= $lastPage; $i++) {
        $paging .= '<li';
        if ($page == $i) {
            $paging .= ' class="active"';
        }
        $paging .= '>' . $link . $i . '">' . $i . '</a></li>';
    }
    if ($page == $lastPage) {
        $paging .= '<li  class="disabled">' . $link . $lastPage . '">后页</a></li>';
    } else {
        $paging .= '<li>' . $link . ($page + 1) . '">后页</a></li>';
    }
    $paging .= '<li>' . $link . $lastPage . '">尾页</a></li>';
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
function request($url, $post)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

function refreshCacheFile($isAdd, $file, $separator, $id, $outerSeparator = '', $text = '')
{
    $data = array();
    if (empty($outerSeparator)) {
        $list = explode($separator, file_get_contents($file));
        foreach ($list as $item) {
            if (!empty($item) && $item != $id) {
                $data[] = $item;
            }
        }
        if ($isAdd) {
            $data[] = $id;
        }
        file_put_contents($file, implode($separator, $data));
    } else {
        $list = explode($outerSeparator, file_get_contents($file));
        foreach ($list as $item) {
            if (empty($item)) {
                continue;
            }
            $detail = explode($separator, $item);
            if (isset($detail[0]) && $detail[0] != $id) {
                $data[] = $item;
            }
        }
        if ($isAdd) {
            $data[] = $text;
        }
        file_put_contents($file, implode($outerSeparator, $data));
    }
}
