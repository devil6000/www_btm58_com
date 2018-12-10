<?php
/**
 * 课程管理
 * ============================================================================
 * 版权所有 2015-2018 微课堂团队，并保留所有权利。
 * 网站地址: https://wx.haoshu888.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！不允许对程序代码以任何形式任何目的的再发布，作者将保留
 * 追究法律责任的权力和最终解释权。
 * ============================================================================
 */
if(empty($setting)){
	message("请先配置相关参数！", $this->createWebUrl('setting'), "error");
}

if ($operation == 'display') {
	if (checksubmit('submit')) { /* 排序 */
		if (is_array($_GPC['lessonorder'])) {
			foreach ($_GPC['lessonorder'] as $pid => $val) {
				$data = array('displayorder' => intval($_GPC['lessonorder'][$pid]));
				pdo_update($this->table_lesson_parent, $data, array('id' => $pid));
			}
		}
		if (is_array($_GPC['sectionorder'])) {
			foreach ($_GPC['sectionorder'] as $sid => $val) {
				$data = array('displayorder' => intval($_GPC['sectionorder'][$sid]));
				pdo_update($this->table_lesson_son, $data, array('id' => $sid));
			}
		}
		message('操作成功!', referer, 'success');
	}

	/* 课程分类 */
	$category = pdo_fetchall("SELECT * FROM " . tablename($this->table_category) . " WHERE uniacid=:uniacid AND parentid=:parentid", array(':uniacid'=>$uniacid,':parentid'=>0));
	foreach($category as $k=>$v){
		$category[$k]['child'] = pdo_fetchall("SELECT * FROM " . tablename($this->table_category) . " WHERE uniacid=:uniacid AND parentid=:parentid", array(':uniacid'=>$uniacid,':parentid'=>$v['id']));
	}

	/* 推荐板块列表 */
	$rec_list = pdo_fetchall("SELECT id,rec_name FROM " .tablename($this->table_recommend). " WHERE uniacid=:uniacid", array(':uniacid'=>$uniacid));

	$pindex = max(1, intval($_GPC['page']));
	$psize = 10;

	$bookname = trim($_GPC['bookname']);
	$teacher  = trim($_GPC['teacher']);
	$pid      = intval($_GPC['pid']);
	$cid      = intval($_GPC['cid']);
	$recid	  = intval($_GPC['recid']);
	$is_free  = trim($_GPC['is_free']);
	$status   = trim($_GPC['status']);

	$condition = " b.uniacid=:uniacid ";
	$params[':uniacid'] = $uniacid;
	if($bookname!=''){
		$condition .= " AND b.bookname LIKE :bookname ";
		$params[':bookname'] = "%".$bookname."%";
	}
	if($teacher!=''){
		$condition .= " AND a.teacher LIKE :teacher ";
		$params[':teacher'] = "%".$teacher."%";
	}
	if($pid>0){
		$condition .= " AND b.pid=:pid ";
		$params[':pid'] = $pid;
	}
	if($cid>0){
		$condition .= " AND b.cid=:cid ";
		$params[':cid'] = $cid;
	}
	if($recid>0){
		$condition .= " AND ((b.recommendid='{$recid}') OR (b.recommendid LIKE '{$recid},%') OR (b.recommendid LIKE '%,{$recid}') OR (b.recommendid LIKE '%,{$recid},%')) ";
	}

	if(in_array($is_free, array('0','1'))){
		if(in_array($is_free, array('0'))){
			$condition .= " AND b.price = :price ";
			$params[':price'] = 0;
		}elseif(in_array($is_free, array('1'))){
			$condition .= " AND b.price > :price ";
			$params[':price'] = 0;
		}
	}
	if($status != ''){
		if($status == 999){
			$condition .= " AND b.stock < :stock ";
			$params[':stock'] = 10;
		}else{
			$condition .= " AND b.status=:status ";
			$params[':status'] = $status;
		}
	}

	$list = pdo_fetchall("SELECT a.teacher, b.id,b.pid,b.cid,b.bookname,b.price,b.buynum,b.stock,b.displayorder,b.status,b.section_status,b.visit_number FROM " .tablename($this->table_teacher). " a LEFT JOIN " .tablename($this->table_lesson_parent). " b ON a.id=b.teacherid WHERE {$condition} ORDER BY b.displayorder DESC,b.id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $params);

	foreach($list as $key=>$value){
		$list[$key]['section'] = pdo_fetchall("SELECT id,parentid,title,displayorder FROM " .tablename($this->table_lesson_son). " WHERE parentid=:parentid ORDER BY displayorder DESC", array(':parentid'=>$value['id']));
		$cat_id = $value['cid'] ? $value['cid'] : $value['pid'];
		if($cat_id>0){
			$list[$key]['category'] = pdo_fetch("SELECT name FROM " .tablename($this->table_category). " WHERE id=:id", array(':id'=>$cat_id));
		}
	}

	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM " .tablename($this->table_teacher). " a LEFT JOIN " . tablename($this->table_lesson_parent) . " b ON a.id=b.teacherid WHERE {$condition}", $params);
	$pager = pagination($total, $pindex, $psize);

}elseif($operation == 'postlesson') {
	$id = intval($_GPC['id']);
	if(!empty($id)){
		$lesson = pdo_fetch("SELECT * FROM " .tablename($this->table_lesson_parent). " WHERE uniacid=:uniacid AND id=:id", array(':uniacid'=>$uniacid,':id'=>$id));
		if(empty($lesson)){
			message("该课程不存在或已被删除！", "", "error");
		}
	}

	/* 课程分类列表 */
	$params[':uniacid'] = $uniacid;
	$params[':parentid'] = 0;
	$category = pdo_fetchall("SELECT id,parentid,name FROM " . tablename($this->table_category) . " WHERE uniacid=:uniacid AND parentid=:parentid", $params);
	foreach($category as $k=>$v){
		$category[$k]['child'] = pdo_fetchall("SELECT id,parentid,name FROM " . tablename($this->table_category) . " WHERE uniacid=:uniacid AND parentid=:parentid", array(':uniacid'=>$uniacid,':parentid'=>$v['id']));
	}

	/* 课程规格 */
	$spec_list = pdo_fetchall("SELECT * FROM " .tablename($this->table_lesson_spec). " WHERE uniacid=:uniacid AND lessonid=:lessonid ORDER BY spec_sort DESC,spec_day ASC", array(':uniacid'=>$uniacid, ':lessonid'=>$id));

	/* 推荐板块列表 */
	$rec_list = pdo_fetchall("SELECT id,rec_name FROM " .tablename($this->table_recommend). " WHERE uniacid=:uniacid", array(':uniacid'=>$uniacid));
	
	/* VIP等级列表 */
	$level_list = pdo_fetchall("SELECT * FROM " .tablename($this->table_vip_level). " WHERE uniacid=:uniacid", array(':uniacid'=>$uniacid));

	/* 讲师列表 */
	$teacher_list = pdo_fetchall("SELECT * FROM " .tablename($this->table_teacher). " WHERE uniacid=:uniacid AND status=:status ORDER BY first_letter ASC", array(':uniacid'=>$uniacid,':status'=>1));

	$commission   = unserialize($lesson['commission']);	        /* 佣金比例 */
	$recidarr     = explode(",", $lesson['recommendid']);       /* 已推荐板块 */
	$vipview      = json_decode($lesson['vipview'], true);      /* 免费学习的VIP等级 */
	$share		  = json_decode($lesson['share'], true);	    /* 分享信息 */
	$buynow_info  = json_decode($lesson['buynow_info'], true);  /* 立即购买信息 */
	$poster_config= json_decode($lesson['poster_config'], true);/* 课程海报信息 */
	$appoint_info = json_decode($lesson['appoint_info'], true); /* 预约报名课程信息 */
	$saler_uids   = json_decode($lesson['saler_uids'], true);   /* 核销人员uid */
	foreach($saler_uids as $k=>$v){
		$saler_info[$k] = pdo_get($this->table_mc_members, array('uid'=>$v), array('uid','nickname','avatar'));
	}

	if(checksubmit('submit')){
		$data = array();
		$data['uniacid']		= $uniacid;
		$data['bookname']		= trim($_GPC['bookname']);
		$data['pid']			= intval($_GPC['pid']);
		$data['cid']			= intval($_GPC['cid']);
		$data['lesson_type']	= intval($_GPC['lesson_type']);
		$data['appoint_info']	= json_encode(array_filter($_GPC['appoint_info']));
		$data['saler_uids']		= json_encode($_GPC['saler_uids']);
		$data['buynow_info']	= json_encode($_GPC['buynow_info']);
		$data['images']			= trim($_GPC['images']);
		$data['price']			= trim($_GPC['price'])?trim($_GPC['price']):0;
		$data['stock']			= intval($_GPC['stock']);
		$data['isdiscount']		= intval($_GPC['isdiscount']);
		$data['vipdiscount']	= intval($_GPC['vipdiscount']);
		$data['integral']		= intval($_GPC['integral']);
		$data['integral_rate']	= floatval($_GPC['integral_rate']);
		$data['deduct_integral']= intval($_GPC['deduct_integral']);
		$data['validity']		= intval($_GPC['validity']);
		$data['virtual_buynum'] = intval($_GPC['virtual_buynum']);
		$data['ico_name']		= trim($_GPC['ico_name']);
		$data['difficulty']		= trim($_GPC['difficulty']);
		$data['teacherid']		= intval($_GPC['teacherid']);
		$data['descript']		= trim($_GPC['descript']);
		$data['displayorder']	= intval($_GPC['displayorder']);
		$data['status']			= intval($_GPC['status']);
		$data['section_status'] = intval($_GPC['section_status']);
		$data['vipview']		= json_encode($_GPC['vipview']);
		$data['share']			= json_encode($_GPC['share']);
		$data['support_coupon'] = intval($_GPC['support_coupon']);
		$data['poster_config']  = json_encode($_GPC['poster_config']);
		$data['addtime']		= time();
		$data['commission']	    = serialize(array('commission1'=>floatval($_GPC['commission1']),'commission2'=>floatval($_GPC['commission2']),'commission3'=>floatval($_GPC['commission3'])));

		$checkTeacher = pdo_get($this->table_teacher, array('id'=>$data['teacherid']), array('teacher_income'));
		if($setting['show_teacher_income']){
			$data['teacher_income']	= intval($_GPC['teacher_income']);
		}else{
			$data['teacher_income']	= $lesson['teacher_income'] ? $lesson['teacher_income'] : $checkTeacher['teacher_income'];
		}
		if($setting['company_income']){
			$data['company_income']	= intval($_GPC['company_income']);
		}
		
		if(empty($data['bookname'])){
			message("请输入课程名称！");
		}
		if(empty($data['pid'])){
			message("请选择课程分类！");
		}
		if(empty($data['teacherid'])){
			message("请选择讲师！");
		}
		if(!in_array($data['status'], array('0','1','2','-1'))){
			message("请选择课程状态！");
		}

		foreach($_GPC['recid'] as $recid){
			$tmprecid .= $recid.',';
		}
		$data['recommendid'] = trim($tmprecid, ",");
		
		if(empty($id)){
			pdo_insert($this->table_lesson_parent, $data);
			$id = pdo_insertid();
			if($id){
				$this->addSysLog($_W['uid'], $_W['username'], 1, "课程管理", "新增ID:{$id}的课程");
			}
		}else{
			unset($data['addtime']);
			$res = pdo_update($this->table_lesson_parent, $data, array('uniacid'=>$uniacid, 'id'=>$id));
			if($res){
				$this->addSysLog($_W['uid'], $_W['username'], 3, "课程管理", "编辑ID:{$id}的课程");
			}
		}

		if($id>0){
			/* 处理课程规格 */
			pdo_delete($this->table_lesson_spec, array('lessonid'=>$id));
			foreach ($_GPC['spec_time'] as $key => $row) {
				$row = floatval($row);
				$price = floatval($_GPC['spec_price'][$key]);
				$spec_name = trim($_GPC['spec_name'][$key]);
				$spec_sort = trim($_GPC['spec_sort'][$key]);

				if (!$row || !$price)
					continue;
				$spec_data = array(
					'uniacid'	 => $uniacid,
					'lessonid'	 => $id,
					'spec_day'	 => $row,
					'spec_price' => $price,
					'spec_name'  => $spec_name,
					'spec_sort'  => $spec_sort,
					'addtime'	 => time(),
				);
				pdo_insert($this->table_lesson_spec, $spec_data);
				$price_array[] = $price;
			}
			$min_price = array_search(min($price_array), $price_array);
			pdo_update($this->table_lesson_parent, array('price'=>$price_array[$min_price]), array('uniacid'=>$uniacid,'id'=>$id));
		}

		if($poster_config['images'] != $_GPC['poster_config']['images']){
			$files = glob(ATTACHMENT_ROOT."images/{$uniacid}/fy_lessonv2/poster/*");
			foreach($files as $file) {
				if(strstr($file, "lesson_{$id}_")){
					unlink($file);
				}
			}
		}
		
		$refurl = $_GPC['refurl']?$_GPC['refurl']:$this->createWebUrl("lesson");
		message("操作成功！", $refurl, "success");
	}

}elseif($operation == 'postsection') {
	$typeStatus = new TypeStatus();
	$saveList = $typeStatus->sectionSaveType();

	$pid = intval($_GPC['pid']); /* 课程id */
	$lesson = pdo_fetch("SELECT id,bookname FROM " .tablename($this->table_lesson_parent). " WHERE uniacid=:uniacid AND id=:id", array(':uniacid'=>$uniacid,':id'=>$pid));
	if(empty($lesson)){
		message("当前课程不存在或已被删除！", "", "error");
	}

	$id = intval($_GPC['id']); /* 章节id */
	if(!empty($id)){
		$section = pdo_fetch("SELECT * FROM " .tablename($this->table_lesson_son). " WHERE uniacid=:uniacid AND id=:id", array(':uniacid'=>$uniacid,':id'=>$id));
		if(empty($section)){
			message("该章节不存在或已被删除！", "", "error");
		}
		$sectionUrl = $_W['siteroot'].'app/'.str_replace("./", "", $this->createMobileUrl('lesson', array('id'=>$lesson['id'])))."&amp;sectionid=".$id;
	}

	/* 存储方式 */
	$qiniu = unserialize($setting['qiniu']);
	if(substr($qiniu['url'],0,7)!='http://'){
		$qiniu['url'] = "http://".$qiniu['url']."/";
	}

	$qcloud = unserialize($setting['qcloud']);
	if(substr($qcloud['url'],0,7)!='http://'){
		$qcloud['url'] = "http://".$qcloud['url']."/";
	}

	if(checksubmit('submit')){
		$data = array();
		$data['uniacid']		= $uniacid;
		$data['parentid']		= $pid;
		$data['title']			= $_GPC['title'];
		$data['images']			= trim($_GPC['images']);
		$data['sectiontype']	= intval($_GPC['sectiontype']);
		$data['savetype']		= trim($_GPC['savetype']);
		$data['videourl']		= trim($_GPC['videourl']);
		$data['videotime']		= str_replace("：",":",trim($_GPC['videotime']));
		$data['content']		= $_GPC['content'];
		$data['displayorder']	= intval($_GPC['displayorder']);
		$data['is_free']	    = intval($_GPC['is_free']);
		$data['status']			= intval($_GPC['status']);
		$data['auto_show']		= intval($_GPC['auto_show']);
		$data['show_time']		= strtotime($_GPC['show_time']);
		$data['test_time']		= intval($_GPC['test_time']);
		$data['addtime']		= time();

		if(empty($data['parentid'])){
			message("课程不存在或已被删除");
		}
		if(empty($data['title'])){
			message("请填写章节名称");
		}
		if(empty($data['sectiontype'])){
			message("请选择章节类型");
		}
		if($data['sectiontype']==1 && empty($data['videourl'])){
			message("请填写章节视频URL");
		}
		if($data['sectiontype']==3 && in_array($data['savetype'], array('2','4','5'))){
			message("音频章节存储方式只能其他存储、七牛云存储和腾讯云存储");
		}
		if(!in_array($data['is_free'], array('0','1'))){
			message("请选择是否为试听章节");
		}
		if(!in_array($data['status'], array('0','1'))){
			message("请选择章节状态");
		}
		if($data['auto_show']==1 && empty($data['show_time'])){
			message("请选择定时上架日期时间");
		}

		if($data['savetype']==2){//内嵌代码存储方式保留内容的空格
			$data['videourl'] = $_GPC['videourl'];
		}
		if($data['sectiontype']==4){//外链章节的url保存在videourl里
			$data['videourl'] = $_GPC['linkurl'];
		}

		if(empty($id)){
			pdo_insert($this->table_lesson_son, $data);
			$id = pdo_insertid();
			pdo_update($this->table_lesson_parent, array('update_time'=>time()), array('id'=>$pid));
			if($id){
				$this->addSysLog($_W['uid'], $_W['username'], 1, "课程管理->章节管理", "新增ID:{$pid}的课程下ID:{$id}的章节");
			}

			message("添加章节成功！", $this->createWebUrl('lesson',array('op'=>'viewsection','pid'=>$pid)), "success");
		}else{
			unset($data['addtime']);
			$res = pdo_update($this->table_lesson_son, $data, array('uniacid'=>$uniacid, 'id'=>$id));
			if($res){
				$this->addSysLog($_W['uid'], $_W['username'], 3, "课程管理->章节管理", "编辑ID:{$pid}的课程下ID:{$id}的章节");
			}

			$refurl = $_GPC['refurl']?$_GPC['refurl']:$this->createWebUrl('lesson',array('op'=>'viewsection','pid'=>$pid));
			message("编辑章节成功！", $refurl, "success");
		}
	}

}elseif($operation == 'viewsection'){
	$pid = intval($_GPC['pid']);
	$lesson = pdo_fetch("SELECT * FROM " .tablename($this->table_lesson_parent). " WHERE uniacid=:uniacid AND id=:id", array(':uniacid'=>$uniacid,':id'=>$pid));
	if(empty($lesson)){
		message("该课程不存在或已被删除！", "", "error");
	}

	if (checksubmit('submit')) { /* 排序 */
		if (is_array($_GPC['sectionorder'])) {
			foreach ($_GPC['sectionorder'] as $sid => $val) {
				$data = array('displayorder' => intval($_GPC['sectionorder'][$sid]));
				pdo_update($this->table_lesson_son, $data, array('id' => $sid));
			}
		}
		
		message('操作成功!', referer, 'success');
	}

	$pindex = max(1, intval($_GPC['page']));
	$psize = 25;
	
	$condition = " uniacid=:uniacid AND parentid=:parentid ";
	$params[':uniacid'] = $uniacid;
	$params[':parentid'] = $pid;
	
	$section_list = pdo_fetchall("SELECT * FROM " .tablename($this->table_lesson_son). " WHERE {$condition} ORDER BY displayorder DESC,id ASC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $params);
	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM " .tablename($this->table_lesson_son). " WHERE {$condition}", $params);
	$pager = pagination($total, $pindex, $psize);

}elseif($op=='inform'){
	$pindex = max(1, intval($_GPC['page']));
	$psize = 10;

	$list = pdo_fetchall("SELECT * FROM " .tablename($this->table_inform). " WHERE uniacid=:uniacid ORDER BY inform_id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, array(':uniacid'=>$uniacid));
	foreach($list as $k=>$v){
		$list[$k]['remain_number'] = pdo_fetchcolumn("SELECT COUNT(*) FROM " .tablename($this->table_inform_fans). " WHERE inform_id=:inform_id", array(':inform_id'=>$v['inform_id']));
	}

	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM " .tablename($this->table_inform). " WHERE uniacid=:uniacid", array(':uniacid'=>$uniacid));
	$pager = pagination($total, $pindex, $psize);

}elseif($operation == 'informStudent'){

	if(checksubmit()){
		$lesson_id = intval($_GPC['lesson_id']);
		$user_type = intval($_GPC['user_type']);
		$content = json_encode($_GPC['content']);

		$lesson = pdo_fetch("SELECT id,bookname,teacherid,status FROM " .tablename($this->table_lesson_parent). " WHERE id=:id", array(':id'=>$lesson_id));
		if(empty($lesson) || $lesson['status']!=1){
			message("您选择的课程不存在或已下架", "", "error");
		}

		pdo_begin();
		try {
			/* 全部粉丝 */
			if($user_type==1){
				$list = pdo_fetchall("SELECT openid FROM " .tablename($this->table_member). " WHERE uniacid=:uniacid AND openid != :openid", array(':uniacid'=>$uniacid, ':openid'=>''));
			
			/* 全部VIP粉丝 */
			}elseif($user_type==2){
				$list = pdo_fetchall("SELECT distinct(b.openid) FROM " .tablename($this->table_member_vip). " a LEFT JOIN " .tablename($this->table_member). " b ON a.uid=b.uid WHERE a.uniacid=:uniacid AND a.validity>:validity AND b.openid != :openid", array(':uniacid'=>$uniacid, ':validity'=>time(), ':openid'=>''));
			
			/* 购买该讲师的粉丝 */
			}elseif($user_type==3){
				$list = pdo_fetchall("SELECT distinct(b.openid) FROM " .tablename($this->table_order). " a LEFT JOIN " .tablename($this->table_member). " b ON a.uid=b.uid WHERE a.uniacid=:uniacid AND a.teacherid=:teacherid AND b.openid != :openid", array(':uniacid'=>$uniacid, ':teacherid'=>$lesson['teacherid'], ':openid'=>''));
			
			/* 购买该课程的粉丝 */
			}elseif($user_type==4){
				$list = pdo_fetchall("SELECT distinct(b.openid) FROM " .tablename($this->table_order). " a LEFT JOIN " .tablename($this->table_member). " b ON a.uid=b.uid WHERE a.uniacid=:uniacid AND a.lessonid=:lessonid AND b.openid != :openid", array(':uniacid'=>$uniacid, ':lessonid'=>$lesson['id'], ':openid'=>''));
			}

			$inform = array(
				'uniacid'	=> $uniacid,
				'lesson_id' => $lesson_id,
				'book_name' => $lesson['bookname'],
				'content'	=> $content,
				'user_type' => $user_type,
				'inform_number' => count($list),
				'status'	=> 1, 
				'addtime'	=> time()
			);
			pdo_insert($this->table_inform, $inform);
			$inform_id = pdo_insertid();


			$now = time();
			$sql_head = "INSERT INTO ".tablename($this->table_inform_fans)." (`uniacid`, `inform_id`,`openid`,`addtime`) VALUES ";
			$sql = "";
			foreach($list as $k=>$v){
				$sql .= "('{$uniacid}','{$inform_id}','{$v[openid]}','{$now}'),";

				if(($k+1)%1000==0 || $k+1==count($list)){
					$sql = substr($sql, 0, strlen($sql)-1);
					pdo_query($sql_head.$sql);
					$sql = "";
				}
			}
			pdo_commit();
			message("添加成功", $this->createWebUrl('lesson', array('op'=>'inform')), "success");

		} catch (Exception $e) {
			load()->func('logging');
			logging_run('管理员后台添加课程通知失败(uniacid:'.$uniacid.')，原因：'.$e->getMessage(), 'trace', 'fylessonv2');
			pdo_rollback(); 
		}
	}else{
		$lessonid = intval($_GPC['lessonid']);   /* 从课程管理携带过来的课程id */
		$sectionid = intval($_GPC['sectionid']); /* 从章节管理携带过来的章节id */
		if($lessonid){
			$lesson = pdo_get($this->table_lesson_parent, array('id'=>$lessonid));
			if(empty($lesson)){
				message('课程不存在');
			}
			
			$lessonUrl = $_W['siteroot'].'app/'.str_replace("./", "", $this->createMobileUrl('lesson', array('id'=>$lesson['id'])));
		}
		if($sectionid){
			$section = pdo_get($this->table_lesson_son, array('id'=>$sectionid));
			if(empty($section)){
				message('章节不存在');
			}
			$lessonid = $section['parentid'];
			$lesson = pdo_get($this->table_lesson_parent, array('id'=>$lessonid));

			$first = '您关注的课程《'.$lesson['bookname'].'》更新了章节“'.$section['title'].'”，快来学习吧！';
			$sectionUrl = $_W['siteroot'].'app/'.str_replace("./", "", $this->createMobileUrl('lesson', array('id'=>$section['parentid'],'sectionid'=>$section['id'])));
		}

		$teacher = pdo_get($this->table_teacher, array('id'=>$lesson['teacherid']), array('teacher'));
		if($lessonUrl){
			$first = '您关注的【'.$teacher['teacher'].'】上新课了，快和我一起加入学习！';
		}
		
		$bookname = $lesson['bookname'];
		$link = $lessonUrl ? $lessonUrl : $sectionUrl;
		$today = date('Y年m月d日');
		$remark = '';
	}

}elseif($operation == 'delete') {
	$pid = intval($_GPC['pid']);
	$cid = intval($_GPC['cid']);
	$sid = intval($_GPC['sid']);
	if($pid>0){
		$lesson = pdo_fetch("SELECT id FROM " .tablename($this->table_lesson_parent). " WHERE uniacid=:uniacid AND id=:id", array(':uniacid'=>$uniacid, ':id'=>$pid));
		if(empty($lesson)){
			message("该课程不存在或已被删除！", "", "error");
		}
		pdo_delete($this->table_lesson_collect, array('uniacid'=>$uniacid,'ctype' => 1, 'outid'=>$pid));
		pdo_delete($this->table_lesson_son, array('uniacid'=>$uniacid, 'parentid'=>$pid));
		pdo_delete($this->table_lesson_parent, array('uniacid'=>$uniacid, 'id'=>$pid));
		pdo_delete($this->table_lesson_praxis, array('uniacid' => $uniacid, 'parentid' => $pid));

		$this->addSysLog($_W['uid'], $_W['username'], 2, "课程管理", "删除ID:{$pid}的课程及所有章节");
		message("删除课程成功！", referer, "success");
	}

	if($cid>0){
		$section = pdo_fetch("SELECT id FROM " .tablename($this->table_lesson_son). " WHERE uniacid=:uniacid AND id=:id ", array(':uniacid'=>$uniacid,':id'=>$cid));
		if(empty($section)){
			message("该章节不存在或已被删除！", "", "error");
		}

		$res = pdo_delete($this->table_lesson_son, array('uniacid'=>$uniacid, 'id'=>$cid));
		pdo_delete($this->table_lesson_praxis, array('uniacid' => $uniacid, 'chapterid' => $cid));
		if($res){
			$this->addSysLog($_W['uid'], $_W['username'], 2, "课程管理", "删除ID:{$pid}的课程下ID:{$cid}的章节");
		}

		message("删除章节成功！", referer, "success");
	}

	if($sid > 0){
	    $praxis = pdo_fetch('SELECT id FROM ' . tablename($this->table_lesson_praxis) . ' WHERE uniacid=:uniacid AND id=:id', array(':uniacid' => $uniacid, ':id' => $sid));
	    if(empty($praxis)){
	        message("该习题不存在或已删除！", referer, "error");
        }
        $res = pdo_delete($this->table_lesson_praxis,array('uniacid' => $uniacid, 'id' => $sid));
	    if($res){
	        $this->addSysLog($_W['uid'], $_W['username'], 2, "习题管理", "删除ID:{$sid}的习题");
        }
        message("删除习题成功！", referer, "success");
    }

}elseif($operation=='record'){
	$type = intval($_GPC['type']);
	$lessonid = intval($_GPC['lessonid']);
	$uid = intval($_GPC['uid']);

	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;

	$condition = " a.uniacid=:uniacid ";
	$params[':uniacid'] = $uniacid;
	if($lessonid>0){
		$condition .= " AND a.lessonid=:lessonid ";
		$params[':lessonid'] = $lessonid;
	}
	if($uid>0){
		$condition .= " AND a.uid=:uid ";
		$params[':uid'] = $uid;
	}

	$list = pdo_fetchall("SELECT a.uid,a.playtime,a.addtime,b.bookname,c.title FROM " .tablename($this->table_playrecord). " a INNER JOIN " .tablename($this->table_lesson_parent). " b ON a.lessonid=b.id INNER JOIN " .tablename($this->table_lesson_son). " c ON a.sectionid=c.id WHERE {$condition} ORDER BY a.addtime DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $params);
	foreach($list as $key=>$value){
		$list[$key]['user'] = pdo_fetch("SELECT nickname,realname,mobile FROM " .tablename('mc_members'). " WHERE uid=:uid", array(':uid'=>$value['uid']));
		$list[$key]['playtime'] = gmdate("H:i:s", $list[$key]['playtime']);
	}

	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM " .tablename($this->table_playrecord). " a INNER JOIN " .tablename($this->table_lesson_parent). " b ON a.lessonid=b.id INNER JOIN " .tablename($this->table_lesson_son). " c ON a.sectionid=c.id WHERE {$condition}", $params);
	$pager = pagination($total, $pindex, $psize);

}elseif($op=='up_spec'){
	set_time_limit(60);
	$list = pdo_fetchall("SELECT * FROM " .tablename($this->table_lesson_parent). " WHERE uniacid=:uniacid ", array(':uniacid'=>$uniacid));
	foreach($list as $v){
		$data = array(
			'uniacid' => $uniacid,
			'lessonid' => $v['id'],
			'spec_day' => '-1',
			'spec_price' => $v['price'],
			'addtime' => time(),
		);
		pdo_insert($this->table_lesson_spec, $data);
	}

}elseif($op=='qrcode'){
	$lessonid = intval($_GPC['lessonid']);
	$lesson = pdo_fetch("SELECT bookname FROM " .tablename($this->table_lesson_parent). " WHERE uniacid=:uniacid AND id=:id", array(':uniacid'=>$uniacid, ':id'=>$lessonid));
	if(empty($lesson)){
		message("该课程不存在或已被删除！", "", "error");
	}
	
	$dirPath = ATTACHMENT_ROOT."images/fy_lessonv2/";
	if(!file_exists($dirPath)){
		mkdir($dirPath, 0777);
	}
	$lessonUrl = $_W['siteroot']."app/".$this->createMobileUrl('lesson', array('op'=>'display','id'=>$lessonid));
	$tmpName = "lesson_".$lessonid.".png";
    $qrcodeName = $dirPath.$tmpName;
    
    include_once IA_ROOT."/framework/library/qrcode/phpqrcode.php";
    QRcode::png($lessonUrl, $qrcodeName, 'L', '8', 2);
    
    $downloadName = $lesson['bookname'].".png";
    
    header("Content-type: octet/stream");
    header("Content-disposition:attachment;filename=".$downloadName.";");
    header("Content-Length:".filesize($qrcodeName));
    readfile($qrcodeName);
    
    unlink($qrcodeName);
	exit();
	
}elseif($op=='updomain'){
	
	if(checksubmit()){
		$limit = intval($_GPC['upnumber']) ;
		$old_domain = trim($_GPC['old_domain']);
		$new_domain = trim($_GPC['new_domain']);
		
		if(empty($limit)){
			message("请选择更新数量", "", "error");
		}
		if(empty($old_domain)){
			message("请输入原音视频域名", "", "error");
		}
		if(empty($new_domain)){
			message("请输入新音视频域名", "", "error");
		}

		$t = 0;

		$section_list = pdo_fetchall("SELECT id,videourl FROM " .tablename($this->table_lesson_son). " WHERE uniacid=:uniacid AND videourl LIKE :videourl LIMIT 0, ".$limit, array(':uniacid'=>$uniacid, ':videourl'=>'%'.$old_domain.'%'));

		foreach($section_list as $item){
			$videourl = str_replace($old_domain, $new_domain, $item['videourl']);
			if(pdo_update($this->table_lesson_son, array('videourl'=>$videourl), array('id'=>$item['id']))){
				$t++;
			}
		}

		message("成功更新{$t}条数据", $this->createWebUrl('lesson', array('op'=>'updomain')), "success");
	}

}elseif($op=='previewVideo'){
	$id = intval($_GPC['id']); /* 章节id */
	$section = pdo_fetch("SELECT * FROM " .tablename($this->table_lesson_son). " WHERE uniacid=:uniacid AND id=:id", array(':uniacid'=>$uniacid,':id'=>$id));
	if(empty($section)){
		message("该章节不存在或已被删除！", "", "error");
	}

	//七牛云对象存储
	if($section['savetype']==1){
		$qiniu = unserialize($setting['qiniu']);
		$playurl = $this->privateDownloadUrl($qiniu['access_key'], $qiniu['secret_key'], $section['videourl']);
		if($qiniu['https']){
			$playurl = str_replace("http://", "https://", $playurl);
		}
	}

	//腾讯云对象存储
	if($section['savetype']==3){
		$qcloud = unserialize($setting['qcloud']);
		$playurl = $this->tencentDownloadUrl($qcloud, $section['videourl']);
		if($qcloud['https']){
			$playurl = str_replace("http://", "https://", $playurl);
		}
	}

	//阿里云点播
	if($section['savetype']==4){
		$aliyun = unserialize($setting['aliyun']);
		$aliyunVod = new AliyunVod($aliyun['region_id'],$aliyun['access_key_id'],$aliyun['access_key_secret']);

		$file = pdo_get($this->table_aliyun_upload, array('uniacid'=>$uniacid,'videoid'=>$section['videourl']), array('name'));
		$suffix = substr(strrchr($file['name'], '.'), 1);
		$audio = strtolower($suffix)=='mp3' ? true : false;

		try {
			$response = $aliyunVod->getVideoPlayAuth($section['videourl']);
			$playAuth = $response->PlayAuth;
		} catch (Exception $e) {
			message("播放失败，错误原因:".$e->getMessage(), "", "error");
		}
	}

	//腾讯云点播
	if($section['savetype']==5){
		$qcloudvod = unserialize($setting['qcloudvod']);
		$newqcloudVod = new QcloudVod($qcloudvod['secret_id'], $qcloudvod['secret_key']);
		try {
			$res = $newqcloudVod->getPlaySign($qcloudvod['safety_key'], $qcloudvod['appid'], $section['videourl'], $exper);
		} catch (Exception $e) {
			message("播放失败，错误原因:".$e->getMessage(), "", "error");
		}
	}

}elseif($op == 'praxis'){
    //习题列表
    $pid = intval($_GPC['pid']);
    $cid = intval($_GPC['cid']);

    $lesson = pdo_fetch("SELECT * FROM " .tablename($this->table_lesson_parent). " WHERE uniacid=:uniacid AND id=:id", array(':uniacid'=>$uniacid,':id'=>$pid));
    if(empty($lesson)){
        message("该课程不存在或已被删除！", "", "error");
    }

    if(!empty($cid)){
        $chapter = pdo_fetch("SELECT * FROM " . tablename($this->table_lesson_son) . " WHERE uniacid=:uniacid AND id=:id", array(':uniacid'=>$uniacid, ':id' => $cid));
        if(empty($chapter)){
            message("该章节不存在或已被删除！","","error");
        }
    }

    if (checksubmit('submit')) { /* 排序 */
        if (is_array($_GPC['sectionorder'])) {
            foreach ($_GPC['sectionorder'] as $sid => $val) {
                $data = array('displayorder' => intval($_GPC['sectionorder'][$sid]));
                pdo_update($this->table_lesson_praxis, $data, array('id' => $sid));
            }
        }

        message('操作成功!', referer, 'success');
    }

    $pindex = max(1, intval($_GPC['page']));
    $psize = 25;

    $condition = " uniacid=:uniacid AND parentid=:parentid AND chapterid=:chapterid";
    $params[':uniacid'] = $uniacid;
    $params[':parentid'] = $pid;
    $params[':chapterid'] = $cid;

    $section_list = pdo_fetchall("SELECT * FROM " .tablename($this->table_lesson_praxis). " WHERE {$condition} ORDER BY displayorder DESC,id ASC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $params);

    $qiniu = unserialize($setting['qiniu']);
    $qcloud = unserialize($setting['qcloud']);
    foreach($section_list as $k=>$v){
        if(in_array($v['sectiontype'], array('1','3'))){
            $tmp = explode('.', $v['videourl']);
            $section_list[$k]['suffix'] = strtolower($tmp[count($tmp)-1]);
            /*七牛云存储*/
            if($v['savetype']==1){
                $section_list[$k]['play_url'] = $this->privateDownloadUrl($qiniu['access_key'], $qiniu['secret_key'], $section_list[$k]['videourl']);
                if($qiniu['https']){
                    $section_list[$k]['play_url'] = str_replace("http://", "https://", $section_list[$k]['play_url']);
                }
            }
            /*腾讯云存储*/
            if($v['savetype']==3){
                $section_list[$k]['play_url'] = $this->tencentDownloadUrl($qcloud, $section_list[$k]['videourl']);
                if($qcloud['https']){
                    $section_list[$k]['play_url'] = str_replace("http://", "https://", $section_list[$k]['play_url']);
                }
            }
        }
    }

    $total = pdo_fetchcolumn("SELECT COUNT(*) FROM " .tablename($this->table_lesson_praxis). " WHERE {$condition}", $params);
    $pager = pagination($total, $pindex, $psize);

}elseif($op == 'postpraxis'){

    $typeStatus = new TypeStatus();
    $saveList = $typeStatus->sectionSaveType();

    $pid = intval($_GPC['pid']);
    $cid = intval($_GPC['cid']);
    $id = intval($_GPC['id']);

    $lesson = pdo_fetch("SELECT id,bookname FROM " .tablename($this->table_lesson_parent). " WHERE uniacid=:uniacid AND id=:id", array(':uniacid'=>$uniacid,':id'=>$pid));
    if(empty($lesson)){
        message("当前课程不存在或已被删除！", "", "error");
    }

    if(!empty($cid)){
        $section = pdo_fetch("SELECT * FROM " .tablename($this->table_lesson_son). " WHERE uniacid=:uniacid AND id=:id", array(':uniacid'=>$uniacid,':id'=>$cid));
        if(empty($section)){
            message("该章节不存在或已被删除！", "", "error");
        }
    }

    if(!empty($id)){
        $praxis = pdo_fetch("SELECT * FROM " . tablename($this->table_lesson_praxis) . " WHERE uniacid=:uniacid AND id=:id", array(':uniacid' => $uniacid,':id' => $id));
        if(empty($praxis)){
            message("该习题不存在或已被删除！", "", "error");
        }
        $sectionUrl = $_W['siteroot'].'app/'.str_replace("./", "", $this->createMobileUrl('lesson', array('id'=>$lesson['id'])))."&amp;sectionid=".$id;
    }

    /* 存储方式 */
    $qiniu = unserialize($setting['qiniu']);
    if(substr($qiniu['url'],0,7)!='http://'){
        $qiniu['url'] = "http://".$qiniu['url']."/";
    }

    $qcloud = unserialize($setting['qcloud']);
    if(substr($qcloud['url'],0,7)!='http://'){
        $qcloud['url'] = "http://".$qcloud['url']."/";
    }

    if(checksubmit('submit')){
        $data = array();
        $data['uniacid']		= $uniacid;
        $data['parentid']		= $pid;
        $data['chapterid']      = $cid;
        $data['subject']		= $_GPC['subject'];
        $data['voidtype']		= trim($_GPC['voidtype']);
        $data['voideurl']		= trim($_GPC['voideurl']);
        $data['audiotype']		= trim($_GPC['audiotype']);
        $data['audiourl']		= trim($_GPC['audiourl']);
        $data['displayorder']	= intval($_GPC['displayorder']);
        $data['answer_a']	    = $_GPC['answer_a'];
        $data['answer_b']	    = $_GPC['answer_b'];
        $data['answer_c']	    = $_GPC['answer_c'];
        $data['answer_d']	    = $_GPC['answer_d'];
        $data['correct']	    = $_GPC['correct'];
        $data['addtime']		= time();

        if(empty($data['parentid'])){
            message("课程不存在或已被删除");
        }
        if(empty($data['subject'])){
            message("请填写习题内容！");
        }


        if($data['voidtype']==2){//内嵌代码存储方式保留内容的空格
            $data['videourl'] = $_GPC['videourl'];
        }

        if($data['audiotype']==2){//内嵌代码存储方式保留内容的空格
            $data['audiourl'] = $_GPC['audiourl'];
        }

        if(empty($id)){
            pdo_insert($this->table_lesson_praxis, $data);
            //var_dump(pdo_debug());die();
            $id = pdo_insertid();
            if($id){
                $this->addSysLog($_W['uid'], $_W['username'], 1, "课程管理->章节管理->习题管理", "新增ID:{$pid}的课程下ID:{$cid}的章节下的ID:{$id}的习题");
            }

            message("添加习题成功！", $this->createWebUrl('lesson',array('op'=>'praxis','pid'=>$pid,'cid'=>$cid)), "success");
        }else{
            unset($data['addtime']);
            $res = pdo_update($this->table_lesson_praxis, $data, array('uniacid'=>$uniacid, 'id'=>$id));
            if($res){
                $this->addSysLog($_W['uid'], $_W['username'], 3, "课程管理->章节管理->习题管理", "编辑ID:{$pid}的课程下ID:{$cid}的章节下的ID:{$id}的习题");
            }

            $refurl = $_GPC['refurl']?$_GPC['refurl']:$this->createWebUrl('lesson',array('op'=>'praxis','pid'=>$pid,'cid'=>$cid));
            message("编辑习题成功！", $refurl, "success");
        }
    }

}


include $this->template('web/lesson');


?>