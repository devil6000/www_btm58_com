<?php
/**
 * 分销佣金管理
 * ============================================================================
 * 版权所有 2015-2018 微课堂团队，并保留所有权利。
 * 网站地址: https://wx.haoshu888.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！不允许对程序代码以任何形式任何目的的再发布，作者将保留
 * 追究法律责任的权力和最终解释权。
 * ============================================================================
 */

$pindex = max(1, intval($_GPC['page']));
$psize = 10;

if($op=='level'){
	$list = pdo_fetchall("SELECT * FROM " .tablename($this->table_commission_level). " WHERE uniacid=:uniacid ORDER BY id ASC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, array(':uniacid'=>$uniacid));

	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename($this->table_commission_level) . " WHERE uniacid=:uniacid", array(':uniacid'=>$uniacid));
	$pager = pagination($total, $pindex, $psize);

}elseif($op=='editlevel'){
	$id = intval($_GPC['id']);
	if($id>0){
		$level = pdo_fetch("SELECT * FROM " .tablename($this->table_commission_level). " WHERE uniacid=:uniacid AND id=:id", array(':uniacid'=>$uniacid,':id'=>$id));
		if(empty($level)){
			message("该分销商等级不存在或已被删除", "", "error");
		}
	}

	if(checksubmit('submit')){
		$data = array(
			'uniacid'	  => $uniacid,
			'levelname'   => trim($_GPC['levelname']),
			'commission1' => floatval($_GPC['commission1']),
			'commission2' => floatval($_GPC['commission2']),
			'commission3' => floatval($_GPC['commission3']),
			'updatemoney' => floatval($_GPC['updatemoney']),
		);
		if(empty($data['levelname'])){
			message("请输入等级名称");
		}
		if(empty($data['commission1'])){
			message("请输入一级分销比例");
		}

		if(empty($id)){
			pdo_insert($this->table_commission_level, $data);
			$id = pdo_insertid();
			if($id){
				$this->addSysLog($_W['uid'], $_W['username'], 1, "分销管理->分销商等级", "新增ID:{$id}的分销商等级");
			}
		}else{
			$res = pdo_update($this->table_commission_level, $data, array('uniacid'=>$uniacid, 'id'=>$id));
			if($res){
				$this->addSysLog($_W['uid'], $_W['username'], 3, "分销管理->分销商等级", "编辑ID:{$id}的分销商等级");
			}
		}

		message("操作成功", $this->createWebUrl("commission", array('op'=>'level')), "success");
	}

}elseif($op=='dellevel'){
	$id = intval($_GPC['id']);
	$level = pdo_fetch("SELECT * FROM " .tablename($this->table_commission_level). " WHERE uniacid=:uniacid AND id=:id", array(':uniacid'=>$uniacid,':id'=>$id));
	
	if(empty($level)){
		message("该分销商等级不存在或已被删除", "", "error");
	}

	$res = pdo_delete($this->table_commission_level, array('uniacid'=>$uniacid, 'id'=>$id));
	if($res){
		if($res){
			$this->addSysLog($_W['uid'], $_W['username'], 2, "分销管理->分销商等级", "删除ID:{$res}的分销商等级");
		}
	}

	message("删除成功", $this->createWebUrl("commission", array('op'=>'level')), "success");

}elseif($op=='commissionlog'){
	$nickname = trim($_GPC['nickname']);
	$bookname = trim($_GPC['bookname']);
	$grade	  = intval($_GPC['grade']);
	$remark	  = trim($_GPC['remark']);

	$condition = " uniacid='{$uniacid}' ";
	if (!empty($_GPC['time']['start'])) {
		$starttime = strtotime($_GPC['time']['start']);
		$endtime = strtotime($_GPC['time']['end']);
		$endtime = !empty($endtime) ? $endtime + 86399 : 0;
		if (!empty($starttime)) {
			$condition .= " AND addtime>=:starttime ";
			$params[':starttime'] = $starttime;
		}
		if (!empty($endtime)) {
			$condition .= " AND addtime<=:endtime ";
			$params[':endtime'] = $endtime;
		}
	}

	if(!empty($nickname)){
		$condition .= " AND nickname LIKE :nickname ";
		$params[':nickname'] = "%".$nickname."%";
	}

	if(!empty($bookname)){
		$condition .= " AND bookname LIKE :bookname ";
		$params[':bookname'] = "%".$bookname."%";
	}
	if(!empty($grade)){
		$condition .= " AND grade = :grade ";
		$params[':grade'] = $grade;
	}
	if(!empty($remark)){
		$condition .= " AND remark LIKE :remark ";
		$params[':remark'] = '%'.$remark.'%';
	}

	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename($this->table_commission_log) . " WHERE {$condition} ", $params);


	if(!$_GPC['export']){
		$list = pdo_fetchall("SELECT * FROM " . tablename($this->table_commission_log) . " WHERE {$condition} ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $params);
		$pager = pagination($total, $pindex, $psize);
	
	}else{
		set_time_limit(180);
		$psize = 10000;
		$max = ceil($total/$psize);
		$random = random(4);

		for($i=1; $i<=$max; $i++){
			$lists = pdo_fetchall("SELECT * FROM " . tablename($this->table_commission_log) . " WHERE {$condition} ORDER BY id DESC LIMIT " . ($i - 1) * $psize . ',' . $psize, $params);

			foreach ($lists as $key => $value) {
				$arr[$key]['id']			 = $value['id'];
				$arr[$key]['uid']			 = $value['uid'];
				$arr[$key]['nickname']       = preg_replace('#[^\x{4e00}-\x{9fa5}A-Za-z0-9]#u','',$value['nickname']);
				$arr[$key]['bookname']       = $value['bookname'];
				$arr[$key]['grade']			 = '其他';
				if($value['grade'] == '1'){
					$arr[$key]['grade'] = '一级分销';
				}elseif($value['grade'] == '2'){
					$arr[$key]['grade'] = '二级分销';
				}elseif($value['grade'] == '3'){
					$arr[$key]['grade'] = '三级分销';
				}
				$arr[$key]['change_num']      = $value['change_num'].'元';
				$arr[$key]['remark']		  = is_numeric($value['remark']) ? "'".$value['remark'] : $value['remark'];
				$arr[$key]['addtime']         = date('Y-m-d H:i:s',$value['addtime']);
			}

			$title = array('编号', '会员ID', '会员昵称', '分销课程', '分销等级', '分销佣金(元)', '备注', '分销时间');
			$filename = '分销佣金明细'.$random.$uniacid.'-'.$i;

			$phpexcel = new FyLessonv2PHPExcel();
			$savetype = $max>1 ? 1 : 0;
			$phpexcel->exportTable($title, $arr, $filename, $savetype);
			unset($arr);

			$filenameArr[] = $filename.'-'.date('Ymd').'.xls';
		}

		/* 打包下载 */
		$filepath = '../data/excel/';
		$pack = $filepath.'分销佣金明细'.$random.$uniacid.'-'.date('Ymd').'.zip';
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
		header('Content-disposition:attachment;filename=分销佣金明细'.$random.$uniacid.'-'.date('Ymd').'.zip');
		$filesize = filesize($pack);
		readfile($pack);
		header('Content-length:'.$filesize);

		$files = glob($filepath.'*');
		foreach($files as $file) {
			if(strstr($file, "分销佣金明细{$random}{$uniacid}-")){
				unlink($file);
			}
		}
	}

}elseif($op=='statis'){
	$keyword = trim($_GPC['keyword']);
	$mobile	  = trim($_GPC['mobile']);
	$ranking  = intval($_GPC['ranking']);

	$condition = " a.uniacid='{$uniacid}' ";
	if(!empty($keyword)){
		$condition .= " AND (b.nickname LIKE :keyword OR b.realname LIKE :keyword) ";
		$params[':keyword'] = "%".$keyword."%";
	}

	if(!empty($mobile)){
		$condition .= " AND b.mobile LIKE :mobile ";
		$params[':mobile'] = "%".$mobile."%";
	}

	if(empty($ranking) || $ranking==1){
		$ORDER = " ORDER BY total_commission DESC ";
	}elseif($ranking==2){
		$ORDER = " ORDER BY pay_commission DESC ";
	}elseif($ranking==3){
		$ORDER = " ORDER BY nopay_commission DESC ";
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

	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename($this->table_member) . " a LEFT JOIN " .tablename('mc_members'). " b ON a.uid=b.uid WHERE {$condition} ", $params);

	if(!$_GPC['export']){
		$list = pdo_fetchall("SELECT a.nopay_commission,a.pay_commission,a.nopay_commission+a.pay_commission AS total_commission,a.addtime,b.uid,b.nickname,b.realname,b.mobile FROM " . tablename($this->table_member) . " a LEFT JOIN " .tablename('mc_members'). " b ON a.uid=b.uid WHERE {$condition} {$ORDER},uid ASC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $params);
		$pager = pagination($total, $pindex, $psize);
	
	}else{
		set_time_limit(180);
		$psize = 10000;
		$max = ceil($total/$psize);
		$random = random(4);

		for($i=1; $i<=$max; $i++){
			$lists = pdo_fetchall("SELECT a.nopay_commission,a.pay_commission,a.nopay_commission+a.pay_commission AS total_commission,a.addtime,b.uid,b.nickname,b.realname,b.mobile FROM " . tablename($this->table_member) . " a LEFT JOIN " .tablename('mc_members'). " b ON a.uid=b.uid WHERE {$condition} {$ORDER},uid ASC LIMIT " . ($i - 1) * $psize . ',' . $psize, $params);

			foreach ($lists as $key => $value) {
				$arr[$key]['uid']			   = $value['uid'];
				$arr[$key]['nickname']         = preg_replace('#[^\x{4e00}-\x{9fa5}A-Za-z0-9]#u','',$value['nickname']);
				$arr[$key]['realname']         = $value['realname'];
				$arr[$key]['mobile']		   = $value['mobile'];
				$arr[$key]['pay_commission']   = $value['pay_commission'];
				$arr[$key]['nopay_commission'] = $value['nopay_commission'];
				$arr[$key]['total_commission'] = $value['total_commission'];
				$arr[$key]['addtime']		   = date('Y-m-d H:i:s', $value['addtime']);
			}

			$title =  array('会员ID', '昵称', '姓名', '手机号码', '已申请佣金(元)', '待申请佣金(元)', '累计佣金(元)', '注册时间');
			$filename = '分销佣金统计'.$random.$uniacid.'-'.$i;

			$phpexcel = new FyLessonv2PHPExcel();
			$savetype = $max>1 ? 1 : 0;
			$phpexcel->exportTable($title, $arr, $filename, $savetype);
			unset($arr);

			$filenameArr[] = $filename.'-'.date('Ymd').'.xls';
		}

		/* 打包下载 */
		$filepath = '../data/excel/';
		$pack = $filepath.'分销佣金统计'.$random.$uniacid.'-'.date('Ymd').'.zip';
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
		header('Content-disposition:attachment;filename=分销佣金统计'.$random.$uniacid.'-'.date('Ymd').'.zip');
		$filesize = filesize($pack);
		readfile($pack);
		header('Content-length:'.$filesize);

		$files = glob($filepath.'*');
		foreach($files as $file) {
			if(strstr($file, "分销佣金统计{$random}{$uniacid}-")){
				unlink($file);
			}
		}
	}

}

include $this->template('web/commission');


?>