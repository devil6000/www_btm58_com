<?php
/**
 * 微课堂首页
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
if((!empty($login_visit) && in_array('index', $login_visit)) || $userAgent){
	checkauth();
}

/* 开屏广告 */
$avd = $this->readCommonCache('fy_lesson_'.$uniacid.'_start_adv');
if(empty($avd)){
	$avd = pdo_fetchall("SELECT * FROM " .tablename($this->table_banner). " WHERE uniacid=:uniacid AND is_show=:is_show AND is_pc=:is_pc AND banner_type=:banner_type ORDER BY displayorder DESC", array(':uniacid'=>$uniacid,':is_show'=>1,':is_pc'=>0, 'banner_type'=>3));
	cache_write('fy_lesson_'.$uniacid.'_start_adv', $avd);
}
if(!empty($avd) && !$_GPC['t']){
	header("Location:".$this->createMobileUrl('startadv', array('uid'=>$_GPC['uid'])));
}


/* 粉丝信息 */
$fans = pdo_fetch("SELECT follow FROM " .tablename($this->table_fans). " WHERE uid=:uid", array(':uid'=>$_W['member']['uid']));

/* 分享设置 */
load()->model('mc');
$sharelink = unserialize($comsetting['sharelink']);
$shareurl = $_W['siteroot'] .'app/'. $this->createMobileUrl('index', array('uid'=>$_W['member']['uid']));

/* 会员信息 */
$uid = $_W['member']['uid'];
$member = pdo_fetch("SELECT mobile,salt FROM " .tablename($this->table_mc_members). " WHERE uid=:uid", array(':uid'=>$uid));

/* 焦点图 */
$banner = $this->readCommonCache('fy_lesson_'.$uniacid.'_index_banner');
if(empty($banner)){
	$banner = pdo_fetchall("SELECT * FROM " .tablename($this->table_banner). " WHERE uniacid=:uniacid AND is_show=:is_show AND is_pc=:is_pc AND banner_type=:banner_type ORDER BY displayorder DESC", array(':uniacid'=>$uniacid,':is_show'=>1,':is_pc'=>0, 'banner_type'=>0));
	cache_write('fy_lesson_'.$uniacid.'_index_banner', $banner);
}

/* 搜索框 */
$search_box = json_decode($setting['search_box'], true);

/* 绑定手机号码是否显示密码 */
$index_verify = json_decode($setting['index_verify'], true);

/* 文章公告 */
$articlelist = pdo_fetchall("SELECT id,title,addtime FROM " .tablename($this->table_article). " WHERE uniacid=:uniacid  AND isshow=:isshow ORDER BY displayorder DESC,id DESC", array(':uniacid'=>$uniacid,':isshow'=>1));

/* 课程分类 */
if(!empty($setting['category_ico'])){
	$allCategoryIco = $_W['attachurl'].$setting['category_ico'];
	$cat_num = 9;
}else{
	$allCategoryIco = "";
	$cat_num = 10;
}
$category_list = $this->readCommonCache('fy_lesson_'.$uniacid.'_index_category');
if(empty($category_list)){
	//$category_list = pdo_fetchall("SELECT * FROM " .tablename($this->table_category). " WHERE uniacid=:uniacid AND parentid=:parentid AND is_show=:is_show ORDER BY displayorder DESC LIMIT {$cat_num}", array(':uniacid'=>$uniacid,':parentid'=>0,':is_show'=>1));
    $category_list = pdo_fetchall("SELECT * FROM " .tablename($this->table_category). " WHERE uniacid=:uniacid AND parentid=:parentid AND is_show=:is_show ORDER BY displayorder DESC LIMIT 2", array(':uniacid'=>$uniacid,':parentid'=>0,':is_show'=>1));
    cache_write('fy_lesson_'.$uniacid.'_index_category', $category_list);
}

