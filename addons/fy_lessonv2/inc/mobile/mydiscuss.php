<?php
/**
 * 我参与的讨论
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/16
 * Time: 19:25
 */
checkauth();

$title = '我参与的讨论';

$uid = $_W['member']['uid'];

$pindex = max(1,intval($_GPC['page']));
$psize = 10;

$nickname = pdo_fetchcolumn('SELECT nickname FROM ' . tablename($this->table_mc_members) . ' WHERE uid=:uid AND uniacid=:uniacid', array(':uid' => $uid, ':uniacid' => $uniacid));

$conditions = 'm.uid=:uid AND m.uniacid=:uniacid';
$params = array(':uid' => $uid, ':uniacid' => $uniacid);

$list = pdo_fetchall('SELECT m.uid,d.addtime,d.title FROM ' . tablename($this->table_mydiscuss) . ' m LEFT JOIN ' . tablename($this->table_discuss) . ' d ON m.discussid=d.id WHERE m.uid=:uid AND m.uniacid=:uniacid ORDER BY m.id DESC LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, $params);
foreach ($list as $key => $item){
    $list[$key]['addtime'] = date('Y-m-d', $item['addtime']);
    $list[$key]['nickname'] = empty($nickname) ? '未设置' : $nickname;
}
//print(json_encode(array('status' => 'success', 'pagecount' => $psize, 'list' => $list)));
if($op == 'display'){
    include $this->template('mydiscuss');
}elseif($op == 'ajaxgetlist'){
    echo json_encode($list);
    exit;
}
