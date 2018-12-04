<?php
/**
 * 课程详情页
 * ============================================================================
 * 版权所有 2015-2018 微课堂团队，并保留所有权利。
 * 网站地址: https://wx.haoshu888.com
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！不允许对程序代码以任何形式任何目的的再发布，作者将保留
 * 追究法律责任的权力和最终解释权。
 */
 
/* 检查是否在微信中访问 */
$userAgent = $this->checkUserAgent();
$login_visit = json_decode($setting['login_visit']);
if((!empty($login_visit) && in_array('lesson', $login_visit)) || $userAgent){
	checkauth();
}

$uid = $_W['member']['uid'];
$id = intval($_GPC['id']);/* 课程id */
$sectionid = intval($_GPC['sectionid']);/* 点播章节id */

if($uid>0){
	$member = pdo_fetch("SELECT a.*,b.follow,c.avatar,c.nickname FROM " .tablename($this->table_member). " a LEFT JOIN " .tablename($this->table_fans). " b ON a.uid=b.uid LEFT JOIN " .tablename($this->table_mc_members). " c ON a.uid=c.uid WHERE a.uid=:uid", array(':uid'=>$uid));
}
if(empty($member['avatar'])){
	$avatar = MODULE_URL."template/mobile/images/default_avatar.jpg";
}else{
	$inc = strstr($member['avatar'], "http://") || strstr($member['avatar'], "https://");
	$avatar = $inc ? $member['avatar'] : $_W['attachurl'].$member['avatar'];
}

$lesson = pdo_fetch("SELECT a.*,b.teacher,b.qq,b.qqgroup,b.qqgroupLink,b.weixin_qrcode,b.teacherphoto,b.teacherdes FROM " .tablename($this->table_lesson_parent). " a LEFT JOIN " .tablename($this->table_teacher). " b ON a.teacherid=b.id WHERE a.uniacid=:uniacid AND a.id=:id AND a.status!=:status LIMIT 1", array(':uniacid'=>$uniacid, ':id'=>$id, ':status'=>0));
if(empty($lesson)){
	message("该课程已下架，您可以看看其他课程~", "", "error");
}
$lesson['qq'] = $config['teacher_qq'] ? $config['teacher_qq'] : $lesson['qq'];
$lesson['qqgroup'] = $config['teacher_qqgroup'] ? $config['teacher_qqgroup'] : $lesson['qqgroup'];
$lesson['qqgroupLink'] = $config['teacher_qqlink'] ? $config['teacher_qqlink'] : $lesson['qqgroupLink'];
$lesson['weixin_qrcode'] = $config['teacher_qrcode'] ? $config['teacher_qrcode'] : $lesson['weixin_qrcode'];

/* 课程规格 */
$spec_list = pdo_fetchall("SELECT * FROM " .tablename($this->table_lesson_spec). " WHERE uniacid=:uniacid AND lessonid=:lessonid ORDER BY spec_sort DESC,spec_price ASC", array(':uniacid'=>$uniacid,':lessonid'=>$id));

/* 显示折扣 */
$discount_lesson = pdo_fetch("SELECT * FROM " .tablename($this->table_discount_lesson). " WHERE uniacid=:uniacid AND lesson_id=:lesson_id AND starttime<:time AND endtime>:time", array(':uniacid'=>$uniacid,':lesson_id'=>$id,':time'=>time()));
if(!empty($discount_lesson)){
	foreach($spec_list as $k=>$v){
		$spec_list[$k]['spec_price'] = round($v['spec_price']*0.01*$discount_lesson['discount'], 2);
	}
	$discount_endtime = date('Y/m/d H:i:s', $discount_lesson['endtime']);
	$diacount_price = explode('.', $spec_list[0]['spec_price']);
}

/* 赚取佣金按钮 */
$poster_config = json_decode($lesson['poster_config'], true);
$lesson_commission = unserialize($lesson['commission']);
$commission1 = $lesson_commission['commission1'];
if(empty($commission1)){
	if($member['agent_level']){
		$commission_level = pdo_get($this->table_commission_level, array('id'=>$member['agent_level']));
		$commission1 = $commission_level['commission1'];
	}else{
		$commission = unserialize($comsetting['commission']);
		$commission1 = $commission['commission1'];
	}
}
$commisson1_amount = round($commission1 * $spec_list[count($spec_list)-1]['spec_price'] * 0.01, 2);

/* 购买按钮名称 */
$buynow_info = json_decode($lesson['buynow_info'], true);
$buynow_name = $buynow_info['buynow_name'] ? $buynow_info['buynow_name'] : $config['buynow_name'];
$buynow_link = $buynow_info['buynow_link'] ? $buynow_info['buynow_link'] : $config['buynow_link'];
$study_name  = $buynow_info['study_name']  ? $buynow_info['study_name']  : $config['study_name'];
$study_link  = $buynow_info['study_link']  ? $buynow_info['study_link']  : $config['study_link'];

