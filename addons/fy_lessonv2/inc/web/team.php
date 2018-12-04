<?php
/**
 * 我的团队
 * ============================================================================
 * 版权所有 2015-2018 微课堂团队，并保留所有权利。
 * 网站地址: https://wx.haoshu888.com
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！不允许对程序代码以任何形式任何目的的再发布，作者将保留
 * 追究法律责任的权力和最终解释权。
 */

$uid = intval($_GPC['uid']);

if($op=='display'){
	$pindex =max(1,$_GPC['page']);
	$psize = 10;

	$member = pdo_fetch("SELECT a.uid,a.nopay_commission+a.pay_commission AS commission,a.addtime, b.nickname,b.realname,b.mobile,b.avatar FROM " .tablename($this->table_member). " a LEFT JOIN " .tablename($this->table_mc_members). " b ON a.uid=b.uid WHERE a.uniacid=:uniacid AND a.uid=:uid", array(':uniacid'=>$uniacid,':uid'=>$uid));

	if(empty($member['avatar'])){
		$member['avatar'] = MODULE_URL."template/mobile/images/default_avatar.jpg";
	}else{
		$inc = strstr($member['avatar'], "http://") || strstr($member['avatar'], "https://");
		$member['avatar'] = $inc ? $member['avatar'] : $_W['attachurl'].$member['avatar'];
	}

	/* 一级会员人数 */
	$teamlist = pdo_fetchall("SELECT a.*, b.nickname,b.realname,b.mobile,b.avatar FROM " .tablename($this->table_member). " a LEFT JOIN " .tablename($this->table_mc_members). " b ON a.uid=b.uid WHERE a.uniacid=:uniacid AND a.parentid=:parentid", array(':uniacid'=>$uniacid,':parentid'=>$uid));
	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM " .tablename($this->table_member). " a LEFT JOIN " .tablename($this->table_mc_members). " b ON a.uid=b.uid WHERE a.uniacid=:uniacid AND a.parentid=:parentid", array(':uniacid'=>$uniacid,':parentid'=>$uid));


	foreach($teamlist as $k1=>$v1){
		if(empty($v1['avatar'])){
			$teamlist[$k1]['avatar'] = MODULE_URL."template/mobile/images/default_avatar.jpg";
		}else{
			$inc = strstr($v1['avatar'], "http://") || strstr($v1['avatar'], "https://");
			$teamlist[$k1]['avatar'] = $inc ? $v1['avatar'] : $_W['attachurl'].$v1['avatar'];
		}

		/* 二级会员人数 */
		$direct2_num = pdo_fetchcolumn("SELECT COUNT(*) FROM " .tablename($this->table_member). " WHERE uniacid=:uniacid AND parentid=:parentid", array(':uniacid'=>$uniacid,':parentid'=>$v1['uid']));
		$teamlist[$k1]['recnum']  = $direct2_num;
	}

}elseif($op=='export'){
	$member = pdo_fetch("SELECT a.nickname,b.nickname AS mc_nickname FROM " .tablename($this->table_member). " a LEFT JOIN ".tablename($this->table_mc_members). " b ON a.uid=b.uid WHERE a.uniacid=:uniacid AND a.uid=:uid", array(':uniacid'=>$uniacid,':uid'=>$uid));
	if(empty($member)){
		message('分销商不存在');
	}

	$list = pdo_fetchall("SELECT a.uid,a.nopay_commission,a.pay_commission,a.payment_amount,a.payment_order,a.status,a.agent_level,a.addtime, b.nickname,b.realname,b.mobile FROM " .tablename($this->table_member). " a LEFT JOIN " .tablename($this->table_mc_members). " b ON a.uid=b.uid WHERE a.parentid=:parentid", array(':parentid'=>$uid));

	foreach ($list as $key => $value) {
		$arr[$key]['uid']				= $value['uid'];
		$arr[$key]['realname']			= $value['realname'];
		$arr[$key]['mobile']			= $value['mobile'];
		$arr[$key]['status']			= $this->getAgentStatusName($value['status']);
		$arr[$key]['levelname']			= $this->getAgentLevelName($value['agent_level']);
		$arr[$key]['pay_commission']	= $value['pay_commission'];
		$arr[$key]['nopay_commission']	= $value['nopay_commission'];
		$arr[$key]['payment_amount']	= $value['payment_amount'];
		$arr[$key]['payment_order']		= $value['payment_order'];
		$arr[$key]['addtime']		    = date('Y-m-d H:i:s', $value['addtime']);
	}

	$title = array('会员ID', '真实姓名', '手机号码', '分销商状态', '分销商级别', '已结算佣金', '未结算佣金','订单金额','订单笔数', '加入时间');
	$filename = $member['mc_nickname'] ? $member['mc_nickname'] : $member['nickname'];
	$filename .= '-直接下级成员';

	$phpexcel = new FyLessonv2PHPExcel();
	$phpexcel->exportTable($title, $arr, $filename);
}


include $this->template('web/team');

?>