<?php
/**
 * 讲师管理
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

if ($operation == 'display') {
	
	$teacher = $_GPC['teacher'];
	$letter  = $_GPC['letter'];
	$status  = $_GPC['status'];
	$teachertype  = $_GPC['teachertype'];
	
	$condition = " uniacid=:uniacid ";
	$params[':uniacid'] = $uniacid;

	if(!empty($teacher)){
		$condition .= " AND teacher LIKE :teacher ";
		$params[':teacher'] = "%".$teacher."%";
	}
	if(!empty($letter)){
		$condition .= " AND first_letter LIKE :letter ";
		$params[':letter'] = "%".$letter."%";
	}
	if($status!=''){
		$condition .= " AND status=:status ";
		$params[':status'] = $status;
	}
	if($teachertype==1){
		$condition .= " AND uid = :uid ";
		$params[':uid'] = 0;
	}elseif($teachertype==2){
		$condition .= " AND uid != :uid";
		$params[':uid'] = 0;
	}
	

	$list = pdo_fetchall("SELECT * FROM " . tablename($this->table_teacher) . " WHERE {$condition} ORDER BY displayorder DESC, id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $params);
	foreach($list as $key=>$value){
		$list[$key]['member'] = pdo_fetch("SELECT nopay_lesson,pay_lesson FROM " .tablename($this->table_member). " WHERE uid=:uid", array(':uid'=>$value['uid']));
	}

	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename($this->table_teacher) . " WHERE {$condition} ", $params);
	$pager = pagination($total, $pindex, $psize);

}elseif($operation == 'post') {
	$id = intval($_GPC['id']); /* 当前讲师id */
	$letter = array("A","B","C","D","E","F","G","H","I","J","K","L","N","M","O","P","Q","R","S","T","U","V","W","X","Y","Z");

	if (!empty($id)) {
		$teacher = pdo_fetch("SELECT * FROM " . tablename($this->table_teacher) . " WHERE uniacid=:uniacid AND id=:id ", array(':uniacid'=>$uniacid,':id'=>$id));
		if(empty($teacher)){
			message("该讲师不存在或已被删除！", "", "error");
		}
	}

	if (checksubmit('submit')) {
		if (!is_numeric($_GPC['teacher_income'])) {
			message("讲师分成必须为整数");
		}
	
		$data = array(
			'uniacid'        => $_W['uniacid'],
			'teacher'        => trim($_GPC['teacher']),
			'teacher_income' => intval($_GPC['teacher_income']),
			'qq'		     => trim($_GPC['qq']),
			'qqgroup'        => trim($_GPC['qqgroup']),
			'qqgroupLink'    => trim($_GPC['qqgroupLink']),
			'weixin_qrcode'  => trim($_GPC['weixin_qrcode']),
			'first_letter'   => trim($_GPC['first_letter']),
			'teacherdes'     => trim($_GPC['teacherdes']),
			'teacherphoto'   => trim($_GPC['teacherphoto']),
			'status'	     => intval($_GPC['status']),
			'upload'	     => intval($_GPC['upload']),
			'displayorder'   => intval($_GPC['displayorder']),
			'addtime'        => time(),
			'update_time'	 => time(),
		);
		if (empty($data['teacher'])) {
			message("请输入讲师名称");
		}
		if ($data['teacher_income']<0 || $data['teacher_income']>100) {
			message("讲师分成必须介于0~100之间");
		}
		if (empty($data['status'])) {
			message("请选择讲师状态");
		}

		if($setting['company_income']){
			$data['company_uid'] = intval($_GPC['company_uid']);
		}

		$isexist = pdo_fetch("SELECT id FROM " .tablename($this->table_teacher). " WHERE uniacid=:uniacid AND teacher=:teacher LIMIT 1", array(':uniacid'=>$uniacid, ':teacher'=>$data['teacher']));

		if (!empty($id)) {
			unset($data['addtime']);
			$res = pdo_update($this->table_teacher, $data, array('id' => $id));
			if($res){
				$this->addSysLog($_W['uid'], $_W['username'], 3, "讲师管理", "编辑ID:{$id}的讲师");
			}
		} else {
			$res = pdo_insert($this->table_teacher, $data);
			$id = pdo_insertid();
			if($id){
				$this->addSysLog($_W['uid'], $_W['username'], 1, "讲师管理", "新增ID:{$id}的讲师");
			}
		}

		if($res){
			if($teacher['status'] != $data['status']){
				if($data['status']==1 || $data['status']==-1){
					$fans = pdo_get($this->table_fans, array('uid'=>$teacher['uid']), array('openid'));
					$tplmessage = pdo_get($this->table_tplmessage, array('uniacid'=>$uniacid), array('teacher_notice','teacher_notice_format'));
					$teacher_notice_format = json_decode($tplmessage['teacher_notice_format'], true);

					if($fans['openid'] && $tplmessage['teacher_notice']){
						if($data['status']==1){
							$first = $teacher_notice_format['first'] ? $teacher_notice_format['first'] : '恭喜您，您的讲师申请已审核通过。';
							$keyword2 = '已通过';
							$remark = $teacher_notice_format['remark'] ? $teacher_notice_format['remark'] : '点击详情进入讲师中心。';
							$url = $_W['siteroot'] . "app/index.php?i={$uniacid}&c=entry&do=teachercenter&m=fy_lessonv2";
						}elseif($data['status']==-1){
							$first = $teacher_notice_format['first'] ? $teacher_notice_format['first'] : '很抱歉，您的讲师申请未通过审核。';
							$keyword2 = '未通过';
							if(trim($_GPC['reason'])){
								$keyword2 .= '，原因：'.$_GPC['reason'];
							}
							$remark = $teacher_notice_format['remark'] ? $teacher_notice_format['remark'] : '点击详情进入讲师中心修改后重新提交申请。';
							$url = $_W['siteroot'] . "app/index.php?i={$uniacid}&c=entry&do=applyteacher&m=fy_lessonv2";
						}
						$keyword1 = $teacher_notice_format['keyword1'] ? $teacher_notice_format['keyword1'] : '讲师申请';

						$sendmessage = array(
							'touser' => $fans['openid'],
							'template_id' => $tplmessage['teacher_notice'],
							'url' => $url,
							'topcolor' => "#7B68EE",
							'data' => array(
								'first' => array(
									'value' => $first,
									'color' => "",
								),
								'keyword1' => array(
									'value' => $keyword1,
									'color' => "",
								),
								'keyword2' => array(
									'value' => $keyword2,
									'color' => "",
								),
								'keyword3' => array(
									'value' => date('Y年m月d日 H:i', time()),
									'color' => "",
								),
								'remark' => array(
									'value' => $remark,
									'color' => "",
								),
							)
						);
						$this->send_template_message(urldecode(json_encode($sendmessage)));
					}
				}
			}

			$refurl = $_GPC['refurl'] ? $_GPC['refurl'] : $this->createWebUrl('teacher', array('op' => 'display'));
			message("更新讲师成功！", $refurl, "success");
		}else{
			message("更新讲师失败，请稍候重试", "", "error");
		}
	}

}elseif ($operation == 'income') {
	$teacher = $_GPC['teacher'];
	$lesson  = $_GPC['lesson'];
	$ordersn = $_GPC['ordersn'];
	
	$condition = " uniacid=:uniacid ";
	$params[':uniacid'] = $uniacid;
	if(!empty($teacher)){
		$condition .= " AND teacher LIKE :teacher ";
		$params[':teacher'] = "%".$teacher."%";
	}
	if(!empty($lesson)){
		$condition .= " AND bookname LIKE :bookname ";
		$params[':bookname'] = "%".$lesson."%";
	}
	if($ordersn!=''){
		$condition .= " AND ordersn=:ordersn ";
		$params[':ordersn'] = $ordersn;
	}

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

	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM " .tablename($this->table_teacher_income). " WHERE {$condition}", $params);

	if(!$_GPC['export']){
		$list = pdo_fetchall("SELECT * FROM " .tablename($this->table_teacher_income). " WHERE {$condition} ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $params);
		$pager = pagination($total, $pindex, $psize);
	
	}else{
		set_time_limit(180);
		$psize = 10000;
		$max = ceil($total/$psize);
		$random = random(4);

		for($i=1; $i<=$max; $i++){
			$outputlist = pdo_fetchall("SELECT * FROM " .tablename($this->table_teacher_income). " WHERE {$condition} ORDER BY id DESC LIMIT " . ($i - 1) * $psize . ',' . $psize, $params);
		
			foreach ($outputlist as $key => $value) {
				$arr[$key]['id']			  = $value['id'];
				$arr[$key]['teacher']         = $value['teacher'];
				$arr[$key]['openid']          = $value['openid'];
				$arr[$key]['ordersn']         = $value['ordersn'];
				$arr[$key]['bookname']        = $value['bookname'];
				$arr[$key]['orderprice']      = $value['orderprice'];
				$arr[$key]['teacher_income']  = $value['teacher_income'];
				$arr[$key]['income_amount']   = $value['income_amount'];
				$arr[$key]['addtime']         = date('Y-m-d H:i:s', $value['addtime']);
			}

			$title =  array('ID', '讲师名称','讲师openid', '订单编号', '课程名称', '课程售价(元)', '讲师分成(%)', '讲师收入(元)', '添加时间');
			$filename = '讲师收入明细'.$random.$uniacid.'-'.$i;

			$phpexcel = new FyLessonv2PHPExcel();
			$savetype = $max>1 ? 1 : 0;
			$phpexcel->exportTable($title, $arr, $filename, $savetype);
			unset($arr);

			$filenameArr[] = $filename.'-'.date('Ymd').'.xls';
		}

		/* 打包下载 */
		$filepath = '../data/excel/';
		$pack = $filepath.'讲师收入明细'.$random.$uniacid.'-'.date('Ymd').'.zip';
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
		header('Content-disposition:attachment;filename=讲师收入明细'.$random.$uniacid.'-'.date('Ymd').'.zip');
		$filesize = filesize($pack);
		readfile($pack);
		header('Content-length:'.$filesize);

		$files = glob($filepath.'*');
		foreach($files as $file) {
			if(strstr($file, "讲师收入明细{$random}{$uniacid}-")){
				unlink($file);
			}
		}
	}
	
}elseif ($operation == 'delete') {
	$id = intval($_GPC['id']);
	$teacher = pdo_fetch("SELECT id FROM " . tablename($this->table_teacher) . " WHERE uniacid=:uniacid AND id=:id ", array(':uniacid'=>$uniacid,':id'=>$id));
	if (empty($teacher)) {
		message("抱歉，讲师不存在或是已经被删除！", $this->createWebUrl('teacher', array('op' => 'display')), "error");
	}

	$lesson = pdo_fetchall("SELECT id FROM " .tablename($this->table_lesson_parent). " WHERE uniacid=:uniacid AND teacherid=:teacherid", array(':uniacid'=>$uniacid,':teacherid'=>$id));
	if($lesson){
		message("该讲师还存在课程，请删除或转移课程后重试！", "", "error");
	}

	pdo_delete($this->table_lesson_collect, array('uniacid'=>$uniacid,'ctype' => 2, 'outid'=>$id));
	$res = pdo_delete($this->table_teacher, array('uniacid'=>$uniacid,'id' => $id));

	if($res){
		$this->addSysLog($_W['uid'], $_W['username'], 2, "讲师管理", "删除ID:{$id}的讲师");
	}
	message("删除讲师成功！", $this->createWebUrl('teacher', array('op' => 'display')), "success");

}elseif($op=='qrcode'){
	$teacherid = intval($_GPC['teacherid']);
	$teacher = pdo_fetch("SELECT teacher FROM " .tablename($this->table_teacher). " WHERE uniacid=:uniacid AND id=:id", array(':uniacid'=>$uniacid, ':id'=>$teacherid));
	if(empty($teacher)){
		message("该讲师不存在或已被删除！", "", "error");
	}
	
	$dirPath = ATTACHMENT_ROOT."images/fy_lessonv2/";
	if(!file_exists($dirPath)){
		mkdir($dirPath, 0777);
	}
	$teacherUrl = $_W['siteroot']."app/".$this->createMobileUrl('teacher', array('teacherid'=>$teacherid));
	$tmpName = "teacher_".$teacherid.".png";
    $qrcodeName = $dirPath.$tmpName;
    
    include_once IA_ROOT."/framework/library/qrcode/phpqrcode.php";
    QRcode::png($teacherUrl, $qrcodeName, 'L', '8', 2);
    
    $downloadName = $teacher['teacher'].".png";
    
    header("Content-type: octet/stream");
    header("Content-disposition:attachment;filename=".$downloadName.";");
    header("Content-Length:".filesize($qrcodeName));
    readfile($qrcodeName);
    
    unlink($qrcodeName);
}

include $this->template('web/teacher');


?>