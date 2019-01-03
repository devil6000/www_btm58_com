<?php 
/**
 * 微课堂小程序入口文件
 * ============================================================================
 * 版权所有 2015-2018 微课堂团队，并保留所有权利。
 * 网站地址: https://wx.haoshu888.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！已购买用户允许对程序代码进行修改和使用，但是不允许对
 * 程序代码以任何形式任何目的的再发布，作者将保留追究法律责任的权力和最终解
 * 释权。
 * ============================================================================
 */
defined('IN_IA') or exit('Access Denied');
class Fy_lessonv2ModuleWxapp extends WeModuleWxapp {
	public $table_article = 'fy_lesson_article';
	public $table_banner = 'fy_lesson_banner';
    public $table_blacklist = 'fy_lesson_blacklist';
    public $table_cashlog = 'fy_lesson_cashlog';
    public $table_category = 'fy_lesson_category';
	public $table_lesson_collect = 'fy_lesson_collect';
	public $table_commission_level = 'fy_lesson_commission_level';
	public $table_commission_log = 'fy_lesson_commission_log';
	public $table_commission_setting = 'fy_lesson_commission_setting';
	public $table_coupon = 'fy_lesson_coupon';
    public $table_evaluate = 'fy_lesson_evaluate';
    public $table_lesson_history = 'fy_lesson_history';
	public $table_inform = 'fy_lesson_inform';
	public $table_inform_fans = 'fy_lesson_inform_fans';
	public $table_market = 'fy_lesson_market';
	public $table_mcoupon = 'fy_lesson_mcoupon';
    public $table_member = 'fy_lesson_member';
	public $table_member_coupon = 'fy_lesson_member_coupon';
    public $table_member_order = 'fy_lesson_member_order';
	public $table_member_recommend = 'fy_lesson_member_recommend';
	public $table_member_vip = 'fy_lesson_member_vip';
    public $table_order = 'fy_lesson_order';
    public $table_lesson_parent = 'fy_lesson_parent';
    public $table_playrecord = 'fy_lesson_playrecord';
	public $table_qcloud_upload = 'fy_lesson_qcloud_upload';
	public $table_qiniu_upload = 'fy_lesson_qiniu_upload';
    public $table_recommend = 'fy_lesson_recommend';
    public $table_setting = 'fy_lesson_setting';
    public $table_lesson_son = 'fy_lesson_son';
	public $table_lesson_spec = 'fy_lesson_spec';
	public $table_static = 'fy_lesson_static';
    public $table_syslog = 'fy_lesson_syslog';
    public $table_teacher = 'fy_lesson_teacher';
    public $table_teacher_income = 'fy_lesson_teacher_income';
	public $table_tplmessage = 'fy_lesson_tplmessage';
    public $table_vip_level = 'fy_lesson_vip_level';
    public $table_vipcard = 'fy_lesson_vipcard';
	public $table_mc_members = 'mc_members';
	public $table_fans = 'mc_mapping_fans';
    public $table_core_cache = 'core_cache';
	public $table_core_paylog = 'core_paylog';
    public $table_users = 'users';
    public $table_lesson_praxis = 'fy_lesson_praxis';
    public $table_praxis_score = 'fy_lesson_praxis_score';
    public $table_discuss = 'fy_discuss';
    public $table_discuss_content = 'fy_discuss_content';
    public $table_recharge_order = 'fy_recharge_order';
    public $table_lesson_meanwhile = 'fy_lesson_meanwhile';


/***************************** 私有方法 ******************************** */

	/* 读取设置缓存
	 * $type 读取缓存类型 1.全局设置表 2.分销设置表
	 */
	private function readCache($type){
		global $_W;

		if($type==1){
			$setting = cache_load('fy_lessonv2_setting_'.$_W['uniacid']);
			if(empty($setting)){
				$setting = $this->getSetting();
				cache_write('fy_lessonv2_setting_'.$_W['uniacid'], $setting);
			}
			return $setting;

		}elseif($type==2){
			$comsetting = cache_load('fy_lessonv2_commission_setting_'.$_W['uniacid']);
			if(empty($comsetting)){
				$comsetting = $this->getComsetting();
				cache_write('fy_lessonv2_commission_setting_'.$_W['uniacid'], $comsetting);
			}
			return $comsetting;
		}
	}

	/* 获取分销设置参数 */
	private function getComsetting(){
		global $_W;
		return pdo_get($this->table_commission_setting, array('uniacid'=>$_W['uniacid']));
	}

	/* 根据小程序uid获取公众号uid */
	private function getAppUid($wxapp_uid, $file = ''){
		global $_W;

		$wxapp_fans = pdo_get($this->table_fans, array('uid'=>$wxapp_uid), array('unionid'));
		if($file == 'unionid'){
			return $wxapp_fans['unionid'] ? $wxapp_fans['unionid'] : '';
		}

		if(!empty($wxapp_fans['unionid'])){
			$fans = pdo_get($this->table_fans, array('uniacid'=>$_W['uniacid'], 'unionid'=>$wxapp_fans['unionid']), array('uid'));
		}

		return $fans['uid'] ? $fans['uid'] : '';
	}


/***************************** 接口数据(实际使用) ******************************** */
	
	/* 保存小程序分销上下级 */
	public function doPageSaveRecommend(){
		global $_W, $_GPC;

		$parentid = $_GPC['parentid'];
		$wxapp_uid = $_GPC['wxapp_uid'];
		$unionid = $this->getAppUid($wxapp_uid, 'unionid');

		if(!empty($unionid)){
			pdo_delete($this->table_member_recommend, array('uniacid'=>$_W['uniacid'], 'unionid'=>$unionid));
		}
		if(!empty($wxapp_uid)){
			pdo_delete($this->table_member_recommend, array('uniacid'=>$_W['uniacid'], 'uid'=>$wxapp_uid));
		}

		if((!empty($unionid) || $wxapp_uid) && $parentid){
			$data = array(
				'uniacid'  => $_W['uniacid'],
				'uid'      => $wxapp_uid,
				'unionid'  => $unionid,
				'parentid' => $parentid,
				'addtime'  => time(),
			);
			pdo_insert($this->table_member_recommend, $data);
			$insertid = pdo_insertid();
		}

		return $this->result(0, '', array('recommend_id'=>$insertid));
	}

	
	/* 分销设置页面 */
	public function doPageShareInfo(){
		global $_W, $_GPC;

		$wxapp_uid = intval($_GPC['wxapp_uid']);

		$setting = $this->readCache(1);
		$comsetting = $this->readCache(2);
		$shareinfo = unserialize($comsetting['sharelink']);
		$shareinfo['images'] = $shareinfo['images'] ? $_W['attachurl'].$shareinfo['images'] : $shareinfo['images'];
		$app_uid = $this->getAppUid($wxapp_uid);

		$data = array(
			'attachurl'	 => $_W['attachurl'],
			'avatar'	 => $_W['account']['avatar'],
			'setting'	 => $setting,
			'app_uid'	 => $app_uid,
			'shareinfo'  => $shareinfo,
			'sharewxapp' => $comsetting['hidden_sale'] ? true : false,
		);

		return $this->result(0, '', $data);
	}
		
