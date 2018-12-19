<?php
/**
 * 我的分享
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/19
 * Time: 20:57
 */

checkauth();

$pindex = max(1, intval($_GPC['page']));
$psize = 10;

$title = '我的分享';
$uid = $_W['member']['uid'];

/* 分享总次数 */
$share_total = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename($this->table_lesson_share) . ' WHERE uniacid=:uniacid AND uid=:uid AND status=1', array(':uniacid' => $uniacid, ':uid' => $uid));


$count = pdo_fetchcolumn('SELECT COUNT(s.id) FROM ' . tablename($this->table_lesson_share_userd) . ' WHERE ' . $conditions, $params);
$count = intval($count);

$list = pdo_fetchall('SELECT * FROM ' . tablename($this->table_lesson_parent) . ' WHERE uniacid=:uniacid AND price > 0 ORDER BY id DESC LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, array(':uniacid' => $uniacid));
foreach($list as $key => $item){
    $id = intval($item['id']);
    $flag = 0;
    /* 是否已兑换 */
    $share_info = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename($this->table_lesson_share_userd) . ' WHERE uniacid=:uniacid AND lessonid=:lid AND uid=:uid', array(':uniacid' => $uniacid, ':lid' => $id, ':uid' => $uid));
    if($share_info){
        $flag = 1;
    }
    /* 是否已购买 */
    $order_count = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename($this->table_order) . ' WHERE uniacid=:uniacid AND uid=:uid AND lessonid=:lid AND status>=1', array(':uniacid' => $uniacid, ':lid' => $id, ':uid' => $uid));
    if($order_count){
        $flag = 2;
    }

    $item['share_flag'] = $flag;
    $list[$key] = $item;
}

$residue = intval($share_total) - intval($count);

if($op == 'display'){
    include $this->template('myshare');
}elseif($op == 'ajaxgetlist'){
    echo json_encode($list);
}elseif($op =='exchange'){
    if($residue <= 0){
        message("没有兑换次数，请先分享", "", "error");
    }

    $id = intval($_GPC['id']);
    $share_info = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename($this->table_lesson_share_userd) . ' WHERE uniacid=:uniacid AND lessonid=:lid AND uid=:uid', array(':uniacid' => $uniacid, ':lid' => $id, ':uid' => $uid));
    if($share_info){
        message("已兑换，不能重复兑换", "", "error");
    }

    $userd_insert = array(
        'uniacid' => $uniacid,
        'lessonid' => $id,
        'uid' => $uid,
        'addtime' => time()
    );

    $i = pdo_insert($this->table_lesson_share_userd, $userd_insert);
    if(empty($i)){
        $info = pdo_fetch('SELECT * FROM ' . tablename($this->table_lesson_share) . ' WHERE uniacid=:uniacid AND uid=:uid AND status=0 LIMIT 1', array(':uniacid' => $uniacid, ':uid' => $uid));
        pdo_update($this->table_lesson_share, array('status' => 1), array('id' => $info['id']));
        message("兑换成功", $this->createMobileUrl('myshare'), "success");
    }

    message("兑换失败", "", "error");
}
