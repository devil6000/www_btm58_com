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
$pindex =max(1,$_GPC['page']);
$psize = 10;

$member = pdo_fetch("SELECT a.uid,a.openid,a.nopay_commission+a.pay_commission AS commission,a.addtime, b.nickname,b.realname,b.mobile,b.avatar FROM " .tablename($this->table_member). " a LEFT JOIN " .tablename('mc_members'). " b ON a.uid=b.uid WHERE a.uniacid=:uniacid AND a.uid=:uid", array(':uniacid'=>$uniacid,':uid'=>$uid));

if(empty($member['avatar'])){
	$member['avatar'] = MODULE_URL."template/mobile/images/default_avatar.jpg";
}else{
	$inc = strstr($member['avatar'], "http://") || strstr($member['avatar'], "https://");
	$member['avatar'] = $inc ? $member['avatar'] : $_W['attachurl'].$member['avatar'];
}

/* 一级会员人数 */
$teamlist = pdo_fetchall("SELECT a.*, b.nickname,b.realname,b.mobile,b.avatar FROM " .tablename($this->table_member). " a LEFT JOIN " .tablename('mc_members'). " b ON a.uid=b.uid WHERE a.uniacid=:uniacid AND a.parentid=:parentid", array(':uniacid'=>$uniacid,':parentid'=>$uid));
$total = pdo_fetchcolumn("SELECT COUNT(*) FROM " .tablename($this->table_member). " a LEFT JOIN " .tablename('mc_members'). " b ON a.uid=b.uid WHERE a.uniacid=:uniacid AND a.parentid=:parentid", array(':uniacid'=>$uniacid,':parentid'=>$uid));


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


include $this->template('team');

?>