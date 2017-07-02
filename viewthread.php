<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/23
 * Time: 14:18
 */

# 修复7.2到x32后 viewthread.php 404的问题
header("Location: http://bbs.wow8.org/forum.php?mod=viewthread&{$_SERVER['QUERY_STRING']}");