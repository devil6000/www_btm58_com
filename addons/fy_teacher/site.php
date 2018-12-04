<?php
/*
 * 微课堂讲师模块微站定义
 * ============================================================================
 * 版权所有 2015-2017 风影随行，并保留所有权利。
 * 网站地址: http://www.haoshu888.com
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！不允许对程序代码以任何形式任何目的的再发布，作者将保留
 * 追究法律责任的权力和最终解释权。
 * ============================================================================
 */
defined('IN_IA') or exit('Access Denied');
include_once dirname(__FILE__).'/../fy_lessonv2/inc/common/TypeStatus.php';
/* 引入阿里云点播API接口 */
include_once(dirname(__FILE__).'/../fy_lessonv2/inc/common/AliyunVod.php');
/* 引入腾讯云点播API接口 */
include_once(dirname(__FILE__).'/../fy_lessonv2/inc/common/QcloudVod.php');

class Fy_teacherModuleSite extends WeModuleSite {
	
	public $table_aliyun_upload		= 'fy_lesson_aliyun_upload';
	public $table_cashlog			= 'fy_lesson_cashlog';
    public $table_category			= 'fy_lesson_category';
	public $table_commission_level  = 'fy_lesson_commission_level';
	public $table_commission_log	= 'fy_lesson_commission_log';
	public $table_commission_setting = 'fy_lesson_commission_setting';
	public $table_evaluate			= 'fy_lesson_evaluate';
	public $table_member			= 'fy_lesson_member';
	public $table_member_order		= 'fy_lesson_member_order';
	public $table_order				= 'fy_lesson_order';
	public $table_lesson_parent		= 'fy_lesson_parent';
	public $table_playrecord		= 'fy_lesson_playrecord';
	public $table_qcloudvod_upload  = 'fy_lesson_qcloudvod_upload';
	public $table_qiniu_upload		= 'fy_lesson_qiniu_upload';
	public $table_qcloud_upload		= 'fy_lesson_qcloud_upload';
	public $table_recommend			= 'fy_lesson_recommend';
	public $table_setting			= 'fy_lesson_setting';
	public $table_lesson_son		= 'fy_lesson_son';
	public $table_lesson_spec		= 'fy_lesson_spec';
	public $table_teacher			= 'fy_lesson_teacher';
	public $table_teacher_income	= 'fy_lesson_teacher_income';
	public $table_vip_level			= 'fy_lesson_vip_level';
	public $table_mc_members		= 'mc_members';
	public $table_lesson_praxis     = 'fy_lesson_praxis';

/*****************************   WEB方法  *********************************/
	/* 课程审核 */
	public function doWebLesson(){
		message("正在前往微课堂V2课程审核", "./index.php?c=site&a=entry&m=fy_lessonv2&do=lesson&status=2", "success");
	}
	/* 讲师审核 */
	public function doWebTeacher(){
		message("正在前往微课堂V2讲师审核", "./index.php?c=site&a=entry&m=fy_lessonv2&do=teacher&status=2", "success");
	}

/***************************** Mobile方法 *********************************/
	
	/* 课程管理 */
	public function doMobileIndex() {
		$this->__mobile(__FUNCTION__);
	}

	/* 订单管理 */
	public function doMobileOrder() {
		$this->__mobile(__FUNCTION__);
	}

	/* 收入管理 */
	public function doMobileIncome() {
		$this->__mobile(__FUNCTION__);
	}

	/* 评价管理 */	
	public function doMobileEvaluate() {
		$this->__mobile(__FUNCTION__);
	}

	/* 七牛云对象存储 */
	public function doMobileQiniu() {
		$this->__mobile(__FUNCTION__);
	}

	/* 腾讯云对象存储 */
	public function doMobileTencent() {
		$this->__mobile(__FUNCTION__);
	}

	/* 阿里云点播 */
	public function doMobileAliyunvod() {
		$this->__mobile(__FUNCTION__);
	}