	/* 首页 */
	public function doPageIndex() {
		global $_W,$_GPC;
		$comsetting = $this->readCache(2);

		$state = $_GPC['state'];

		if($comsetting['hidden_sale']){
			$url = "";
		}else{
			$url = $_W['siteroot'].'app/'.str_replace("./", "", $this->createMobileUrl('index', array('state' => $state)));
		}
		
		return $this->result(0, '', $url);
	}

	/* 我的课程 */
	public function doPageMylesson() {
		global $_W;
		$comsetting = $this->readCache(2);

		if($comsetting['hidden_sale']){
			$url = "";
		}else{
			$url = $_W['siteroot'].'app/'.str_replace("./", "", $this->createMobileUrl('mylesson')).'&status=';
		}
		
		return $this->result(0, '', $url);
	}

	/* vip页面 */
	public function doPageVip() {
		global $_W;
		$comsetting = $this->readCache(2);

		if($comsetting['hidden_sale']){
			$url = "";
		}else{
			$url = $_W['siteroot'].'app/'.str_replace("./", "", $this->createMobileUrl('vip'));
		}

		return $this->result(0, '', $url);
	}

	/* 支付参数 */
	public function doPagePay() {
        global $_GPC, $_W;

        $title = $_GPC['title'];
		$orderid = intval($_GPC['orderid']);

		/* 购买VIP订单 */
        $viporder = pdo_fetch("SELECT * FROM " .tablename($this->table_member_order). " WHERE id=:id", array(':id'=>$orderid));
        /* 购买课程订单 */
        $lessonorder = pdo_fetch("SELECT * FROM " .tablename($this->table_order). " WHERE id=:id", array(':id'=>$orderid));
        /* 充值订单 */
        $rechargeorder = pdo_fetch('SELECT * FROM ' . tablename($this->table_recharge_order) . ' WHERE id=:id', array(':id' => $orderid));

		$fee = !empty($viporder) ? $viporder['vipmoney'] : $lessonorder['price'];

        if(empty($fee)){
            $fee = $rechargeorder['money'];
        }

		if(is_numeric($_W['openid'])){
			load()->model('mc');
			$openid = mc_uid2openid($_W['openid']);
		}

		$paylog = pdo_get($this->table_core_paylog, array('tid' => $orderid, 'status'=>0));
		if(!empty($paylog)){
			pdo_delete($this->table_core_paylog, array('tid' => $orderid));
		}

        $order = array(
            'tid'	=> $orderid,
            'user'	=> $openid ? $openid : $_W['openid'],
            'fee'	=> floatval($fee),
            'title' => $title,
        );

        $pay_params = $this->pay($order);
        if (is_error($pay_params)) {
            return $this->result(1, $pay_params);
        }

        if(!empty($rechargeorder)){
            $pay_params['order_type'] = 1; //充值
        }else{
            $pay_params['order_type'] = !empty($viporder) ? '1' : '2'; /* 订单类型 1.vip订单 2.课程订单 */
        }
        return $this->result(0, '', $pay_params);
    }


