<?php
/**
 * 微课堂V2模块订阅器
 */
defined('IN_IA') or exit('Access Denied');

class Fy_lessonv2ModuleReceiver extends WeModuleReceiver {
	public $table_article = 'fy_lesson_article';
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

	public function receive() {
		global $_W;

		$type = $this->message['type'];
		$event = $this->message['event'];
		$from = $this->message['from'];
		$to = $this->message['to'];
		$scene = $this->message['scene']; /*格式：lesson_48、uid_3*/
		$uniacid = $_W['uniacid'];

		/* 用户关注事件 */
		if($event=="subscribe"){
			if(strstr($scene, "lesson_")){
				$this->sendLessonNews($uniacid, $scene, $from);
			}

			if(strstr($scene, "uid_")){
				$this->recommendMember($uniacid, $scene, $from);
			}
		}
	}

	/**
	 * 用户关注公众号发送图文消息
	 * 场景：用户在课程详情页关注公众号
	 */
	private function sendLessonNews($uniacid, $scene, $from){
		global $_W;
		load()->func('logging');

		$lessonid = str_replace("lesson_", "", $scene);
		$lesson = pdo_fetch("SELECT id,bookname,images,share FROM " .tablename($this->table_lesson_parent). " WHERE id=:id", array(':id'=>$lessonid));
		if(!$lesson){
			logging_run('用户关注公众号(uniacid:'.$uniacid.')发送课程图文消息失败，原因：课程(id:'.$lessonid.')不存在', 'trace', 'fylessonv2');
			return;
		}
		$share = json_decode($lesson['share'], true);

		$fans = pdo_fetch("SELECT nickname FROM " .tablename($this->table_fans). " WHERE uniacid=:uniacid AND openid=:openid", array(':uniacid'=>$uniacid,':openid'=>$from));
		$nickname = isset($fans['nickname']) ? $fans['nickname']."，" : "";
		$title = !empty($share['title']) ? $share['title'] : "欢迎继续回来，点击继续学习《{$lesson['bookname']}》课程";
		$description = !empty($share['descript']) ? $share['descript'] : "点击继续学习《{$lesson['bookname']}》";

		$message = array(
			'touser' => $from,
			'msgtype' => 'news',
			'news' => array(
				'articles' => array(
					'0' => array(
						'title' => $nickname.$title,
						'description' => $description,
						'url' => $_W['siteroot'].'app/'.$this->createMobileUrl('lesson', array('id'=>$lessonid)),
						'picurl' => !empty($share['images']) ? $_W['attachurl'].$share['images'] : $_W['attachurl'].$lesson['images']
					)
				)
			)
		);
		$account_api = WeAccount::create();
		$token = $account_api->getAccessToken();
		if(is_error($token)){
			logging_run('用户关注公众号(uniacid:'.$uniacid.')发送课程图文消息失败，原因：获取access_token失败', 'trace', 'fylessonv2');
			return;
		}

		$url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=".$token;
		$result = ihttp_request($url, json_encode($message, JSON_UNESCAPED_UNICODE));
	}

	/**
	 * 用户识别海报关注公众号，关联上下级关系
	 * 场景：用户通过好友分享海报关注公众号
	 */
	private function recommendMember($uniacid, $scene, $from){
		$comsetting = $this->readCache(2); /* 分销设置 */

		load()->model('mc');
		$recid = str_replace("uid_", "", $scene); /*推荐人id*/
		$fansinfo = mc_fansinfo($from);
		$uid = $fansinfo['uid'];
		if(empty($uid)){
        	return;
        }

		$setting = $this->readCache(1); /* 基本设置 */
		$comsetting = $this->readCache(2); /* 分销设置 */

		$member = pdo_get($this->table_member, array('uid'=>$uid));
		$recmember = pdo_get($this->table_member, array('uid'=>$recid));
		if(!empty($member)){
			return;
		}

		$mc_member = pdo_get($this->table_mc_members, array('uid'=>$uid));
		if(!empty($mc_member)){
			$tmpno = '';
			for ($i = 0; $i < 7 - strlen($uid); $i++) {
				$tmpno .= 0;
			}
			$insertarr = array(
				'uniacid' => $uniacid,
				'uid' => $uid,
				'studentno' => $tmpno . $uid,
				'openid' => $from,
				'nickname' => $fansinfo['nickname'] ? $fansinfo['nickname'] : $mc_member['nickname'],
				'parentid' => $recmember['status']==1 ? $recmember['uid'] : 0,
				'status' => $comsetting['agent_status'],
				'uptime' => 0,
				'addtime' => time(),
			);
			pdo_insert($this->table_member, $insertarr);
			$source_id = pdo_insertid();
			$member = pdo_get($this->table_member, array('uid'=>$uid));
		}

		if($source_id>0){
			/* 新会员注册发放优惠券&&成功推荐下级，给直接推荐人发放优惠券 */
			$this->sendCouponByNewMember($member, $recmember, $setting);
			/* 新下级加入、通知一二三级推荐人 */
			$this->setMemberParentId($member, $recmember, $setting, $source_id);
		}
	}

