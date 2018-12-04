<?php
/**
 * 推荐板块课程列表
 * ============================================================================
 * 版权所有 2015-2018 微课堂团队，并保留所有权利。
 * 网站地址: https://wx.haoshu888.com
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！不允许对程序代码以任何形式任何目的的再发布，作者将保留
 * 追究法律责任的权力和最终解释权。
 */

checkauth();

$pindex = max(1, intval($_GPC['page']));
$psize = 10;

if ($op == 'display') {
	$recid = intval($_GPC['recid']);
	$recommend = pdo_fetch("SELECT * FROM " .tablename($this->table_recommend). " WHERE id=:id AND is_show=:is_show", array(':id'=>$recid, ':is_show'=>1));
	if(empty($recommend)){
		message("该推荐版块不存在", "", "error");
	}

	$list = pdo_fetchall("SELECT * FROM " .tablename($this->table_lesson_parent). " WHERE uniacid='{$uniacid}' AND status=1 AND (recommendid='{$recid}' OR (recommendid LIKE '{$recid},%') OR (recommendid LIKE '%,{$recid}') OR (recommendid LIKE '%,{$recid},%')) ORDER BY displayorder DESC, id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
	foreach ($list as $key => $value) {
		$list[$key]['soncount'] = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename($this -> table_lesson_son) . " WHERE parentid=:parentid", array(':parentid'=>$value['id']));
		$list[$key]['price'] = $value['price']>0 ? "¥".$value['price'] : "免费";
		if($value['price']>0){
			$list[$key]['buyTotal'] = $value['buynum'] + $value['virtual_buynum'];
		}else{
			$list[$key]['buyTotal'] = $value['buynum'] + $value['virtual_buynum'] + $value['visit_number'];
		}
		if($value['score']>0){
			$list[$key]['score_rate'] = $value['score']*100;
		}else{
			$list[$key]['score_rate'] = "";
		}
	}

	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM " .tablename($this->table_lesson_parent). " WHERE uniacid='{$uniacid}' AND status=1 AND (recommendid='{$recid}' OR (recommendid LIKE '{$recid},%') OR (recommendid LIKE '%,{$recid}') OR (recommendid LIKE '%,{$recid},%')) ");
}

if(!$_W['isajax']){
	include $this -> template('recommend');
}else{
	echo json_encode($list);
}

?>