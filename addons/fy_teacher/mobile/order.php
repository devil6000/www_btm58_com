<?php
/*
 * 课程订单
 * ============================================================================
 * 版权所有 2015-2018 微课堂团队，并保留所有权利。
 * 网站地址: http://wx.haoshu888.com
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！不允许对程序代码以任何形式任何目的的再发布，作者将保留
 * 追究法律责任的权力和最终解释权。
 * ============================================================================
 */

$pindex = max(1, intval($_GPC['page']));
$psize = 10;

$config = $this->module['config'];

if($op=='display'){
	$linkNav = array(
		'0'	=> array(
			'title'	=> "课程订单",
			'link'	=> $this->createMobileUrl('order')
		)
	);

	$condition = " a.uniacid=:uniacid AND a.teacherid=:teacherid ";
	$params[':uniacid'] = $uniacid;
	$params[':teacherid'] = $_SESSION[$uniacid.'_teacher_id'];

	if(!empty($_GPC['keyword'])){
		$condition .= " AND ((a.ordersn LIKE :keyword) OR (a.bookname LIKE :keyword) OR (b.nickname LIKE :keyword) OR (b.realname LIKE :keyword) OR (b.mobile LIKE :keyword))";
		$params[':keyword'] = "%".trim($_GPC['keyword'])."%";
	}
	if($_GPC['status'] != ''){
		$condition .= " AND a.status=:status ";
		$params[':status'] = intval($_GPC['status']);
	}
	if($_GPC['starttime'] != ''){
		$condition .= " AND a.addtime >= :starttime ";
		$params[':starttime'] = strtotime($_GPC['starttime']);
	}
	if($_GPC['endtime'] != ''){
		$condition .= " AND a.addtime <= :endtime ";
		$params[':endtime'] = strtotime($_GPC['endtime'])+86399;
	}

	$list = pdo_fetchall("SELECT a.*,b.nickname,b.realname,b.mobile FROM " .tablename($this->table_order). " a LEFT JOIN " .tablename($this->table_mc_members). " b ON a.uid=b.uid WHERE {$condition} ORDER BY a.id desc, a.addtime DESC LIMIT " .($pindex - 1) * $psize. ',' . $psize, $params);
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' .tablename($this->table_order). " a LEFT JOIN " .tablename($this->table_mc_members). " b ON a.uid=b.uid WHERE {$condition}", $params);
	$pager = $this->pagination($total, $pindex, $psize);

}elseif($op=='details'){
	$orderid = intval($_GPC['orderid']);
	$linkNav = array(
		'0'	=> array(
			'title'	=> "课程订单",
			'link'	=> $this->createMobileUrl('order')
		),
		'1'	=> array(
			'title'	=> "订单详情",
			'link'	=> $this->createMobileUrl('order',array('op'=>'details','orderid'=>$orderid))
		),
	);

	$order = pdo_fetch("SELECT a.*,b.nickname,b.realname,b.mobile,b.avatar FROM " .tablename($this->table_order). " a LEFT JOIN " .tablename($this->table_mc_members). " b ON a.uid=b.uid WHERE a.uniacid=:uniacid AND a.id=:id AND a.teacherid=:teacherid ", array(':uniacid'=>$uniacid, ':id'=>$orderid, ':teacherid'=>$_SESSION[$uniacid.'_teacher_id']));
	if(empty($order)){
		message("该订单不存在或您无权查看");
	}

	$evaluate = pdo_fetch("SELECT a.*,b.nickname,b.avatar FROM " .tablename($this->table_evaluate). " a LEFT JOIN " .tablename($this->table_mc_members). " b ON a.uid=b.uid WHERE a.uniacid=:uniacid AND a.orderid=:orderid ", array(':uniacid'=>$uniacid, ':orderid'=>$orderid));

	if(checksubmit('submit')){
		if(!empty($evaluate['reply'])){
			message("该评论已回复，无需再次回复");
		}
		if(empty($_GPC['reply'])){
			message("请输入回复内容");
		}

		$result = pdo_update($this->table_evaluate, array('reply'=>$_GPC['reply']), array('uniacid'=>$uniacid,'orderid'=>$orderid));
		if($result){
			message("回复成功", $this->createMobileUrl('order',array('op'=>'details','orderid'=>$orderid)), "success");
		}else{
			//pdo_debug();exit();
			message("回复失败，请稍候重试");
		}
	}
}

include $this->template('order');