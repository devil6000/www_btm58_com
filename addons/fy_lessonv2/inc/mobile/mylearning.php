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
$status = intval($_GPC['status']);

$condition = " a.uniacid=:uniacid AND a.uid=:uid AND a.status=:status";
$params[':uniacid'] = $uniacid;
$params[':uid'] = $uid;
$params[':status'] = $status;


//$mylearninglist = pdo_fetchall('SELECT a.*,b.images,b.difficulty,b.virtual_buynum FROM ' . tablename($this->table_order) . 'a LEFT JOIN ' . tablename($this->table_lesson_parent) . ' b ON a.lessonid=b.id WHERE {$condition} ORDER BY a.id DESC LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, $params);
$mylearninglist = pdo_fetchall("SELECT a.*,b.images,b.difficulty,b.virtual_buynum,b.bookname FROM " . tablename($this->table_mystudy) . " a LEFT JOIN " . tablename($this->table_lesson_parent) . " b ON a.lessonid=b.id WHERE {$condition} ORDER BY a.id DESC LIMIT " . ($pindex - 1) * $psize . "," . $psize, $params);
foreach ($mylearninglist as $key => $item){
    $item['addtime'] = date('Y-m-d H:i', $item['addtime']);
    //判断学习进度
    $scount = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename($this->table_lesson_son) . ' WHERE parentid=:pid', array(':pid' => $item['lessonid']));
    $learn = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename($this->table_mystudy_rate) . ' WHERE studyid=:sid AND status=1 AND uid=:uid', array(':sid' => $item['id'], ':uid' => $uid));
    if(empty($scount)){
        $rate = 0;
    }else{
        $rate = $learn / $scount * 100;
    }
    $item['rate'] = round($rate,2);
    $item['count'] = $scount;
    $item['learn'] = $learn;
    $mylearninglist[$key] = $item;
}

if($op == 'display'){
    include $this->template('mylearning');
}elseif($op=='ajaxgetlist'){
    echo json_encode($mylearninglist);
}