	/* 腾讯云点播 */
	public function doMobileQcloudvod() {
		$this->__mobile(__FUNCTION__);
	}

	/* 个人设置 */
	public function doMobileAccount() {
		$this->__mobile(__FUNCTION__);
	}

	/* 帐号登陆 */
    public function doMobilelogin() {
		$this->__mobile(__FUNCTION__);
    }

	/* 注销登录 */
    public function doMobilelogout() {
		$this->__mobile(__FUNCTION__);
	}

	/* 生成验证码 */
	public function doMobileCode(){
		error_reporting(0);
		load()->classs('captcha');
		session_start();

		$captcha = new Captcha();
		$captcha->build(140, 43);
		$hash = md5(strtolower($captcha->phrase));
		isetcookie('__code', $hash);
		$_SESSION['__code'] = $hash;

		$captcha->output();
	}

/************************************************ 公共方法 *************************************/
	public function __web($f_name){
		global $_W,$_GPC;
		$uniacid = $_W['uniacid'];
		$op = $operation = $_GPC['op'] ? $_GPC['op'] : 'display';
		$versions = "2.5.8";

		include_once  'web/'.strtolower(substr($f_name,5)).'.php';
	}
	
	public function __mobile($f_name){
		global $_W,$_GPC;
		$uniacid = $_W['uniacid'];
		$op = $operation = $_GPC['op'] ? $_GPC['op'] : 'display';
		$teacher_avatar = $_SESSION[$uniacid.'_teacher_avatar'];
		$versions = "2.5.8";
		
		if(!in_array(strtolower(substr($f_name,8)), array('login','logout'))){
			if(empty($_SESSION[$uniacid.'_teacher_id'])){
				header("Location:".$this->createMobileUrl('login'));
			}
		}

		include_once  'mobile/'.strtolower(substr($f_name,8)).'.php';

	}

	/* 验证登录验证码 */
	private function codeVerify($code) {
		global $_W, $_GPC;
		session_start();
		$codehash = md5(strtolower($code));
		if (!empty($_GPC['__code']) && $codehash == $_SESSION['__code']) {
			$return = true;
			$_SESSION['__code'] = '';
			isetcookie('__code', '');
		} else {
			$return = false;
		}
		
		return $return;
	}

	/* Ajax上传图片 */
	public function doMobileAjaxUploadImage(){
		global $_W;

		load()->func('file');
		$res = file_upload($_FILES['uploadFile']);

		if (!empty($_W['setting']['remote']['type'])) {
			$remotestatus = file_remote_upload($res['path']);
			if (is_error($remotestatus)) {
				message('远程附件上传失败，请检查配置并重新上传');
			}
		}

		echo json_encode($res);
	}

	/*
     * 企业付款接口
     */
    private function companyPay($post, $fans) {
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        $account = $_W['account'];
        $setting = pdo_fetch("SELECT mchid,mchkey,serverIp FROM " . tablename($this->table_setting) . " WHERE uniacid='{$uniacid}'");
        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';
        $pars = array();
        $pars['mch_appid'] = $account['key']; /* 公众账号appid */
        $pars['mchid'] = $setting['mchid'];   /* 商户号 */
        $pars['nonce_str'] = random(32);   /* 随机字符串 */
        $pars['partner_trade_no'] = $setting['mchid'] . date('Ymd') . rand(1000000000, 9999999999); /* 商户订单号 */
        $pars['openid'] = $fans['openid'];   /* 用户openid */
        $pars['check_name'] = 'NO_CHECK';   /* 校验用户姓名选项，不校验 */
        $pars['re_user_name'] = $fans['nickname'];   /* 收款用户姓名 */
        $pars['amount'] = $post['total_amount'] * 100; /* 付款金额，单位：分 */
        $pars['desc'] = $post['desc'];    /* 企业付款描述信息 */
        $pars['spbill_create_ip'] = $setting['serverIp'] ? $setting['serverIp'] : $_SERVER["SERVER_ADDR"]; /* Ip地址 */

        ksort($pars, SORT_STRING);
        $string1 = '';
        foreach ($pars as $k => $v) {
            $string1 .= "{$k}={$v}&";
        }

        $string1 .= "key={$setting['mchkey']}";
        $pars['sign'] = strtoupper(md5($string1));
        $xml = '<xml>';
        foreach ($pars as $k => $v) {
            $xml .= "<{$k}>{$v}</{$k}>";
        }
        $xml .= '</xml>';

        $extras = array();
        $extras['CURLOPT_CAINFO'] = '../addons/fy_lesson/cert//rootca' . $uniacid . '.pem';
        $extras['CURLOPT_SSLCERT'] = '../addons/fy_lesson/cert/apiclient_cert' . $uniacid . '.pem';
        $extras['CURLOPT_SSLKEY'] = '../addons/fy_lesson/cert/apiclient_key' . $uniacid . '.pem';
        load()->func('communication');

        $resp = ihttp_request($url, $xml, $extras);
        $tmp = str_replace("<![CDATA[", "", $resp['content']);
        $tmp = str_replace("]]>", "", $tmp);
        $tmp = simplexml_load_string($tmp);
        $result = json_decode(json_encode($tmp), TRUE);

        return $result;
    }

