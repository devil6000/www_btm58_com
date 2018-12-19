<?php
/**
 * 个人中心
 * ============================================================================
 * 版权所有 2015-2018 微课堂团队，并保留所有权利。
 * 网站地址: https://wx.haoshu888.com
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！不允许对程序代码以任何形式任何目的的再发布，作者将保留
 * 追究法律责任的权力和最终解释权。
 */

checkauth();

$title = "个人中心";

$common = json_decode($setting['common'], true);
$self_item = $common['self_item'];

$self_diy = unserialize($setting['self_diy']);
$memberid = $_W['member']['uid'];

/* 学号 */
$tmpno = '';
for($i=0;$i<7-strlen($memberid);$i++){
	$tmpno .= 0;
}
$studentno = $tmpno.$memberid;

/* 课程会员信息 */
$lessonmember = pdo_fetch("SELECT * FROM " .tablename($this->table_member). " WHERE uid=:uid", array(':uid'=>$memberid));
/* 自动更新会员头像，24小时检查一次 */
if(time() - $lessonmember['uptime'] > 86400){
	if(!empty($_W['fans']['tag']['avatar'])){
		pdo_update($this->table_mc_members, array('avatar'=>$_W['fans']['tag']['avatar']), array('uid'=>$memberid));
		pdo_update($this->table_member, array('uptime'=>time()), array('uid'=>$memberid));
	}
}

$memberinfo = pdo_fetch("SELECT uid,mobile,credit1,credit2,nickname,avatar FROM " .tablename($this->table_mc_members). " WHERE uid=:uid LIMIT 1", array(':uid'=>$memberid));

if(empty($memberinfo['avatar'])){
	$avatar = MODULE_URL."template/mobile/images/default_avatar.jpg";
}else{
	$inc = strstr($memberinfo['avatar'], "http://") || strstr($memberinfo['avatar'], "https://");
	$avatar = $inc ? $memberinfo['avatar'] : $_W['attachurl'].$memberinfo['avatar'];
}

/* 已学习的课程 */
$member_learn = pdo_fetchcolumn("SELECT COUNT(id) FROM" . tablename($this->table_mystudy) . " WHERE uniacid=:uniacid AND uid=:uid", array(':uniacid' => $uniacid, ':uid' => $memberid));

/* VIP等级数量 */
$memberListCount = pdo_fetchcolumn("SELECT COUNT(*) FROM " .tablename($this->table_vip_level). " WHERE uniacid=:uniacid AND is_show=:is_show", array(':uniacid'=>$uniacid,':is_show'=>1));

/* 已购VIP数量 */
$memberVipCount = pdo_fetchcolumn("SELECT COUNT(*) FROM " .tablename($this->table_member_vip). " WHERE uid=:uid AND validity>:validity", array(':uid'=>$memberid,':validity'=>time()));

/* 检查会员是否讲师身份 */
$teacher = pdo_fetch("SELECT id FROM " .tablename($this->table_teacher). " WHERE uid=:uid", array(':uid'=>$memberid));

/* 关注的课程数量 */
$collect_lesson = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename($this->table_lesson_collect) . " WHERE uid=:uid AND ctype=:ctype", array(':uid'=>$memberid, ':ctype'=>1));

/* 关注的讲师数量 */
$collect_teacher = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename($this->table_lesson_collect) . " WHERE uid=:uid AND ctype=:ctype", array(':uid'=>$memberid, ':ctype'=>2));

/* 机构名下讲师数量 */
$company_teachers = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename($this->table_teacher) . " WHERE uniacid=:uniacid AND company_uid=:company_uid", array('uniacid'=>$uniacid,':company_uid'=>$memberid));

/* 检查是否在微信中访问 */
$userAgent = $this->checkUserAgent();
$agent = $userAgent ? 1 : 0;

/* 手动更新会员头像 */
if($_GPC['updateInfo']){
	$fans = pdo_fetch("SELECT openid FROM " .tablename($this->table_fans). " WHERE uid=:uid", array(':uid'=>$memberid));
	if(!empty($fans['openid'])){
		load()->classs('weixin.account');
		$accObj = WeixinAccount::create($_W['acid']);
		$access_token = $accObj->fetch_token();

		$url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$access_token."&openid=".$fans['openid']."&lang=zh_CN";
        $output = ihttp_get($url);
		$res = json_decode($output['content'], true);
		if($res['subscribe']==0){
			message("请关注公众号后重试", $this->createMobileUrl('follow'), "error");
		}
		$data = array(
			'nickname' => $res['nickname'],
			'avatar'   => $res['headimgurl']
		);
		if(pdo_update($this->table_mc_members, $data, array('uid'=>$memberid))){
			message("更新成功", $this->createMobileUrl('self'), "success");
		}else{
			message("更新失败，请稍后重试", $this->createMobileUrl('self'), "error");
		}
	}
}



//include $this->template('new_self');

include $this->template('member_center');

?>