	/* 支付返回确认 */
    public function payResult($params) {
        global $_W, $_GPC;
        $orderid = $params['tid'];

        /* 购买VIP订单 */
        $viporder = pdo_fetch("SELECT * FROM " .tablename($this->table_member_order). " WHERE id=:id", array(':id'=>$orderid));
        /* 购买课程订单 */
        $lessonorder = pdo_fetch("SELECT * FROM " .tablename($this->table_order). " WHERE id=:id", array(':id'=>$orderid));
        /* 充值订单 */
        $rechargeorder = pdo_fetch('SELECT * FROM ' . tablename($this->table_recharge_order) . ' WHERE id=:id', array(':id' => $orderid));

        $uniacid = $viporder['uniacid'] ? $viporder['uniacid'] : $lessonorder['uniacid'];
		$uid = $viporder['uid'] ? $viporder['uid'] : $lessonorder['uid'];
		
		$lessonmember = pdo_fetch("SELECT a.*,b.openid FROM " . tablename($this->table_member) . " a LEFT JOIN ".tablename($this->table_fans)." b ON a.uid=b.uid WHERE a.uniacid=:uniacid AND a.uid=:uid", array(':uniacid'=>$uniacid,':uid'=>$uid));

        $setting = $this->readCache(1);   /* 基本设置 */
		$comsetting = $this->readCache(2);/* 分销设置 */

        if (!empty($viporder)) {
            if ((($params['result'] == 'success' && $params['from'] == 'notify') || $params['type'] == 'credit') && $viporder['status'] == 0) {
                /* 支付成功逻辑操作 */
                $data = array('status' => $params['result'] == 'success' ? 1 : 0);
				if(!empty($params['type'])){
					$data['paytype'] = $params['type'];
				}else{
					$data['paytype'] = $params['trade_type'] ? 'wxapp' : '';
				}
                
                $data['paytime'] = time();
                if (pdo_update($this->table_member_order, $data, array('id' => $orderid))) {
                    /* 更新用户VIP有效期 */
					$validity = $this->updateVipValidity($uniacid, $lessonmember, $viporder);
					
					/* 订单金额加入今日销售额汇总表 */
					$this->staticAmount($uniacid, 1, $viporder['vipmoney']);
					
					/* 判断分销员状态变化 */
					$this->checkAgentStatus($lessonmember, $comsetting, $viporder['vipmoney']);
					
                    /* 一级佣金 */
                    if ($viporder['member1'] > 0 && $viporder['commission1'] > 0) {
                    	$this->sendCommissionToUser($uniacid, $viporder['member1'], $lessonmember, 1, $setting, $viporder, $viporder['commission1'], 1);
                    }
					
					/* 二级佣金 */
                    if ($viporder['member2'] > 0 && $viporder['commission2'] > 0) {
						$this->sendCommissionToUser($uniacid, $viporder['member2'], $lessonmember, 1, $setting, $viporder, $viporder['commission2'], 2);
                    }

					/* 三级佣金 */
                    if ($viporder['member3'] > 0 && $viporder['commission3'] > 0) {
                    	$this->sendCommissionToUser($uniacid, $viporder['member3'], $lessonmember, 1, $setting, $viporder, $viporder['commission3'], 3);
                    }
					
					/* 购买成功模版消息通知用户 */
					$this->sendMessageToUser($uniacid, $setting, $viporder, 1, $validity);
					/* 新VIP订单提醒(管理员) */
					$this->sendOrderMessageToAdmin($setting, $viporder, 1);
					/* 更新会员vip字段 */
					$this->updateMemberVip($uid, 1);

					/* 赠送积分操作 */
                    if ($viporder['integral'] > 0) {
                    	$this->handleUserIntegral($type=1, $viporder['ordersn'], $viporder['uid'], $viporder['integral']);
                    }
                }
            }

            if ($params['from'] == 'return') {
                message("购买成功", $this->createMobileUrl('vip', array('ispay'=>1)), 'success');
            }
        } elseif (!empty($lessonorder)) {
            if ((($params['result'] == 'success' && $params['from'] == 'notify') || $params['type'] == 'credit' || $params['fee'] == 0) && $lessonorder['status'] == 0) {
                /* 支付成功逻辑操作 */
                $data = array('status' => $params['result'] == 'success' ? 1 : 0);
                if(!empty($params['type'])){
					$data['paytype'] = $params['type'];
				}else{
					$data['paytype'] = $params['trade_type'] ? 'wxapp' : '';
				}

                $data['paytime'] = time();
				if($lessonorder['validity']>0){
					$data['validity'] = time()+86400*$lessonorder['validity'];
				}
                if (pdo_update($this->table_order, $data, array('id' => $orderid))) {
                    /* 增加课程购买人数和减少库存 */
                    $this->updateLessonNumber($lessonorder, $setting);
					
					/* 订单金额加入今日销售额汇总表 */
					$this->staticAmount($uniacid, 2, $lessonorder['price']);
					
					/* 判断分销员状态变化 */
					$this->checkAgentStatus($lessonmember, $comsetting, $lessonorder['price']);
					
                    /* 一级佣金 */
                    if ($lessonorder['member1'] > 0 && $lessonorder['commission1'] > 0) {
                    	$this->sendCommissionToUser($uniacid, $lessonorder['member1'], $lessonmember, 2, $setting, $lessonorder, $lessonorder['commission1'], 1);
                    }
					
					/* 二级佣金 */
                    if ($lessonorder['member2'] > 0 && $lessonorder['commission2'] > 0) {
                        $this->sendCommissionToUser($uniacid, $lessonorder['member2'], $lessonmember, 2, $setting, $lessonorder, $lessonorder['commission2'], 2);
                    }
					
					/* 三级佣金 */
                    if ($lessonorder['member3'] > 0 && $lessonorder['commission3'] > 0) {
                        $this->sendCommissionToUser($uniacid, $lessonorder['member3'], $lessonmember, 2, $setting, $lessonorder, $lessonorder['commission3'], 3);
                    }
					
                    /* 讲师分成 */
                    if ($lessonorder['price'] > 0 && $lessonorder['teacher_income'] > 0) {
                        $this->sendCommissionToTeacher($uniacid, $lessonorder, $setting);
                    }

					/* 机构分成 */
                    if ($lessonorder['price'] > 0 && $lessonorder['company_uid'] > 0 && $lessonorder['company_income'] > 0) {
                        $this->sendCommissionToCompany($uniacid, $lessonorder, $setting);
                    }
					
                    
					/* 购买成功模版消息通知用户 */
					$this->sendMessageToUser($uniacid, $setting, $lessonorder, 2, $validity="");
					/* 新课程订单提醒(管理员) */
					$this->sendOrderMessageToAdmin($setting, $lessonorder, 2);
					
                    /* 赠送积分操作 */
                    if ($lessonorder['integral'] > 0) {
                    	$this->handleUserIntegral($type=2, $lessonorder['ordersn'], $lessonorder['uid'], $lessonorder['integral']);
                    }

					/* 给用户发放优惠券 */
					$this->sendCouponByBuyLesson($lessonmember, $setting);
                }
            }

            if ($params['from'] == 'return') {
                message("购买课程成功！", $this->createMobileUrl('lesson', array('id'=>$lessonorder['lessonid'], 'ispay'=>1)), 'success');
            }
        } elseif (!empty($rechargeorder)){
            if ((($params['result'] == 'success' && $params['from'] == 'notify') || $params['type'] == 'credit') && $viporder['status'] == 0){
                $uid = $rechargeorder['uid'];
                /* 支付成功逻辑操作 */
                $data = array('status' => $params['result'] == 'success' ? 1 : 0);
                if(!empty($params['type'])){
                    $data['paytype'] = $params['type'];
                }else{
                    $data['paytype'] = $params['trade_type'] ? 'wxapp' : '';
                }

                $data['paytime'] = time();
                if (pdo_update($this->table_recharge_order, $data, array('id' => $orderid))) {
                    $mc = pdo_fetch('SELECT * FROM ' . tablename($this->table_mc_members) . ' WHERE uid=:id', array(':id' => $uid));
                    $mc_data = array('credit2' => ($mc['credit2'] + $rechargeorder['money']));
                    pdo_update($this->table_mc_members, $mc_data, array('uid' => $uid));

                    $record = array(
                        'uid' => $uid,
                        'uniacid' => $_W['uniacid'],
                        'credittype' => 'credit2',
                        'num' => $rechargeorder['money'],
                        'operator' => $uid,
                        'module' => '',
                        'clerk_id' => 1,
                        'store_id' => 0,
                        'clerk_type' => 2,
                        'createtime' => time(),
                        'remark' => '会员余额充值：' . $rechargeorder['money'] . ' 元',
                        'real_uniacid' => $_W['uniacid']
                    );

                    pdo_insert('mc_credits_record', $record);
                }
            }
        }
    }

	/* VIP订单支付成功，更新用户VIP时长
	 * $uniacid 公众号id
	 * $lessonmember 微课堂会员信息
	 * $order 订单信息
	 * return 会员VIP有效期
	 **/
	private function updateVipValidity($uniacid, $lessonmember, $order){
		$memberVip = pdo_fetch("SELECT * FROM " .tablename($this->table_member_vip). " WHERE uid=:uid AND level_id=:level_id", array(':uid'=>$order['uid'],':level_id'=>$order['level_id']));
		$newLevel = pdo_fetch("SELECT discount FROM " .tablename($this->table_vip_level). " WHERE id=:id", array(':id'=>$order['level_id']));
		if(!empty($memberVip)){
			if(time()>=$memberVip['validity']){
				$vipData = array(
					'validity' => time()+$order['viptime']*86400,
					'discount'=> $newLevel['discount'],
					'update_time' => time(),
				);
			}else{
				$vipData = array(
					'validity' => $memberVip['validity']+$order['viptime']*86400,
					'discount'=> $newLevel['discount'],
					'update_time' => time(),
				);
			}
			pdo_update($this->table_member_vip, $vipData, array('id'=>$memberVip['id']));
		}else{
			$vipData = array(
				'uniacid' => $uniacid,
				'uid'	  => $order['uid'],
				'level_id'=> $order['level_id'],
				'validity'=> time()+$order['viptime']*86400,
				'discount'=> $newLevel['discount'],
				'addtime' => time(),
			);
			pdo_insert($this->table_member_vip, $vipData);
		}

		return $vipData['validity'];
	}