	/*
     * 使用七牛云存储防盗链
     * $accessKey
     * $secretKey
     * $baseUrl
     */

    private function qiniuDownloadUrl($accessKey, $secretKey, $baseUrl, $expires = 3600) {
        $deadline = time() + $expires;

        $pos = strpos($baseUrl, '?');
        if ($pos !== false) {
            $baseUrl .= '&e=';
        } else {
            $baseUrl .= '?e=';
        }
        $baseUrl .= $deadline;
        $hmac = hash_hmac('sha1', $baseUrl, $secretKey, true);
        $find = array('+', '/');
        $replace = array('-', '_');
        $hmac = str_replace($find, $replace, base64_encode($hmac));

        $token = $accessKey . ':' . $hmac;
        return "$baseUrl&token=$token";
    }

	private function tencentDownloadUrl($qcloud, $access_url) {
		$appid		 = $qcloud['appid'];
		$bucket		 = $qcloud['bucket'];
		$secret_id   = $qcloud['secretid'];
		$secret_key  = $qcloud['secretkey'];
		$expired	 = time() + 7200;
		$onceExpired = 0;
		$current	 = time();
		$rdm		 = rand();
		$userid		 = "0";
		$explode	 = explode("/", $access_url);
		$tmp		 = array_reverse($explode);
		$fileid		 = "/".$appid."/".$bucket."/".$tmp[0];

		$srcStr = 'a='.$appid.'&b='.$bucket.'&k='.$secret_id.'&e='.$expired.'&t='.$current.'&r='.$rdm.'&f=';
		$srcStrOnce = 'a='.$appid.'&b='.$bucket.'&k='.$secret_id.'&e='.$onceExpired .'&t='.$current.'&r='.$rdm.'&f='.$fileid;
		$signStr = base64_encode(hash_hmac('SHA1', $srcStr, $secret_key, true).$srcStr);
		
		return $access_url .= "?sign={$signStr}";
	}

	/* 获取基本设置参数 */
	private function getSetting(){
		global $_W;
		return pdo_fetch("SELECT * FROM " .tablename($this->table_setting). " WHERE uniacid=:uniacid", array(':uniacid'=>$_W['uniacid']));
	}

	/* 获取分销设置参数 */
	private function getComsetting(){
		global $_W;
		return pdo_fetch("SELECT * FROM " .tablename($this->table_commission_setting). " WHERE uniacid=:uniacid", array(':uniacid'=>$_W['uniacid']));
	}

