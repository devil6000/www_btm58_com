<?php defined('IN_IA') or exit('Access Denied');?><div class="main">
	<form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
		<div class="panel panel-default">
			<div class="panel-heading">全局设置</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">非微信端访问</label>
					<div class="col-sm-9">
						<label class="radio-inline"><input type="radio" name="visit_limit" value="1" <?php  if($setting['visit_limit']==1) { ?>checked<?php  } ?> /> 允许</label> &nbsp;
						<label class="radio-inline"><input type="radio" name="visit_limit" value="0" <?php  if($setting['visit_limit']==0) { ?>checked<?php  } ?> /> 禁止</label>
						<span class="help-block">开启非微信端访问后，用户可在浏览器里访问微课堂，<strong style="color:#609dd2;">但目前QQ浏览器未开放禁止下载按钮API，所以可能造成客户缓存下载您的视频</strong></span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">需登录访问页面</label>
					<div class="col-sm-9">
						<label class="radio-inline"><input type="checkbox" name="login_visit[]" value="index" <?php  if(in_array('index', $login_visit)) { ?>checked<?php  } ?> /> 首页</label>&nbsp;
						<label class="radio-inline"><input type="checkbox" name="login_visit[]" value="lesson" <?php  if(in_array('lesson', $login_visit)) { ?>checked<?php  } ?> /> 课程详情页</label>&nbsp;
						<label class="radio-inline"><input type="checkbox" name="login_visit[]" value="search" <?php  if(in_array('search', $login_visit)) { ?>checked<?php  } ?> /> 全部课程</label>&nbsp;
						<label class="radio-inline"><input type="checkbox" name="login_visit[]" value="getcoupon" <?php  if(in_array('getcoupon', $login_visit)) { ?>checked<?php  } ?> /> 优惠券</label>&nbsp;
						<span class="help-block">选中的前台页面，用户在手机端需要先登录帐号才能访问</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">开启课程库存</label>
					<div class="col-sm-9">
						<label class="radio-inline"><input type="radio" name="stock_config" value="1" <?php  if($setting['stock_config']==1) { ?>checked<?php  } ?> /> 开启</label> &nbsp;
						<label class="radio-inline"><input type="radio" name="stock_config" value="0" <?php  if($setting['stock_config']==0) { ?>checked<?php  } ?> /> 关闭</label>
						<span class="help-block"><strong></strong>开启课程库存后，库存为0的课程将显示为“已售罄”且不能购买</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">引导关注公众号</label>
					<div class="col-sm-9">
						<label class="radio-inline"><input type="radio" name="isfollow" value="1" <?php  if($setting['isfollow']==1) { ?>checked<?php  } ?> /> 顶部引导关注</label> &nbsp;
						<label class="radio-inline"><input type="radio" name="isfollow" value="2" <?php  if($setting['isfollow']==2) { ?>checked<?php  } ?> /> 顶部和课程页关注</label> &nbsp;
						<label class="radio-inline"><input type="radio" name="isfollow" value="0" <?php  if($setting['isfollow']==0) { ?>checked<?php  } ?> /> 关闭</label>
						<span class="help-block">该项仅在微信客户端中生效，未关注公众号的用户访问时，“顶部引导关注”为顶部引导关注；<strong>“顶部和课程页关注”为顶部引导关注和课程详情页强制关注，该方式请在“系统—公众号应用—订阅管理”里开启微课堂V2的订阅器</strong></span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">顶部关注提示语</label>
					<div class="col-sm-9">
						<input type="text" name="follow_word" value="<?php  echo $setting['follow_word'];?>" class="form-control" />
						<span class="help-block">建议不超过12个汉字，包括标点符号</span>
					</div>
				</div>

				<div class="form-group">
					<label class="col-sm-2 control-label">课程页关注设置</label>
					<div class="col-sm-9">
						<div class="input-group">
							<span class="input-group-addon">关注标题</span>
							<input type="text" name="lesson_follow_title" value="<?php  echo $setting['lesson_follow_title'];?>" class="form-control" />
							<span class="input-group-addon">关注描述</span>
							<input type="text" name="lesson_follow_desc" value="<?php  echo $setting['lesson_follow_desc'];?>" class="form-control" />
						</div>
						<span class="help-block">
							1、课程页强制关注公众号，关注标题默认为“长按指纹识别二维码，关注公众号”；<br/>
							2、关注描述默认为“关注后继续学习，可接收最新课程通知”
						</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">公众号二维码</label>
					<div class="col-sm-9">
						<?php  echo tpl_form_field_image('qrcode', $setting['qrcode']);?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">首页全部分类图标</label>
					<div class="col-sm-9">
						<?php  echo tpl_form_field_image('category_ico', $setting['category_ico']);?>
						<span class="help-block">不上传全部分类图标，则首页不显示。<a href="<?php echo MODULE_URL;?>/template/mobile/images/ico-allcategory.png" target="_blank" style="color:blue;">点击查看默认图标</a></span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">会员推广海报</label>
					<div class="col-sm-9">
						<?php  echo tpl_form_field_image('posterbg', $setting['posterbg']);?>
						<span class="help-block">宽度:640 px，高度:940 px，处理技巧：从PS导出图片时，要导出为WEB所用的jpg图片格式(快捷键是：ctrl+shift+alt+s)，将会大大减少图片所占内存大小，提高用户打开速度！&nbsp;&nbsp;&nbsp;<a href="http://wx.haoshu888.com/sucai/posterbg.psd" style="color:blue;">模版海报下载</a></span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">海报头像</label>
					<div class="col-sm-10">
						<div class="input-group">
							<span class="input-group-addon">离左侧距离</span>
							<input type="text" name="poster_config[avatar_left]" value="<?php  echo $poster['avatar_left'];?>" class="form-control">
							<span class="input-group-addon">px</span>
							<span class="input-group-addon">离顶部距离</span>
							<input type="text" name="poster_config[avatar_top]" value="<?php  echo $poster['avatar_top'];?>" class="form-control">
							<span class="input-group-addon">px</span>
						</div>
						<span class="help-block">使用默认的海报设置请留空</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">海报二维码</label>
					<div class="col-sm-10">
						<div class="input-group">
							<span class="input-group-addon">离左侧距离</span>
							<input type="text" name="poster_config[qrcode_left]" value="<?php  echo $poster['qrcode_left'];?>" class="form-control">
							<span class="input-group-addon">px</span>
							<span class="input-group-addon">离顶部距离</span>
							<input type="text" name="poster_config[qrcode_top]" value="<?php  echo $poster['qrcode_top'];?>" class="form-control">
							<span class="input-group-addon">px</span>
						</div>
						<span class="help-block">使用默认的海报设置请留空</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">海报用户昵称</label>
					<div class="col-sm-10">
						<div class="input-group">
							<span class="input-group-addon">离左侧距离</span>
							<input type="text" name="poster_config[nickname_left]" value="<?php  echo $poster['nickname_left'];?>" class="form-control">
							<span class="input-group-addon">px</span>
							<span class="input-group-addon">离顶部距离</span>
							<input type="text" name="poster_config[nickname_top]" value="<?php  echo $poster['nickname_top'];?>" class="form-control">
							<span class="input-group-addon">px</span>
							<span class="input-group-addon">字体大小</span>
							<input type="text" name="poster_config[nickname_fontsize]" value="<?php  echo $poster['nickname_fontsize'];?>" class="form-control">
							<span class="input-group-addon">px</span>
							<span class="input-group-addon">字体颜色</span>
							<input type="text" name="poster_config[nickname_fontcolor]" value="<?php  echo $poster['nickname_fontcolor'];?>" class="form-control" style="width:100px;">
						</div>
						<span class="help-block">使用默认的海报设置请留空，字体颜色请使用十六进制颜色值，例如：#056D9F</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">海报显示项</label>
					<div class="col-sm-9">
						<label class="radio-inline"><input type="checkbox" name="poster_config[avatar_show]" value="1" <?php  if($poster['avatar_show']==1) { ?>checked<?php  } ?> /> 显示头像</label>&nbsp;
						<label class="radio-inline"><input type="checkbox" name="poster_config[nickname_show]" value="1" <?php  if($poster['nickname_show']==1) { ?>checked<?php  } ?> /> 显示昵称</label>
						<span class="help-block">选中的显示项将显示在会员海报里</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">管理员openid</label>
					<div class="col-sm-9">
						<input type="text" name="manageopenid" value="<?php  echo $setting['manageopenid'];?>" class="form-control">
						<div class="help-block">
							新订单和新提现申请(到微信零钱且提现为人工审核方式)提醒管理员，多个管理员openid之间用英文状态逗号“,”隔开
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">自动计划任务</label>
					<div class="col-sm-9">
						<div class="input-group">
							<span class="input-group-addon">订单关闭时间</span>
							<input type="text" name="closespace" value="<?php  echo $setting['closespace'];?>" class="form-control">
							<span class="input-group-addon">分钟</span>
							<span class="input-group-addon">订单自动好评</span>
							<input type="text" name="autogood" value="<?php  echo $setting['autogood'];?>" class="form-control">
							<span class="input-group-addon">天</span>
						</div>
						<span class="help-block">
							1、订单关闭时间默认为60分钟，0表示不自动关闭未付款订单；<br/>
							2、订单自动好评，购买课程成功后超过指定天数自动好评，0或留空表示关闭自动好评
						</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">课程页讲师分成</label>
					<div class="col-sm-9">
						<label class="radio-inline"><input type="radio" name="show_teacher_income" value="1" <?php  if($setting['show_teacher_income']==1) { ?>checked<?php  } ?> /> 显示</label> &nbsp;
						<label class="radio-inline"><input type="radio" name="show_teacher_income" value="0" <?php  if($setting['show_teacher_income']==0) { ?>checked<?php  } ?> /> 不显示</label>
						<span class="help-block"><strong></strong>默认显示，后台发布课程时，是否显示讲师分成</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">审核课程评价</label>
					<div class="col-sm-9">
						<label class="radio-inline"><input type="radio" name="audit_evaluate" value="1" <?php  if($setting['audit_evaluate']==1) { ?>checked<?php  } ?> /> 开启</label> &nbsp;
						<label class="radio-inline"><input type="radio" name="audit_evaluate" value="0" <?php  if($setting['audit_evaluate']==0) { ?>checked<?php  } ?> /> 关闭</label>
						<span class="help-block"><strong></strong>默认关闭，开启后用户评价课程需要后台审核后显示在前台课程评价里</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">首页绑定手机</label>
					<div class="col-sm-9">
						<label class="radio-inline"><input type="radio" name="modify_mobile" value="2" <?php  if($setting['modify_mobile']==2) { ?>checked<?php  } ?> /> 首页验证</label> &nbsp;
						<label class="radio-inline"><input type="radio" name="modify_mobile" value="0" <?php  if($setting['modify_mobile']==0) { ?>checked<?php  } ?> /> 关闭</label>
						<span class="help-block"><strong></strong>开启首页验证后，进入首页时，未绑定手机号码用户将提示绑定手机号码；该选项需要使用阿里短信验证</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">首页验证选项</label>
					<div class="col-sm-9">
						<label class="radio-inline">
							<input type="checkbox" name="index_verify[]" value="password" <?php  if(in_array('password', $index_verify)) { ?>checked<?php  } ?>>密码
						</label>
						&nbsp;
						<label class="radio-inline">
							<input type="checkbox" name="index_verify[]" value="verify_mobile" <?php  if(in_array('verify_mobile', $index_verify)) { ?>checked<?php  } ?>>不显示关闭按钮
						</label>
						<span class="help-block">开启首页验证绑定手机号码时，可以设置是否可以密码和关闭绑定手机号码对话框</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">定时任务链接</label>
					<div class="col-sm-9">
						<a href="javascript:;" id="copyCrontab"><?php  echo $_W['siteroot'];?>app/<?php  echo str_replace("./","",$this->createMobileUrl('crontab'));?></a>
						<span class="help-block">建议定时每3分钟执行一次；定时任务用于取消超期未支付、未评价订单、会员过期优惠券等，您可以设置服务器定时执行该URL来操作定时任务。</span>
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
require(['jquery', 'util'], function($, util){
	$(function(){
		util.clip($("#copyCrontab")[0], $("#copyCrontab").text());
	});
});
</script>