if($uid>0){
	/* 查询是否收藏该课程 */
	$collect = pdo_fetch("SELECT * FROM " .tablename($this->table_lesson_collect). " WHERE uniacid=:uniacid AND uid=:uid AND outid=:outid AND ctype=:ctype LIMIT 1", array(':uniacid'=>$uniacid,':uid'=>$uid,':outid'=>$id,':ctype'=>1));

	/* 查询是否购买该课程 */
	$isbuy = pdo_fetch("SELECT * FROM " .tablename($this->table_order). " WHERE uid=:uid AND lessonid=:lessonid AND status>=:status AND paytime>:paytime AND is_delete=:is_delete ORDER BY id DESC LIMIT 1", array(':uid'=>$uid,':lessonid'=>$id,':status'=>1,':paytime'=>0,':is_delete'=>0));
}
if(empty($isbuy) && $lesson['status']=='-1'){
	message("该课程已下架，您可以看看其他课程~");
}

if($uid>0){
	/* 增加会员课程足迹 */
	$history = pdo_fetch("SELECT * FROM " .tablename($this->table_lesson_history). " WHERE lessonid=:lessonid AND uid=:uid LIMIT 1", array(':lessonid'=>$id,':uid'=>$uid));
	if(empty($history)){
		$insertdata = array(
			'uniacid'  => $uniacid,
			'uid'	   => $uid,
			'lessonid' => $id,
			'addtime'  => time(),
		);
		pdo_insert($this->table_lesson_history, $insertdata);
		pdo_update($this->table_lesson_parent, array('visit_number'=>$lesson['visit_number']+1), array('id'=>$lesson['id']));
	}else{
		pdo_update($this->table_lesson_history, array('addtime'=>time()), array('lessonid'=>$id,'uid'=>$uid));
	}
}

/* 标题 */
$title = $lesson['bookname'];

/* 章节列表 */
$section_list = pdo_fetchall("SELECT id FROM " .tablename($this->table_lesson_son). " WHERE parentid=:parentid AND status=:status AND auto_show=:auto_show AND show_time<=:show_time", array(':parentid'=>$id, ':status'=>0, ':auto_show'=>1, ':show_time'=>time()));
foreach($section_list as $item){
   pdo_update($this->table_lesson_son, array('status'=>1), array('id'=>$item['id']));
}

$section_list = pdo_fetchall("SELECT * FROM " .tablename($this->table_lesson_son). " WHERE parentid=:parentid AND status=:status ORDER BY displayorder DESC, id ASC", array(':parentid'=>$id,':status'=>1));

/*课程VIP免费学习*/
$level_name = "";
if(is_array(json_decode($lesson['vipview']))){
	foreach(json_decode($lesson['vipview']) as $v){
		$level = $this->getLevelById($v);
		if(!empty($level['level_name']) && $level['is_show']==1){
			$level_name .= $level['level_name']."/";
		}
	}
	$level_name = trim($level_name, "/");
}

if($sectionid>0){
	/* 点播章节 */
	$section = pdo_fetch("SELECT * FROM " .tablename($this->table_lesson_son). " WHERE parentid=:parentid AND id=:id AND status=:status LIMIT 1", array(':parentid'=>$id,':id'=>$sectionid,':status'=>1));
}

/**
  $play  用户学习资格标识
  $plays 是否试听用户组
  $show_isbuy 显示开始学习按钮
 */
if($section['is_free']==1){
	$play = true;
	$plays = false;
}
if($lesson['price']==0){
	$play = true;
	$plays = true;
	$show_isbuy = true;
}
if($isbuy){
	if($isbuy['validity']==0){
		$play = true;
		$plays = true;
		$show_isbuy = true;
	}else{
		if($isbuy['validity']>time()){
			$play = true;
			$plays = true;
			$show_isbuy = true;
		}
	}
}
/* 讲师自己课程免费 */
$teacher = pdo_fetch("SELECT id FROM " .tablename($this->table_teacher). " WHERE uid=:uid", array(':uid'=>$uid));
if($lesson['teacherid'] == $teacher['id']){
	$play = true;
	$plays = true;
	$show_isbuy = true;
}

