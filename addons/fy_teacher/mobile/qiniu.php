<?php
/*
 * 七牛云存储视频管理
 * ============================================================================
 * 版权所有 2015-2018 微课堂团队，并保留所有权利。
 * 网站地址: http://wx.haoshu888.com
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！不允许对程序代码以任何形式任何目的的再发布，作者将保留
 * 追究法律责任的权力和最终解释权。
 * ============================================================================
 */

/* 配置信息 */
$setting = pdo_fetch("SELECT savetype,qiniu FROM " .tablename($this->table_setting). " WHERE uniacid=:uniacid LIMIT 1", array(':uniacid'=>$uniacid));
$qiniu = unserialize($setting['qiniu']);
if(!empty($qiniu['url'])){
	$qiniu['url'] = "http://".str_replace("http://","",$qiniu['url'])."/";
}

$config = $this->module['config'];
if($config['qiniu_switch'] != 1){
	message("当前系统未开启七牛云对象存储，如有疑问，请联系管理员");
}

/* 引入七牛云存储API接口 */
require_once(MODULE_ROOT.'/mobile/Qiniu/autoload.php');

/* 讲师信息 */
$teacher = pdo_fetch("SELECT * FROM " .tablename($this->table_teacher). " WHERE uniacid=:uniacid AND id=:id LIMIT 1", array(':uniacid'=>$uniacid, ':id'=>$_SESSION[$uniacid.'_teacher_id']));

if($op=='display'){
	$linkNav = array(
		'0'	=> array(
			'title'	=> "视频管理",
			'link'	=> $this->createMobileUrl('qiniu')
		)
	);

	$pindex = max(1, intval($_GPC['page']));
	$psize = 8;

	$condition = " uniacid=:uniacid AND teacher=:teacher ";
	$params[':uniacid'] = $uniacid;
	$params[':teacher'] = $teacher['id'];
	if(!empty($_GPC['keyword'])){
		$condition .= " AND name LIKE :name ";
		$params[':name'] = "%".trim($_GPC['keyword'])."%";
	}

	$list = pdo_fetchall("SELECT * FROM " .tablename($this->table_qiniu_upload). " WHERE {$condition} ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $params);

	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM " .tablename($this->table_qiniu_upload). " WHERE {$condition}", $params);
	$pager = $this->pagination($total, $pindex, $psize);

}else if($op=='upload'){
	$linkNav = array(
		'0'	=> array(
			'title'	=> "视频管理",
			'link'	=> $this->createMobileUrl('qiniu')
		),
		'1'	=> array(
			'title'	=> "上传视频",
			'link'	=> $this->createMobileUrl('qiniu', array('op'=>'upload'))
		),
	);

	if($teacher['upload'] !=1){
		message("抱歉，您没有上传课程的权限，如有疑问，请联系管理员!", "", "error");
	}

	$upload_domain = $config['upload_domain'];
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

	$auth = new Qiniu\Auth($qiniu['access_key'], $qiniu['secret_key']);
	$token = $auth->uploadToken($qiniu['bucket']);

}elseif($op=='saveFileUrl'){
	$data = array(
		'uniacid'	=> $uniacid,
		'uid'		=> $teacher['uid'],
		'openid'	=> $teacher['openid'],
		'teacher'	=> $teacher['id'],
		'name'		=> trim($_GPC['name']),
		'com_name'	=> trim($_GPC['com_name']),
		'qiniu_url' => $qiniu['url'].trim($_GPC['com_name']),
		'size'		=> intval($_GPC['size']),
		'addtime'	=> time(),
	);
	pdo_insert($this->table_qiniu_upload, $data);

}elseif($op=='delFile'){
	$id = intval($_GPC['id']);
	$file = pdo_fetch("SELECT * FROM " .tablename($this->table_qiniu_upload). " WHERE id=:id AND teacher=:teacher", array(':id'=>$id, ':teacher'=>$teacher['id']));
	if(empty($file)){
		message("该文件不存在!");
	}
	if(pdo_delete($this->table_qiniu_upload, array('id'=>$id))){
		message("删除成功!", $this->createMobileUrl('qiniu'), "success");
	}else{
		message("删除失败!");
	}
}elseif($op=='preview'){
	$linkNav = array(
		'0'	=> array(
			'title'	=> "七牛云存储",
			'link'	=> $this->createMobileUrl('qiniu')
		),
		'1'	=> array(
			'title'	=> "预览视频",
			'link'	=> $this->createMobileUrl('qiniu', array('op'=>'preview','id'=>$_GPC['id']))
		),
	);

	$id = intval($_GPC['id']);
	$file = pdo_fetch("SELECT * FROM " .tablename($this->table_qiniu_upload). " WHERE uniacid=:uniacid AND id=:id AND teacher=:teacher", array(':uniacid'=>$uniacid, ':id'=>$id, ':teacher'=>$_SESSION[$uniacid.'_teacher_id']));
	if(empty($file)){
		message("该视频文件不存在!", "", "error");
	}


	if(!empty($qiniu['url'])){
		$videourl = $qiniu['url'].$file['com_name'];
	}
	$playurl = $this->qiniuDownloadUrl($qiniu['access_key'], $qiniu['secret_key'], $videourl);
	if($qiniu['https']){
		$playurl = str_replace("http://", "https://", $playurl);
	}
	
}

include $this->template('qiniu');