	/* 订单金额加入今日销售额汇总表
	 * $uniacid 公众号id
	 * $type 订单类型 1.VIP订单 2.课程订单
	 * $orderAmount 订单金额
	 */
	private function staticAmount($uniacid, $type, $orderAmount){
		$today= strtotime("today");
		$exit = pdo_fetch("SELECT * FROM " .tablename($this->table_static). " WHERE uniacid=:uniacid AND static_time=:static_time", array(':uniacid'=>$uniacid,':static_time'=>$today));
		if(empty($exit)){
			if($type==1){
				$staticData = array(
					'uniacid' 		  => $uniacid,
					'vipOrder_num'    => 1,
					'vipOrder_amount' => $orderAmount,
					'static_time'     => $today
				);
			}elseif($type==2){
				$staticData = array(
					'uniacid' 		     => $uniacid,
					'lessonOrder_num'    => 1,
					'lessonOrder_amount' => $orderAmount,
					'static_time'        => $today
				);
			}
			pdo_insert($this->table_static, $staticData);
		}else{
			if($type==1){
				$staticData = array(
					'vipOrder_num'    => $exit['vipOrder_num']+1,
					'vipOrder_amount' => $exit['vipOrder_amount']+$orderAmount,
				);
			}elseif($type==2){
				$staticData = array(
					'lessonOrder_num'    => $exit['lessonOrder_num']+1,
					'lessonOrder_amount' => $exit['lessonOrder_amount']+$orderAmount,
				);
			}
			pdo_update($this->table_static, $staticData, array('uniacid'=>$uniacid,'static_time'=>$today));
		}		
	}

	/**
	 * 检查分销商状态变化
	 * $member 分销商会员信息
	 * $comsetting 分销设置参数
	 * $price 订单价格
	 */
	public function checkAgentStatus($member, $comsetting, $price){
		$orderAmount = $member['payment_amount'] ? $member['payment_amount']+$price : $this->getMemberOrderAmount($member['uid']);
		$orderTotal = $member['payment_order'] ? $member['payment_order']+1 : $this->getMemberOrderNumber($member['uid']);

		$memberinfo = array(
			'payment_amount' => $orderAmount,
			'payment_order'  => $orderTotal
		);

		/* 分销商状态变更 */
		$agent_condition = unserialize($comsetting['agent_condition']);
		if($member['status']==0){
			if($orderAmount >= $agent_condition['order_amount'] && $orderTotal >= $agent_condition['order_total']){
				$memberinfo['status'] = 1;
			}
		}

		/* 分销商等级变更 2. 购买订单总额 3.购买订单笔数 */
		if($comsetting['upgrade_condition']==2){
			$upgradeLevel = $this->upgradeAgentLevel($member['uniacid'], $member['agent_level'], $orderAmount, $comsetting);
			if(!empty($upgradeLevel)){
				$memberinfo['agent_level'] = $upgradeLevel;
			}

		}elseif($comsetting['upgrade_condition']==3){
			$upgradeLevel = $this->upgradeAgentLevel($member['uniacid'], $member['agent_level'], $orderTotal, $comsetting);
			if(!empty($upgradeLevel)){
				$memberinfo['agent_level'] = $upgradeLevel;
			}
		}

		pdo_update($this->table_member, $memberinfo, array('uid'=>$member['uid']));
	}

	/* 发放佣金操作 
	 * $uniacid 公众号id
	 * $uid 用户id
	 * $lessonmember 用户信息
	 * $type 1.vip订单 2.课程订单
	 * $setting 全局配置信息
	 * $order 订单信息
	 * $commission 佣金
	 * $level 佣金级别 1.一级佣金 2.二级佣金 3.三级佣金
	 */
	private function sendCommissionToUser($uniacid, $uid, $lessonmember, $type, $setting, $order, $commission, $level){
		global $_W;
		$comsetting = $this->readCache(2); /* 分销设置 */
		$tplmessage = pdo_fetch("SELECT cnotice,cnotice_format FROM " .tablename($this->table_tplmessage). " WHERE uniacid=:uniacid", array(':uniacid'=>$uniacid));
		$cnotice_format = json_decode($tplmessage['cnotice_format'], true);

		if($type==1){
			$first = $cnotice_format['vip_first'] ? $cnotice_format['vip_first'] : "您获得了一笔新的VIP分销佣金。";
			$orderContent = "[{$order['level_name']}]服务-{$order['viptime']}天";
			$remark = $cnotice_format['vip_remark'] ? $cnotice_format['vip_remark'] : "点击详情即可查看佣金明细。";
		}elseif($type==2){
			$first = $cnotice_format['lesson_first'] ? $cnotice_format['lesson_first'] : "您获得了一笔新的课程分销佣金。";
			$orderContent = "课程：《{$order['bookname']}》";
			$remark = $cnotice_format['lesson_remark'] ? $cnotice_format['lesson_remark'] : "点击详情即可查看佣金明细。";
		}
		
		$member = pdo_fetch("SELECT openid FROM " .tablename($this->table_fans). " WHERE uid=:uid", array(':uid'=>$uid));
		$customer =  pdo_fetch("SELECT nickname FROM " .tablename($this->table_mc_members). " WHERE uid=:uid", array(':uid'=>$order['uid']));

        $senddata = array(
            'openid' 	  => $member['openid'],
            'cnotice' 	  => $tplmessage['cnotice'],
            'url' 		  => $_W['siteroot'] . "app/index.php?i={$uniacid}&c=entry&op=commissionlog&do=commission&m=fy_lessonv2",
            'first' 	  => $first,
            'keyword1' 	  => "{$commission}元",
            'keyword2' 	  => date("Y-m-d H:i:s", time()),
            'remark' 	  => "下级成员：{$customer['nickname']}\n消费内容：{$orderContent}\n".$remark,
        );
        if ($comsetting['sale_rank'] == 2) {/* VIP身份才可获得佣金 */
        	$memberVip = pdo_fetchall("SELECT * FROM " .tablename($this->table_member_vip). " WHERE uid=:uid AND validity>:validity", array(':uid'=>$uid,':validity'=>time()));
            if (!empty($memberVip)) {
                $this->changecommisson($order, "{$orderContent}分销订单", $uid, $commission, $level, $level."级佣金:订单号" . $order['ordersn'], $senddata);
            } else {
            	if($level==1){
            		pdo_update($this->table_member_order, array('commission1' => 0), array('id' => $order['id']));
            	}elseif($level==2){
            		pdo_update($this->table_member_order, array('commission2' => 0), array('id' => $order['id']));
            	}elseif($level==3){
            		pdo_update($this->table_member_order, array('commission3' => 0), array('id' => $order['id']));
            	}
            }
        } else {
            $this->changecommisson($order, "{$orderContent}分销订单", $uid, $commission, $level, $level."级佣金:订单号" . $order['ordersn'], $senddata);
        }
	}

