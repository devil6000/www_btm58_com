<?php
/**
 * 限时折扣
 * ============================================================================
 * 版权所有 2015-2018 微课堂团队，并保留所有权利。
 * 网站地址: https://wx.haoshu888.com
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！不允许对程序代码以任何形式任何目的的再发布，作者将保留
 * 追究法律责任的权力和最终解释权。
 */

$pindex =max(1,$_GPC['page']);
$psize = 10;

if($op=='display'){
	$discount_id = intval($_GPC['discount_id']);
	$discount = pdo_get($this->table_discount, array('uniacid'=>$uniacid, 'discount_id'=>$discount_id));
	if(empty($discount)){
		message('限时折扣活动不存在');
	}
	if($discount['starttime'] > time()){
		message('限时折扣活动未开始');
	}
	if($discount['endtime'] < time()){
		message('限时折扣活动已结束');
	}

	$banner = pdo_fetch("SELECT * FROM " .tablename($this->table_banner). " WHERE uniacid=:uniacid AND banner_type=:banner_type AND link LIKE :link", array(':uniacid'=>$uniacid,':banner_type'=>2, ':link'=>"%&discount_id={$discount_id}&%"));

	$title = $discount['title'];
	$condition = " b.uniacid=:uniacid AND b.discount_id=:discount_id";
	$params[':uniacid'] = $uniacid;
	$params[':discount_id'] = $discount_id;
	
	$list = pdo_fetchall("SELECT a.*,b.discount FROM " . tablename($this->table_lesson_parent) . " a LEFT JOIN " . tablename($this->table_discount_lesson) . " b ON a.id=b.lesson_id WHERE {$condition} LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $params);
	foreach($list as $k=>$v){
		$list[$k]['discount_name'] = $v['discount']*0.1.'折';
		$list[$k]['discount_price'] = round($v['price']*$v['discount']*0.01, 2);
	}
}

if($_W['isajax']){
	echo json_encode($list);
	exit();
}

include $this->template('discount');

?>