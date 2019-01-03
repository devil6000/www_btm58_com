<?php
/**
 * 充值
 * Created by PhpStorm.
 * User: appleimac
 * Date: 18/12/27
 * Time: 下午3:14
 */

checkauth();

$uid = $_W['member']['uid'];

$title = '充值';

if($op == 'display'){
    $member = pdo_fetch('SELECT * FROM ' . tablename($this->table_mc_members) . ' WHERE uid=:id', array(':id' => $uid));
    $credit2 = $member['credit2'];

    include $this->template('recharge');
} elseif ($op == 'recharge'){
    $money = floatval($_GPC['money']);

    $ordersn = date('Ymd') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
    $insert = array(
        'uniacid' => $uniacid,
        'uid' => $uid,
        'ordersn' => $ordersn,
        'money' => $money,
        'addtime' => time(),
        'status' => 0
    );

    pdo_insert($this->table_recharge_order, $insert);
    $orderid = pdo_insertid();

    header("Location:" . $this->createMobileUrl('op', array('op' => 'confirm','orderid' => $orderid, 'modal' => 'recharge')));

}