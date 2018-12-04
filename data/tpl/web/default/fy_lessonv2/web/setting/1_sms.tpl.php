<?php defined('IN_IA') or exit('Access Denied');?><div class="main">
	<form method="post" class="form-horizontal form" enctype="multipart/form-data">
        <div class="panel panel-default">
            <div class="panel-heading">
                短信配置
            </div>
            <div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">短信接口</label>
					<div class="col-sm-9">
						<label class="radio-inline"><input type="radio" name="dayu_sms[versions]" value="1" <?php  if($sms['versions']==1) { ?>checked<?php  } ?> /> 阿里云短信</label>
					</div>
				</div>

				<!-- 阿里短信 -->
				<div class="aliyun-sms" <?php  if($sms['versions']!=1) { ?>style="display:none;"<?php  } ?>>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">Access Key ID	</label>
						<div class="col-sm-9">
							<input type="text" name="dayu_sms[access_key]" class="form-control" value="<?php  echo $sms['access_key'];?>">
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">Access Key Secret	</label>
						<div class="col-sm-9">
							<input type="text" name="dayu_sms[access_secret]" class="form-control" value="<?php  echo $sms['access_secret'];?>">
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">短信签名</label>
						<div class="col-sm-9">
							<input type="text" name="dayu_sms[sign]" class="form-control" value="<?php  echo $sms['sign'];?>">
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">短信验证码模版ID</label>
						<div class="col-sm-9">
							<input type="text" name="dayu_sms[verify_code]" class="form-control" value="<?php  echo $sms['verify_code'];?>">
							<span class="help-block">如果您不希望验证手机号码，请不要输入模版ID。<br/>短信验证码模版应包含变量：${code}，例如：您的短信验证码是:${code}，请不要告诉任何人。</span>
						</div>
					</div>	
				</div>
            </div>
        </div>
        <div class="form-group col-sm-12">
            <input type="hidden" name="id" value="<?php  echo $setting['id'];?>" />
            <input type="submit" name="submit" value="保存设置" class="btn btn-primary col-lg-1" />
            <input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
        </div>
	</form>
</div>

<script type="text/javascript">
$(function() {
	$(':radio[name="dayu_sms[versions]"]').click(function() {
		if($(this).val() == '1') {
			$(".aliyun-sms").show();
		}
	});
});
</script>