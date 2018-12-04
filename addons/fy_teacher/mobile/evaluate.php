<?php
/*
 * 评论管理
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
			'title'	=> '评论列表',
			'link'	=> $this->createMobileUrl('evaluate'),
		),
	);

	$condition = " a.uniacid=:uniacid AND a.teacherid=:teacherid ";
	$params[':uniacid'] = $uniacid;
	$params[':teacherid'] = $_SESSION[$uniacid."_teacher_id"];
	if($_GPC['keyword'] != ''){
		$condition .= " AND (a.bookname LIKE :keyword OR a.ordersn LIKE :keyword OR b.nickname LIKE :keyword OR b.realname LIKE :keyword  OR b.mobile LIKE :keyword) ";
		$params[':keyword'] = "%".trim($_GPC['keyword'])."%";
	}
	if ($_GPC['reply'] != '') {
		if($_GPC['reply']==0){
			$condition .= " AND a.reply IS NULL ";
		}elseif($_GPC['reply']==1){
			$condition .= " AND a.reply IS NOT NULL ";
		}
	}
	if ($_GPC['status'] != '') {
		$condition .= " AND a.status = ".$_GPC['status'];
	}
	if($_GPC['starttime'] != ''){
		$condition .= " AND a.addtime >= :starttime ";
		$params[':starttime'] = strtotime($_GPC['starttime']);
	}
	if($_GPC['endtime'] != ''){
		$condition .= " AND a.addtime <= :endtime ";
		$params[':endtime'] = strtotime($_GPC['endtime'])+86399;
	}

	$list = pdo_fetchall("SELECT a.*,b.nickname,b.realname,b.mobile,b.avatar FROM " .tablename($this->table_evaluate). " a LEFT JOIN " .tablename($this->table_mc_members). " b ON a.uid=b.uid WHERE {$condition} ORDER BY a.id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $params);
	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM " .tablename($this->table_evaluate). " a LEFT JOIN " .tablename($this->table_mc_members). " b ON a.uid=b.uid WHERE {$condition}", $params);
	$pager = $this->pagination($total, $pindex, $psize);

}elseif($op=='details'){
	$id = $_GPC['id'];

	$linkNav = array(
		'0'	=> array(
			'title'	=> '评论列表',
			'link'	=> $this->createMobileUrl('evaluate'),
		),
		'1'	=> array(
			'title'	=> '评论详情',
			'link'	=> $this->createMobileUrl('evaluate', array('op'=>'details','id'=>$id)),
		),
	);
	
	$evaluate = pdo_fetch("SELECT a.*,b.nickname,b.realname,b.mobile,b.avatar FROM " .tablename($this->table_evaluate). " a LEFT JOIN " .tablename($this->table_mc_members). " b ON a.uid=b.uid WHERE a.uniacid=:uniacid AND a.id=:id", array(':uniacid'=>$uniacid, ':id'=>$id));
	if(empty($evaluate)){
		message("该评价不存在或已被删除！");
	}

	if(checksubmit('submit')){
		if(!empty($evaluate['reply'])){
			message("该条评论已回复，请勿重复回复");
		}

		$data = array(
			'reply'  => trim($_GPC['reply']),
		);
		if($config['evaluate_audit_switch']){
			$data['status'] = intval($_GPC['status']);
		}

		$result = pdo_update($this->table_evaluate, $data, array('uniacid'=>$uniacid,'id'=>$id));
		if($result){
			message('回复成功', refresh, 'success');
		}else{
			message('回复失败或保存信息无变化', refresh, 'error');
		}
	}


}


include $this->template('evaluate');