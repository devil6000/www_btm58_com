<?php
/**
 * 营销管理
 * ============================================================================
 * 版权所有 2015-2018 微课堂团队，并保留所有权利。
 * 网站地址: https://wx.haoshu888.com
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！不允许对程序代码以任何形式任何目的的再发布，作者将保留
 * 追究法律责任的权力和最终解释权。
 */
 
 $market = pdo_fetch("SELECT * FROM " .tablename($this->table_market). " WHERE uniacid=:uniacid", array(':uniacid'=>$uniacid));

 $typeStatus = new TypeStatus();
 $source = $typeStatus->couponSource();

 $pindex = max(1, intval($_GPC['page']));
 $psize = 15;


/* 抵扣设置 */
if($op=='display'){
	if(checksubmit('submit')){
		$data = array(
			'uniacid' => $uniacid,
			'deduct_switch' => $_GPC['deduct_switch'],
			'deduct_money'  => $_GPC['deduct_money'],
		);

		if($market){
			pdo_update($this->table_market, $data, array('uniacid'=>$uniacid));
		}else{
			$data['addtime'] = time();
			pdo_insert($this->table_market, $data);
		}
		
		message("操作成功", $this->createWebUrl('market'), "success");
	}

/* 优惠券列表 */
}elseif($op=='coupon'){
	if (checksubmit('submitOrder')) { /* 排序 */
		if (is_array($_GPC['displayorder'])) {
			foreach ($_GPC['displayorder'] as $k => $v) {
				$data = array('displayorder' => intval($v));
				pdo_update($this->table_mcoupon, $data, array('id' => $k));
			}
		}
		message('操作成功!', referer, 'success');
	}

	

	$list = pdo_fetchall("SELECT * FROM " .tablename($this->table_mcoupon). " WHERE uniacid=:uniacid ORDER BY status DESC,displayorder DESC, id DESC LIMIT ".($pindex - 1) * $psize . ',' . $psize, array(':uniacid'=>$uniacid));
	foreach($list as $k=>$v){
		$category = pdo_fetch("SELECT name FROM " .tablename($this->table_category). " WHERE id=:id", array(':id'=>$v['category_id']));
		$list[$k]['category_name'] = $category['name'] ? "[".$category['name']."]课程分类" : "全部课程分类";
		unset($category);
	}

	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM " .tablename($this->table_mcoupon). " WHERE uniacid=:uniacid", array(':uniacid'=>$uniacid));
	$pager = pagination($total, $pindex, $psize);

/* 添加优惠券 */
}elseif($op=='addCoupon'){
	$coupon_id = intval($_GPC['coupon_id']);
	if($coupon_id>0){
		$coupon = pdo_fetch("SELECT * FROM " .tablename($this->table_mcoupon). " WHERE uniacid=:uniacid AND id=:id", array(':uniacid'=>$uniacid,':id'=>$coupon_id));
		
		if(empty($coupon)){
			message("优惠券不存在", "", "error");
		}

		$validity = json_decode($coupon['validity']);
	}

	$category_list = pdo_fetchall("SELECT * FROM " .tablename($this->table_category). " WHERE uniacid=:uniacid AND parentid=:parentid", array(':uniacid'=>$uniacid,':parentid'=>0));

	if(checksubmit('submit')){
		$data = array(
			'uniacid'			=> $uniacid,
			'name'				=> trim($_GPC['name']),
			'images'			=> trim($_GPC['images']),
			'amount'			=> floatval($_GPC['amount']),
			'conditions'		=> floatval($_GPC['conditions']),
			'category_id'		=> intval($_GPC['category_id']),
			'is_exchange'		=> intval($_GPC['is_exchange']),
			'exchange_integral' => intval($_GPC['exchange_integral']),
			'max_exchange'		=> intval($_GPC['max_exchange']),
			'total_exchange'    => intval($_GPC['total_exchange']),
			'already_exchange'  => intval($_GPC['already_exchange']),
			'validity_type'	    => intval($_GPC['validity_type']),
			'days1'			    => strtotime($_GPC['days1']),
			'days2'				=> intval($_GPC['days2']),
			'status'			=> intval($_GPC['status']),
			'receive_link'		=> intval($_GPC['receive_link']),
			'displayorder'		=> intval($_GPC['displayorder']),
		);

		if(empty($data['name'])){
			message("请输入优惠券名称", "", "error");
		}
		if(empty($data['amount'])){
			message("请输入优惠券面值", "", "error");
		}
		if($data['is_exchange']==1){
			if($data['max_exchange']==0){
				message("请输入最大兑换数量", "", "error");
			}
			if($data['already_exchange']>$data['total_exchange']){
				message("已兑换数量不能大于兑换总数量", "", "error");
			}
			
		}
		if(empty($data['validity_type'])){
			message("请选择有效期方式", "", "error");
		}
		if($data['validity_type']==1 && empty($data['days1'])){
			message("请选择固定有效期日期", "", "error");
		}
		if($data['validity_type']==2 && empty($data['days2'])){
			message("请输入自增有效期天数", "", "error");
		}
		
		if($coupon_id>0){
			$data['update_time'] = time();
			if(pdo_update($this->table_mcoupon, $data, array('id'=>$coupon_id))){
				message("更新成功", $this->createWebUrl('market', array('op'=>'coupon')), "success");
			}else{
				message("更新失败", "", "error");
			}
		}else{
			$data['addtime'] = time();
			if(pdo_insert($this->table_mcoupon, $data)){
				message("新增成功", $this->createWebUrl('market', array('op'=>'coupon')), "success");
			}else{
				message("新增失败", "", "error");
			}
		}
	}

/* 删除优惠券 */
}elseif($op=='delAllCoupon'){
	$ids = $_GPC['ids'];

	$t = 0;
	if(!empty($ids) && is_array($ids)){
		foreach($ids as $id){
			if(pdo_delete($this->table_mcoupon, array('uniacid'=>$uniacid,'id' => $id))){
				$t++;
			}
		}
	}
	message("批量删除{$t}个优惠券活动", $this->createWebUrl('market', array('op'=>'coupon')), "success");

/* 优惠券规则 */
}elseif($op=='couponRule'){
	$coupon_list = pdo_fetchall("SELECT * FROM " .tablename($this->table_mcoupon). " WHERE uniacid=:uniacid AND status=:status", array(':uniacid'=>$uniacid,':status'=>1));

	$regGive = json_decode($market['reg_give'], true);
	$recommend = json_decode($market['recommend'], true);
	$buyLesson = json_decode($market['buy_lesson'], true);
	$shareLesson = json_decode($market['share_lesson'], true);

	if(checksubmit('submit')){
		$data = array(
			'uniacid'			=> $uniacid,
			'reg_give'			=> json_encode($_GPC['reg_give']),
			'recommend'			=> json_encode($_GPC['recommend']),
			'recommend_time'	=> intval($_GPC['recommend_time']),
			'buy_lesson'		=> json_encode($_GPC['buy_lesson']),
			'buy_lesson_time'	=> intval($_GPC['buy_lesson_time']),
			'share_lesson'		=> json_encode($_GPC['share_lesson']),
			'share_lesson_time' => intval($_GPC['share_lesson_time']),
			'coupon_desc'		=> trim($_GPC['coupon_desc']),
		);

		if($market){
			pdo_update($this->table_market, $data, array('uniacid'=>$uniacid));
		}else{
			$data['addtime'] = time();
			pdo_insert($this->table_market, $data);
		}

		message("操作成功", $this->createWebUrl('market', array('op'=>'couponRule')), "success");
	}

/* 发放优惠券 */
}elseif($op=='sendCoupon'){
	ignore_user_abort(true);
	set_time_limit(600);

	/* 优惠券列表 */
	$couponList = pdo_fetchall("SELECT * FROM " .tablename($this->table_mcoupon). " WHERE uniacid=:uniacid", array(':uniacid'=>$uniacid));
	/* VIP等级 */
	$levelList = pdo_fetchall("SELECT * FROM " .tablename($this->table_vip_level). " WHERE uniacid=:uniacid", array(':uniacid'=>$uniacid));

	if(checksubmit('submit')){
		$coupon_id = intval($_GPC['coupon_id']);
		$send_type = intval($_GPC['send_type']);
		$uids = explode(",", trim($_GPC['uids']));
		$level_id = $_GPC['level_id'];
		$startDate = strtotime($_GPC['time']['start'] . " 00:00:00");
		$endDate = strtotime($_GPC['time']['end'] . " 23:59:59");

		if(checksubmit('submit')){
			if(empty($coupon_id)){
				message("请选择优惠券", "", "error");
			}
			$coupon = pdo_fetch("SELECT * FROM " .tablename($this->table_mcoupon). " WHERE id=:id", array(':id'=>$coupon_id));
			if(empty($coupon)){
				message("优惠券不存在", "", "error");
			}

			$list = array();
			if($send_type==1){
				/*全部会员*/
				$list = pdo_fetchall("SELECT a.uid,b.openid,b.nickname FROM " .tablename($this->table_member). " a LEFT JOIN " .tablename($this->table_fans). " b ON a.uid=b.uid WHERE a.uniacid=:uniacid", array(':uniacid'=>$uniacid));

			}elseif($send_type==2){
				if(empty($uids)){
					message("请输入指定会员uid", "", "error");
				}

				/*指定会员*/
				foreach($uids as $key=>$value){
					$item = pdo_fetch("SELECT a.uid,b.openid,b.nickname FROM " .tablename($this->table_member). " a LEFT JOIN " .tablename($this->table_fans). " b ON a.uid=b.uid WHERE a.uid IN (:uniacid)", array(':uniacid'=>$value));
					if(empty($item)) continue;

					$list[$key] = $item;
					unset($item);
				}

			}elseif($send_type==3){
				/*指定VIP等级*/
				if(empty($level_id)){
					message("请选择指定的会员VIP等级", "", "error");
				}
				$list = pdo_fetchall("SELECT a.uid,b.openid,b.nickname FROM " .tablename($this->table_member_vip). " a LEFT JOIN ".tablename($this->table_fans)." b ON a.uid=b.uid WHERE a.level_id=:level_id", array(':level_id'=>$level_id));

			}elseif($send_type==4){
				/*指定注册日期*/
				if(empty($startDate) || empty($endDate)){
					message("请选择加入日期", "", "error");
				}
				$list = pdo_fetchall("SELECT a.uid,b.openid,b.nickname FROM " .tablename($this->table_member). " a LEFT JOIN ".tablename($this->table_fans)." b ON a.uid=b.uid WHERE a.addtime>=:startDate AND addtime<=:endDate", array(':startDate'=>$startDate,':endDate'=>$endDate));
			}

			$validity = $coupon['validity_type']==1 ? $coupon['days1'] : time()+ $coupon['days2']*86400;
			$now = time();

			$sql_head = "INSERT INTO ".tablename($this->table_member_coupon)." (`uniacid`, `uid`,`amount`,`conditions`,`validity`,`category_id`,`status`,`source`,`coupon_id`,`addtime`) VALUES ";
			$sql = "";

			foreach($list as $k=>$v){
				$sql .= "('{$uniacid}','{$v[uid]}','{$coupon[amount]}','{$coupon[conditions]}','{$validity}','{$coupon[category_id]}','0','7','$coupon[id]','{$now}'),";

				if(($k+1)%1000==0 || $k+1==count($list)){
					$sql = substr($sql, 0, strlen($sql)-1);
					pdo_query($sql_head.$sql);
					$sql = "";
				}
			}

			message("发放成功", $this->createWebUrl('market', array('op'=>'sendCoupon')), "success");
		}
	}

/* 优惠券记录 */
}elseif($op=='couponLog'){
	$pindex = max(1, intval($_GPC['page']));
	$psize = 10;

	$condition = "a.uniacid=:uniacid";
	$params[':uniacid'] = $uniacid;

	if (!empty($_GPC['ordersn'])) {
		$condition .= " AND a.ordersn LIKE :ordersn ";
		$params[':ordersn'] = "%".$_GPC['ordersn']."%";
	}
	if (!empty($_GPC['nickname'])) {
		$condition .= " AND ((b.nickname LIKE :nickname) OR (b.realname LIKE :nickname) OR (b.mobile LIKE :nickname)) ";
		$params[':nickname'] = "%".$_GPC['nickname']."%";
	}
	if ($_GPC['status']!='') {
		$condition .= " AND a.status=:status ";
		$params[':status'] = $_GPC['status'];
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

	$list = pdo_fetchall("SELECT a.*,b.nickname,b.mobile,b.realname FROM " .tablename($this->table_member_coupon). " a LEFT JOIN " .tablename($this->table_mc_members). " b ON a.uid=b.uid WHERE {$condition} ORDER BY a.id DESC LIMIT ".($pindex - 1) * $psize . ',' . $psize, $params);
	foreach($list as $k=>$v){
		if($v['category_id']>0){
			$category = pdo_fetch("SELECT name FROM " .tablename($this->table_category). " WHERE id=:id", array(':id'=>$v['category_id']));
		}
		$list[$k]['category_name'] = $category['name'] ? "[".$category['name']."]课程分类" : "全部课程分类";
		if(time()>$v['validity'] && $v['status']==0){
			pdo_update($this->table_member_coupon, array('status'=>-1), array('id'=>$v['id']));
			$list[$k]['status'] = -1;
		}
		unset($category);
	}
	
	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM " .tablename($this->table_member_coupon). " a LEFT JOIN " .tablename($this->table_mc_members). " b ON a.uid=b.uid WHERE {$condition}", $params);
	
	$pager = pagination($total, $pindex, $psize);

/* 优惠券记录详情 */
}elseif($op=='couponDetail'){
	$id = intval($_GPC['id']);
	$member_coupon = pdo_fetch("SELECT a.*,b.nickname,b.mobile,b.realname FROM " .tablename($this->table_member_coupon). " a LEFT JOIN " .tablename($this->table_mc_members). " b ON a.uid=b.uid WHERE a.id=:id", array(':id'=>$id));

	if(empty($member_coupon)){
		message("该优惠券记录不存在", "", "error");
	}

	$category = pdo_fetch("SELECT name FROM " .tablename($this->table_category). " WHERE id=:id", array(':id'=>$member_coupon['category_id']));
	$category_name = $category['name'] ? $category['name']." 课程分类" : "全部课程分类";

/* 限时折扣 */
}elseif($op=='discount'){
	$condition = "uniacid = :uniacid";
	$params[':uniacid'] = $uniacid;
	if($_GPC['keyword']){
		$condition = "title LIKT :title";
		$params[':title'] = '%'.$_GPC['keyword'].'%';
	}

	$list = pdo_fetchall("SELECT * FROM " .tablename($this->table_discount). " WHERE {$condition} ORDER BY displayorder DESC LIMIT ".($pindex - 1) * $psize . ',' . $psize, $params);

	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM " .tablename($this->table_discount). " WHERE {$condition}", $params);
	$pager = pagination($total, $pindex, $psize);

/* 添加限时折扣活动 */
}elseif($op=='addDiscount'){
	$discount_id = intval($_GPC['discount_id']);
	if($discount_id){
		$discount = pdo_get($this->table_discount, array('uniacid'=>$uniacid, 'discount_id'=>$discount_id));
		if(empty($discount)){
			message('该限时折扣活动不存在');
		}
	}

	$starttime = $discount['starttime'] ? date('Y-m-d H:i:s', $discount['starttime']) : date('Y-m-d H:i:s', strtotime('tomorrow'));
	$endtime = $discount['endtime'] ? date('Y-m-d H:i:s', $discount['endtime']) : date('Y-m-d H:i:s', strtotime('+5 days 23:59:59'));

	if(checksubmit('submit')){
		$data = array(
			'uniacid'	  => $uniacid,
			'title'		  => trim($_GPC['title']),
			'member_discount' => intval($_GPC['member_discount']),
			'starttime'	  => strtotime($_GPC['time']['start']),
			'endtime'	  => strtotime($_GPC['time']['end']),
			'update_time' => time(),
		);

		if(empty($data['title'])){
			message("请输入活动名称", "", "error");
		}

		pdo_begin();
		try{
			if($discount_id){
				pdo_update($this->table_discount, $data, array('uniacid'=>$uniacid, 'discount_id'=>$discount_id));

				$lessonData = array(
					'member_discount' => $data['member_discount'],
					'starttime'		  => $data['starttime'],
					'endtime'		  => $data['endtime'],
				);
				pdo_update($this->table_discount_lesson, $lessonData, array('discount_id'=>$discount_id));
			}else{
				$data['addtime'] = time();
				pdo_insert($this->table_discount, $data);
			}
			pdo_commit();
			message("更新成功", $this->createWebUrl('market', array('op'=>'discount')), "success");

		}catch(Exception $e){
			pdo_rollback();
			message("更新失败，错误信息:".print_r($e, true), "", "error");
		}
	}

/* 删除限时折扣活动 */
}elseif($op=='delDiscount'){
	$discount_id = intval($_GPC['discount_id']);
	$discount = pdo_get($this->table_discount, array('uniacid'=>$uniacid, 'discount_id'=>$discount_id));
	if(empty($discount)){
		message('该限时折扣活动不存在');
	}
	
	pdo_begin();
	try{
		pdo_delete($this->table_discount, array('discount_id'=>$discount_id));
		pdo_delete($this->table_discount_lesson, array('discount_id'=>$discount_id));
		pdo_commit();

		message("删除成功", $this->createWebUrl('market', array('op'=>'discount')), "success");
	}catch(Exception $e){
		pdo_rollback();
		message("删除失败，错误信息:".print_r($e, true), "", "error");
	}	

/* 限时折扣活动课程列表 */
}elseif($op=='discountLesson'){
	$discount_id = intval($_GPC['discount_id']);
	$discount = pdo_get($this->table_discount, array('uniacid'=>$uniacid, 'discount_id'=>$discount_id));
	if(empty($discount)){
		message('该限时折扣活动不存在');
	}

	$condition = " b.uniacid=:uniacid AND b.discount_id=:discount_id";
	$params[':uniacid'] = $uniacid;
	$params[':discount_id'] = $discount_id;

	$list = pdo_fetchall("SELECT a.id,a.bookname,a.price,b.discount,b.starttime,b.endtime FROM " .tablename($this->table_lesson_parent). " a LEFT JOIN " .tablename($this->table_discount_lesson). " b ON a.id=b.lesson_id WHERE {$condition} LIMIT ".($pindex - 1) * $psize . ',' . $psize, $params);

	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM " .tablename($this->table_lesson_parent). " a LEFT JOIN " .tablename($this->table_discount_lesson). " b ON a.id=b.lesson_id WHERE {$condition}", $params);
	$pager = pagination($total, $pindex, $psize);

/* 添加课程到折扣活动 */
}elseif($op=='addDiscountLesson'){
	$discount_id = intval($_GPC['discount_id']);
	$discount = pdo_get($this->table_discount, array('uniacid'=>$uniacid, 'discount_id'=>$discount_id));
	if(empty($discount)){
		message('该限时折扣活动不存在');
	}

	$lessons = pdo_getall($this->table_discount_lesson, array(), array('lesson_id'));
	$lesson_ids = array();
	if(!empty($lessons)){
		foreach($lessons as $k=>$v){
			$lesson_ids[$k] = $v['lesson_id'];
		}
	}

	$condition = "uniacid=:uniacid AND status=:status";
	$params[':uniacid'] = $uniacid;
	$params[':status'] = 1;

	if(!empty($_GPC['bookname'])){
		$condition .= " AND bookname LIKE :bookname";
		$params[':bookname'] = '%'.trim($_GPC['bookname']).'%';
	}

	$list = pdo_fetchall("SELECT id,bookname,price,addtime FROM " .tablename($this->table_lesson_parent). " WHERE {$condition} LIMIT ".($pindex - 1) * $psize . ',' . $psize, $params);

	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM " .tablename($this->table_lesson_parent). " WHERE {$condition}",$params);
	$pager = pagination($total, $pindex, $psize);

/* 在限时活动里添加或移除课程 */
}elseif($op=='discountLessonPost'){
	$idarr = $_GPC['id'];
	$discount_id = intval($_GPC['discount_id']);
	$posttype = trim($_GPC['posttype']);
	$lesson_discount = $_GPC['discount'];

	$discount = pdo_get($this->table_discount, array('uniacid'=>$uniacid, 'discount_id'=>$discount_id));
	if(empty($discount)){
		message('该限时折扣活动不存在');
	}
	if($posttype != 'cancel' && !is_numeric($lesson_discount)){
		message("课程折扣必须为整数", "", "error");
	}
	if($posttype != 'cancel' && $lesson_discount <=0){
		message("课程折扣不能小于1%", "", "error");
	}
	if($posttype != 'cancel' && $lesson_discount >=100){
		message("课程折扣不能大于99%", "", "error");
	}

	$data = array(
		'uniacid'	  => $uniacid,
		'discount_id' => $discount_id,
		'discount'	  => $lesson_discount,
		'member_discount' => $discount['member_discount'],
		'starttime'	  => $discount['starttime'],
		'endtime'	  => $discount['endtime'],
		'addtime'	  => time()
	);
	if(is_array($idarr) && !empty($idarr)){
		foreach($idarr as $value){
			if($posttype=='cancel'){
				pdo_delete($this->table_discount_lesson, array('uniacid'=>$uniacid, 'discount_id'=>$discount_id, 'lesson_id'=>$value));
			}else{
				$data['lesson_id'] = $value;
				pdo_insert($this->table_discount_lesson, $data);
			}
		}

		if($posttype=='cancel'){
			$succword = "批量取消成功！";
		}else{
			$succword = "批量添加成功！";
		}

		message($succword, $this->createWebUrl('market', array('op'=>'discountLesson','discount_id'=>$discount_id)), "success");

	}else{
		message("参数错误，系统已自动修复，请重试！", referer, "error");
	}
}

include $this->template('web/market');

?>