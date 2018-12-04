<?php
/*
 * 腾讯云点播
 * ============================================================================
 * 版权所有 2015-2018 微课堂团队，并保留所有权利。
 * 网站地址: http://wx.haoshu888.com
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！不允许对程序代码以任何形式任何目的的再发布，作者将保留
 * 追究法律责任的权力和最终解释权。
 * ============================================================================
 */


/* 配置信息 */
$setting = pdo_fetch("SELECT qcloudvod FROM " .tablename($this->table_setting). " WHERE uniacid=:uniacid LIMIT 1", array(':uniacid'=>$uniacid));
/* 参数设置 */
$config = $this->module['config'];
$qcloudvod = unserialize($setting['qcloudvod']);
$newqcloudVod = new QcloudVod($qcloudvod['secret_id'], $qcloudvod['secret_key']);

if($config['qcloud_switch'] != 1){
	message("当前系统未开启腾讯云点播，如有疑问，请联系管理员");
}

/* 讲师信息 */
$teacher = pdo_fetch("SELECT * FROM " .tablename($this->table_teacher). " WHERE uniacid=:uniacid AND id=:id LIMIT 1", array(':uniacid'=>$uniacid, ':id'=>$_SESSION[$uniacid.'_teacher_id']));

if($op=='display'){
	$linkNav = array(
		'0'	=> array(
			'title'	=> "腾讯云点播",
			'link'	=> $this->createMobileUrl('qcloudvod')
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

	$list = pdo_fetchall("SELECT * FROM " .tablename($this->table_qcloudvod_upload). " WHERE {$condition} ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $params);

	foreach($list as $key=>$value){
		$tmp = explode('.', $value['name']);
		$list[$key]['suffix'] = strtolower($tmp[count($tmp)-1]);
	}

	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM " .tablename($this->table_qcloudvod_upload). " WHERE {$condition}", $params);
	$pager = pagination($total, $pindex, $psize);

}else if($op=='upload'){
	$linkNav = array(
		'0'	=> array(
			'title'	=> "腾讯云点播",
			'link'	=> $this->createMobileUrl('qcloudvod')
		),
		'1'	=> array(
			'title'	=> "上传视频",
			'link'	=> $this->createMobileUrl('qcloudvod', array('op'=>'upload'))
		),
	);

	if($teacher['upload'] !=1){
		message("抱歉，您没有上传课程的权限，如有疑问，请联系管理员!", "", "error");
	}

	if(!empty($config['starttime']) && !empty($config['endtime'])){
		if($config['starttime'] < $config['endtime']){
			//时间跨度为一天
			$today = date('Y-m-d ');
			$starttime = strtotime($today.$config['starttime']);
			$endtime = strtotime($today.$config['endtime']);
			if(time() < $starttime || time() > $endtime){
				message("上传时间为每天{$config['starttime']}~{$config['endtime']}");
			}
		}else{
			//时间跨度为两天
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

}elseif($op=='getUploadInfo'){
	$signature = $newqcloudVod->getUploadSign();

	$data = array(
		'signature' => $signature
	);

	echo json_encode($data);
	exit();

}elseif($op=='saveVideo'){
	$data = array(
		'uniacid'	=> $uniacid,
		'uid'		=> '',
		'teacherid'	=> $_SESSION[$uniacid.'_teacher_id'],
		'name'		=> trim($_GPC['filename']),
		'videoid'	=> trim($_GPC['videoid']),
		'videourl'	=> trim($_GPC['videourl']),
		'size'		=> $_GPC['size'],
		'addtime'	=> time(),
	);
	$res = pdo_insert($this->table_qcloudvod_upload, $data);
	if($res){
		echo '写入数据库成功';
	}else{
		pdo_debug();
	}
	exit();

}elseif($op=='delVideo'){
	$id = intval($_GPC['id']);
	$file = pdo_fetch("SELECT * FROM " .tablename($this->table_qcloudvod_upload). " WHERE uniacid=:uniacid AND id=:id AND teacherid=:teacherid", array(':uniacid'=>$uniacid, ':id'=>$id, 'teacherid'=>$_SESSION[$uniacid.'_teacher_id']));
	if(empty($file)){
		message("该文件不存在!", "", "error");
	}

	$paramArray = array(
		'Action' => 'DeleteVodFile',
		'fileId' => $file['videoid'],
		'priority' => 0,
	);

	$deleteUrl = $newqcloudVod->generateUrl($paramArray, 'GET', 'vod.api.qcloud.com', '/v2/index.php');
	$response = $this->http_request($deleteUrl);
	$res = json_decode($response, true);
	if($res['code']=='0'){
		if(pdo_delete($this->table_qcloudvod_upload, array('id'=>$id))){
			message("删除成功", $this->createMobileUrl('qcloudvod'), "success");
		}else{
			message("删除失败，请稍后重试", "", "error");
		}
	}else{
		message('删除失败，腾讯云点播返回信息：'.$res['message']);
	}

}elseif($op=='preview'){
	$linkNav = array(
		'0'	=> array(
			'title'	=> "腾讯云点播",
			'link'	=> $this->createMobileUrl('qcloudvod')
		),
		'1'	=> array(
			'title'	=> "预览视频",
			'link'	=> $this->createMobileUrl('qcloudvod', array('op'=>'preview','id'=>$_GPC['id']))
		),
	);

	$id = intval($_GPC['id']);
	$file = pdo_fetch("SELECT * FROM " .tablename($this->table_qcloudvod_upload). " WHERE uniacid=:uniacid AND id=:id AND teacherid=:teacherid", array(':uniacid'=>$uniacid, ':id'=>$id, ':teacherid'=>$_SESSION[$uniacid.'_teacher_id']));

	if(empty($file)){
		message("该文件不存在!", "", "error");
	}

	try {
		$res = $newqcloudVod->getPlaySign($qcloudvod['safety_key'], $qcloudvod['appid'], $file['videoid'], $exper);
	} catch (Exception $e) {
		message("播放失败，错误原因:".$e->getMessage(), "", "error");
	}
}

include $this->template('qcloudVod');