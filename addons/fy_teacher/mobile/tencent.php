<?php
/*
 * 腾讯云存储视频管理
 * ============================================================================
 * 版权所有 2015-2018 微课堂团队，并保留所有权利。
 * 网站地址: http://wx.haoshu888.com
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！不允许对程序代码以任何形式任何目的的再发布，作者将保留
 * 追究法律责任的权力和最终解释权。
 * ============================================================================
 */


/* 配置信息 */
$setting = pdo_fetch("SELECT qcloud FROM " .tablename($this->table_setting). " WHERE uniacid=:uniacid LIMIT 1", array(':uniacid'=>$uniacid));
/* 参数设置 */
$config = $this->module['config'];

$qcloud = unserialize($setting['qcloud']);
if(!empty($qcloud['url'])){
	$qcloud['url'] = "http://".$qcloud['url'];
}

if($config['tencent_switch'] != 1){
	message("当前系统未开启腾讯云对象存储，如有疑问，请联系管理员");
}


/* 引入腾讯云存储API接口 */
require_once(MODULE_ROOT.'/mobile/Qcloud/include.php');
$bucketName = $qcloud['bucket'];


/* 讲师信息 */
$teacher = pdo_fetch("SELECT * FROM " .tablename($this->table_teacher). " WHERE uniacid=:uniacid AND id=:id LIMIT 1", array(':uniacid'=>$uniacid, ':id'=>$_SESSION[$uniacid.'_teacher_id']));

if($op=='display'){
	$linkNav = array(
		'0'	=> array(
			'title'	=> "腾讯云存储",
			'link'	=> $this->createMobileUrl('tencent')
		)
	);

	$pindex = max(1, intval($_GPC['page']));
	$psize = 10;

	$condition = " uniacid=:uniacid AND teacherid=:teacherid ";
	$params[':uniacid'] = $uniacid;
	$params[':teacherid'] = $_SESSION[$uniacid.'_teacher_id'];
	if(!empty($_GPC['keyword'])){
		$condition .= " AND name LIKE :name ";
		$params[':name'] = "%".trim($_GPC['keyword'])."%";
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

	$list = pdo_fetchall("SELECT * FROM " .tablename($this->table_qcloud_upload). " WHERE {$condition} ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $params);

	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM " .tablename($this->table_qcloud_upload). " WHERE {$condition}", $params);
	$pager = pagination($total, $pindex, $psize);

}else if($op=='upload'){
	$linkNav = array(
		'0'	=> array(
			'title'	=> "腾讯云存储",
			'link'	=> $this->createMobileUrl('tencent')
		),
		'1'	=> array(
			'title'	=> "上传视频",
			'link'	=> $this->createMobileUrl('tencent', array('op'=>'upload'))
		),
	);

	if($teacher['upload'] !=1){
		message("抱歉，您没有上传课程的权限，如有疑问，请联系管理员!", "", "error");
	}

	if(!empty($config['starttime']) && !empty($config['endtime'])){
		if($config['starttime'] < $config['endtime']){//时间跨度为一天
			$today = date('Y-m-d ');
			$starttime = strtotime($today.$config['starttime']);
			$endtime = strtotime($today.$config['endtime']);
			if(time() < $starttime || time() > $endtime){
				message("上传时间为每天{$config['starttime']}~{$config['endtime']}");
			}
		}else{//时间跨度为两天
			$now = date('H:i');
			if($now > $config['starttime']){
				$today = date('Y-m-d ');
				$starttime = strtotime($today.$config['starttime']);
				$endtime = strtotime($today.$config['endtime'])+86400;
			}elseif($now < $config['starttime']){
				$today = date('Y-m-d ');
				$starttime = strtotime($today.$config['starttime'])-86400;
				$endtime = strtotime($today.$config['endtime']);
			}
			if(time() < $starttime || time() > $endtime){
				message("上传时间为每天{$config['starttime']}~第二天{$config['endtime']}");
			}
		}
	}

	/* 允许上传视频格式 */
	if(!empty($config['video_type']))
	$allowVideo = explode(",", $config['video_type']);
	foreach($allowVideo as $type){
		$video_type .= "video/".$type.",";
	}
	$video_type = trim($video_type,",");

	$expired = time() + 3600;
	$signature = QcloudCos\Auth::createReusableSignature($expired, $bucketName, $filepath = null, $qcloud);

}elseif($op=='saveQcloudUrl'){
	$com_name = urldecode($_GPC['com_name']);
	$sys_link = trim($_GPC['sys_link']);
	$size = trim($_GPC['size']);

	$data = array(
		'uniacid'	=> $uniacid,
		'uid'		=> $_SESSION['uid'],
		'teacherid'	=> $_SESSION[$uniacid.'_teacher_id'],
		'name'		=> str_replace("/".$_SESSION[$uniacid.'_teacher_id']."/", "", $com_name),
		'com_name'	=> $_GPC['com_name'],
		'sys_link'  => $sys_link,
		'size'		=> $size,
		'addtime'	=> time(),
	);
	pdo_insert($this->table_qcloud_upload, $data);

}elseif($op=='delQcloud'){
	$id = intval($_GPC['id']);
	$file = pdo_fetch("SELECT * FROM " .tablename($this->table_qcloud_upload). " WHERE uniacid=:uniacid AND id=:id AND teacherid=:teacherid", array(':uniacid'=>$uniacid, ':id'=>$id, ':teacherid'=>$_SESSION[$uniacid.'_teacher_id']));
	if(empty($file)){
		message("该视频文件不存在!", "", "error");
	}
	if(pdo_delete($this->table_qcloud_upload, array('id'=>$id))){
		message("删除成功!", $this->createMobileUrl('tencent'), "success");
	}else{
		message("删除失败!", "", "error");
	}
}elseif($op=='preview'){
	$linkNav = array(
		'0'	=> array(
			'title'	=> "腾讯云存储",
			'link'	=> $this->createMobileUrl('tencent')
		),
		'1'	=> array(
			'title'	=> "预览视频",
			'link'	=> $this->createMobileUrl('tencent', array('op'=>'preview','id'=>$_GPC['id']))
		),
	);

	$id = intval($_GPC['id']);
	$file = pdo_fetch("SELECT * FROM " .tablename($this->table_qcloud_upload). " WHERE uniacid=:uniacid AND id=:id AND teacherid=:teacherid", array(':uniacid'=>$uniacid, ':id'=>$id, ':teacherid'=>$_SESSION[$uniacid.'_teacher_id']));
	if(empty($file)){
		message("该视频文件不存在!", "", "error");
	}

	if(!empty($qcloud['url'])){
		$tmp_url = explode("myqcloud.com", $file['sys_link']);
		$sys_link = $qcloud['url'].$tmp_url[1];
	}

	$playurl = $this->tencentDownloadUrl($qcloud, $sys_link);
	if($qcloud['https']){
		$playurl = str_replace("http://", "https://", $playurl);
	}
}



if($op!='getSign'){
	include $this->template('tencent');
}