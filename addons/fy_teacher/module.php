<?php
/**
 * 微课堂讲师模块定义
 *
 * @author 风影随行
 * @url http://www.haoshu888.com
 */
defined('IN_IA') or exit('Access Denied');

class Fy_teacherModule extends WeModule {

	public function settingsDisplay($settings) {
		global $_W, $_GPC;
		$upload_max = ini_get('upload_max_filesize');
		if(checksubmit()) {
			$dat = array(
				'starttime'		 => str_replace("：", ":", trim($_GPC['starttime'])),
				'endtime'		 => str_replace("：", ":", trim($_GPC['endtime'])),
				'qiniu_switch'   => intval($_GPC['qiniu_switch']),
				'tencent_switch' => intval($_GPC['tencent_switch']),
				'aliyun_switch'  => intval($_GPC['aliyun_switch']),
				'qcloud_switch'  => intval($_GPC['qcloud_switch']),
				'commission_switch'		=> intval($_GPC['commission_switch']),
				'evaluate_audit_switch' => intval($_GPC['evaluate_audit_switch']),
				'video_type'	 => str_replace("，", ",", trim($_GPC['video_type'])),
				'upload_max'     => intval($_GPC['upload_max']),
			);
			
			$this->saveSettings($dat);
			message("保存成功", refresh, 'success');
		}

		include $this->template('setting');
	}

}