<?php
/**
 * 财务管理
 * ============================================================================
 * 版权所有 2015-2018 微课堂团队，并保留所有权利。
 * 网站地址: https://wx.haoshu888.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！不允许对程序代码以任何形式任何目的的再发布，作者将保留
 * 追究法律责任的权力和最终解释权。
 * ============================================================================
 */

/* 初始化状态类 */
$typeStatus = new TypeStatus();
$cashStatusList = $typeStatus->cashStatus();

$pindex = max(1, intval($_GPC['page']));
$psize = 10;

if($op=='display'){
	$today = strtotime("today");
	
	/* 检查过去10天订单统计 */
	for($i=1; $i<=10; $i++){
		$checkTime = $today - (10-$i)*86400;
		$checkEndTime = $checkTime + 86399;
		$check = pdo_fetch("SELECT * FROM " .tablename($this->table_static). " WHERE uniacid=:uniacid AND static_time=:static_time", array(':uniacid'=>$uniacid,':static_time'=>$checkTime));
		if(empty($check)){
			$lessonOrder_num = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename($this->table_order). " WHERE uniacid=:uniacid AND paytime >= :checkTime AND paytime < :checkEndTime", array(':uniacid'=>$uniacid, ':checkTime'=>$checkTime, ':checkEndTime'=>$checkEndTime));
			$lessonOrder_amount = pdo_fetch("SELECT SUM(price) as amount FROM ".tablename($this->table_order). " WHERE uniacid=:uniacid AND paytime >= :checkTime AND paytime < :checkEndTime", array(':uniacid'=>$uniacid, ':checkTime'=>$checkTime, ':checkEndTime'=>$checkEndTime));
			
			$vipOrder_num = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename($this->table_member_order). " WHERE uniacid=:uniacid AND paytime >= :checkTime AND paytime < :checkEndTime", array(':uniacid'=>$uniacid, ':checkTime'=>$checkTime, ':checkEndTime'=>$checkEndTime));
			$vipOrder_amount = pdo_fetch("SELECT SUM(vipmoney) as amount FROM ".tablename($this->table_member_order). " WHERE uniacid=:uniacid AND paytime >= :checkTime AND paytime < :checkEndTime", array(':uniacid'=>$uniacid, ':checkTime'=>$checkTime, ':checkEndTime'=>$checkEndTime));

			$newData = array(
				'uniacid'	  => $uniacid,
				'lessonOrder_num' => $lessonOrder_num,
				'lessonOrder_amount' => $lessonOrder_amount['amount'],
				'vipOrder_num' => $vipOrder_num,
				'vipOrder_amount' => $vipOrder_amount['amount'],
				'static_time' => $checkTime,
			);
			pdo_insert($this->table_static, $newData);
			unset($newData);
		}
	}

	$exit = pdo_fetch("SELECT * FROM " .tablename($this->table_static). " WHERE uniacid=:uniacid AND static_time=:static_time", array(':uniacid'=>$uniacid,':static_time'=>$today));
	$yestoday = pdo_fetch("SELECT * FROM " .tablename($this->table_static). " WHERE uniacid=:uniacid AND static_time=:static_time", array(':uniacid'=>$uniacid,':static_time'=>$today-86400));

	$starttime = empty($_GPC['time']['start']) ? strtotime(date('Y-m-d')) - 7 * 86400 : strtotime($_GPC['time']['start']);
	$endtime = empty($_GPC['time']['end']) ? TIMESTAMP : strtotime($_GPC['time']['end']) + 86399;

	$list = pdo_fetchall("SELECT * FROM " .tablename($this->table_static). " WHERE uniacid=:uniacid AND static_time>=:starttime AND static_time<=:endtime ORDER BY static_time ASC", array(':uniacid'=>$uniacid, ':starttime'=>$starttime, ':endtime'=>$endtime));

	$day = $amount = array();
	if (!empty($list)) {
		$incomeTotal = 0;
		foreach ($list as $row) {
			$day[] = date('m-d', $row['static_time']);
			$lessonOrder_amount[] = intval($row['lessonOrder_amount']);
			$vipOrder_amount[] = intval($row['vipOrder_amount']);
			$incomeTotal = $incomeTotal + intval($row['lessonOrder_amount']) + intval($row['vipOrder_amount']);
		}
	}

}elseif($op=='commission'){

	$status = $_GPC['status'];
	$lesson_type = intval($_GPC['lesson_type']);
	$cash_way    = intval($_GPC['cash_way']);
	$cashid      = intval($_GPC['cashid']);
	$nickname    = trim($_GPC['nickname']);

	$condition = " a.uniacid=:uniacid ";
	$params[':uniacid'] = $uniacid;

	if($status != ''){
		$condition .= " AND a.status=:status ";
		$params[':status'] = $status;
	}
	if(!empty($lesson_type)){
		$condition .= " AND a.lesson_type=:lesson_type ";
		$params[':lesson_type'] = $lesson_type;
	}
	if(!empty($cash_way)){
		$condition .= " AND a.cash_way=:cash_way ";
		$params[':cash_way'] = $cash_way;
	}
	if(!empty($cashid)){
		$condition .= " AND a.id=:id ";
		$params[':id'] = $cashid;
	}
	if(!empty($nickname)){
		$condition .= " AND (b.nickname LIKE :nickname OR b.realname LIKE :nickname OR b.mobile LIKE :nickname) ";
		$params[':nickname'] = "%".$nickname."%";
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

	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename($this->table_cashlog) . "  a LEFT JOIN " .tablename($this->table_mc_members). " b ON a.uid=b.uid WHERE {$condition}", $params);

	if(!$_GPC['export']){
		$list = pdo_fetchall("SELECT a.*,b.mobile,b.nickname,b.avatar FROM " . tablename($this->table_cashlog) . " a LEFT JOIN " .tablename($this->table_mc_members). " b ON a.uid=b.uid WHERE {$condition} ORDER BY a.id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $params);
		foreach($list as $k=>$v){
			if(empty($v['avatar'])){
				$list[$k]['avatar'] = MODULE_URL."template/mobile/images/default_avatar.jpg";
			}else{
				$list[$k]['avatar'] = strstr($v['avatar'], "http://") ? $v['avatar'] : $_W['attachurl'].$v['avatar'];
			}
		}
		$pager = pagination($total, $pindex, $psize);
	
	}else{
		set_time_limit(180);
		$psize = 10000;
		$max = ceil($total/$psize);
		$random = random(4);

		for($i=1; $i<=$max; $i++){
			if($status=='0'){
				$tmpname = "待打款提现申请";
			}elseif($status==1){
				$tmpname = "已打款提现申请";
			}elseif($status==-1){
				$tmpname = "无效提现申请";
			}else{
				$tmpname = "提现申请";
			}
			
			$outputlist = pdo_fetchall("SELECT a.*,b.mobile,b.nickname,b.avatar FROM " . tablename($this->table_cashlog) . " a LEFT JOIN " .tablename($this->table_mc_members). " b ON a.uid=b.uid WHERE {$condition} ORDER BY a.id DESC LIMIT " . ($i - 1) * $psize . ',' . $psize, $params);
			
			foreach ($outputlist as $key => $value) {
				$arr[$key]['id']           = $value['id'];
				$arr[$key]['nickname']     = preg_replace('#[^\x{4e00}-\x{9fa5}A-Za-z0-9]#u','',$value['nickname']);
				$arr[$key]['mobile']       = $value['mobile'];
				if($value['cash_way']==1){
					$arr[$key]['cash_way'] = '帐户余额';
				}elseif($value['cash_way']==2){
					$arr[$key]['cash_way'] = '微信钱包';
				}elseif($value['cash_way']==3){
					$arr[$key]['cash_way'] = '支付宝';
				}
				$arr[$key]['pay_account']  = $value['pay_account'];
				$arr[$key]['pay_name']     = $value['pay_name'];
				$arr[$key]['lesson_type']= $value['lesson_type']==1?'分销佣金提现':'课程收入提现';
				$arr[$key]['cash_num']	   = $value['cash_num'];
				$arr[$key]['addtime']	   = date('Y-m-d H:i:s', $value['addtime']);
				$arr[$key]['cash_type']	   = $value['cash_type']==1?'管理员审核':'自动到账';
				$arr[$key]['disposetime']  = date('Y-m-d H:i:s', $value['disposetime']);
				$arr[$key]['status']	   = $cashStatusList[$value['status']];
				$arr[$key]['partner_trade_no'] = $value['partner_trade_no'];
				$arr[$key]['payment_no']       = $value['payment_no'];
				$arr[$key]['remark']           = $value['remark'];
			}

			$title = array('提现单号', '粉丝昵称', '手机号码','提现方式','提现帐号','提现帐号人姓名', '提现类型', '申请佣金(元)', '申请时间', '处理方式', '处理时间','状态','商户订单号','微信订单号','管理员备注');
			$filename = $tmpname.$random.$uniacid.'-'.$i;

			$phpexcel = new FyLessonv2PHPExcel();
			$savetype = $max>1 ? 1 : 0;
			$phpexcel->exportTable($title, $arr, $filename, $savetype);
			unset($arr);

			$filenameArr[] = $filename.'-'.date('Ymd').'.xls';
		}

		/* 打包下载 */
		$filepath = '../data/excel/';
		$pack = $filepath.$tmpname.$random.$uniacid.'-'.date('Ymd').'.zip';
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
		header('Content-disposition:attachment;filename='.$tmpname.$random.$uniacid.'-'.date('Ymd').'.zip');
		$filesize = filesize($pack);
		readfile($pack);
		header('Content-length:'.$filesize);

		$files = glob($filepath.'*');
		foreach($files as $file) {
			if(strstr($file, "{$tmpname}{$random}{$uniacid}-")){
				unlink($file);
			}
		}
	}

}elseif($op=='detail'){
	$id = intval($_GPC['id']);
	$cashlog = pdo_fetch("SELECT a.*,b.mobile,b.nickname,b.realname,b.avatar FROM " .tablename($this->table_cashlog). " a LEFT JOIN " .tablename($this->table_mc_members). " b ON a.uid=b.uid WHERE a.uniacid=:uniacid AND a.id=:id", array(':uniacid'=>$uniacid,':id'=>$id));
	if(empty($cashlog)){
		message("该条提现申请不存在或已被删除！", "", "error");
	}
	if(empty($cashlog['avatar'])){
		$cashlog['avatar'] = MODULE_URL."template/mobile/images/default_avatar.jpg";
	}else{
		$cashlog['avatar'] = strstr($cashlog['avatar'], "http://") ? $cashlog['avatar'] : $_W['attachurl'].$cashlog['avatar'];
	}

	if(checksubmit('submit')){
		if($cashlog['status']!=0){
			message("该条提现申请已处理！", "", "error");
		}

		$status = intval($_GPC['status']); /* 状态 0.待打款 1.已打款 -1.作废无效佣金 -2.驳回申请 */
		$remark = trim($_GPC['remark']);   /* 管理员备注信息 */

		$upcashlog = array();
		$upcashlog['remark'] = $remark;
		if($status == 1){
			if($cashlog['cash_way']==2){ //提现到微信钱包
				$post = array('total_amount'=>$cashlog['cash_num'], 'desc'=>'用户申请微课堂佣金提现');
				$fans = array('openid'=>$cashlog['openid'], 'nickname'=>$cashlog['nickname']);
				$result = $this->companyPay($post,$fans);

				if($result['result_code']=='SUCCESS'){
					$upcashlog['status']           = 1;
					$upcashlog['disposetime']      = strtotime($result['payment_time']);
					$upcashlog['partner_trade_no'] = $result['partner_trade_no'];
					$upcashlog['payment_no']	   = $result['payment_no'];

					$res = pdo_update($this->table_cashlog, $upcashlog, array('id'=>$cashlog['id']));
					if($res){
						$this->addSysLog($_W['uid'], $_W['username'], 3, "分销管理->待打款提现申请", "[处理成功]提现单号:{$id}的提现申请");
					}
					message("提现处理成功，佣金已发放到用户微信钱包！", $this->createWebUrl('finance', array('op'=>'commission','status'=>0)), "success");

				}elseif($result['result_code']=='FAIL'){
					$this->addSysLog($_W['uid'], $_W['username'], 3, "分销管理->待打款提现申请", "[处理失败]提现单号:{$id}的提现申请，原因:".$result['return_msg']);
					message($result['return_msg']."，微信接口返回信息：".$result['err_code_des'], "", "error");
				}
			}elseif($cashlog['cash_way']==3){ //提现到支付宝
				if(empty($remark)){
					message("请输入管理员备注", "", "warning");
				}
				$upcashlog['status']           = 1;
				$upcashlog['disposetime']      = time();
				pdo_update($this->table_cashlog, $upcashlog, array('id'=>$cashlog['id']));

				message("提现处理成功", $this->createWebUrl('finance', array('op'=>'commission','status'=>0)), "success");
			}
			
		}elseif($status=='-1' || $status=='-2'){
			if(empty($remark)){
				message("请输入管理员备注", "", "warning");
			}

			$upcashlog['status']	  = $status;
			$upcashlog['disposetime'] = time();

			$res = pdo_update($this->table_cashlog, $upcashlog, array('id'=>$cashlog['id']));
			if($res){
				$lessonMember = pdo_get($this->table_member, array('uid'=>$cashlog['uid']));
				/* 分销佣金 */
				if($status=='-2' && $cashlog['lesson_type']==1){
					$commissionData = array(
						'nopay_commission' => $lessonMember['nopay_commission'] + $cashlog['cash_num'],
						'pay_commission'   => $lessonMember['pay_commission'] - $cashlog['cash_num']
					);
					pdo_update($this->table_member, $commissionData, array('uid'=>$cashlog['uid']));

					
					$commissionLog = array(
						'uniacid'	 => $uniacid,
						'orderid'	 => 0,
						'uid'		 => $cashlog['uid'],
						'nickname'	 => $cashlog['nickname'],
						'bookname'	 => '分销佣金申请驳回:'.$cashlog['id'],
						'change_num' => $cashlog['cash_num'],
						'grade'		 => '-1',
						'remark'	 => $remark,
						'addtime'	 => time(),
					);
					pdo_insert($this->table_commission_log, $commissionLog);
				}

				/* 课程佣金 */
				if($status=='-2' && $cashlog['lesson_type']==2){
					$incomeData = array(
						'nopay_lesson' => $lessonMember['nopay_lesson'] + $cashlog['cash_num'],
						'pay_lesson'   => $lessonMember['pay_lesson'] - $cashlog['cash_num']
					);
					pdo_update($this->table_member, $incomeData, array('uid'=>$cashlog['uid']));

					$teacher = pdo_get($this->table_teacher, array('uid'=>$cashlog['uid']), array('teacher'));			
					$teacherIncomeLog = array(
						'uniacid'		 => $uniacid,
						'uid'			 => $cashlog['uid'],
						'teacher'	     => $teacher['teacher'],
						'bookname'		 => '课程佣金申请驳回:'.$cashlog['id'],
						'orderprice'	 => $cashlog['cash_num'],
						'teacher_income' => 100,
						'income_amount'	 => $cashlog['cash_num'],
						'addtime'		 => time(),
					);
					pdo_insert($this->table_teacher_income, $teacherIncomeLog);
				}

				$this->addSysLog($_W['uid'], $_W['username'], 3, "分销管理->待打款提现申请", "[处理成功]设置提现单号:{$id}的提现申请为".$cashStatusList[$status]);
			}

			message("操作成功，提现申请状态已设置为：".$cashStatusList[$status], $this->createWebUrl('finance', array('op'=>'commission')), "success");
		}
	}

}elseif($op=='handle'){
	if(checksubmit()){
		$user_id = intval($_GPC['user_id']);
		$type = intval($_GPC['commission_type']);
		$amount = trim($_GPC['amount']);
		$remark = trim($_GPC['remark']);

		if(empty($user_id)){
			message("请输入会员ID");
		}
		$user = pdo_fetch("SELECT a.nopay_commission,a.pay_commission,a.nopay_lesson,a.pay_lesson,b.nickname FROM " .tablename($this->table_member). " a LEFT JOIN " .tablename($this->table_mc_members). " b ON a.uid=b.uid WHERE a.uid=:uid", array(':uid'=>$user_id));
		if(empty($user)){
			message("该会员不存在");
		}

		if(empty($type)){
			message("请选择佣金类型");
		}
		if(empty($amount)){
			message("请输入调整金额");
		}
		if(!is_numeric($amount)){
			message("调整金额必须是数字");
		}
		if(empty($remark)){
			message("请输入备注信息");
		}

		$data = array();
		if($type==1){
			$commission_txt = "分销商佣金";
			$data['nopay_commission'] = $user['nopay_commission'] + $amount;
		}
		if($type==2){
			$commission_txt = "课程佣金";
			$data['nopay_lesson'] = $user['nopay_lesson'] + $amount;
		}

		pdo_begin();
		try {
			pdo_update($this->table_member, $data, array('uid'=>$user_id));

			if($type==1){
				$log = array(
					'uniacid'	=> $uniacid,
					'orderid'	=> 0,
					'uid'		=> $user_id,
					'nickname'	=> $user['nickname'],
					'bookname'	=> '管理员后台操作:'.$amount,
					'change_num' => $amount,
					'grade'		=> -1,
					'remark'	=> $remark,
					'addtime'	=> time()
				);
				pdo_insert($this->table_commission_log, $log);

			}elseif($type==2){
				$teacher = pdo_get($this->table_teacher, array('uid'=>$user_id), array('teacher'));
				$log = array(
					'uniacid'	=> $uniacid,
					'uid'		=> $user_id,
					'teacher'	=> $teacher['teacher'],
					'ordersn'	=> '备注:'.$remark,
					'bookname'	=> '管理员后台操作:'.$amount,
					'orderprice' => $amount,
					'teacher_income' => '',
					'income_amount'	 => $amount,
					'addtime'	=> time()
				);
				pdo_insert($this->table_teacher_income, $log);
			}


			pdo_commit();

		} catch (Exception $e) {
			load()->func('logging');
			logging_run('管理员后台操作'.$commission_txt.'失败(uniacid:'.$uniacid.')，原因：'.$e->getMessage(), 'trace', 'fylessonv2_finance');
			pdo_rollback(); 
		}

		message("操作成功", $this->createWebUrl('finance', array('op'=>'handle')), "success");
	}

}

include $this->template('web/finance');


?>