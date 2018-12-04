<?php
/**
 * 课程订单详情
 * ============================================================================
 * 版权所有 2015-2018 微课堂团队，并保留所有权利。
 * 网站地址: https://wx.haoshu888.com
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！不允许对程序代码以任何形式任何目的的再发布，作者将保留
 * 追究法律责任的权力和最终解释权。
 */

checkauth();
$uid = $_W['member']['uid'];

/* 订单状态名称 */
$orderStatusList = $this->orderStatus();

if($op == 'display'){
	$title = '订单详情';

	$orderid = intval($_GPC['orderid']);
	$order = pdo_fetch("SELECT a.*,b.images FROM " .tablename($this->table_order). " a LEFT JOIN " .tablename($this->table_lesson_parent). " b ON a.lessonid=b.id WHERE a.id=:id AND a.uid=:uid ", array(':id'=>$orderid, ':uid'=>$uid));
	if(empty($order)){
		message("订单不存在!");
	}

	/* 报名课程信息 */
	$appoint_info = json_decode($order['appoint_info'], true);

	/* 核销信息 */
	$verify_info = json_decode($order['verify_info'], true);
	if($verify_info['verify_uid']>0){
		$verify_user = pdo_get($this->table_mc_members, array('uid'=>$verify_info['verify_uid']), array('nickname'));
	}

	/* 报名课程核销二维码 */
	if($order['lesson_type']==1){
		include(IA_ROOT."/framework/library/qrcode/phpqrcode.php");
		$dirpath = "../attachment/images/{$uniacid}/fy_lessonv2/";
		$this->checkdir($dirpath);

		$qrcodeUrl = $_W['siteroot'].'app/'.$this->createMobileUrl('verifyorder', array('orderid'=>$orderid));
		$qrcode = $dirpath.'order_'.$orderid.'.png'; /* 生成的文件名 */
		QRcode::png($qrcodeUrl, $qrcode, 'L', 4, 2);
	}

}

include $this -> template('orderDetail');

?>