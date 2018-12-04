<?php
/*
 * 登录操作
 * ============================================================================
 * 版权所有 2015-2018 微课堂团队，并保留所有权利。
 * 网站地址: http://wx.haoshu888.com
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！不允许对程序代码以任何形式任何目的的再发布，作者将保留
 * 追究法律责任的权力和最终解释权。
 * ============================================================================
 */

/* 已登录则跳转到主页 标记 */
if(!empty($_SESSION[$uniacid.'_teacher_id'])){
	header("Location:{$this->createMobileUrl('index')}");
}

if(checksubmit('submit')){
	$account  = trim($_GPC['account']);
	$password = $_GPC['password'];
	$code = trim($_GPC['code']);
	if(empty($account) || empty($password)){
		message('请输入登陆账号和密码！', '', 'error');
	}

	if(!$this->codeVerify($code)){
		message('验证码错误，请重新输入！', '', 'error');
	}

	/* 查询用户信息 */
	$teacher = pdo_fetch("SELECT * FROM " . tablename($this->table_teacher) . " WHERE uniacid=:uniacid AND account=:account", array(':uniacid'=>$uniacid,':account'=>$account));
	$hash = md5($password . $_W['config']['setting']['authkey']);

	/*验证用户密码*/
	if($teacher['password'] != $hash){
		message('登陆帐号或密码错误！', $this->createMobileUrl('login'), 'error');
	}

	/* 检查用户账号状态 */
	if(!in_array($teacher['status'], array('1','3'))){
		message('抱歉，该讲师未通过审核！', $this->createMobileUrl('login'), 'error');
	}

	load()->model('mc');
	$user = mc_fetch($teacher['uid'], array('avatar'));

	/*设置登录SESSION*/
	session_start();
	$_SESSION[$uniacid.'_teacher_id']	   = $teacher['id'];
	$_SESSION[$uniacid.'_teacher_account'] = $teacher['account'];
	$_SESSION[$uniacid.'_teacher_avatar']  = $user['avatar'];

	header("Location:{$this->createMobileUrl('index')}");
}

include $this->template('login');