	/**
     * 更新用户佣金和添加日志
     * $order 订单信息
     * $uid 获得佣金会员ID
     * $change_num 变动数目
     * $grade 佣金等级 1.一级佣金 2.二级佣金 3.三级佣金
     * $remark 变动备注说明
	 * $senddata 发送模版消息内容
     */
    private function changecommisson($order, $bookname, $uid, $change_num, $grade, $remark, $senddata) {
        global $_W;

		$comsetting = $this->readCache(2); /* 分销设置 */
		$agentMember = pdo_fetch("SELECT * FROM " .tablename($this->table_member). " WHERE uid=:uid", array(':uid'=>$uid));
		$uniacid = $agentMember['uniacid'];
		
        if ($agentMember['status'] == 1) {
            $memupdate = array();

            /* 查询该分销代理商是否升级 */
			if($comsetting['upgrade_condition']==1){//分销累计佣金
				$total_commission = $agentMember['pay_commission'] + $agentMember['nopay_commission'] + $change_num;
				$upgradeLevel = $this->upgradeAgentLevel($uniacid, $agentMember['agent_level'], $total_commission, $comsetting);
				if(!empty($upgradeLevel)){
					$memupdate['agent_level'] = $upgradeLevel;
				}
			}

            $memupdate['nopay_commission'] = $agentMember['nopay_commission'] + $change_num;
            pdo_update($this->table_member, $memupdate, array('uid' => $agentMember['uid']));

            $member = pdo_fetch("SELECT nickname FROM " . tablename($this->table_mc_members) . " WHERE uid=:uid", array(':uid'=>$uid));
            $logarr = array(
                'uniacid' => $uniacid,
                'orderid' => $order['id'],
                'uid' => $uid,
                'nickname' => $member['nickname'],
                'bookname' => $bookname,
                'change_num' => $change_num,
                'grade' => $grade,
                'remark' => $remark,
                'addtime' => time(),
            );
            pdo_insert($this->table_commission_log, $logarr);
			$this->commissionMessage($senddata, $order['acid']); /* 发放佣金模版消息通知 */
        } else {
            if ($grade == 1) {
                $updatearr['commission1'] = 0;
            } elseif ($grade == 2) {
                $updatearr['commission2'] = 0;
            } elseif ($grade == 3) {
                $updatearr['commission3'] = 0;
            }
            pdo_update($this->table_order, $updatearr, array('id' => $order['id']));
        }
    }

	/*
	 * 支付订单成功通知用户 
	 * $uniacid 公众号id
	 * $setting 全局配置参数
	 * $order 订单信息
	 * $type 订单类型 1.VIP订单  2.课程订单
	 * $validity VIP有效期
	 */
	 public function sendMessageToUser($uniacid, $setting, $order, $type, $validity){
	 	global $_W;

		$tplmessage = pdo_fetch("SELECT buysucc,buysucc_format FROM " .tablename($this->table_tplmessage). " WHERE uniacid=:uniacid", array(':uniacid'=>$uniacid));
		$buysucc_format = json_decode($tplmessage['buysucc_format'], true);

	 	if($type==1){
	 		$url = $_W['siteroot'] . "app/index.php?i={$uniacid}&c=entry&do=vip&m=fy_lessonv2";
			$orderContent = "开通/续费[{$order['level_name']}]服务-{$order['viptime']}天";
			$remark = "\n有效期至：" . date('Y-m-d H:i:s', $validity);
			if(!empty($buysucc_format['remark1'])){
				$remark .= "\n".$buysucc_format['remark1'];
			}
		}elseif($type==2){
			$url = $_W['siteroot'] . "app/index.php?i={$uniacid}&c=entry&status=&do=mylesson&m=fy_lessonv2";
			$orderContent = "课程：《{$order['bookname']}》";
			if(!empty($buysucc_format['remark2'])){
				$remark .= "\n\n".$buysucc_format['remark2'];
			}
		}
		
		$fans = pdo_fetch("SELECT openid FROM " .tablename($this->table_fans). " WHERE uid=:uid", array(':uid'=>$order['uid']));

	 	$sendmessage = array(
            'touser' => $fans['openid'],
            'template_id' => $tplmessage['buysucc'],
            'url' => $url,
            'topcolor' => "",
            'data' => array(
                'name' => array(
                    'value' => $orderContent,
                    'color' => "",
                ),
                'remark' => array(
                    'value' => $remark,
                    'color' => "",
                ),
            )
        );
        $this->send_template_message(urldecode(json_encode($sendmessage)), $order['acid']);
	 }

	 /* 
	 * 新订单提醒管理员
	 * $setting 全局配置参数
	 * $order 订单信息
	 * $type 订单类型 1.VIP订单  2.课程订单
	 */
	private function sendOrderMessageToAdmin($setting, $order, $type){
		$tplmessage = pdo_fetch("SELECT neworder, neworder_format FROM " .tablename($this->table_tplmessage). " WHERE uniacid=:uniacid", array(':uniacid'=>$setting['uniacid']));
		$neworder_format = json_decode($tplmessage['neworder_format'], true);

		if($type==1){
			$first = $neworder_format['vip_first'] ? $neworder_format['vip_first'] : "您有一条新的VIP订单消息";
			$orderContent = "开通/续费[{$order['level_name']}]服务-{$order['viptime']}天";
			$amount = $order['vipmoney'];
			$remark = $neworder_format['vip_remark'] ? $neworder_format['vip_remark'] : "详情请登录网站后台查看！";
		}elseif($type==2){
			$first = $neworder_format['lesson_first'] ? $neworder_format['lesson_first'] : "您有一条新的课程订单消息";
			$orderContent = "课程：《{$order['bookname']}》";
			$amount = $order['price'];
			$paytype = $order['coupon_amount'] ? "(优惠券已抵扣".$order['coupon_amount']."元)" : "";
			$remark = $neworder_format['lesson_remark'] ? $neworder_format['lesson_remark'] : "详情请登录网站后台查看！";
		}
		
		$manage = explode(",", $setting['manageopenid']);
        foreach ($manage as $manageopenid) {
            $sendneworder = array(
                'touser' => $manageopenid,
                'template_id' => $tplmessage['neworder'],
                'url' => "",
                'topcolor' => "#7B68EE",
                'data' => array(
                    'first' => array(
                        'value' => $first,
                        'color' => "",
                    ),
                    'keyword1' => array(
                        'value' => $order['ordersn'],
                        'color' => "",
                    ),
                    'keyword2' => array(
                        'value' => "{$amount} 元{$paytype}",
                        'color' => "",
                    ),
                    'keyword3' => array(
                        'value' => $orderContent,
                        'color' => "",
                    ),
                    'remark' => array(
                        'value' => $remark,
                        'color' => "",
                    ),
                )
            );
            $this->send_template_message(urldecode(json_encode($sendneworder)), $order['acid']);
        }
	}