/* 限时折扣 */
$discount_banner = $this->readCommonCache('fy_lesson_'.$uniacid.'_index_discount_banner');
if(empty($discount_banner)){
	$discount_banner = pdo_fetchall("SELECT * FROM " .tablename($this->table_banner). " WHERE uniacid=:uniacid AND is_show=:is_show AND is_pc=:is_pc AND banner_type=:banner_type ORDER BY displayorder DESC", array(':uniacid'=>$uniacid,':is_show'=>1,':is_pc'=>0, 'banner_type'=>2));
	cache_write('fy_lesson_'.$uniacid.'_index_discount_banner', $discount_banner);
}

/* 同时购 */
$meanwhile_banner = $this->readCommonCache('fy_lesson_' . $uniacid . '_index_meanwhile_banner');
if(empty($meanwhile_banner)){
    $meanwhile = pdo_fetchall('SELECT * FROM ' . tablename($this->table_lesson_meanwhile) . ' WHERE uniacid=:uniacid ORDER BY displayorder DESC, id DESC LIMIT 2', array(':uniacid' => $uniacid));
    if($meanwhile){
        foreach($meanwhile as $key => $item){
            $meanwhile_banner[$key]['meanwhile'] = $item;
            $meanwhile_banner[$key]['list'] = pdo_fetchall('SELECT p.id,p.bookname,p.price,c.name,s.spec_price,m.spec_id FROM ' . tablename($this->table_meanwhile_lesson) . ' m LEFT JOIN ' . tablename($this->table_lesson_parent) . ' p ON m.lesson_id=p.id LEFT JOIN ' . tablename($this->table_category) . ' c ON p.pid=c.id LEFT JOIN ' . tablename($this->table_lesson_spec) . ' s ON m.spec_id=s.spec_id WHERE m.meanwhileid=:id', array(':id' => $item['id']));
        	foreach($meanwhile_banner[$key]['list'] as & $meanwhile_banner_list){
			    $lessonorder = pdo_fetch("SELECT * FROM " . tablename($this->table_order) . " WHERE status>0 and status<2 and uid=:uid and is_delete=0 and lesson_ids like :ids and validity>=:time LIMIT 1", array(':uid'=>$uid,':ids' => '%,' . $meanwhile_banner_list['id'] . ',%', ':time' => time()));
        		if(!empty($lessonorder)){
        			$meanwhile_banner[$key]['buystatus'] = 1;
        			$meanwhile_banner_list['buystatus'] = 1;
        		}
        	}
        	unset($meanwhile_banner_list);
        }
        cache_write('fy_lesson_'.$uniacid.'_index_meanwhile_banner', $meanwhile_banner);
    }
}
else{
	foreach($meanwhile_banner as $key => $item){
       	foreach($item['list'] as & $meanwhile_banner_list){
       		
		    $lessonorder = pdo_fetch("SELECT * FROM " . tablename($this->table_order) . " WHERE status>0 and status<2 and uid=:uid and is_delete=0 and lesson_ids like :ids and validity>=:time LIMIT 1", array(':uid'=>$uid,':ids' => '%,' . $meanwhile_banner_list['id'] . ',%', ':time' => time()));
       		if(!empty($lessonorder)){
       			$meanwhile_banner[$key]['buystatus'] = 1;
       			$meanwhile_banner_list['buystatus'] = 1;
       		}
       	}
       	unset($meanwhile_banner_list);
    }
}

/* 最新课程 */
if($setting['show_newlesson']){
	$newlesson = $this->readCommonCache('fy_lesson_'.$uniacid.'_index_newlesson');
	if(empty($newlesson)){
		$newlesson = pdo_fetchall("SELECT id,bookname,price,images,buynum,virtual_buynum,visit_number,section_status,update_time,difficulty FROM " .tablename($this->table_lesson_parent). " WHERE uniacid=:uniacid AND status=:status AND ico_name=:icon ORDER BY update_time DESC LIMIT 0,{$setting['show_newlesson']}", array(':uniacid'=>$uniacid, ':status'=>1, ':icon' => 'ico-new'));
		foreach($newlesson as $k=>$v){
			$newlesson[$k]['tran_time'] = $this->tranTime($v['update_time']);
			$newlesson[$k]['section'] = pdo_fetch("SELECT title FROM " .tablename($this->table_lesson_son). " WHERE parentid=:parentid ORDER BY id DESC LIMIT 0,1", array(':parentid'=>$v['id']));
		}
		cache_write('fy_lesson_'.$uniacid.'_index_newlesson', $newlesson);
	}
}