	/* https请求（支持GET和POST） */
    private function http_request($url, $messageDatas = null) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($messageDatas)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $messageDatas);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }

	private function pagination($tcount, $pindex, $psize = 10, $url = '', $context = array('before' => 3, 'after' => 3)){
		global $_W;
		$pdata = array(
			'tcount' => 0,
			'tpage'  => 0,
			'cindex' => 0,
			'findex' => 0,
			'pindex' => 0,
			'nindex' => 0,
			'lindex' => 0,
			'options' => ''
		);

		$pdata['tcount'] = $tcount;
		$pdata['tpage'] = ceil($tcount / $psize);
		if($pdata['tpage'] <= 1) {
			return '';
		}
		$cindex = $pindex;
		$cindex = min($cindex, $pdata['tpage']);
		$cindex = max($cindex, 1);
		$pdata['cindex'] = $cindex;
		$pdata['findex'] = 1;
		$pdata['pindex'] = $cindex > 1 ? $cindex - 1 : 1;
		$pdata['nindex'] = $cindex < $pdata['tpage'] ? $cindex + 1 : $pdata['tpage'];
		$pdata['lindex'] = $pdata['tpage'];

		if($url) {
			$pdata['faa'] = 'href="?' . str_replace('*', $pdata['findex'], $url) . '"';
			$pdata['paa'] = 'href="?' . str_replace('*', $pdata['pindex'], $url) . '"';
			$pdata['naa'] = 'href="?' . str_replace('*', $pdata['nindex'], $url) . '"';
			$pdata['laa'] = 'href="?' . str_replace('*', $pdata['lindex'], $url) . '"';
		} else {
			$_GET['page'] = $pdata['findex'];
			$pdata['faa'] = 'href="' . $_W['script_name'] . '?' . http_build_query($_GET) . '"';
			$_GET['page'] = $pdata['pindex'];
			$pdata['paa'] = 'href="' . $_W['script_name'] . '?' . http_build_query($_GET) . '"';
			$_GET['page'] = $pdata['nindex'];
			$pdata['naa'] = 'href="' . $_W['script_name'] . '?' . http_build_query($_GET) . '"';
			$_GET['page'] = $pdata['lindex'];
			$pdata['laa'] = 'href="' . $_W['script_name'] . '?' . http_build_query($_GET) . '"';
		}


		$html = '<div class="pagelist">';
		if($pdata['cindex'] > $pdata['tpage'] || $pdata['cindex']==1) {
			$html .= "<span style=\"margin-right:4px;\">首页</span>";
			$html .= "<span>上一页</span>";
		}else{
			$html .= "<a {$pdata['faa']} class=\"pager-nav\">首页</a>";
			$html .= "<a {$pdata['paa']} class=\"pager-nav\">上一页</a>";
		}

		if($context['after'] != 0 && $context['before'] != 0) {
			$range = array();
			$range['start'] = max(1, $pdata['cindex'] - $context['before']);
			$range['end'] = min($pdata['tpage'], $pdata['cindex'] + $context['after']);
			if ($range['end'] - $range['start'] < $context['before'] + $context['after']) {
				$range['end'] = min($pdata['tpage'], $range['start'] + $context['before'] + $context['after']);
				$range['start'] = max(1, $range['end'] - $context['before'] - $context['after']);
			}
			for ($i = $range['start']; $i <= $range['end']; $i++) {
				if($url) {
					$aa = 'href="?' . str_replace('*', $i, $url) . '"';
				} else {
					$_GET['page'] = $i;
					$aa = 'href="?' . http_build_query($_GET) . '"';
				}
				$html .= ($i == $pdata['cindex'] ? '<span class="current">' . $i . '</span>' : "<a {$aa}>" . $i . '</a>');
			}
		}

		if($pdata['cindex'] >= $pdata['tpage']) {
			$html .= "<span style=\"margin-right:4px;\">下一页</span>";
			$html .= "<span>尾页</span>";
		}else{
			$html .= "<a {$pdata['naa']} class=\"pager-nav\">下一页</a>";
			$html .= "<a {$pdata['laa']} class=\"pager-nav\">尾页</a>";
		}
		$html .= '</div>';
		return $html;
	}

}
?>