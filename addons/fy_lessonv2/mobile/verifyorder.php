<?php
/**
 * 报名课程核销验证
 * ============================================================================
 * 版权所有 2015-2018 微课堂团队，并保留所有权利。
 * 网站地址: https://wx.haoshu888.com
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！不允许对程序代码以任何形式任何目的的再发布，作者将保留
 * 追究法律责任的权力和最终解释权。
 */

checkauth();
$uid = $_W['member']['uid'];

/* 订单信息 */
$orderStatusList = $this->orderStatus();
$orderid = intval($_GPC['orderid']);

$order = pdo_fetch("SELECT a.*,b.saler_uids,b.images FROM " .tablename($this->table_order). " a LEFT JOIN " .tablename($this->table_lesson_parent). " b ON a.lessonid=b.id WHERE a.id=:id AND a.status>:status", array(':id'=>$orderid,':status'=>0));
if(empty($order)){
	message("订单不存在!");
}

/* 报名信息 */
$appoint_info = json_decode($order['appoint_info'], true);

/* 核销信息 */
$verify_info = json_decode($order['verify_info'], true);
if($verify_info['verify_uid']>0){
	$verify_user = pdo_get($this->table_mc_members, array('uid'=>$verify_info['verify_uid']), array('nickname'));
}

$saler = json_decode($order['saler_uids'], true);
if(!in_array($uid, $saler)){
	message("您没有权限查看此课程订单!");
}

if($op == 'display'){
	$title = '课程订单核销详情';

}elseif($op == 'verify'){
	$title = '课程订单核销结果';

	if($order['is_verify'] == 1){
		message("订单已核销，核销员:".$verify_user['nickname']."(uid:".$verify_info['verify_uid'].")，核销时间:".date('Y-m-d H:i:s', $verify_info['verify_time']));
	}

	$now_verify_info = array(
		'verify_uid'  => $uid,
		'verify_time' => time(),
	);
	$data = array(
		'is_verify'		=> 1,
		'verify_info'	=> json_encode($now_verify_info),
	);

	if(pdo_update($this->table_order, $data, array('id'=>$orderid))){
		message("核销成功", $this->createMobileUrl('verifyorder', array('orderid'=>$orderid)), "success");
	}else{
		message("核销失败，请稍后重试", "", "error");
	}
}

include $this -> template('verifyOrder');

?>