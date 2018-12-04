<?php
/*
 * 修改密码
 * ============================================================================
 * 版权所有 2015-2018 微课堂团队，并保留所有权利。
 * 网站地址: http://wx.haoshu888.com
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！不允许对程序代码以任何形式任何目的的再发布，作者将保留
 * 追究法律责任的权力和最终解释权。
 * ============================================================================
 */
$config = $this->module['config'];
if($op=='display'){
	$linkNav = array(
		'0'	=> array(
			'title'	=> '修改密码',
			'link'	=> $this->createMobileUrl('account'),
		),
	);

	$teacher = pdo_fetch("SELECT * FROM " .tablename($this->table_teacher). " WHERE uniacid=:uniacid AND id=:id", array(':uniacid'=>$uniacid, ':id'=>$_SESSION[$uniacid."_teacher_id"]));

	if(checksubmit('submit')){
		$oldpass = $_GPC['mpass'];
		$newpass = $_GPC['newpass'];
		$renewpass = $_GPC['renewpass'];
		$authkey = $_W['config']['setting']['authkey'];

		if(empty($oldpass)){
			message("请输入原密码");
		}
		if(strlen($newpass)<6 || strlen($newpass)>20){
			message("新密码应介于6~20位");
		}
		if($newpass != $renewpass){
			message("两次输入的密码不一致");
		}
		if(md5($oldpass.$authkey) != $teacher['password']){
			message("原密码输入错误");
		}

		$result = pdo_update($this->table_teacher, array('password'=>md5($newpass.$authkey)), array('uniacid'=>$uniacid,'id'=>$teacher['id']));
		
		if($result){
			header("Location:".$this->createMobileUrl('logout'));
		}else{
			message("修改密码失败，请稍后重试");
		}
	}
}


include $this->template('account');