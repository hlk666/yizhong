<?php
class HpPaging
{
    public static function getPaging($page, $lastPage, $currentPage = null)
    {
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
}