	/**
	 * 更新用户vip字段
	 */
	 public function updateMemberVip($uid, $vip){
		 return pdo_update($this->table_member, array('vip'=>$vip), array('uid'=>$uid));
	 }

	 /* 用户积分操作
	 * $type 订单类型 1.VIP订单 2.课程订单
	 * $ordersn 订单编号
	 * $uid 用户id（需要操作积分的用户）
	 * $integral 操作积分数额   +.增加  -.减少
	 */
	private function handleUserIntegral($type, $ordersn, $uid, $integral){
		$typeName = $type==1 ? '微课堂VIP订单' : '微课堂课程订单';
		load()->model('mc');
		$log = array(
			'0' => "", /* 操作管理员uid */
			'1' => $typeName."：{$ordersn}", /* 增减积分备注 */
			'2' => 'fy_lessonv2', /* 模块标识 */
			'3' => '', /* 店员uid */
			'4' => '', /* 门店id */
			'5' => '', /* 1(线上操作) 2(系统后台,公众号管理员和操作员) 3(店员) */
		);
        mc_credit_update($uid, 'credit1', $integral, $log);
	}

	/* 增加课程购买人数
	 * $order 订单信息
	 * $setting 全局配置信息
	 */
	private function updateLessonNumber($order, $setting){
		$lesson = pdo_fetch("SELECT stock,buynum FROM " . tablename($this->table_lesson_parent) . " WHERE id=:id", array(':id'=>$order['lessonid']));
		$lessonupdate = array(
			'buynum' => $lesson['buynum'] + 1,
		);
        pdo_update($this->table_lesson_parent, $lessonupdate, array('id' => $order['lessonid']));
	}

	/* 讲师课程佣金处理
	 * $uniacid 公众号id
	 * $lessonid 课程id
	 * $order 订单信息
	 */
	private function sendCommissionToTeacher($uniacid, $order, $setting){
		global $_W;
		$teacher = pdo_fetch("SELECT a.uid,a.teacher,b.openid FROM " .tablename($this->table_teacher). " a LEFT JOIN ".tablename($this->table_fans)." b ON a.uid=b.uid WHERE a.id=:id", array(':id'=>$order['teacherid']));

	    if ($teacher['uid']>0) {
	        $teachermember = pdo_fetch("SELECT id,uid,nopay_lesson FROM " . tablename($this->table_member) . " WHERE uid=:uid", array(':uid'=>$teacher['uid']));
	        $nopay_lesson = round($order['price'] * $order['teacher_income'] * 0.01, 2);

	        pdo_update($this->table_member, array('nopay_lesson' => $teachermember['nopay_lesson'] + $nopay_lesson), array('uid' => $teacher['uid']));

	        $incomedata = array(
	            'uniacid' 		 => $uniacid,
	            'uid' 			 => $teacher['uid'],
	            'teacher' 		 => $teacher['teacher'],
	            'ordersn' 		 => $order['ordersn'],
	            'bookname' 		 => $order['bookname'],
	            'orderprice' 	 => $order['price'],
	            'teacher_income' => $order['teacher_income'],
	            'income_amount'  => $nopay_lesson,
	            'addtime' 		 => time(),
	        );
	        pdo_insert($this->table_teacher_income, $incomedata);

			$tplmessage = pdo_fetch("SELECT cnotice,cnotice_format FROM " .tablename($this->table_tplmessage). " WHERE uniacid=:uniacid", array(':uniacid'=>$uniacid));
			$cnotice_format = json_decode($tplmessage['cnotice_format'], true);

	        $sendteacher = array(
	            'openid' 	  => $teacher['openid'],
	            'cnotice' 	  => $tplmessage['cnotice'],
	            'url' 		  => $_W['siteroot'] . "app/index.php?i={$uniacid}&c=entry&do=income&m=fy_lessonv2",
	            'first' 	  => $cnotice_format['teacher_first'] ? $cnotice_format['teacher_first'] : "您的课程《{$order['bookname']}》成功出售，您获得了一笔新的课程佣金。",
	            'keyword1' 	  => $nopay_lesson . "元",
	            'keyword2' 	  => date("Y-m-d H:i:s", time()),
	            'remark' 	  => $cnotice_format['teacher_remark'] ? $cnotice_format['teacher_remark'] : "详情请进入讲师中心查看课程收入。",
	        );
	        $this->commissionMessage($sendteacher, $order['acid']);
	    }
	}

	/* 机构课程佣金处理
	 * $uniacid 公众号id
	 * $lessonid 课程id
	 * $order 订单信息
	 */
	private function sendCommissionToCompany($uniacid, $order, $setting){
		global $_W;

		$fans = pdo_get($this->table_fans, array('uid'=>$order['company_uid']), array('openid','nickname'));

		$member = pdo_fetch("SELECT id,uid,nopay_commission FROM " . tablename($this->table_member) . " WHERE uid=:uid", array(':uid'=>$order['company_uid']));
		$nopay_commission = round($order['price'] * $order['company_income'] * 0.01, 2);

		pdo_update($this->table_member, array('nopay_commission' => $member['nopay_commission'] + $nopay_commission), array('uid' => $order['company_uid']));

		$incomedata = array(
			'uniacid'	 => $uniacid,
			'orderid'	 => $order['id'],
			'uid'		 => $order['company_uid'],
			'nickname'	 => $order['ordersn'],
			'bookname'	 => $order['bookname'],
			'change_num' => $nopay_commission,
			'grade'		 => 0,
			'remark'	 => '下级讲师出售课程',
			'company_income' => '下级讲师出售课程',
			'addtime'	 => time(),
		);
		pdo_insert($this->table_commission_log, $incomedata);

		$tplmessage = pdo_fetch("SELECT cnotice,cnotice_format FROM " .tablename($this->table_tplmessage). " WHERE uniacid=:uniacid", array(':uniacid'=>$uniacid));
		$cnotice_format = json_decode($tplmessage['cnotice_format'], true);

		$sendteacher = array(
			'openid' 	  => $fans['openid'],
			'cnotice' 	  => $tplmessage['cnotice'],
			'url' 		  => $_W['siteroot'] . "app/index.php?i={$uniacid}&c=entry&op=commissionlog&do=commission&m=fy_lessonv2",
			'first' 	  => "您的下级讲师课程《{$order['bookname']}》成功出售，您获得了一笔新的佣金。",
			'keyword1' 	  => $nopay_commission . "元",
			'keyword2' 	  => date("Y-m-d H:i:s", time()),
			'remark' 	  => "详情请进入佣金明细查看。",
		);
		$this->commissionMessage($sendteacher, $order['acid']);
	}

