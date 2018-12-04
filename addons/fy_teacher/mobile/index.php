<?php
/*
 * 课程管理
 * ============================================================================
 * 版权所有 2015-2018 微课堂团队，并保留所有权利。
 * 网站地址: http://wx.haoshu888.com
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！不允许对程序代码以任何形式任何目的的再发布，作者将保留
 * 追究法律责任的权力和最终解释权。
 * ============================================================================
 */

$setting = pdo_fetch("SELECT vipdiscount,qiniu FROM " .tablename($this->table_setting). " WHERE uniacid=:uniacid LIMIT 1", array(':uniacid'=>$uniacid));

$catList = pdo_fetchall("SELECT id,name FROM " .tablename($this->table_category). " WHERE uniacid=:uniacid AND parentid=:parentid", array(':uniacid'=>$uniacid,':parentid'=>0));
foreach($catList as $k=>$v){
	$catList[$k]['child'] = pdo_fetchall("SELECT id,parentid,name FROM " . tablename($this->table_category) . " WHERE uniacid=:uniacid AND parentid=:parentid", array(':uniacid'=>$uniacid,':parentid'=>$v['id']));
}

$config = $this->module['config'];

$pindex = max(1, intval($_GPC['page']));
$psize = 10;
if($op=='display'){
	$linkNav = array(
		'0'	=> array(
			'title'	=> "课程管理",
			'link'	=> $this->createMobileUrl('index')
		)
	);

	$bookname = trim($_GPC['bookname']);
	$cat_id   = intval($_GPC['cat_id']);
	$status   = $_GPC['status'];

	$condition = " uniacid=:uniacid AND teacherid=:teacherid ";
	$params[':uniacid'] = $uniacid;
	$params[':teacherid'] = $_SESSION[$uniacid.'_teacher_id'];
	if($bookname != ''){
		$condition .= " AND bookname LIKE :bookname ";
		$params[':bookname'] = "%".$bookname."%";
	}
	if($cat_id>0){
		$condition .= " AND pid = :pid ";
		$params[':pid'] = $cat_id;
	}
	if(in_array($status, array('0','1','2','-1'))){
		$condition .= " AND status = :status ";
		$params[':status'] = $status;
	}

	$list = pdo_fetchall("SELECT * FROM " .tablename($this->table_lesson_parent). " WHERE {$condition} ORDER BY status DESC,id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $params);
	foreach($list as $k=>$v){
		$list[$k]['category_parent'] = pdo_fetch("SELECT name FROM " .tablename($this->table_category). " WHERE id=:id", array(':id'=>$v['pid']));
		$list[$k]['category_child'] = pdo_fetch("SELECT name FROM " .tablename($this->table_category). " WHERE id=:id", array(':id'=>$v['cid']));
	}

	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM " .tablename($this->table_lesson_parent). " WHERE {$condition}", $params);
	$pager = $this->pagination($total, $pindex, $psize);

}elseif($op=='edit'){
	load()->func('tpl');

	$id = intval($_GPC['id']);

	$linkNav = array(
		'0'	=> array(
			'title'	=> "课程管理",
			'link'	=> $this->createMobileUrl('index')
		),
		'1'	=> array(
			'title'	=> $id>0 ? "编辑课程" : "添加课程",
			'link'	=> $this->createMobileUrl('index', array('op'=>'edit', 'id'=>$id))
		)
	);

	/* 推荐板块列表 */
	$rec_list = pdo_fetchall("SELECT id,rec_name FROM " .tablename($this->table_recommend). " WHERE uniacid='{$uniacid}'");
	if($id>0){
		$lesson = pdo_fetch("SELECT * FROM " .tablename($this->table_lesson_parent). " WHERE uniacid=:uniacid AND teacherid=:teacherid AND id=:id LIMIT 1", array(':uniacid'=>$uniacid,':teacherid'=>$_SESSION[$uniacid.'_teacher_id'],':id'=>$id));
		if(empty($lesson)){
			message("该课程不存在或您无权查看");
		}
	}

	/* VIP等级列表 */
	$vipview = json_decode($lesson['vipview']); /* 当前课程支持免费学习的VIP等级 */
	$level_list = pdo_fetchall("SELECT * FROM " .tablename($this->table_vip_level). " WHERE uniacid=:uniacid", array(':uniacid'=>$uniacid));

	/* 课程佣金比例 */
	$commission = unserialize($lesson['commission']);

	/* 课程规格 */
	$spec_list = pdo_fetchall("SELECT * FROM " .tablename($this->table_lesson_spec). " WHERE uniacid=:uniacid AND lessonid=:lessonid ORDER BY spec_day ASC", array(':uniacid'=>$uniacid, ':lessonid'=>$id));
	
	/* 讲师信息 */
	$teacher = pdo_get($this->table_teacher, array('id' => $_SESSION[$uniacid.'_teacher_id']));
	$lesson_income = $lesson['teacher_income'] ? $lesson['teacher_income'] : $teacher['teacher_income'];
	
	if(checksubmit('submit')){
		$data = array();
		$data['uniacid']		= $uniacid;
		$data['bookname']		= trim($_GPC['bookname']);
		$data['pid']			= intval($_GPC['pid']);
		$data['cid']			= intval($_GPC['cid']);
		$data['images']			= trim($_GPC['images']);
		$data['price']			= trim($_GPC['price'])?trim($_GPC['price']):0;
		$data['validity']		= intval($_GPC['validity']);
		$data['stock']			= intval($_GPC['stock']);
		$data['isdiscount']		= intval($_GPC['isdiscount']);
		$data['vipdiscount']	= intval($_GPC['vipdiscount']);
		$data['virtual_buynum'] = intval($_GPC['virtual_buynum']);
		$data['difficulty']		= trim($_GPC['difficulty']);
		$data['teacherid']		= $_SESSION[$uniacid."_teacher_id"];
		$data['descript']		= trim($_GPC['descript']);
		$data['vipview']		= json_encode($_GPC['vipview']);
		$data['teacher_income']	= $lesson['teacher_income'] ? $lesson['teacher_income'] : $teacher['teacher_income'];
		$data['support_coupon']	= intval($_GPC['support_coupon']);
		$data['section_status']	= intval($_GPC['section_status']);
		$data['addtime']		= time();
		$data['commission']	    = serialize(array('commission1'=>floatval($_GPC['commission1']),'commission2'=>floatval($_GPC['commission2']),'commission3'=>floatval($_GPC['commission3'])));

		if(empty($data['bookname'])){
			message("请输入课程名称！");
		}
		if(empty($data['pid'])){
			message("请选择课程分类！");
		}
		if(empty($data['images'])){
			message("请上传课程封面！");
		}
		if(empty($data['difficulty'])){
			message("请填写课程难度！");
		}
	
		if(empty($id)){
			$data['status'] = 2; //审核中
			pdo_insert($this->table_lesson_parent, $data);
			$id = pdo_insertid();
		}else{
			unset($data['addtime']);
			if($lesson['status'] !=2 ){
				$data['status'] = intval($_GPC['status']);
			}
			pdo_update($this->table_lesson_parent, $data, array('uniacid'=>$uniacid, 'id'=>$id));
		}

		if($id>0){
			/* 处理课程规格 */
			pdo_delete($this->table_lesson_spec, array('lessonid'=>$id));
			foreach ($_GPC['spec_time'] as $key => $row) {
				$row = floatval($row);
				$price = floatval($_GPC['spec_price'][$key]);
				if (!$row || !$price)
					continue;
				$spec_data = array(
					'uniacid' => $uniacid,
					'lessonid' => $id,
					'spec_day' => $row,
					'spec_price' => $price,
					'addtime' => time(),
				);
				pdo_insert($this->table_lesson_spec, $spec_data);
				$price_array[] = $price;
			}
			$min_price = array_search(min($price_array), $price_array);
			pdo_update($this->table_lesson_parent, array('price'=>$price_array[$min_price]), array('uniacid'=>$uniacid,'id'=>$id));
			
		}
		
		$refurl = $_GPC['refurl']?$_GPC['refurl']:$this->createMobileUrl("index");
		message("编辑课程成功！", $refurl, "success");
	}

}elseif($op=='viewSection'){
	$pid = intval($_GPC['pid']);
	$linkNav = array(
		'0'	=> array(
			'title'	=> "课程管理",
			'link'	=> $this->createMobileUrl('index')
		),
		'1'	=> array(
			'title'	=> "章节管理",
			'link'	=> $this->createMobileUrl('index', array('op'=>'viewSection', 'pid'=>$pid))
		)
	);

	$lesson = pdo_fetch("SELECT * FROM " .tablename($this->table_lesson_parent). " WHERE uniacid=:uniacid AND id=:id AND teacherid=:teacherid", array(':uniacid'=>$uniacid, ':id'=>$pid, ':teacherid'=>$_SESSION[$uniacid."_teacher_id"]));
	if(empty($lesson)){
		message("课程不存在或您无权查看！", "", "error");
	}

	$params[':uniacid']	= $uniacid;
	$params[':pid']	= $pid;
	$section_list = pdo_fetchall("SELECT * FROM " .tablename($this->table_lesson_son). " WHERE uniacid=:uniacid AND parentid=:pid ORDER BY displayorder DESC,id ASC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $params);
	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM " .tablename($this->table_lesson_son). " WHERE uniacid=:uniacid AND parentid=:pid", $params);
	$pager = $this->pagination($total, $pindex, $psize);

}elseif($op=='editSection'){
	$typeStatus = new TypeStatus();
	$saveList = $typeStatus->sectionSaveType();

	$pid = intval($_GPC['pid']); /* 课程id */
	$id = intval($_GPC['id']); /* 章节id */
	$linkNav = array(
		'0'	=> array(
			'title'	=> "课程管理",
			'link'	=> $this->createMobileUrl('index')
		),
		'1'	=> array(
			'title'	=> "章节管理",
			'link'	=> $this->createMobileUrl('index', array('op'=>'viewSection', 'pid'=>$pid))
		),
		'2'	=> array(
			'title'	=> $id>0 ? "编辑章节" : "添加章节",
			'link'	=> $this->createMobileUrl('index', array('op'=>'editSection', 'pid'=>$pid, 'id'=>$id))
		)
	);

	$lesson = pdo_fetch("SELECT id,bookname FROM " .tablename($this->table_lesson_parent). " WHERE uniacid=:uniacid AND id=:pid AND teacherid=:teacherid", array(':uniacid'=>$uniacid, ':pid'=>$pid, ':teacherid'=>$_SESSION[$uniacid."_teacher_id"]));
	if(empty($lesson)){
		message("课程不存在或您无权查看！", "", "error");
	}

	if(!empty($id)){
		$section = pdo_fetch("SELECT * FROM " .tablename($this->table_lesson_son). " WHERE uniacid=:uniacid AND id=:id", array(':uniacid'=>$uniacid, ':id'=>$id));
		if(empty($section)){
			message("该章节不存在或您无权查看！", "", "error");
		}
	}

	/* 存储方式 */
	$qiniu = unserialize($setting['qiniu']);
	if(substr($qiniu['url'],0,7)!='http://'){
		$qiniu['url'] = "http://".$qiniu['url']."/";
	}

	if(checksubmit('submit')){
		$data = array();
		$data['uniacid']		= $uniacid;
		$data['parentid']		= $pid;
		$data['title']			= $_GPC['title'];
		$data['sectiontype']	= intval($_GPC['sectiontype']);
		$data['savetype']		= trim($_GPC['savetype']);
		$data['videourl']		= trim($_GPC['videourl']);
		$data['videotime']		= trim($_GPC['videotime']);
		$data['content']		= $_GPC['content'];
		$data['is_free']	    = intval($_GPC['is_free']);
		$data['test_time']	    = intval($_GPC['test_time']);
		$data['status']			= intval($_GPC['status']);
		$data['addtime']		= time();

		if(empty($data['title'])){
			message("请填写章节名称！");
		}
		if(in_array($data['sectiontype'], array('1','3')) && empty($data['videourl'])){
			message("请填写章节视频URL！");
		}
		if($data['sectiontype']==3 && in_array($data['savetype'], array('2','4','5'))){
			message("音频章节存储方式只能其他存储、七牛云存储和腾讯云存储");
		}
		if(!in_array($data['is_free'], array('0','1'))){
			message("请选择是否为试听章节！");
		}
		if(!in_array($data['status'], array('0','1'))){
			message("请选择是否上架！");
		}

		if($data['savetype']==2){
			$data['videourl'] = $_GPC['videourl'];
		}

		if(empty($id)){
			pdo_insert($this->table_lesson_son, $data);
			$id = pdo_insertid();

			message("添加章节成功！", $this->createMobileUrl('index',array('op'=>'viewSection','pid'=>$pid)), "success");
		}else{
			unset($data['addtime']);
			$res = pdo_update($this->table_lesson_son, $data, array('uniacid'=>$uniacid, 'id'=>$id));

			$refurl = $_GPC['refurl']?$_GPC['refurl']:$this->createMobileUrl('index',array('op'=>'viewSection','pid'=>$pid));
			message("编辑章节成功！", $refurl, "success");
		}
	}

}elseif($op=='delete'){
	$id = intval($_GPC['id']);
	$section = pdo_fetch("SELECT parentid FROM " .tablename($this->table_lesson_son). " WHERE uniacid=:uniacid AND id=:id ", array(':uniacid'=>$uniacid, ':id'=>$id));
	if(empty($section)){
		message("该章节不存在或您无权查看");
	}

	$lesson =  pdo_fetch("SELECT id FROM " .tablename($this->table_lesson_parent). " WHERE uniacid=:uniacid AND id=:pid AND teacherid=:teacherid", array(':uniacid'=>$uniacid, ':pid'=>$section['parentid'], ':teacherid'=>$_SESSION[$uniacid."_teacher_id"]));
	if(empty($lesson)){
		message("该课程不存在或您无权查看");
	}

	pdo_delete($this->table_lesson_son, array('uniacid'=>$uniacid, 'id'=>$id));
	message("删除成功", $this->createMobileUrl('index',array('op'=>'viewSection','pid'=>$section['parentid'])), "success");

}elseif($op=='delAll'){
	$ids = $_GPC['ids'];
	if(empty($ids) || !is_array($ids)){
		message("未选中任何章节");
	}
	$section = pdo_fetch("SELECT parentid FROM " .tablename($this->table_lesson_son). " WHERE uniacid=:uniacid AND id=:id ", array(':uniacid'=>$uniacid, ':id'=>$ids[0]));
	$lesson =  pdo_fetch("SELECT id FROM " .tablename($this->table_lesson_parent). " WHERE uniacid=:uniacid AND id=:pid AND teacherid=:teacherid", array(':uniacid'=>$uniacid, ':pid'=>$section['parentid'], ':teacherid'=>$_SESSION[$uniacid."_teacher_id"]));
	if(empty($lesson)){
		message("非法操作");
	}

	$secIds = "";
	foreach($ids as $id){
		$sec = pdo_fetch("SELECT parentid FROM " .tablename($this->table_lesson_son). " WHERE uniacid=:uniacid AND id=:id ", array(':uniacid'=>$uniacid, ':id'=>$id));
		if($sec['parentid'] != $lesson['id']){
			message("非法操作");
		}
		pdo_delete($this->table_lesson_son, array('id'=>$id));
	}

	message("批量删除成功", $refurl, "success");
}

include $this->template('index');