if($uid>0){
	/* vip免费学习课程对于普通课程生效 */
	$memberVip_list = pdo_fetchall("SELECT level_id FROM  " .tablename($this->table_member_vip). " WHERE uid=:uid AND validity>:validity", array(':uid'=>$uid,':validity'=>time()));
	if(!empty($memberVip_list)){
		foreach($memberVip_list as $v){
			if(in_array($v['level_id'], json_decode($lesson['vipview']))){
				if($lesson['lesson_type']==0){
					$play = true;
					$plays = true;
					$show_isbuy = true;
					$freeEvaluate = true; //VIP免费评价标识
					break;
				}
			}
		}
	}
}

if($sectionid>0){
	if(empty($section)){
		message("该章节不存在或已被删除！", "", "error");
	}

	if(!$play){
		message("请先购买课程后再学习！", $this->createMobileUrl('lesson', array('id'=>$id)), "warning");
	}

	/**
	 * 视频课程格式
	 * @sectiontype 1.视频章节 2.图文章节 3.音频课程 4、外链章节
	 * @savetype	0.其他存储 1.七牛存储 2.内嵌播放代码模式 3.腾讯云存储
	 */
	if(in_array($section['sectiontype'], array('1','3'))){
		if(strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone')||strpos($_SERVER['HTTP_USER_AGENT'], 'iPad')){
			$systemType = $this->checkSystenType();
		}
		if($section['savetype']==1){
			$qiniu = unserialize($setting['qiniu']);
			if($qiniu['https']==1){
				$section['videourl'] = str_replace("http://", "https://", $section['videourl']);
			}
			$section['videourl'] = $this->privateDownloadUrl($qiniu['access_key'],$qiniu['secret_key'],$section['videourl']);

		}elseif($section['savetype']==3){
			$qcloud		 = unserialize($setting['qcloud']);
			if($qcloud['https']==1){
				$section['videourl'] = str_replace("http://", "https://", $section['videourl']);
			}
			$section['videourl'] = $this->tencentDownloadUrl($qcloud, $section['videourl']);

		}elseif($section['savetype']==4){
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
		}elseif($section['savetype']==5){
			$qcloudvod = unserialize($setting['qcloudvod']);
			$newqcloudVod = new QcloudVod($qcloudvod['secret_id'], $qcloudvod['secret_key']);
			try {
				$exper = '';
				if($section['is_free'] && $section['test_time']){
					$exper = $section['test_time'];
				}
				$qcloudVodRes = $newqcloudVod->getPlaySign($qcloudvod['safety_key'], $qcloudvod['appid'], $section['videourl'], $exper);
			} catch (Exception $e) {
				message("播放失败，错误原因:".$e->getMessage(), "", "error");
			}
		}
	}
	
	if($section['sectiontype']==4){
		header("Location:".$section['videourl']);
	}
}


/* 脚部广告 */
$avd = $this->readCommonCache('fy_lesson_'.$uniacid.'_lesson_adv');
if(empty($avd)){
	$avd = pdo_fetchall("SELECT * FROM " .tablename($this->table_banner). " WHERE uniacid=:uniacid AND is_show=:is_show AND is_pc=:is_pc AND banner_type=:banner_type ORDER BY displayorder DESC", array(':uniacid'=>$uniacid,':is_show'=>1,':is_pc'=>0, 'banner_type'=>1));
	cache_write('fy_lesson_'.$uniacid.'_lesson_adv', $avd);
}
if(!empty($avd)){
	$advs = array_rand($avd,1);
	$advs = $avd[$advs];
}

/* 构造分享信息开始 */
$share_info = json_decode($lesson['share'], true);    /* 课程单独分享信息 */
$sharelesson = unserialize($comsetting['sharelesson']);  /* 全局课程分享信息 */

if(!empty($share_info['title'])){
	$sharelesson['title'] = $share_info['title'];
}else{
	if(empty($section)){
		$sharelesson['title'] = $lesson['bookname'].' - '.$setting['sitename'];
	}else{
		$sharelesson['title'] = $section['title'].' - '.$lesson['bookname'].' - '.$setting['sitename'];
	}
}
$sharelesson['desc'] = $share_info['descript'] ? $share_info['descript'] : str_replace("【课程名称】","《".$title."》",$sharelesson['title']);

$sharelesson['images'] = $share_info['images'] ? $share_info['images'] : $sharelesson['images'];
if(empty($sharelesson['images'])){
	$sharelesson['images'] = $lesson['images'];
}

$sharelesson['link'] = $_W['siteroot'] .'app/'. $this->createMobileUrl('lesson', array('id'=>$id,'sectionid'=>$sectionid,'uid'=>$uid));
/* 构造分享信息结束 */


/* 评价列表 */
$pindex =max(1,$_GPC['page']);
$psize = 10;