	/* 获得佣金模通知 */
    private function commissionMessage($data) {
		$message = array(
			'touser' => $data['openid'],
			'template_id' => $data['cnotice'],
			'url' => $data['url'],
			'topcolor' => "",
			'data' => array(
				'first' => array(
					'value' => $data['first'],
					'color' => "",
				),
				'keyword1' => array(
					'value' => $data['keyword1'],
					'color' => "",
				),
				'keyword2' => array(
					'value' => $data['keyword2'],
					'color' => "",
				),
				'remark' => array(
					'value' => $data['remark'],
					'color' => "",
				),
			)
		);
		$this->send_template_message(urldecode(json_encode($message)));
    }

	/* 用户购买课程赠送优惠券 */
	private function sendCouponByBuyLesson($member, $setting){
		global $_W;
		$uniacid = $_W['uniacid'];

		$market = pdo_fetch("SELECT * FROM " .tablename($this->table_market). " WHERE uniacid=:uniacid", array(':uniacid'=>$uniacid));
		$buyLesson = json_decode($market['buy_lesson'], true);

		if(!empty($buyLesson)){
			if($market['buy_lesson_time']>0){
				$buyTotal = pdo_fetchcolumn("SELECT COUNT(*) FROM " .tablename($this->table_member_coupon). " WHERE uid=:uid AND source=:source", array(':uid'=>$member['uid'], 'source'=>2));
				if($buyTotal >= $market['buy_lesson_time']) return;
			}

			$t = 0;
			foreach($buyLesson as $item){
				$coupon = pdo_fetch("SELECT * FROM " .tablename($this->table_mcoupon). " WHERE id=:id", array(':id'=>$item));
				if(empty($coupon)) continue;
				$lessonCoupon = array(
					'uniacid'	  => $uniacid,
					'uid'		  => $member['uid'],
					'amount'      => $coupon['amount'],
					'conditions'  => $coupon['conditions'],
					'validity'	  => $coupon['validity_type']==1 ? $coupon['days1'] : time()+ $coupon['days2']*86400,
					'category_id' => $coupon['category_id'],
					'status'	  => 0, /* 未使用 */
					'source'	  => 2, /* 购买课程赠送 */
					'coupon_id'	  => $coupon['id'],
					'addtime'	  => time(),
				);
				if(pdo_insert($this->table_member_coupon, $lessonCoupon)){
					$t++;
				}
			}
			$fans = pdo_fetch("SELECT openid,nickname FROM " .tablename($this->table_fans). " WHERE uid=:uid", array(':uid'=>$member['uid']));
			$tplmessage = pdo_fetch("SELECT receive_coupon, receive_coupon_format FROM " .tablename($this->table_tplmessage). " WHERE uniacid=:uniacid", array(':uniacid'=>$uniacid));
			$receive_coupon_format = json_decode($tplmessage['receive_coupon_format'], true);

			$sendmessage = array(
				'touser' => $fans['openid'],
				'template_id' => $tplmessage['receive_coupon'],
				'url' => $_W['siteroot'] . 'app/' . $this->createMobileUrl('coupon'),
				'topcolor' => "#7B68EE",
				'data' => array(
					'first' => array(
						'value' => "恭喜您购买成功，系统赠您{$t}张优惠券已发放到您的帐号，请注意查收。",
						'color' => "#2392EA",
						),
					'keyword1' => array(
						'value' => $fans['nickname'],
						'color' => "",
					),
					'keyword2' => array(
						'value' => $t." 张",
						'color' => "",
					),
					'keyword3' => array(
						'value' => date('Y年m月d日', time()),
						'color' => "",
					),
					'remark' => array(
						'value' => $receive_coupon_format['remark'] ? $receive_coupon_format['remark'] : "点击详情可查看您的帐号优惠券详情哦~",
						'color' => "",
					),
				)
			);
			$this->send_template_message(urldecode(json_encode($sendmessage)));
		}
	}

	/* 获取用户购买订单总额 */
	public function getMemberOrderAmount($uid){
		$lessonAmount = pdo_fetchall("SELECT SUM(price) as amount FROM " .tablename($this->table_order). " WHERE uid=:uid AND status>=:status", array(':uid'=>$uid, ':status'=>1));
		$vipAmount = pdo_fetchall("SELECT SUM(vipmoney) as amount FROM " .tablename($this->table_member_order). " WHERE uid=:uid AND status=:status", array(':uid'=>$uid, ':status'=>1));
			
		$orderAmount = $lessonAmount[0]['amount'] + $vipAmount[0]['amount'];
		return $orderAmount;
	}