/* 热门好课 */
$hotlesson = $this->readCommonCache('fy_lesson_' . $uniacid . '_index_hotlesson');
if(empty($hotlesson)){
    $hotlesson = pdo_fetchall('SELECT * FROM ' . tablename($this->table_lesson_parent) . ' WHERE uniacid=:uniacid AND status=1 AND ico_name=:ico ORDER BY id DESC LIMIT 0,5', array(':uniacid' => $uniacid,':ico' => 'ico-hot'));
    cache_write('fy_lesson_' . $uniacid . '_index_hotlesson', $hotlesson);
}

/* 板块课程 */
$list = $this->readCommonCache('fy_lesson_'.$uniacid.'_index_recommend');
if(empty($list)){
	$list = pdo_fetchall("SELECT id AS recid,rec_name,show_style,limit_number FROM " .tablename($this->table_recommend). " WHERE uniacid=:uniacid AND is_show=:is_show ORDER BY displayorder DESC,id DESC", array(':uniacid'=>$uniacid,':is_show'=>1));
	foreach($list as $key=>$rec){
		$list[$key]['lesson'] = pdo_fetchall("SELECT * FROM " .tablename($this->table_lesson_parent). " WHERE uniacid='{$uniacid}' AND status=1 AND (recommendid='{$rec['recid']}' OR (recommendid LIKE '{$rec['recid']},%') OR (recommendid LIKE '%,{$rec['recid']}') OR (recommendid LIKE '%,{$rec['recid']},%')) ORDER BY displayorder DESC, id DESC LIMIT ".$rec['limit_number']);
		foreach($list[$key]['lesson'] as $k=>$val){
			$list[$key]['lesson'][$k]['count'] = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename($this->table_lesson_son) . " WHERE parentid=:parentid ", array(':parentid'=>$val['id']));
			if($val['ico_name']=='ico-vip' && (!empty($val['vipview']) && $val['vipview']!='null')){
				$list[$key]['lesson'][$k]['ico_name'] = 'ico-vip';
			}
		}
		if(empty($list[$key]['lesson'])){
			unset($list[$key]);
		}
	}
	cache_write('fy_lesson_'.$uniacid.'_index_recommend', $list);
}


/* 绑定手机号码 */
if(checksubmit('modify_mobile')){
	$data = array();

	$data['mobile'] = trim($_GPC['mobile']);
	if(empty($data['mobile'])){
		message("请输入您的手机号码");
	}
	if(!(preg_match("/1\d{10}/",$data['mobile']))){
		message("您输入的手机号码格式有误");
	}
	$exist = pdo_fetch("SELECT uid FROM " .tablename($this->table_mc_members). " WHERE uniacid=:uniacid AND mobile=:mobile", array(':uniacid'=>$uniacid,':mobile'=>$data['mobile']));
	if(!empty($exist) && $member['mobile']!=$data['mobile']){
		message("该手机号码已存在，请重新输入其他手机号码");
	}

	$mobile_code = trim($_GPC['verify_code']);
	if(empty($mobile_code)){
		message("请输入的短信验证码");
	}
	if($mobile_code != $_SESSION['mobile_code']){
		message("短信验证码错误");
	}

	if(in_array('password', $index_verify)){
		if(empty($_GPC['pwd1'])){
			message("请输入登录密码");
		}
		if($_GPC['pwd1'] != $_GPC['pwd2']){
			message("两次密码不一致");
		}

		$data['password'] = md5($_GPC['pwd1'] . $member['salt'] . $_W['config']['setting']['authkey']);
	}

	if(pdo_update($this->table_mc_members, $data, array('uid'=>$uid))){
		cache_build_memberinfo($uid);
		/* 销毁短信验证码 */
		unset($_SESSION['mobile_code']);
		message("绑定手机成功", $this->createMobileUrl('index'), "success");
	}
}

include $this->template('index');

?>