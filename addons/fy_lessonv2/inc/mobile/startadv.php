<?php
/**
 * 开屏广告
 * ============================================================================
 * 版权所有 2015-2018 微课堂团队，并保留所有权利。
 * 网站地址: https://wx.haoshu888.com
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！不允许对程序代码以任何形式任何目的的再发布，作者将保留
 * 追究法律责任的权力和最终解释权。
 */


/* 开屏广告 */
$avd = $this->readCommonCache('fy_lesson_'.$uniacid.'_start_adv');
if(empty($avd)){
	$avd = pdo_fetchall("SELECT * FROM " .tablename($this->table_banner). " WHERE uniacid=:uniacid AND is_show=:is_show AND is_pc=:is_pc AND banner_type=:banner_type ORDER BY displayorder DESC", array(':uniacid'=>$uniacid,':is_show'=>1,':is_pc'=>0, 'banner_type'=>3));
	cache_write('fy_lesson_'.$uniacid.'_start_adv', $avd);
}
if(!empty($avd)){
	$advs = array_rand($avd,1);
	$advs = $avd[$advs];
}else{
	header("Location:".$this->createMobileUrl('index',array('uid'=>$_GPC['uid'],'t'=>time())));
}


include $this->template('startadv');

?>