	/* 给新注册会员和直接推荐人发放优惠券 
	 * $member 新会员信息
	 * $recmember 推荐人信息
	 * $setting 基本设置信息
	 */
	private function sendCouponByNewMember($member, $recmember, $setting){
		global $_W;
		$uniacid = $_W['uniacid'];

		$market = pdo_get($this->table_market, array('uniacid'=>$uniacid));
		$tplmessage = pdo_get($this->table_tplmessage, array('uniacid'=>$uniacid));
		$receive_coupon_format = json_decode($tplmessage['receive_coupon_format'], true);

		$regGive = json_decode($market['reg_give'], true);
		$recommend = json_decode($market['recommend'], true);

		if(!empty($regGive)){
			$regTotal = pdo_fetchcolumn("SELECT COUNT(*) FROM " .tablename($this->table_member_coupon). " WHERE uid=:uid AND source=:source", array(':uid'=>$recmember['uid'], 'source'=>6));
			if($regTotal>0) return;

			$t = 0;
			foreach($regGive as $item){
				$coupon = pdo_fetch("SELECT * FROM " .tablename($this->table_mcoupon). " WHERE id=:id", array(':id'=>$item));
				if(empty($coupon)) continue;
				$regCoupon = array(
					'uniacid'	  => $uniacid,
					'uid'		  => $member['uid'],
					'amount'      => $coupon['amount'],
					'conditions'  => $coupon['conditions'],
					'validity'	  => $coupon['validity_type']==1 ? $coupon['days1'] : time()+ $coupon['days2']*86400,
					'category_id' => $coupon['category_id'],
					'status'	  => 0, /* 未使用 */
					'source'	  => 6, /* 新会员注册 */
					'coupon_id'	  => $coupon['id'],
					'addtime'	  => time(),
				);
				if(pdo_insert($this->table_member_coupon, $regCoupon)){
					$t++;
				}
			}
			$newFans = pdo_fetch("SELECT openid,nickname FROM " .tablename($this->table_fans). " WHERE uid=:uid", array(':uid'=>$member['uid']));

			$sendmessage1 = array(
				'touser' => $newFans['openid'],
				'template_id' => $tplmessage['receive_coupon'],
				'url' => $_W['siteroot'] . 'app/' . $this->createMobileUrl('coupon'),
				'topcolor' => "#7B68EE",
				'data' => array(
					'first' => array(
						'value' => $newFans['nickname']."，终于等到您了。系统赠您{$t}张新会员专享优惠券已发放到您的帐号，请您查收。",
						'color' => "#2392EA",
						),
					'keyword1' => array(
						'value' => $newFans['nickname'],
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
			$this->send_template_message(urldecode(json_encode($sendmessage1)));
		}

		if(!empty($recommend) && !empty($recmember)){
			if($market['recommend_time']>0){
				$recTotal = pdo_fetchcolumn("SELECT COUNT(*) FROM " .tablename($this->table_member_coupon). " WHERE uid=:uid AND source=:source", array(':uid'=>$recmember['uid'], 'source'=>3));
				if($recTotal >= $market['recommend_time']) return;
			}

			$t = 0;
			foreach($recommend as $item){
				$coupon = pdo_fetch("SELECT * FROM " .tablename($this->table_mcoupon). " WHERE id=:id", array(':id'=>$item));
				if(empty($coupon)) continue;
				$recCoupon = array(
					'uniacid'	  => $uniacid,
					'uid'		  => $recmember['uid'],
					'amount'      => $coupon['amount'],
					'conditions'  => $coupon['conditions'],
					'validity'	  => $coupon['validity_type']==1 ? $coupon['days1'] : time()+ $coupon['days2']*86400,
					'category_id' => $coupon['category_id'],
					'status'	  => 0, /* 未使用 */
					'source'	  => 3, /* 新会员注册 */
					'coupon_id'	  => $coupon['id'],
					'addtime'	  => time(),
				);
				if(pdo_insert($this->table_member_coupon, $recCoupon)){
					$t++;
				}
			}
			$recFans = pdo_fetch("SELECT openid,nickname FROM " .tablename($this->table_fans). " WHERE uid=:uid", array(':uid'=>$recmember['uid']));

			$sendmessage2 = array(
				'touser' => $recFans['openid'],
				'template_id' => $tplmessage['receive_coupon'],
				'url' => $_W['siteroot'] . 'app/' . $this->createMobileUrl('coupon'),
				'topcolor' => "#7B68EE",
				'data' => array(
					'first' => array(
						'value' => "恭喜您成功推荐下级成员，系统赠您{$t}张优惠券已发放到您的帐号，请注意查收。",
						'color' => "#2392EA",
						),
					'keyword1' => array(
						'value' => $recFans['nickname'],
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
			$this->send_template_message(urldecode(json_encode($sendmessage2)));
		}
	}

	/*
	 * 设置会员推荐人ID
	 * $member    会员信息
	 * $recmember 推荐人信息
	 * $setting   基本设置信息
	 */
	private function setMemberParentId($member, $recmember, $setting, $source_id){
		/* 分销设置 */
		$comsetting = $this->readCache(2);

		$recid = $recmember['status']==1 ? $recmember['uid'] : 0;
		/*新用户加入通知一级推荐人*/
		if ($comsetting['is_sale'] == 1 && $recid > 0) {
			$this->sendNoticeToMember1($member, $recmember, $setting, $source_id, $comsetting);
		}
		/*新用户加入通知二级推荐人*/
		$recmember2 = pdo_fetch("SELECT * FROM " . tablename($this->table_member) . " WHERE uid=:uid", array(':uid'=>$recmember['parentid']));
		if ($comsetting['is_sale'] == 1 && $recmember2['uid'] > 0) {
			$this->sendNoticeToMember2($member, $recmember2, $setting, $comsetting);
		}
		 
		/*新用户加入通知三级推荐人*/
		$recmember3 = pdo_fetch("SELECT * FROM " . tablename($this->table_member) . " WHERE uid=:uid", array(':uid'=>$recmember2['parentid']));
		if ($comsetting['is_sale'] == 1 && $recmember3['uid'] > 0) {
			$this->sendNoticeToMember3($member, $recmember3, $setting, $comsetting);
		}
	}

	/* 新用户加入通知一级推荐人 
	 * $member 新用户信息
	 * $recmember 一级推荐人信息
	 * $setting 基本设置信息
	 * $comsetting 分销设置
	 **/
	private function sendNoticeToMember1($member, $recmember, $setting, $source_id, $comsetting){
	 	global $_W;
		
	 	if ($comsetting['level'] >= 1) {
	 		$commission = unserialize($comsetting['commission']);
			$fans = pdo_fetch("SELECT nickname,openid FROM " . tablename('mc_mapping_fans') . "  WHERE uid=:uid", array(':uid'=>$recmember['uid']));
			/* 开启直推下级获得奖励 */
			$rec_income = json_decode($comsetting['rec_income'], true);

			if (floatval($rec_income['credit1']) > 0) {
                load()->model('mc');
				$log = array(0, '直接推荐下级成员加入', 'fy_lessonv2');
				mc_credit_update($recmember['uid'], 'credit1', $rec_income['credit1'], $log);
            }
			if (floatval($rec_income['credit2']) > 0) {
                pdo_update($this->table_member, array('nopay_commission' => $recmember['nopay_commission'] + $rec_income['credit2']), array('uid' => $recmember['uid']));
                $logarr = array(
                    'uniacid' 	=> $_W['uniacid'],
                    'orderid' 	=> $source_id,
                    'uid' 		=> $recmember['uid'],
                    'openid' 	=> $fans['openid'],
                    'nickname' 	=> $fans['nickname'],
                    'bookname' 	=> "推荐下级成员",
                    'change_num' => $rec_income['credit2'],
                    'grade' 	=> 1,
                    'remark' 	=> "直接推荐下级成员加入",
                    'addtime'	=> time(),
                );
                pdo_insert($this->table_commission_log, $logarr);
            }
			
			if ($recmember['agent_level'] > 0) {
                $level = pdo_fetch("SELECT * FROM " . tablename($this->table_commission_level) . " WHERE id=:id", array(':id'=>$recmember['agent_level']));
            }
            if ($comsetting['self_sale'] == 1) { /* 开启分销内购，一级分销人拿二级佣金 */
                if (!empty($level)) {
                    $commission = $level['commission2'];
                } else {
                    $commission = $commission['commission2'];
                }
            } else {
                if (!empty($level)) {
                    $commission = $level['commission1'];
                } else {
                    $commission = $commission['commission1'];
                }
            }

			if($comsetting['sale_rank']==2){
				/* 如果获得佣金是VIP身份且推荐人不是VIP身份，则不发送下级加入模版消息通知 */
				$member_vip = pdo_fetchall("SELECT * FROM " .tablename($this->table_member_vip). " WHERE uid=:uid AND validity>:validity", array(':uid'=>$recmember['uid'], ':validity'=>time()));
				if(empty($member_vip)){
					return;
				}
			}
			$this->sendNewUserNoticeToRecmember($fans['openid'], $setting, $member['nickname'], $commission, $type=1);
		}
	}

	/* 新用户加入通知二级推荐人 
	 * $member 新用户信息
	 * $recmember 二级推荐人信息
	 * $setting 基本设置信息
	 * $comsetting 分销设置
	 **/
	private function sendNoticeToMember2($member, $recmember, $setting, $comsetting){
	 	global $_W;
		
	 	if ($comsetting['level'] >= 2) {
	 		$commission = unserialize($comsetting['commission']);
			$fans = pdo_fetch("SELECT nickname,openid FROM " . tablename('mc_mapping_fans') . "  WHERE uid=:uid", array(':uid'=>$recmember['uid']));
			
			if ($recmember['agent_level'] > 0) {
                $level = pdo_fetch("SELECT * FROM " . tablename($this->table_commission_level) . " WHERE id=:id", array(':id'=>$recmember['agent_level']));
            }
            if ($comsetting['self_sale'] == 1) { /* 开启分销内购，一级分销人拿二级佣金 */
                if (!empty($level)) {
                    $commission = $level['commission3'];
                } else {
                    $commission = $commission['commission3'];
                }
            } else {
                if (!empty($level)) {
                    $commission = $level['commission2'];
                } else {
                    $commission = $commission['commission2'];
                }
            }
			$this->sendNewUserNoticeToRecmember($fans['openid'], $setting, $member['nickname'], $commission, $type=2);
		}
	}

	/* 新用户加入通知三级推荐人 
	 * $member 新用户信息
	 * $recmember 三级推荐人信息
	 * $setting 基本设置信息
	 * $comsetting 分销设置
	 **/
	private function sendNoticeToMember3($member, $recmember, $setting, $comsetting){
	 	global $_W;
		
	 	if ($comsetting['level'] >= 3) {
	 		$commission = unserialize($comsetting['commission']);
			$fans = pdo_fetch("SELECT nickname,openid FROM " . tablename('mc_mapping_fans') . "  WHERE uid=:uid", array(':uid'=>$recmember['uid']));
			
			if ($recmember['agent_level'] > 0) {
                $level = pdo_fetch("SELECT * FROM " . tablename($this->table_commission_level) . " WHERE id=:id", array(':id'=>$recmember['agent_level']));
            }
            if ($comsetting['self_sale'] == 1) { /* 开启分销内购，一级分销人拿二级佣金 */
                $commission = 0;
            } else {
                if (!empty($level)) {
                    $commission = $level['commission3'];
                } else {
                    $commission = $commission['commission3'];
                }
            }
			$this->sendNewUserNoticeToRecmember($fans['openid'], $setting, $member['nickname'], $commission, $type=3);
		}
	}

	/* 新下级加入 模版消息通知推荐人 
	 * $toOpenid 上级openid
	 * $setting 设置信息
	 * $nickname 下级用户昵称
	 * $commission 佣金比例
	 * $type 等级 1.一级 2.二级 3.三级
	 */
	private function sendNewUserNoticeToRecmember($toOpenid, $setting, $nickname, $commission, $type){
		global $_W;
		if($type==1){
			$level = "一级";
		}elseif($type==2){
			$level = "二级";
		}elseif($type==3){
			$level = "三级";
		}

		$tplmessage = pdo_fetch("SELECT newjoin, newjoin_format FROM " .tablename($this->table_tplmessage). " WHERE uniacid=:uniacid", array(':uniacid'=>$setting['uniacid']));
		$newjoin_format = json_decode($tplmessage['newjoin_format'], true);
		
		$send = array(
            'touser' => $toOpenid,
            'template_id' => $tplmessage['newjoin'],
            'url' => $_W['siteroot'] . 'app/' . $this->createMobileUrl('team', array('level' => $type)),
            'topcolor' => "#e25804",
            'data' => array(
                'first' => array(
                    'value' => $newjoin_format['first'] ? $newjoin_format['first'] : "恭喜您有新的下级成员加入",
                    'color' => "",
                ),
                'keyword1' => array(
                    'value' => $nickname ? $nickname : '未设置',
                    'color' => "",
                ),
                'keyword2' => array(
                    'value' => $level,
                    'color' => "",
                ),
                'keyword3' => array(
                    'value' => $newjoin_format['keyword3'] ? $newjoin_format['keyword3']."{$commission}%" : "下级消费金额的{$commission}%",
                    'color' => "",
                ),
                'remark' => array(
                    'value' => $newjoin_format['remark'] ? $newjoin_format['remark'] : "您的下级成员进行消费时，您将有机会获得佣金~",
                    'color' => "",
                ),
            )
        );
        if ($commission > 0) {
            $this->send_template_message(urldecode(json_encode($send)));
        }
	}

	/* 发送模版消息 */
    private function send_template_message($messageDatas, $acid = null) {
        global $_W, $_GPC;
        if (empty($acid)) {
            $acid = $_W['account']['acid'];
        }

        load()->classs('weixin.account');
        $accObj = WeixinAccount::create($acid);
        $access_token = $accObj->fetch_token();

        $urls = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=" . $access_token;
        $ress = ihttp_request($urls, $messageDatas);

        return json_decode($ress, true);
    }

	/* 读取缓存
	 * $type 读取缓存类型 1.全局设置表 2.分销设置表
	 */
	private function readCache($type){
		global $_W;

		if($type==1){
			$setting = cache_load('fy_lessonv2_setting_'.$_W['uniacid']);
			if(empty($setting)){
				$setting = pdo_fetch("SELECT * FROM " .tablename($this->table_setting). " WHERE uniacid=:uniacid", array(':uniacid'=>$_W['uniacid']));
				cache_write('fy_lessonv2_setting_'.$_W['uniacid'], $setting);
			}
			return $setting;

		}elseif($type==2){
			$comsetting = cache_load('fy_lessonv2_commission_setting_'.$_W['uniacid']);
			if(empty($comsetting)){
				$comsetting = pdo_fetch("SELECT * FROM " .tablename($this->table_commission_setting). " WHERE uniacid=:uniacid", array(':uniacid'=>$_W['uniacid']));
				cache_write('fy_lessonv2_commission_setting_'.$_W['uniacid'], $comsetting);
			}
			return $comsetting;
		}
	}

}