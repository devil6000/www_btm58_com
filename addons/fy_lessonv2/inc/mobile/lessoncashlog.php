<?php
/**
 * 讲师收入提现记录
 * ============================================================================
 * 版权所有 2015-2018 微课堂团队，并保留所有权利。
 * 网站地址: https://wx.haoshu888.com
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！不允许对程序代码以任何形式任何目的的再发布，作者将保留
 * 追究法律责任的权力和最终解释权。
 */

checkauth();

/* 初始化提现状态 */
$typeStatus = new TypeStatus();
$cashStatusList = $typeStatus->cashStatus();

$uid = $_W['member']['uid'];

$pindex =max(1,$_GPC['page']);
$psize = 10;

$list = pdo_fetchall("SELECT * FROM " .tablename($this->table_cashlog). " WHERE uid=:uid AND lesson_type=:lesson_type ORDER BY id DESC LIMIT " . ($pindex-1) * $psize . ',' . $psize, array(':uid'=>$uid, ':lesson_type'=>2));
foreach($list as $key=>$value){
	$list[$key]['statu'] = $cashStatusList[$value['status']];
	$list[$key]['disposetime'] = $value['status']!=0 ? date("Y-m-d", $value['disposetime']) : "";
	$list[$key]['remark'] = $value['remark'] ? $value['remark'] : "";
	$list[$key]['addtime'] = date("Y-m-d", $value['addtime']);
}
$total = pdo_fetchcolumn("SELECT COUNT(*) FROM " .tablename($this->table_cashlog). " WHERE uid=:uid AND lesson_type=:lesson_type", array(':uid'=>$uid, ':lesson_type'=>2));

$title = "讲师收入提现记录(".$total.")";

if(!$_W['isajax']){
	include $this->template('lessoncashlog');
}else{
	echo json_encode($list);
}


?>