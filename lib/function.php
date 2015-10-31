<?php
function checkLogin()
{
    if (!isset($_SESSION["isLogin"]) || $_SESSION["isLogin"] != true || $_SESSION["loginType"] != 2){
        return false;
    }
    return true;
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
        $navigation .= ' 前页 ';
    } else {
        $navigation .= " <a href='$url=$prePage'>前页</a> ";
    }
    if ($nextPage == 0) {
        $navigation .= ' 后页 ';
    } else {
        $navigation .= " <a href='$url=$nextPage'>后页</a> ";
    }
    $navigation .= " <a href='$url=$lastPage'>尾页</a> ";
    
    $result['offset'] = $offset;
    $result['navigation'] .= $navigation;
    return $result;
}