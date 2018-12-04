<?php defined('IN_IA') or exit('Access Denied');?><div class="main">
	<form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
		<div class="panel panel-default">
			<div class="panel-heading">手机端设置</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">网站名称</label>
					<div class="col-sm-9">
						<input type="text" name="sitename" value="<?php  echo $setting['sitename'];?>" class="form-control" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">网站版权</label>
					<div class="col-sm-9">
						<input type="text" name="copyright" value="<?php  echo $setting['copyright'];?>" class="form-control" />
					</div>
				</div>
				<div class="form-group hide">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">网站logo</label>
					<div class="col-sm-9">
						<?php  echo tpl_form_field_image('logo', $setting['logo']);?>
						<span class="help-block">建议尺寸64px * 64px，建议格式PNG</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">首页最近更新课程数量</label>
					<div class="col-sm-9">
						<div class="col-sm-9">
						<input type="text" name="show_newlesson" value="<?php  echo $setting['show_newlesson'];?>" class="form-control" />
						<span class="help-block">默认显示3个课程，0为关闭显示最新更新课程</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">底部自选菜单</label>
					<div class="col-sm-9">
						<label class="radio-inline"><input type="radio" name="teacherlist" value="0" <?php  if($setting['teacherlist']==0) { ?>checked<?php  } ?> /> 不显示</label>&nbsp;
						<label class="radio-inline"><input type="radio" name="teacherlist" value="1" <?php  if($setting['teacherlist']==1) { ?>checked<?php  } ?> /> 讲师列表</label>&nbsp;
						<label class="radio-inline"><input type="radio" name="teacherlist" value="2" <?php  if($setting['teacherlist']==2) { ?>checked<?php  } ?> /> VIP会员</label>&nbsp;
						<label class="radio-inline"><input type="radio" name="teacherlist" value="3" <?php  if($setting['teacherlist']==3) { ?>checked<?php  } ?> /> 优惠券</label>&nbsp;
						<span class="help-block">开启该选项后，在微课堂导航栏菜单将显示相应的菜单</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">课程详情页默认显示</label>
					<div class="col-sm-9">
						<label class="radio-inline"><input type="radio" name="lesson_show" value="0" <?php  if($setting['lesson_show']==0) { ?>checked<?php  } ?> /> 课程详情</label>&nbsp;
						<label class="radio-inline"><input type="radio" name="lesson_show" value="1" <?php  if($setting['lesson_show']==1) { ?>checked<?php  } ?> /> 课程目录</label>
						<span class="help-block">该选项为手机端用户进入课程详情页时默认显示的页面</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">购买需完善信息</label>
					<div class="col-sm-9">
						<label class="radio-inline"><input type="radio" name="mustinfo" value="0" <?php  if($setting['mustinfo']==0) { ?>checked<?php  } ?> onclick="changeUserInfo(this.value)" /> 无须</label> &nbsp;
						<label class="radio-inline"><input type="radio" name="mustinfo" value="1" <?php  if($setting['mustinfo']==1) { ?>checked<?php  } ?> onclick="changeUserInfo(this.value)"/> 必须</label>
						<label class="radio-inline"><input name="appoint_mustinfo" type="checkbox" value="1" <?php  if($setting['appoint_mustinfo']) { ?>checked<?php  } ?>> 报名课程也需要完善信息</label>
						<span class="help-block">该选项指用户在购买普通课程或开通会员VIP前是否需要完善信息，勾选“报名课程也需要完善信息”后，报名课程下单前也需要完善以下信息</span>
					</div>
				</div>
				<div class="form-group" id="user_info" <?php  if($setting['mustinfo']==0) { ?>style="display:none;"<?php  } ?>>
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">需完善信息</label>
					<div class="col-sm-9">
						<label class="radio-inline">
							<input type="checkbox" name="user_info[]" value="realname" <?php  if(in_array('realname',$user_info)) { ?>checked<?php  } ?>>姓名
						</label>
						<label class="radio-inline">
							<input type="checkbox" name="user_info[]" value="mobile" <?php  if(in_array('mobile',$user_info)) { ?>checked<?php  } ?>>手机号码
						</label>
						<label class="radio-inline">
							<input type="checkbox" name="user_info[]" value="msn" <?php  if(in_array('msn',$user_info)) { ?>checked<?php  } ?>>微信号
						</label>
						<label class="radio-inline">
							<input type="checkbox" name="user_info[]" value="occupation" <?php  if(in_array('occupation',$user_info)) { ?>checked<?php  } ?>>职业
						</label>
						<label class="radio-inline">
							<input type="checkbox" name="user_info[]" value="company" <?php  if(in_array('company',$user_info)) { ?>checked<?php  } ?>>公司
						</label>
						<label class="radio-inline">
							<input type="checkbox" name="user_info[]" value="graduateschool" <?php  if(in_array('graduateschool',$user_info)) { ?>checked<?php  } ?>>学校
						</label>
						<label class="radio-inline">
							<input type="checkbox" name="user_info[]" value="grade" <?php  if(in_array('grade',$user_info)) { ?>checked<?php  } ?>>班级
						</label>
						<label class="radio-inline">
							<input type="checkbox" name="user_info[]" value="address" <?php  if(in_array('address',$user_info)) { ?>checked<?php  } ?>>地址
						</label>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">视频播放器</label>
					<div class="col-sm-9">
						<?php  if(is_array($video_player)) { foreach($video_player as $key => $item) { ?>
						<label class="radio-inline">
							<input type="radio" name="video_player" value="<?php  echo $key;?>" <?php  if($setting['video_player']==$key) { ?>checked<?php  } ?> /> <?php  echo $item;?>
						</label>&nbsp;
						<?php  } } ?>
						<span class="help-block">视频播放器仅对其他存储、七牛云存储和腾讯云存储起作用，阿里云点播为自带的视频播放器</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">首页搜索框</label>
					<div class="col-sm-9">
						<label class="radio-inline"><input type="radio" name="search_box[isshow]" value="0" <?php  if($search_box['isshow']==0) { ?>checked<?php  } ?> onclick="changeSearch(this.value)" /> 隐藏</label> &nbsp;
						<label class="radio-inline"><input type="radio" name="search_box[isshow]" value="1" <?php  if($search_box['isshow']==1) { ?>checked<?php  } ?> onclick="changeSearch(this.value)"/> 显示</label>
					</div>
				</div>
				<div class="form-group" id="search_box" <?php  if($search_box['isshow']==0) { ?>style="display:none;"<?php  } ?>>
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">搜索框位置</label>
					<div class="col-sm-9">
						<div class="input-group">
							<span class="input-group-addon">离左侧位置</span>
							<input type="text" name="search_box[left]" value="<?php  echo $search_box['left'];?>" class="form-control">
							<span class="input-group-addon">px</span>
							<span class="input-group-addon">离顶部位置</span>
							<input type="text" name="search_box[top]" value="<?php  echo $search_box['top'];?>" class="form-control">
							<span class="input-group-addon">px</span>
						</div>
					</div>
				</div>

				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">自定义菜单①</label>
					<div class="col-sm-9">
						<div class="input-group">
							<span class="input-group-addon">名称</span>
							<input type="text" name="diy_name[]" value="<?php  echo $self_diy[0]['diy_name'];?>" class="form-control">
							<span class="input-group-addon">链接</span>
							<input type="text" name="diy_link[]" value="<?php  echo $self_diy[0]['diy_link'];?>" placeholder="以http://或https://开头" class="form-control">
							<span class="input-group-addon">图标</span>
							<input type="text" name="diy_image[]" value="<?php  echo $self_diy[0]['diy_image'];?>" class="form-control">
						</div>
						<span class="help-block">
							自定义菜单将显示在手机端“个人中心”里<br/>
							图标请参考<a href="http://fontawesome.dashgame.com/" target="_blank" style="color:#0f7cda;">Font Awesome图标集</a>，例如"address-book"、"bandcamp"
						</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">自定义菜单②</label>
					<div class="col-sm-9">
						<div class="input-group">
							<span class="input-group-addon">名称</span>
							<input type="text" name="diy_name[]" value="<?php  echo $self_diy[1]['diy_name'];?>" class="form-control">
							<span class="input-group-addon">链接</span>
							<input type="text" name="diy_link[]" value="<?php  echo $self_diy[1]['diy_link'];?>" placeholder="以http://或https://开头" class="form-control">
							<span class="input-group-addon">图标</span>
							<input type="text" name="diy_image[]" value="<?php  echo $self_diy[1]['diy_image'];?>" class="form-control">
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">自定义菜单③</label>
					<div class="col-sm-9">
						<div class="input-group">
							<span class="input-group-addon">名称</span>
							<input type="text" name="diy_name[]" value="<?php  echo $self_diy[2]['diy_name'];?>" class="form-control">
							<span class="input-group-addon">链接</span>
							<input type="text" name="diy_link[]" value="<?php  echo $self_diy[2]['diy_link'];?>" placeholder="以http://或https://开头" class="form-control">
							<span class="input-group-addon">图标</span>
							<input type="text" name="diy_image[]" value="<?php  echo $self_diy[2]['diy_image'];?>" class="form-control">
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">自定义菜单④</label>
					<div class="col-sm-9">
						<div class="input-group">
							<span class="input-group-addon">名称</span>
							<input type="text" name="diy_name[]" value="<?php  echo $self_diy[3]['diy_name'];?>" class="form-control">
							<span class="input-group-addon">链接</span>
							<input type="text" name="diy_link[]" value="<?php  echo $self_diy[3]['diy_link'];?>" placeholder="以http://或https://开头" class="form-control">
							<span class="input-group-addon">图标</span>
							<input type="text" name="diy_image[]" value="<?php  echo $self_diy[3]['diy_image'];?>" class="form-control">
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
function changeUserInfo(status){
	if(status==1){
		document.getElementById("user_info").style.display = "block";
	}else{
		document.getElementById("user_info").style.display = "none";
	}
}
function changeSearch(status){
	if(status==1){
		document.getElementById("search_box").style.display = "block";
	}else{
		document.getElementById("search_box").style.display = "none";
	}
}
</script>