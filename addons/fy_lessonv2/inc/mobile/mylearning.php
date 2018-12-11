<?php
/**
 * 我的学习
 * Created by PhpStorm.
 * User: appleimac
 * Date: 18/12/11
 * Time: 下午2:45
 */

checkauth();

$pindex = max(1, intval($_GPC['page']));
$psize = 10;

$title = '我的学习';
$uid = $_W['member']['uid'];
$status = $_GPC['status'];

$condition = " a.uniacid=:uniacid AND a.uid=:uid AND a.is_delete=:is_delete AND a.status=1";
$params[':uniacid'] = $uniacid;
$params[':uid'] = $uid;
$params[':is_delete'] = 0;


$mylearninglist = pdo_fetchall('SELECT a.*,b.images,b.difficulty,b.virtual_buynum FROM ' . tablename($this->table_order) . 'a LEFT JOIN ' . tablename($this->table_lesson_parent) . ' b ON a.lessonid=b.id WHERE {$condition} ORDER BY a.id DESC LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, $params);

if($op == 'display'){
    include $this->template('mylearning');
}elseif($op=='ajaxgetlist'){
    echo json_encode($mylearninglist);
}
