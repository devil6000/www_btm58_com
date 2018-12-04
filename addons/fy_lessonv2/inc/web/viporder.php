<?php
/**
 * 会员VIP服务订单管理
 * ============================================================================
 * 版权所有 2015-2018 微课堂团队，并保留所有权利。
 * 网站地址: https://wx.haoshu888.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！不允许对程序代码以任何形式任何目的的再发布，作者将保留
 * 追究法律责任的权力和最终解释权。
 * ============================================================================
 */
load()->model('mc');

if ($op == 'display') {
	$pindex = max(1, intval($_GPC['page']));
	$psize = 10;

	$condition = " a.uniacid = :uniacid";
	$params[':uniacid'] = $uniacid;

	if (!empty($_GPC['ordersn'])) {
		$condition .= " AND a.ordersn LIKE :ordersn ";
		$params[':ordersn'] = "%{$_GPC['ordersn']}%";
	}
	if ($_GPC['status']!='') {
		$condition .= " AND a.status=:status ";
		$params[':status'] = $_GPC['status'];
	}
	if (!empty($_GPC['nickname'])) {
		$condition .= " AND b.nickname LIKE :nickname ";
		$params[':nickname'] = "%{$_GPC['nickname']}%";
	}
	if (!empty($_GPC['time']['start'])) {
		$starttime = strtotime($_GPC['time']['start']);
		$endtime = strtotime($_GPC['time']['end']);
		$endtime = !empty($endtime) ? $endtime + 86399 : 0;
		if (!empty($starttime)) {
			$condition .= " AND a.addtime>=:starttime ";
			$params[':starttime'] = $starttime;
		}
		if (!empty($endtime)) {
			$condition .= " AND a.addtime<=:endtime ";
			$params[':endtime'] = $endtime;
		}
	}

	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' .tablename($this->table_member_order). " a LEFT JOIN " .tablename($this->table_mc_members). " b ON a.uid=b.uid WHERE {$condition}", $params);

	if(!$_GPC['export']){

		$list = pdo_fetchall("SELECT a.*,b.nickname,b.realname,b.mobile FROM " .tablename($this->table_member_order). " a LEFT JOIN " .tablename($this->table_mc_members). " b ON a.uid=b.uid WHERE {$condition} ORDER BY a.id desc, a.addtime DESC LIMIT " .($pindex - 1) * $psize. ',' . $psize, $params);
		foreach($list as $k=>$v){
			$list[$k]['level'] = pdo_fetch("SELECT * FROM " .tablename($this->table_vip_level). " WHERE id=:id", array(':id'=>$v['level_id']));
		}
		
		$pager = pagination($total, $pindex, $psize);
	}else{
		set_time_limit(180);
		$psize = 10000;
		$max = ceil($total/$psize);
		$random = random(4);

		for($i=1; $i<=$max; $i++){
			$outputlist = pdo_fetchall("SELECT a.*,b.nickname,b.realname,b.mobile FROM " .tablename($this->table_member_order). " a LEFT JOIN " .tablename($this->table_mc_members). " b ON a.uid=b.uid WHERE {$condition} ORDER BY a.id desc, a.addtime DESC LIMIT " . ($i - 1) * $psize . ',' . $psize, $params);

			foreach ($outputlist as $key => $value) {
				$level = pdo_fetch("SELECT * FROM " .tablename($this->table_vip_level). " WHERE id=:id", array(':id'=>$value['level_id']));
				$level_name = $level['level_name'] ? $level['level_name'] : "默认VIP";
				
				$arr[$key]['ordersn']         = "'".$value['ordersn'];
				$arr[$key]['nickname']		  = preg_replace('#[^\x{4e00}-\x{9fa5}A-Za-z0-9]#u','',$value['nickname']);
				$arr[$key]['realname']        = $value['realname'];
				$arr[$key]['mobile']          = $value['mobile'];
				$arr[$key]['viptime']         = $level_name ."-". $value['viptime']."天";
				$arr[$key]['vipmoney']        = $value['vipmoney'];
				$arr[$key]['commission1']     = $value['commission1'];
				$arr[$key]['commission2']     = $value['commission2'];
				$arr[$key]['commission3']     = $value['commission3'];
				if($value['paytype'] == 'credit'){
					$arr[$key]['paytype'] = "余额支付";
				}elseif($value['paytype'] == 'wechat'){
					$arr[$key]['paytype'] = "微信支付";
				}elseif($value['paytype'] == 'alipay'){
					$arr[$key]['paytype'] = "支付宝支付";
				}elseif($value['paytype'] == 'offline'){
					$arr[$key]['paytype'] = "线下支付";
				}elseif($value['paytype'] == 'admin'){
					$arr[$key]['paytype'] = "后台支付";
				}elseif($value['paytype'] == 'vipcard'){
					$arr[$key]['paytype'] = "服务卡支付";
				}elseif($value['paytype'] == 'wxapp'){
					$arr[$key]['paytype'] = "微信小程序";
				}else{
					$arr[$key]['paytype'] = "无";
				}
				
				if($value['status'] == '0'){
					$arr[$key]['status'] = "未支付";
				}elseif($value['status'] == '1'){
					$arr[$key]['status'] = "已付款";
				}
				$arr[$key]['addtime']	 = date('Y-m-d H:i:s', $value['addtime']);
			}

			$title = array('订单编号', '昵称', '姓名', '手机号码', '服务时长', '服务价格(元)', '一级佣金(元)', '二级佣金(元)', '三级佣金(元)', '付款方式', '订单状态', '下单时间');
			$filename = 'VIP订单'.$random.$uniacid.'-'.$i;

			$phpexcel = new FyLessonv2PHPExcel();
			$savetype = $max>1 ? 1 : 0;
			$phpexcel->exportTable($title, $arr, $filename, $savetype);
			unset($arr);

			$filenameArr[] = $filename.'-'.date('Ymd').'.xls';
		}

		/* 打包下载 */
		$filepath = '../data/excel/';
		$pack = $filepath.'VIP订单'.$random.$uniacid.'-'.date('Ymd').'.zip';
		$zip = new ZipArchive();

		if($zip->open($pack, ZipArchive::CREATE)=== TRUE){
			foreach($filenameArr as $file){
				if(file_exists($filepath.$file)){
					$zip->addFile($filepath.$file);
				}else{
					exit('无法打开文件，或者文件创建失败');
				}
			}
			$zip->close();
		}

		header('Content-Type:text/html;charset=utf-8');
		header('Content-disposition:attachment;filename=VIP订单'.$random.$uniacid.'-'.date('Ymd').'.zip');
		$filesize = filesize($pack);
		readfile($pack);
		header('Content-length:'.$filesize);

		$files = glob($filepath.'*');
		foreach($files as $file) {
			if(strstr($file, "VIP订单{$random}{$uniacid}-")){
				unlink($file);
			}
		}
	}

}elseif ($op == 'detail') {
	$id = intval($_GPC['id']);
	$order = pdo_fetch("SELECT a.*,b.nickname,b.realname,b.mobile,b.msn,b.occupation,b.company,b.graduateschool,b.grade,b.address,b.avatar FROM " .tablename($this->table_member_order). " a LEFT JOIN " .tablename($this->table_mc_members). " b ON a.uid=b.uid WHERE a.uniacid=:uniacid AND a.id=:id", array(':uniacid'=>$uniacid,':id'=>$id));
	if (empty($order)) {
		message('该订单不存在或已被删除!');
	}
	if($order['paytype']=='wechat'){
		$wechatPay = $this->getWechatPayNo($order['id']);
		$wechatPay['transaction'] = unserialize($wechatPay['tag']);
	}
	
	if(empty($order['avatar'])){
		$avatar = MODULE_URL."template/mobile/images/default_avatar.jpg";
	}else{
		$inc = strstr($order['avatar'], "http://");
		$avatar = $inc ? $order['avatar'] : $_W['attachurl'].$order['avatar'];
	}

	if($order['member1']>0){
		$member1 = pdo_fetch("SELECT nickname,avatar FROM " .tablename($this->table_mc_members). " WHERE uid=:uid", array(':uid'=>$order['member1']));
		if(empty($member1['avatar'])){
			$avatar1 = MODULE_URL."template/mobile/images/default_avatar.jpg";
		}else{
			$avatar1 = strstr($member1['avatar'], "http://") ? $member1['avatar'] : $_W['attachurl'].$member1['avatar'];
		}
	}
	if($order['member2']>0){
		$member2 = pdo_fetch("SELECT nickname,avatar FROM " .tablename($this->table_mc_members). " WHERE uid=:uid", array(':uid'=>$order['member2']));
		if(empty($member2['avatar'])){
			$avatar2 = MODULE_URL."template/mobile/images/default_avatar.jpg";
		}else{
			$avatar2 = strstr($member2['avatar'], "http://") ? $member2['avatar'] : $_W['attachurl'].$member2['avatar'];
		}
	}
	if($order['member3']>0){
		$member3 = pdo_fetch("SELECT nickname,avatar FROM " .tablename($this->table_mc_members). " WHERE uid=:uid", array(':uid'=>$order['member3']));
		if(empty($member3['avatar'])){
			$avatar3 = MODULE_URL."template/mobile/images/default_avatar.jpg";
		}else{
			$avatar3 = strstr($member3['avatar'], "http://") ? $member3['avatar'] : $_W['attachurl'].$member3['avatar'];
		}
	}

}elseif ($op == 'vipMember') {
	/* vip等级列表 */
	$vip_level = pdo_getall($this->table_vip_level, array('uniacid'=>$uniacid));
	foreach($vip_level as $item){
		$vipLevelList[$item['id']] = $item['level_name'];
	}

	$keyword  = trim($_GPC['keyword']);
	$level_id = $_GPC['level_id'];
	$status   = $_GPC['status'];

	$pindex = max(1, intval($_GPC['page']));
	$psize = 10;

	$condition = " a.uniacid = :uniacid";
	$params[':uniacid'] = $uniacid;

	if (!empty($_GPC['keyword'])) {
		$condition .= " AND ((b.nickname LIKE :keyword) OR (b.realname LIKE :keyword) OR (b.mobile LIKE :keyword)) ";
		$params[':keyword'] = "%{$_GPC['keyword']}%";
	}
	if ($_GPC['level_id']) {
		$condition .= " AND a.level_id=:level_id ";
		$params[':level_id'] = $_GPC['level_id'];
	}
	if ($_GPC['status']!='') {
		if($_GPC['status']==1){
			$condition .= " AND a.validity >= :validity ";
		}
		if($_GPC['status']==-1){
			$condition .= " AND a.validity < :validity ";
		}
		$params[':validity'] = time();
	}

	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' .tablename($this->table_member_vip). " a LEFT JOIN " .tablename($this->table_mc_members). " b ON a.uid=b.uid WHERE {$condition}", $params);
	
	if(!$_GPC['export']){
		$list = pdo_fetchall("SELECT a.uid,a.level_id,a.discount,a.validity,b.nickname,b.realname,b.mobile FROM " .tablename($this->table_member_vip). " a LEFT JOIN " .tablename($this->table_mc_members). " b ON a.uid=b.uid WHERE {$condition} ORDER BY a.uid desc, a.validity DESC LIMIT " .($pindex - 1) * $psize. ',' . $psize, $params);
		
		$pager = pagination($total, $pindex, $psize);
	}else{
		set_time_limit(180);
		$psize = 10000;
		$max = ceil($total/$psize);
		$random = random(4);

		for($i=1; $i<=$max; $i++){
			$outputlist = pdo_fetchall("SELECT a.*,b.realname,b.mobile FROM " .tablename($this->table_member_vip). " a LEFT JOIN " .tablename($this->table_mc_members). " b ON a.uid=b.uid WHERE {$condition} ORDER BY a.uid desc, a.validity DESC LIMIT " . ($i - 1) * $psize . ',' . $psize, $params);

			foreach ($outputlist as $key => $value) {				
				$arr[$key]['uid']		  = $value['uid'];
				$arr[$key]['nickname']    = preg_replace('#[^\x{4e00}-\x{9fa5}A-Za-z0-9]#u','',$value['nickname']);
				$arr[$key]['realname']    = $value['realname'];
				$arr[$key]['mobile']      = $value['mobile'];
				$arr[$key]['level_name']  = $vipLevelList[$value['level_id']];
				$arr[$key]['discount']    = $value['discount'];
				$arr[$key]['validity']	  = date('Y-m-d H:i', $value['validity']);
				if($value['validity'] >= time()){
					$arr[$key]['status']  = "生效中";
				}else{
					$arr[$key]['status']  = "已过期";
				}
				$arr[$key]['addtime']     = date('Y-m-d H:i', $value['addtime']);
				$arr[$key]['update_time'] = $value['update_time'] ? date('Y-m-d H:i', $value['update_time']) : '无';
			}

			$title = array('会员ID', '昵称', '姓名', '手机号码', '等级名称', '折扣(%)', '有效期', '状态', '首次开通时间', '上次续费时间');
			$filename = 'VIP会员'.$random.$uniacid.'-'.$i;

			$phpexcel = new FyLessonv2PHPExcel();
			$savetype = $max>1 ? 1 : 0;
			$phpexcel->exportTable($title, $arr, $filename, $savetype);
			unset($arr);

			$filenameArr[] = $filename.'-'.date('Ymd').'.xls';
		}

		/* 打包下载 */
		$filepath = '../data/excel/';
		$pack = $filepath.'VIP会员'.$random.$uniacid.'-'.date('Ymd').'.zip';
		$zip = new ZipArchive();

		if($zip->open($pack, ZipArchive::CREATE)=== TRUE){
			foreach($filenameArr as $file){
				if(file_exists($filepath.$file)){
					$zip->addFile($filepath.$file);
				}else{
					exit('无法打开文件，或者文件创建失败');
				}
			}
			$zip->close();
		}

		header('Content-Type:text/html;charset=utf-8');
		header('Content-disposition:attachment;filename=VIP会员'.$random.$uniacid.'-'.date('Ymd').'.zip');
		$filesize = filesize($pack);
		readfile($pack);
		header('Content-length:'.$filesize);

		$files = glob($filepath.'*');
		foreach($files as $file) {
			if(strstr($file, "VIP会员{$random}{$uniacid}-")){
				unlink($file);
			}
		}
	}


}elseif ($op == 'delete') {
	$id = $_GPC['id'];
	$order = pdo_fetch("SELECT ordersn FROM " .tablename($this->table_member_order). " WHERE uniacid=:uniacid AND id=:id LIMIT 1", array(':uniacid'=>$uniacid, ':id'=>$id));
	if(empty($order)){
		message("该订单不存在或已被删除", "", "error");
	}

	$res = pdo_delete($this->table_member_order, array('uniacid'=>$uniacid,'id' => $id));
	if($res){
		$this->addSysLog($_W['uid'], $_W['username'], 2, "VIP订单", "删除订单编号:{$order['ordersn']}的VIP订单");
	}

	echo "<script>alert('删除成功！');location.href='".$this->createWebUrl('viporder', array('op' => 'display', 'page' => $_GPC['page']))."';</script>";

}elseif($op=='createOrder'){
	$level_list = pdo_fetchall("SELECT * FROM " .tablename($this->table_vip_level). " WHERE uniacid=:uniacid", array(':uniacid'=>$uniacid));

	if(checksubmit('submit')){
		$data = array(
			'uniacid'	=> $uniacid,
			'uid'		=> intval($_GPC['uid']),
			'level_id'	=> intval($_GPC['level_id']),
			'validity'	=> strtotime($_GPC['validity']),
		);

		if(empty($data['uid'])){
			message("请输入会员UID", "", "error");
		}
		if(empty($data['level_id'])){
			message("请选择要开通的VIP等级", "", "error");
		}
		if($data['validity'] < time()){
			message("有效期不能小于当前时间", "", "error");
		}

		/*检查会员等级是否存在*/
		$level = pdo_fetch("SELECT * FROM " .tablename($this->table_vip_level). " WHERE uniacid=:uniacid AND id=:id", array(':uniacid'=>$uniacid, ':id'=>$data['level_id']));
		if(empty($level)){
			message("该会员等级不存在，请重新选择", "", "error");
		}
		$data['discount'] = $level['discount'];

		/*检查会员是否开通过该等级*/
		$member_vip = pdo_fetch("SELECT * FROM " .tablename($this->table_member_vip). " WHERE uniacid=:uniacid AND uid=:uid AND level_id=:level_id", array(':uniacid'=>$uniacid, ':uid'=>$data['uid'], ':level_id'=>$data['level_id']));
		if(empty($member_vip)){
			$data['addtime'] = time();
			$res = pdo_insert($this->table_member_vip, $data);
		}else{
			$data['update_time'] = time();
			$res = pdo_update($this->table_member_vip, $data, array('id'=>$member_vip['id']));
		}

		if($res){
			/* 添加VIP服务订单 */
			$days = ceil(($data['validity']-time())/86400);
			$vipOrder = array(
				'acid' => $_W['acid'],
				'uniacid' => $uniacid,
				'ordersn' => date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8),
				'uid' => $data['uid'],
				'viptime' => $days,
				'vipmoney' => 0,
				'paytype' => 'admin',
				'status' => 1,
				'paytime' => time(),
				'addtime' => time(),
				'level_id' => $data['level_id'],
				'level_name' => $level['level_name'],
			);
			pdo_insert($this->table_member_order, $vipOrder);

			/* 更新会员vip字段 */
			$this->updateMemberVip($data['uid'], 1);

			/* 写入系统日志 */
			$this->addSysLog($_W['uid'], $_W['username'], 1, "VIP订单->创建VIP服务", "给[uid:".$data['uid']."]的会员开通[id:".$level['id']." - ".$level['level_name']."]的VIP等级，有效期至:".$_GPC['validity']);
			message("创建会员VIP成功", $this->createWebUrl('viporder', array('op'=>'createOrder')), "success");
		}else{
			message("创建会员VIP失败，请稍候重试", "", "error");
		}

	}

}elseif($op=='vipcard'){
	/* VIP等级列表 */
	$level_list = pdo_fetchall("SELECT * FROM " .tablename($this->table_vip_level). " WHERE uniacid=:uniacid", array(':uniacid'=>$uniacid));
	
	$pindex = max(1, intval($_GPC['page']));
	$psize = 10;
	$time = time();

	$condition = " uniacid = '{$uniacid}' ";
	if (!empty($_GPC['ordersn'])) {
		$condition .= " AND ordersn LIKE '%{$_GPC['ordersn']}%' ";
	}
	if (!empty($_GPC['nickname'])) {
		$condition .= " AND nickname LIKE '%{$_GPC['nickname']}%' ";
	}
	if ($_GPC['is_use'] != '') {
		if($_GPC['is_use']==0){
			$condition .= " AND is_use=0 AND validity>'{$time}' ";
		}elseif($_GPC['is_use']==1){
			$condition .= " AND is_use='{$_GPC['is_use']}' ";
		}elseif($_GPC['is_use']==-1){
			$condition .= " AND is_use=0 AND validity<'{$time}' ";
		}
	}
	if (!empty($_GPC['level_id'])) {
		$condition .= " AND level_id={$_GPC['level_id']} ";
	}
	if (!empty($_GPC['time']['start'])) {
		$starttime = strtotime($_GPC['time']['start']);
		$endtime = strtotime($_GPC['time']['end']);
		$endtime = !empty($endtime) ? $endtime + 86399 : 0;
		if (!empty($starttime)) {
			$condition .= " AND use_time >= '{$starttime}' ";
		}
		if (!empty($endtime)) {
			$condition .= " AND use_time < '{$endtime}' ";
		}
	}

	$list = pdo_fetchall("SELECT * FROM " .tablename($this->table_vipcard). " WHERE {$condition} ORDER BY addtime DESC LIMIT " .($pindex - 1) * $psize. ',' . $psize);
	foreach($list as $k=>$v){
		$list[$k]['level'] = $this->getLevelById($v['level_id']);
	}
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' .tablename($this->table_vipcard). " WHERE {$condition}");
	$pager = pagination($total, $pindex, $psize);

	if($_GPC['export']==1){
		$outputlist = pdo_fetchall("SELECT * FROM " .tablename($this->table_vipcard). " WHERE {$condition} ORDER BY addtime DESC");

		foreach ($outputlist as $key => $value) {
			$level = $this->getLevelById($v['level_id']);
			
			$arr[$key]['card_id']		= $value['card_id'];
			$arr[$key]['password']	= $value['password'];
			$arr[$key]['level_name']	= $level['level_name'];
			$arr[$key]['viptime']		= $value['viptime'];
			$arr[$key]['validity']	= date('Y-m-d H:i:s',$value['validity']);
			if($value['is_use']==1){
				$status = "已使用";
			}elseif($value['is_use']==0 && $value['validity']>time()){
				$status = "未使用";
			}elseif($value['is_use']==0 && $value['validity']<time()){
				$status = "已过期";
			}
			$arr[$key]['is_use']		= $status;
			$arr[$key]['nickname']    = $value['nickname'];
			$arr[$key]['ordersn']     = $value['ordersn'];
			$arr[$key]['use_time']    = $value['use_time']?date('Y-m-d H:i:s', $value['use_time']):'';
			$arr[$key]['addtime']     = date('Y-m-d H:i:s', $value['addtime']);
		}

		$title = array('服务卡号', '卡密','VIP等级', '卡时长(天)','有效期', '卡状态', '使用者', '订单号', '使用时间', '添加时间');
		$phpexcel = new FyLessonv2PHPExcel();
		$phpexcel->exportTable($title, $arr, 'VIP服务卡');
	}

}elseif($op=='delCard'){
	$id = $_GPC['id'];
	$card = pdo_fetch("SELECT password FROM " .tablename($this->table_vipcard). " WHERE uniacid=:uniacid AND id=:id LIMIT 1", array(':uniacid'=>$uniacid, ':id'=>$id));
	if(empty($card)){
		message("该VIP服务卡不存在或已被删除", "", "error");
	}
	$res = pdo_delete($this->table_vipcard, array('uniacid'=>$uniacid,'id' => $id));
	if($res){
		$this->addSysLog($_W['uid'], $_W['username'], 2, "VIP服务卡", "删除服务卡密:{$card['password']}的VIP服务卡");
	}

	echo "<script>alert('删除成功！');location.href='".$this->createWebUrl('viporder', array('op' => 'vipcard', 'page' => $_GPC['page']))."';</script>";

}elseif($op=='delAllCard'){
	$ids = $_GPC['ids'];
	if(!empty($ids) && is_array($ids)){
		$num = 0;
		$card = "";
		foreach($ids as $id){
			$card .= $this->getVipCardPwd($id).",";
			if(pdo_delete($this->table_vipcard, array('uniacid'=>$uniacid,'id' => $id))){
				$num++;
			}
		}

		$card = trim($card, ",");
		$this->addSysLog($_W['uid'], $_W['username'], 2, "VIP服务卡", "批量删除{$num}个VIP服务卡,[{$card}]");
		message("批量删除成功", $this->createWebUrl('viporder', array('op'=>'vipcard')), "success");
	}else{
		message("未选中任何服务卡", "", "error");
	}

}elseif($op=='addVipCode'){
	$level_list = pdo_fetchall("SELECT * FROM " .tablename($this->table_vip_level). " WHERE uniacid=:uniacid ORDER BY sort DESC", array(':uniacid'=>$uniacid));
	if(checksubmit('submit')){
		$prefix = trim($_GPC['prefix']);
		$level_id = intval($_GPC['level_id']);
		$number = intval($_GPC['number']);
		$days = floatval($_GPC['days']);
		$validity = strtotime($_GPC['validity']);
		
		$level = $this->getLevelById($level_id);

		if(strlen($prefix) != 2){
			message("请输入服务卡的两位前缀", "", "error");
		}
		if(empty($level_id)){
			message("请选择的VIP等级", "", "error");
		}
		if(empty($level)){
			message("选择的VIP等级不存在", "", "error");
		}
		if($number < 1){
			message("请输入正确的服务卡数量", "", "error");
		}
		if($number > 500){
			message("单次生成服务卡不要超过500张", "", "error");
		}
		if($validity < time()){
			message("有效期必须大于当前时间", "", "error");
		}

		set_time_limit(120);
		ob_end_clean();
		ob_implicit_flush(true);
		str_pad(" ", 256);

		$total = 0;
		for($i=1;$i<=$number;$i++){
			$rand = mt_rand(0, 9999).mt_rand(0, 99999);
			$card_id = rand(1,9).str_pad($rand, 9, '0', STR_PAD_LEFT);

			$seek=mt_rand(0,9999).mt_rand(0,9999).mt_rand(0,9999).mt_rand(0,9999);
			$start=mt_rand(0,16);
			$str=strtoupper(substr(md5($seek),$start,16));
			$str=str_replace("O",chr(mt_rand(65,78)),$str);
			$str=str_replace("0",chr(mt_rand(65,78)),$str);

			$vipData = array(
				'uniacid'	=> $uniacid,
				'card_id'   => $card_id,
				'password'	=> $prefix.$str,
				'level_id'  => $level_id,
				'viptime'	=> $days,
				'validity'	=> $validity,
				'addtime'   => time()
			);
			if(pdo_insert($this->table_vipcard, $vipData)){
				$total++;
				unset($vipData);
			}
		}

		if($total){
			$this->addSysLog($_W['uid'], $_W['username'], 1, "VIP订单->VIP服务卡", "成功生成{$total}个有效期为{$days}天的服务卡");
		}
		message("成功生成{$total}个服务卡", $this->createWebUrl('viporder', array('op'=>'vipcard')), "success");
	}

}elseif($op=='updateVip'){
	$level_list = pdo_fetchall("SELECT * FROM " .tablename($this->table_vip_level). " WHERE uniacid=:uniacid", array(':uniacid'=>$uniacid));
	
	if(checksubmit('submit')){
		$level_id = intval($_GPC['level_id']);
		$level = $this->getLevelById($level_id);
		if(empty($level)){
			message("指定VIP等级不存在", "", "error");
		}
		
		$member_list = pdo_fetchall("SELECT uid,vip,validity FROM " .tablename($this->table_member). " WHERE uniacid=:uniacid AND vip=:vip AND validity>:validity", array(':uniacid'=>$uniacid,':vip'=>1, ':validity'=>time()));
		$t = 0;
		foreach($member_list as $member){
			$memberVip = pdo_fetch("SELECT * FROM " .tablename($this->table_member_vip). " WHERE uid=:uid AND  level_id=:level_id", array(':uid'=>$member['uid'],':level_id'=>$level_id));
			if(empty($memberVip)){
				$data = array(
					'uniacid' => $uniacid,
					'uid'	  => $member['uid'],
					'level_id'=> $level_id,
					'validity'=> $member['validity'],
					'discount'=> $level['discount'],
					'addtime' => time(),
				);
				if(pdo_insert($this->table_member_vip, $data)){
					$t++;
				}
			}
		}
		
		
		$lesson_list = pdo_fetchall("SELECT * FROM " .tablename($this->table_lesson_parent). " WHERE uniacid=:uniacid AND vipview=:vipview", array(':uniacid'=>$uniacid,':vipview'=>1));
		$s=0;
		foreach($lesson_list as $v){
			$lessonData = array(
				'vipview'=>json_encode(array("{$level_id}"))
			);
			if(pdo_update($this->table_lesson_parent, $lessonData, array('id'=>$v['id']))){
				$s++;
			}
		}
		
		message("成功同步{$t}位用户VIP信息，{$s}个课程", "", "success");
	}

}

include $this->template('web/viporder');

?>