	/* 获取用户购买订单笔数 */
	public function getMemberOrderNumber($uid){
		$lessonTotal = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename($this->table_order). " WHERE uid=:uid AND status>=:status ", array(':uid'=>$uid, ':status'=>1));
		$vipTotal = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename($this->table_member_order). " WHERE uid=:uid AND status=:status", array(':uid'=>$uid, ':status'=>1));

		$orderTotal = $lessonTotal + $vipTotal;
		return $orderTotal;
	}

	/* 检查分销商是否升级 */
	private function upgradeAgentLevel($uniacid, $agentLevel, $total_commission, $comsetting){
		$levellist = pdo_fetchall("SELECT * FROM " . tablename($this->table_commission_level) . " WHERE uniacid=:uniacid ORDER BY id ASC", array(':uniacid'=>$uniacid));
		if (!empty($levellist)) {
			/* 分销商升级处理开始 */
			if ($agentLevel == 0) {
				$commission = unserialize($comsetting['commission']);
				if ($commission['updatemoney'] > 0 && $total_commission >= $commission['updatemoney']) {
					foreach ($levellist as $key => $value) {
						if ($value['updatemoney'] > 0 && $total_commission >= $value['updatemoney']) {
							$upgradeLevel = intval($levellist[$key + 1]['id']);
						} else {
							break;
						}
					}
					if (empty($upgradeLevel)) {
						$upgradeLevel = $levellist[0]['id'];
					}
				}
			} else {
				$level = pdo_fetch("SELECT * FROM " . tablename($this->table_commission_level) . " WHERE id=:id", array(':id'=>$agentLevel));
				if ($level['updatemoney'] > 0 && $total_commission >= $level['updatemoney']) {
					foreach ($levellist as $key => $value) {
						if ($value['id'] == $level['id']) {
							$levelkey = $key;
						}
						if ($value['updatemoney'] > 0 && $total_commission >= $value['updatemoney']) {
							$upgradeLevel = intval($levellist[$key + 1]['id']);
						} else {
							break;
						}
					}
					if ($upgradeLevel == $level['id']) {
						$upgradeLevel = $levellist[$levelkey + 1]['id'];
					}
				}
			}

			return $upgradeLevel;
			/* 分销商升级处理结束 */
		}
	}


	/* 发送模版消息 */
    private function send_template_message($messageDatas) {
        global $_W;

        load()->classs('weixin.account');
        $accObj = WeixinAccount::create($_W['uniacid']);
        $access_token = $accObj->fetch_token();

        $urls = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=" . $access_token;
        $ress = $this->http_request($urls, $messageDatas);

        return json_decode($ress, true);
    }

	/* https请求（支持GET和POST） */
    private function http_request($url, $messageDatas = null) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($messageDatas)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $messageDatas);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }


/***************************** 接口数据(审核使用) ******************************** */
	/* 基本设置参数 */
	public function doPageSetting() {
		global $_W;
		$setting = $this->readCache(1);

		$data = array(
			'setting' => $setting
		);

		return $this->result(0, '', $data);
	}
	
	/* 轮播图 */
	public function doPageBanner() {
		global $_W;

		$condition = array(
			'uniacid' => $_W['uniacid'],
			'banner_type' => 0,
			'is_pc'	  => 0,
			'is_show' => 1
		);
		$banner = pdo_getall($this->table_banner, $condition);
		foreach($banner as $k=>$v){
			$banner[$k]['img'] = $_W['attachurl'].$v['picture'];
		}
		$this->result(0, '', $banner);
	}

	/* 首页展示课程 */
	public function doPageFreelesson(){
		global $_W;
		$setting = $this->readCache(1);

		$lesson = pdo_fetchall("SELECT id,bookname,price,images,buynum,virtual_buynum,update_time,visit_number FROM " .tablename($this->table_lesson_parent). " WHERE uniacid=:uniacid AND price=:price AND lesson_type=:lesson_type", array(':uniacid'=>$_W['uniacid'], ':price'=>0, ':lesson_type'=>2));
		foreach($lesson as $k=>$v){
			$lesson[$k]['count'] = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename($this->table_lesson_son) . " WHERE parentid=:parentid ", array(':parentid'=>$v['id']));
			$lesson[$k]['images'] = $_W['attachurl'].$v['images'];
			$lesson[$k]['buynumber'] = $v['buynum']+$v['virtual_buynum']+$v['visit_number'];
		}
		
		$this->result(0, '', array('lesson'=>$lesson));
	}

	/* 课程详情 */
	public function doPageLesson(){
		global $_W, $_GPC;

		$uniacid = $_W['uniacid'];
		$id = $_GPC['id'];
		$sectionid = $_GPC['sectionid'];
		$setting = $this->readCache(1);

		/* 课程信息 */
		$lesson = pdo_fetch("SELECT a.*,b.teacher,b.qq,b.qqgroup,b.qqgroupLink,b.weixin_qrcode,b.teacherphoto,b.teacherdes FROM " .tablename($this->table_lesson_parent). " a LEFT JOIN " .tablename($this->table_teacher). " b ON a.teacherid=b.id WHERE a.uniacid=:uniacid AND a.id=:id AND a.status!=:status LIMIT 1", array(':uniacid'=>$uniacid, ':id'=>$id, ':status'=>0));
		if(empty($lesson)){
			$data = array(
				'code'  => -1,
				'msg' => '该课程已下架，您可以看看其他课程',
			);
			$this->result(0, '', $data);
		}
		$lesson['images'] = $_W['attachurl'].$lesson['images'];
		
		/* 章节列表 */
		$section_list = pdo_fetchall("SELECT * FROM " .tablename($this->table_lesson_son). " WHERE parentid=:parentid AND status=:status ORDER BY displayorder DESC, id ASC", array(':parentid'=>$id,':status'=>1));

		$data = array(
			'code'		   => 0,
			'lesson'	   => $lesson,
			'section'	   => $section,
			'section_list' => $section_list,
			'section_count'=> count($section_list),
		);
		$this->result(0, '', $data);
	}

	/* 图文章节课程 */
	public function doPageLessonsArticle(){
		global $_W, $_GPC;

		$uniacid = $_W['uniacid'];
		$id = $_GPC['lessonid'];
		$sectionid = $_GPC['sectionid'];

		$lesson = pdo_fetch("SELECT a.*,b.teacher,b.qq,b.qqgroup,b.qqgroupLink,b.weixin_qrcode,b.teacherphoto,b.teacherdes FROM " .tablename($this->table_lesson_parent). " a LEFT JOIN " .tablename($this->table_teacher). " b ON a.teacherid=b.id WHERE a.uniacid=:uniacid AND a.id=:id AND a.status!=:status LIMIT 1", array(':uniacid'=>$uniacid, ':id'=>$id, ':status'=>0));
		if(empty($lesson)){
			$data = array(
				'code' => -1,
				'msg'  => '该课程已下架，您可以看看其他课程',
			);
			$this->result(0, '', $data);
		}

		$section = pdo_fetch("SELECT * FROM " .tablename($this->table_lesson_son). " WHERE parentid=:parentid AND id=:id AND status=:status LIMIT 1", array(':parentid'=>$id,':id'=>$sectionid,':status'=>1));
		if(empty($section)){
			$data = array(
				'code' => -1,
				'msg'  => '该章节不存在或已被删除',
			);
			$this->result(0, '', $data);
		}

		$section['content'] = htmlspecialchars_decode($section['content']);
		$section['addtime'] = date('Y-m-d', $section['addtime']);

		$data = array(
			'code'	   => 0,
			'advs'	   => $_W['attachurl'].$advs['img'],
			'lesson'   => $lesson,
			'section'  => $section,
		);
		$this->result(0, '', $data);
	}


}