$evaluate_list = pdo_fetchall("SELECT a.lessonid,a.bookname,a.nickname,a.grade,a.content,a.reply,a.addtime, b.avatar FROM " .tablename($this->table_evaluate). " a LEFT JOIN " .tablename($this->table_mc_members). " b ON a.uid=b.uid WHERE a.lessonid=:lessonid AND a.status=:status ORDER BY a.addtime DESC, a.id DESC LIMIT " . ($pindex-1) * $psize . ',' . $psize, array('lessonid'=>$id,':status'=>1));
foreach($evaluate_list as $key=>$value){
	if($value['grade']==1){
		$evaluate_list[$key]['grade'] = "好评";
		$evaluate_list[$key]['ico'] = " ";
	}elseif($value['grade']==2){
		$evaluate_list[$key]['grade'] = "中评";
		$evaluate_list[$key]['ico'] = "s2";
	}elseif($value['grade']==3){
		$evaluate_list[$key]['grade'] = "差评";
		$evaluate_list[$key]['ico'] = "s3";
	}
	$evaluate_list[$key]['addtime'] = date('Y-m-d', $value['addtime']);
	if(empty($value['avatar'])){
		$evaluate_list[$key]['avatar'] = MODULE_URL."template/mobile/images/default_avatar.jpg";
	}else{
		$inc = strstr($value['avatar'], "http://") || strstr($value['avatar'], "https://");
		$evaluate_list[$key]['avatar'] = $inc ? $value['avatar'] : $_W['attachurl'].$value['avatar'];
	}
}

$total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename($this->table_evaluate) . " WHERE lessonid=:lessonid AND status=:status", array(':lessonid'=>$id,':status'=>1));

if($op=='display'){
	/* 评价开关 */
	if($isbuy['status']==1){
		$already_evaluate = pdo_fetch("SELECT id FROM " .tablename($this->table_evaluate). " WHERE uid=:uid AND lessonid=:lessonid AND orderid>:orderid ", array(':uid'=>$uid,':lessonid'=>$id,':orderid'=>0));
		if(empty($already_evaluate)){
			$allow_evaluate = true;
			$evaluate_url   = $this->createMobileUrl("evaluate",array('op'=>'display',"orderid"=>$isbuy['id']));
		}
	}else{
		/* 课程价格为免费 或 会员为VIP身份且课程权限为VIP会员免费观看 */
		if($lesson['price']==0 || $freeEvaluate){
			$already_evaluate = pdo_fetch("SELECT id FROM " .tablename($this->table_evaluate). " WHERE uid=:uid AND lessonid=:lessonid AND orderid=:orderid ", array(':uid'=>$uid,':lessonid'=>$id,':orderid'=>0));
			if(empty($already_evaluate)){
				$allow_evaluate = true;
				$evaluate_url   = $this->createMobileUrl("evaluate",array('op'=>'freeorder',"lessonid"=>$id));
			}
		}
	}
	 
	/*生成课程参数二维码*/
	$dirpath = "../attachment/images/{$uniacid}/fy_lessonv2/";
	$this->checkdir($dirpath);

	$imagepath = $dirpath."lesson_{$id}.jpg";
	if((!file_exists($imagepath) || filectime($imagepath) > 7*86400) && $userAgent){
		$codeArray = array (
			'expire_seconds' => 2592000,
			'action_name' => QR_LIMIT_STR_SCENE,
			'action_info' => array (
				'scene' => array (
					'scene_str' => "lesson_{$id}",
				),
			),
		);
		$account_api = WeAccount::create();
		//$res = $account_api->barCodeCreateFixed($codeArray);
		if(!empty($res['ticket'])){
			$qrcodeurl = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".$res['ticket'];
			$this->saveImage($qrcodeurl, $dirpath."qrcode_{$id}.", 'lesson_qrcode');
			$this->resize($dirpath."qrcode_{$id}.jpg", $dirpath."qrcode_{$id}.jpg", "170", "170", "100");
			$this->img_water_mark("../addons/fy_lessonv2/template/mobile/images/lesson-qrcode-bg.jpg", $dirpath."qrcode_{$id}.jpg", $dirpath, "lesson_{$id}.jpg", "16", "24");
			unlink($dirpath."qrcode_{$id}.jpg");
		}
	}

	/* 随机获取客服列表 */
	if($_GPC['ispay']==1 && $member['gohome']==0){
		$service = json_decode($setting['qun_service'], true);
		if(!empty($service)){
			$rand = rand(0, count($service)-1);
			$now_service = $service[$rand];
		}
	}

	if($section['sectiontype']==2 && $sectionid>0){/* 图文章节 */
		include $this->template('lesson_article');
	}else{
		include $this->template('lesson');
	}

}elseif($op=='ajaxgetlist'){
	echo json_encode($evaluate_list);
	exit();
}


?>