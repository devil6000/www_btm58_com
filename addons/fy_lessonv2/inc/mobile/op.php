<?php
/**
 * 订单支付
 * Created by PhpStorm.
 * User: appleimac
 * Date: 18/12/27
 * Time: 上午10:14
 */

checkauth();

if($op == 'confirm'){
    $orderid = intval($_GPC['orderid']);
    $modal = trim($_GPC['modal']);
    if(!in_array($modal, array('recharge'))){
        message('没有找到需要支付的订单','','error');
    }

    if($modal == 'recharge'){
        $order = pdo_fetch('SELECT * FROM ' . tablename($this->table_recharge_order) . ' WHERE id=:id', array(':id' => $orderid));
        if($order['status'] != 0){
            message('抱歉，您的订单已经付款或是被关闭', $this->createMobileUrl('recharge'), 'error');
        }

        $params['tid']     = $orderid;
        $params['user']    = $_W['openid'] ? $_W['openid'] : $order['uid'];
        $params['fee']     = $order['money'];
        $params['title']   = '充值金额：' . $order['money'];
        $params['ordersn'] = $order['ordersn'];
        $params['virtual'] = false;
    }

    $paylog = pdo_get($this->table_core_paylog, array('tid' => $orderid, 'status'=>0));
    if(!empty($paylog)){
        pdo_delete($this->table_core_paylog, array('tid' => $orderid));
    }

    load()->model('activity');
    load()->model('module');
    activity_coupon_type_init();
    if(!$this->inMobile) {
        message('支付功能只能在手机上使用', '', '');
    }
    $params['module'] = $this->module['name'];
    $log = pdo_get($this->table_core_paylog, array('uniacid' => $_W['uniacid'], 'module' => $params['module'], 'tid' => $params['tid']));
    if (empty($log)) {
        $log = array(
            'uniacid' => $_W['uniacid'],
            'acid' => $_W['acid'],
            'openid' => $_W['member']['uid'],
            'module' => $this->module['name'],
            'tid' => $params['tid'],
            'fee' => $params['fee'],
            'card_fee' => $params['fee'],
            'status' => '0',
            'is_usecard' => '0',
        );
        pdo_insert('core_paylog', $log);
    }
    if($log['status'] == '1') {
        message('这个订单已经支付成功, 不需要重复支付.', '', 'info');
    }
    $setting = uni_setting($_W['uniacid'], array('payment', 'creditbehaviors'));
    if(!is_array($setting['payment'])) {
        message('没有有效的支付方式, 请联系网站管理员.', '', 'error');
    }
    $pay = $setting['payment'];
    foreach ($pay as &$value) {
        $value['switch'] = $value['pay_switch'];
    }
    unset($value);
    if (empty($_W['member']['uid'])) {
        $pay['credit']['switch'] = false;
    }
    if ($params['module'] == 'paycenter') {
        $pay['delivery']['switch'] = false;
        $pay['line']['switch'] = false;
    }
    if (!empty($pay['credit']['switch'])) {
        $credtis = mc_credit_fetch($_W['member']['uid']);
        $credit_pay_setting = mc_fetch($_W['member']['uid'], array('pay_password'));
        $credit_pay_setting = $credit_pay_setting['pay_password'];
    }
    include $this->template('op');
}