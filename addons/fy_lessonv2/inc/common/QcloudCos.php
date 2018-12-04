<?php

/**
 * 腾讯云对象存储(播放)签名
 * 
 */
class QcloudCos {
   /**
	* COS-v5签名 用于xml源站
	* @param string $method 请求类型 method
	* @param string $filename 文件名称
	* @return string 签名字符串
	*/
	public function getXmlSignature($SecretId, $SecretKey, $method, $access_url){
		// 文件名称
		$parse	  = parse_url($access_url);
		$filename = urldecode($parse['path']);

		// 整理参数
		$queryParams = array();
		$headers = array();
		$method = strtolower($method ? $method : 'head');
		$filename = $filename ? $filename : '/';
		substr($filename, 0, 1) != '/' && ($filename = '/' . $filename);
		// 工具方法
		function getObjectKeys($obj)
		{
			$list = array_keys($obj);
			sort($list);
			return $list;
		}
		function obj2str($obj)
		{
			$list = array();
			$keyList = getObjectKeys($obj);
			$len = count($keyList);
			for ($i = 0; $i < $len; $i++) {
				$key = $keyList[$i];
				$val = isset($obj[$key]) ? $obj[$key] : '';
				$key = strtolower($key);
				$list[] = rawurlencode($key) . '=' . rawurlencode($val);
			}
			return implode('&', $list);
		}
		// 要用到的 Authorization 参数列表
		$qSignAlgorithm = 'sha1';
		$qAk = $SecretId;
		$qSignTime = (string)(time() - 60) . ';' . (string)(time() + 5400);
		$qKeyTime = $qSignTime;
		$qHeaderList = strtolower(implode(';', getObjectKeys($headers)));
		$qUrlParamList = strtolower(implode(';', getObjectKeys($queryParams)));
		// 签名算法说明文档：https://www.qcloud.com/document/product/436/7778
		// 步骤一：计算 SignKey
		$signKey = hash_hmac("sha1", $qKeyTime, $SecretKey);
		// 步骤二：构成 FormatString
		$formatString = implode("\n", array(strtolower($method), $filename, obj2str($queryParams), obj2str($headers), ''));
		// 步骤三：计算 StringToSign
		$stringToSign = implode("\n", array('sha1', $qSignTime, sha1($formatString), ''));
		// 步骤四：计算 Signature
		$qSignature = hash_hmac('sha1', $stringToSign, $signKey);
		// 步骤五：构造 Authorization
		$authorization = implode('&', array(
			'q-sign-algorithm=' . $qSignAlgorithm,
			'q-ak=' . $qAk,
			'q-sign-time=' . $qSignTime,
			'q-key-time=' . $qKeyTime,
			'q-header-list=' . $qHeaderList,
			'q-url-param-list=' . $qUrlParamList,
			'q-signature=' . $qSignature
		));
		return $access_url.'?'.$authorization;
	}

	/**
	* COS-v4签名 用于json源站
	* @param array $qcloud 配置参数
	* @param string $access_url 视频文件地址
	*/
	public function getJsonSignature($qcloud, $access_url){
		$appid		 = $qcloud['appid'];
		$bucket		 = $qcloud['bucket'];
		$secret_id   = $qcloud['secretid'];
		$secret_key  = $qcloud['secretkey'];
		$expired	 = time() + 7200;
		$current	 = time();
		$rdm		 = rand(1000,9999);

		$srcStr = 'a='.$appid.'&b='.$bucket.'&k='.$secret_id.'&e='.$expired.'&t='.$current.'&r='.$rdm.'&f=';
		$signStr = base64_encode(hash_hmac('SHA1', $srcStr, $secret_key, true).$srcStr);
		
		return $access_url .= "?sign={$signStr}";
	}
}