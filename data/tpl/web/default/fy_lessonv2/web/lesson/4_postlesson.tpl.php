<?php defined('IN_IA') or exit('Access Denied');?><link href="<?php echo MODULE_URL;?>template/web/style/lessonTab/lesson-tab.css?v=<?php  echo $versions;?>" rel="stylesheet">
<script type="text/javascript" src="<?php echo MODULE_URL;?>template/web/style/lessonTab/prefixfree.min.js?v=<?php  echo $versions;?>"></script>
<div class="main">
	<form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
		<div class="tab-group">
			<section id="tab1" title="基本信息" class="lesson-tab-section">
				<div class="panel-body">
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style="color:red;font-weight:bolder;">*</span>课程类型</label>
						<div class="col-sm-9">
							<label class="radio-inline"><input type="radio" name="lesson_type" value="0" <?php  if(empty($lesson) || $lesson['lesson_type'] == 0) { ?>checked="true"<?php  } ?>/> 普通课程</label>
							&nbsp;&nbsp;
							<label class="radio-inline"><input type="radio" name="lesson_type" value="1" <?php  if($lesson['lesson_type'] == 1) { ?>checked="true"<?php  } ?>/> 报名课程</label>
							&nbsp;&nbsp;
							<label class="radio-inline"><input type="radio" name="lesson_type" value="2" <?php  if($lesson['lesson_type'] == 2) { ?>checked="true"<?php  } ?> /> 小程序审核课程</label>
							<span class="help-block">
								普通课程为音频、视频以及图文等课程；报名课程下单时需要一起提交相关信息；<br/>
								小程序审核课程是专门展示给微信审核员查看的课程，请设置小程序审核课程全部为图文章节且	
							</span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style="color:red;font-weight:bolder;">*</span>课程名称</label>
						<div class="col-sm-9">
							<input type="text" name="bookname" class="form-control" value="<?php  echo $lesson['bookname'];?>" />
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label"><span style="color:red;font-weight:bolder;">*</span>课程分类</label>
						<div class="col-sm-8 col-xs-12">
							<div class="row row-fix tpl-category-container">
								<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
									<select class="form-control" id="category_parent" name="pid" onchange="renderCategory(this.value)">
										<option value="0">请选择一级分类</option>
										<?php  if(is_array($category)) { foreach($category as $item) { ?>
										<option value="<?php  echo $item['id'];?>"><?php  echo $item['name'];?></option>
										<?php  } } ?>
									</select>
								</div>
								<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
									<select class="form-control" id="category_child" name="cid">
										<option value="0">请选择二级分类</option>
									</select>
								</div>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style="color:red;font-weight:bolder;">*</span>讲师名称</label>
						<div class="col-sm-3">
							<select name="teacherid" class="form-control">
								<option value="">请选择...</option>
								<?php  if(is_array($teacher_list)) { foreach($teacher_list as $teacher) { ?>
								<option value="<?php  echo $teacher['id'];?>" <?php  if($teacher['id']==$lesson['teacherid']) { ?>selected<?php  } ?>><?php  echo $teacher['first_letter'];?>-<?php  echo $teacher['teacher'];?></option>
								<?php  } } ?>
							</select>
						</div>
					</div>
					<?php  if($setting['show_teacher_income']) { ?>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style="color:red;font-weight:bolder;">*</span>讲师分成</label>
						<div class="col-sm-9">
							<div class="input-group">
								<input type="text" name="teacher_income" class="form-control" value="<?php  echo $lesson['teacher_income'];?>" />
								<span class="input-group-addon">%</span>
							</div>
							<div class="help-block">讲师分成 = 课程售价 x 讲师分成百分比</div>
						</div>
					</div>
					<?php  } ?>
					<?php  if($setting['company_income']) { ?>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style="color:red;font-weight:bolder;">*</span>机构分成</label>
						<div class="col-sm-9">
							<div class="input-group">
								<input type="text" name="company_income" class="form-control" value="<?php  echo $lesson['company_income'];?>" />
								<span class="input-group-addon">%</span>
							</div>
							<div class="help-block">机构分成 = 课程售价 x 机构分成百分比</div>
						</div>
					</div>
					<?php  } ?>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style="color:red;font-weight:bolder;">*</span>课程封面</label>
						<div class="col-sm-9">
							<?php  echo tpl_form_field_image('images', $lesson['images'])?>
							<span class="help-block">建议尺寸 600 * 350px，也可根据自己的实际情况做图片尺寸</span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">免费学习等级</label>
						<div class="col-xs-9 col-sm-9" style="margin-top: 7px;">
						   <?php  if(is_array($level_list)) { foreach($level_list as $key => $level) { ?>
								<input type="checkbox" name="vipview[]" value="<?php  echo $level['id'];?>" <?php  if(in_array($level['id'],$vipview)) { ?>checked<?php  } ?>><?php  echo $level['level_name'];?>&nbsp;&nbsp;
								<?php  if(($key+1)%4==0) { ?><br/><?php  } ?>
						   <?php  } } ?>
						   <span class="help-block">选中的VIP等级级别会员可免费学习该课程</span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">课程难度</label>
						<div class="col-sm-9">
							<input type="text" name="difficulty" class="form-control" value="<?php  echo $lesson['difficulty'];?>" />
							<div class="help-block">例如：入门篇、进阶篇、高级篇</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">排序</label>
						<div class="col-sm-9">
							<input type="text" name="displayorder" class="form-control" value="<?php  echo $lesson['displayorder'];?>" />
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">开始学习</label>
						<div class="col-sm-9">
							<div class="input-group">
								<span class="input-group-addon">自定义名称</span>
								<input type="text" name="buynow_info[study_name]" value="<?php  echo $buynow_info['study_name'];?>" class="form-control">
								<span class="input-group-addon">网页链接</span>
								<input type="text" name="buynow_info[study_link]" value="<?php  echo $buynow_info['study_link'];?>" class="form-control" placeholder="含http://或https://">
							</div>
							<span class="help-block">默认请留空，课程详情页右下角“开始学习”自定义名称，显示优先级：此处设置名称 > 参数设置里[开始学习] > 默认名称[开始学习]</span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">立即购买</label>
						<div class="col-sm-9">
							<div class="input-group">
								<span class="input-group-addon">自定义名称</span>
								<input type="text" name="buynow_info[buynow_name]" value="<?php  echo $buynow_info['buynow_name'];?>" class="form-control">
								<span class="input-group-addon">网页链接</span>
								<input type="text" name="buynow_info[buynow_link]" value="<?php  echo $buynow_info['buynow_link'];?>" class="form-control" placeholder="含http://或https://">
							</div>
							<span class="help-block">默认请留空，课程详情页右下角“立即购买”自定义名称，显示优先级：此处设置名称 > 参数设置里[立即购买] > 默认名称[立即购买]</span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style="color:red;font-weight:bolder;">*</span>课程状态</label>
						<div class="col-sm-9">
							<label class="radio-inline"><input type="radio" name="status" value="1" <?php  if(empty($lesson) || $lesson['status'] == 1) { ?>checked="true"<?php  } ?> /> 上架</label>
							&nbsp;&nbsp;
							<label class="radio-inline"><input type="radio" name="status" value="0" <?php  if(!empty($lesson) && $lesson['status'] == 0) { ?>checked="true"<?php  } ?> /> 下架</label>
							&nbsp;&nbsp;
							<label class="radio-inline"><input type="radio" name="status" value="2" <?php  if($lesson['status'] == 2) { ?>checked="true"<?php  } ?> /> 审核中</label>
							&nbsp;&nbsp;
							<label class="radio-inline"><input type="radio" name="status" value="-1" <?php  if($lesson['status'] == -1) { ?>checked="true"<?php  } ?> /> 暂停销售</label>
							<span class="help-block">【上架】课程正常销售，【下架】【审核中】课程将不能再进行观看或购买，【暂停销售】已购买课程用户可正常观看，未购买用户不能查看</span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style="color:red;font-weight:bolder;">*</span>连载状态</label>
						<div class="col-sm-9">
							<label class="radio-inline">
								<input type="radio" name="section_status" value="1" <?php  if(empty($lesson) || $lesson['section_status'] == 1) { ?>checked="true"<?php  } ?> /><span class="label label-success">更新中</span>
							</label>
							&nbsp;&nbsp;
							<label class="radio-inline">
								<input type="radio" name="section_status" value="0" <?php  if(!empty($lesson) && $lesson['section_status'] == 0) { ?>checked="true"<?php  } ?> /><span class="label label-default">已完结</span>
							</label>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">课程链接</label>
						<div class="col-sm-9">
							<div style="padding-top:8px;font-size: 14px;"><a href="javascript:;" id="copy-btn"><?php  echo $_W['siteroot'];?>app/<?php  echo str_replace("./", "", $this->createMobileUrl('lesson', array('id'=>$lesson['id'])));?></a></div>
						</div>
					</div>
				</div>
			</section>
			<section id="tab2" title="价格信息" class="lesson-tab-section">
				<div class="panel-body">
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">课程价格</label>
						<div class="col-sm-9">
							<div class="input-group">
								<input type="text" name="price" class="form-control" value="<?php  echo $lesson['price'];?>" readonly="readonly"/>
								<span class="input-group-addon">元</span>
							</div>
							<div class="help-block">该选项无需填写，请添加“课程规格”即可，系统自动获取规格最低价格，0为免费课程</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">课程规格</label>
						<div class="col-sm-9">
							<div class="form-group item">
								<div class="col-sm-12">
									<?php  if(is_array($spec_list)) { foreach($spec_list as $item) { ?>
									<div class="input-group">
										<span class="input-group-addon">有效期</span>
										<input type="text" name="spec_time[]" value="<?php  echo $item['spec_day'];?>" class="form-control">
										<span class="input-group-addon">天</span>
										<span class="input-group-addon">需</span>
										<input type="text" name="spec_price[]" value="<?php  echo $item['spec_price'];?>" class="form-control">
										<span class="input-group-addon">元</span>
										<span class="input-group-addon">报名课程规格</span>
										<input type="text" name="spec_name[]" value="<?php  echo $item['spec_name'];?>" class="form-control">
										<span class="input-group-addon">排序</span>
										<input type="text" name="spec_sort[]" value="<?php  echo $item['spec_sort'];?>" class="form-control">
									</div>
									<?php  } } ?>
									<div id="specdiv"></div>
								</div>
							</div>
							<a href="javascript:;" id="spec-add" style="color:#0e9e53;"><i class="fa fa-plus-circle"></i> 点击添加新规格</a>
							<span class="help-block">
								有效期为-1表示永久有效，保存时，“有效期”或“价格”为空的规格将自动删除<br/>
								报名课程有效期建议填写-1表示永久有效，报名课程规格名仅显示在报名课程里<br/>
								序号越大，排序越靠前
							</span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">库存</label>
						<div class="col-sm-9">
							<input type="text" name="stock" class="form-control" value="<?php  echo $lesson['stock'];?>" />
							<span class="help-block">如需开启库存，请先在“基本设置—>全局设置”里开启课程库存</span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">VIP会员课程折扣</label>
						<div class="col-sm-9">
							<label class="radio-inline">
								<input type="radio" name="isdiscount" value="1" <?php  if($lesson['isdiscount']==1) { ?>checked<?php  } ?>>开启
							</label>
							<label class="radio-inline">
								<input type="radio" name="isdiscount" value="0" <?php  if($lesson['isdiscount']==0) { ?>checked<?php  } ?>>关闭
							</label>
							<span class="help-block">开启VIP会员课程折扣后，VIP会员购买课程将享受优惠</span>
						</div>
					</div>
					<div class="form-group vip-discount" <?php  if($lesson['isdiscount']==0) { ?>style="display: none;"<?php  } ?>>
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">会员折扣</label>
						<div class="col-sm-9">
							<div class="input-group">
								<input type="text" name="vipdiscount" class="form-control" value="<?php  echo $lesson['vipdiscount'];?>" />
								<span class="input-group-addon">%</span>
							</div>
							<span class="help-block">开启VIP会员课程折扣后，该项留空或为0表示使用当前会员对应的VIP会员等级最低折扣</span>
						</div>
					</div>
				</div>
			</section>
			<section id="tab3" title="课程介绍" class="lesson-tab-section">
				<div class="panel-body">
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">课程介绍</label>
						<div class="col-sm-9">
							<div class="input-group">
								<?php  echo tpl_ueditor('descript', $lesson['descript']);?>
							</div>
							<div class="help-block"></div>
						</div>
					</div>
				</div>
			</section>
			<section id="tab4" title="营销信息" class="lesson-tab-section">
				<div class="panel-body">
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">赠送固定积分</label>
						<div class="col-sm-9">
							<div class="input-group">
								<input type="text" name="integral" class="form-control" value="<?php  echo $lesson['integral'];?>" />
								<span class="input-group-addon">积分</span>
							</div>
							<div class="help-block">购买该课程赠送固定的积分数，0为关闭</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">赠送比例积分</label>
						<div class="col-sm-9">
							<div class="input-group">
								<input type="text" name="integral_rate" class="form-control" value="<?php  echo $lesson['integral_rate'];?>" />
								<span class="input-group-addon"></span>
							</div>
							<div class="help-block"><strong style="color:#777;">启用该选项后会自动覆盖赠送固定积分选项，</strong>购买该课程赠送支付金额一定比例的积分，0为关闭。例如设置1.5，用户购买课程实际支付50元，则获赠1.5x50=75积分</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">抵扣积分</label>
						<div class="col-sm-9">
							<div class="input-group">
								<input type="text" name="deduct_integral" class="form-control" value="<?php  echo $lesson['deduct_integral'];?>" />
								<span class="input-group-addon">积分</span>
							</div>
							<div class="help-block">用户购买课程时可用积分抵扣的数量，<strong style="color:#777;">请先在“营销管理—>抵扣设置”里设置积分抵扣</strong></div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">虚拟购买人数</label>
						<div class="col-sm-9">
							<div class="input-group">
								<input type="text" name="virtual_buynum" class="form-control" value="<?php  echo $lesson['virtual_buynum'];?>" />
								<span class="input-group-addon">人</span>
							</div>
							<div class="help-block">前台购买人数=真实购买人数+虚拟购买人数+访问课程人数</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">推荐到板块</label>
						<div class="col-xs-9 col-sm-9" style="margin-top: 7px;">
						   <?php  if(is_array($rec_list)) { foreach($rec_list as $key => $rec) { ?>
								<input type="checkbox" name="recid[]" value="<?php  echo $rec['id'];?>" <?php  if(in_array($rec['id'],$recidarr)) { ?>checked<?php  } ?>><?php  echo $rec['rec_name'];?>&nbsp;&nbsp;
								<?php  if(($key+1)%4==0) { ?><br/><?php  } ?>
						   <?php  } } ?>
					   </div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">优惠券抵扣</label>
						<div class="col-sm-9">
							<label class="radio-inline"><input type="radio" name="support_coupon" value="1" <?php  if(empty($lesson) || $lesson['support_coupon'] == 1) { ?>checked="true"<?php  } ?> /> 支持</label>
							&nbsp;&nbsp;
							<label class="radio-inline"><input type="radio" name="support_coupon" value="0" <?php  if(!empty($lesson) && $lesson['support_coupon'] == 0) { ?>checked="true"<?php  } ?> /> 不支持</label>
							<span class="help-block">不支持优惠券抵扣的课程将无法使用优惠券抵扣支付金额</span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">课程小标识</label>
						<div class="col-sm-9">
							<label class="radio-inline"><input type="radio" name="ico_name" value="ico-new" <?php  if($lesson['ico_name'] == 'ico-new') { ?>checked="true"<?php  } ?> /> New新课程</label>
							&nbsp;
							<label class="radio-inline"><input type="radio" name="ico_name" value="ico-hot" <?php  if($lesson['ico_name'] == 'ico-hot') { ?>checked="true"<?php  } ?> /> Hot人气</label>
							&nbsp;
							<label class="radio-inline"><input type="radio" name="ico_name" value="ico-vip" <?php  if($lesson['ico_name'] == 'ico-vip') { ?>checked="true"<?php  } ?> /> VIP免费</label>
							&nbsp;
							<label class="radio-inline"><input type="radio" name="ico_name" value="" <?php  if($lesson['ico_name'] == '') { ?>checked="true"<?php  } ?> /> 无</label>
							<span class="help-block">选择的小标识将显示在课程的右上角，当课程小标识选择“VIP免费”时，只有VIP免费课程右上角会出现“VIP免费”标识。</span>
						</div>
					</div>
				</div>
			</section>
			<section id="tab5" title="分销分享" class="lesson-tab-section">
				<div class="panel-body">
					<div class="form-group item">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">一级佣金比例</label>
						<div class="col-sm-4">
							<div class="input-group">
								<input type="text" name="commission1" value="<?php  echo $commission['commission1'];?>" class="form-control"><span class="input-group-addon">%</span>
							</div>
							<span class="help-block">留空或为0表示使用系统全局佣金比例</span>
						</div>
					</div>
					<div class="form-group item <?php  if(in_array($comsetting['level'],array('1'))) { ?>hide<?php  } ?>">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">二级佣金比例</label>
						<div class="col-sm-4">
							<div class="input-group">
								<input type="text" name="commission2" value="<?php  echo $commission['commission2'];?>" class="form-control"><span class="input-group-addon">%</span>
							</div>
							<span class="help-block">留空或为0表示使用系统全局佣金比例</span>
						</div>
					</div>
					<div class="form-group item <?php  if(in_array($comsetting['level'],array('1','2'))) { ?>hide<?php  } ?>">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">三级佣金比例</label>
						<div class="col-sm-4">
							<div class="input-group">
								<input type="text" name="commission3" value="<?php  echo $commission['commission3'];?>" class="form-control"><span class="input-group-addon">%</span>
							</div>
							<span class="help-block">留空或为0表示使用系统全局佣金比例</span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">微信分享标题</label>
						<div class="col-sm-9">
							<input type="text" name="share[title]" class="form-control" value="<?php  echo $share['title'];?>" />
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">微信分享图标</label>
						<div class="col-sm-9">
							<?php  echo tpl_form_field_image("share[images]", $share['images'])?>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">微信分享描述</label>
						<div class="col-sm-9">
							<textarea style="height:70px;" class="form-control" name="share[descript]"><?php  echo $share['descript'];?></textarea>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">课程海报背景图</label>
						<div class="col-sm-9">
							<?php  echo tpl_form_field_image("poster_config[images]", $poster_config['images'])?>
							<span class="help-block">
								1、设置课程海报后，手机端课程详情页右上角将显示一级分销佣金；<br/>
								2、建议尺寸宽度:640 px，高度:940 px，处理技巧：从PS导出图片时，要导出为WEB所用的jpg图片格式(快捷键是：ctrl+shift+alt+s)，将会大大减少图片所占内存大小，提高用户打开速度！ 
							</span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">海报头像</label>
						<div class="col-sm-9">
							<div class="input-group">
								<span class="input-group-addon">离左侧距离</span>
								<input type="text" name="poster_config[avatar_left]" value="<?php  echo $poster_config['avatar_left'];?>" class="form-control">
								<span class="input-group-addon">px</span>
								<span class="input-group-addon">离顶部距离</span>
								<input type="text" name="poster_config[avatar_top]" value="<?php  echo $poster_config['avatar_top'];?>" class="form-control">
								<span class="input-group-addon">px</span>
							</div>
							<span class="help-block">海报头像为空或0表示不显示头像</span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">海报二维码</label>
						<div class="col-sm-9">
							<div class="input-group">
								<span class="input-group-addon">离左侧距离</span>
								<input type="text" name="poster_config[qrcode_left]" value="<?php  echo $poster_config['qrcode_left'];?>" class="form-control">
								<span class="input-group-addon">px</span>
								<span class="input-group-addon">离顶部距离</span>
								<input type="text" name="poster_config[qrcode_top]" value="<?php  echo $poster_config['qrcode_top'];?>" class="form-control">
								<span class="input-group-addon">px</span>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">海报用户昵称</label>
						<div class="col-sm-10">
							<div class="input-group">
								<span class="input-group-addon">离左侧距离</span>
								<input type="text" name="poster_config[nickname_left]" value="<?php  echo $poster_config['nickname_left'];?>" class="form-control">
								<span class="input-group-addon">px</span>
								<span class="input-group-addon">离顶部距离</span>
								<input type="text" name="poster_config[nickname_top]" value="<?php  echo $poster_config['nickname_top'];?>" class="form-control">
								<span class="input-group-addon">px</span>
								<span class="input-group-addon">字体大小</span>
								<input type="text" name="poster_config[nickname_fontsize]" value="<?php  echo $poster_config['nickname_fontsize'];?>" class="form-control">
								<span class="input-group-addon">px</span>
								<span class="input-group-addon">字体颜色</span>
								<input type="text" name="poster_config[nickname_fontcolor]" value="<?php  echo $poster_config['nickname_fontcolor'];?>" class="form-control" style="width:100px;">
							</div>
							<span class="help-block">海报用户昵称留空或为0表示不显示用户昵称，字体颜色请使用十六进制颜色值，例如：#056D9F</span>
						</div>
					</div>
				</div>
			</section>
			<section id="tab6" title="报名课程专用" class="lesson-tab-section">
				<div class="panel-body">
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">报名课程填写信息</label>
						<div class="col-sm-9">
							<div class="form-group item">
								<div class="col-sm-6">
									<?php  if(is_array($appoint_info)) { foreach($appoint_info as $item) { ?>
									<div class="input-group">
										<span class="input-group-addon">字段名称</span>
										<input type="text" name="appoint_info[]" value="<?php  echo $item;?>" class="form-control">
									</div>
									<?php  } } ?>
									<div id="appointdiv"></div>
								</div>
							</div>
							<a href="javascript:;" id="appoint-add" style="color:#0e9e53;"><i class="fa fa-plus-circle"></i> 点击添加新表单</a>
							<span class="help-block">该项仅对报名课程生效，用户下单时需要填写该信息。保存时，留空的字段名称将自动删除</span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">线下核销人员</label>
						<div class="col-sm-9">
							<div class='input-group-btn'>
                                <button class="btn btn-default" type="button" onclick="popwin = $('#modal-module-member').modal();">添加核销人员</button>
                            </div>
							<span class="help-block">添加核销人员后，该核销人员可线下进行扫码核销报名课程</span>
							<div class="input-group multi-img-details" id='saler_container'>
								<?php  if(is_array($saler_info)) { foreach($saler_info as $saler) { ?>
								<div class="multi-item saler-item" uid='<?php  echo $saler['uid'];?>'>
									 <img class="img-responsive img-thumbnail" src='<?php  echo $saler['avatar'];?>' onerror="this.src='../addons/fy_lessonv2/template/mobile/images/nopic.jpg'; this.title='头像未找到.'">
									 <div class='img-nickname'><?php  echo $saler['nickname'];?></div>
									<input type="hidden" value="<?php  echo $saler['uid'];?>" name="saler_uids[]">
									<em onclick="remove_member(this)"  class="close">×</em>
								</div>
								<?php  } } ?>
							</div>
							<div id="modal-module-member"  class="modal fade" tabindex="-1">
								<div class="modal-dialog" style='width: 920px;'>
									<div class="modal-content">
										<div class="modal-header"><button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button><h3>添加核销人员</h3></div>
										<div class="modal-body" >
											<div class="row">
												<div class="input-group">
													<input type="text" class="form-control" name="kmember" value="" id="search-km" placeholder="请输入用户昵称/姓名/手机号" />
													<span class='input-group-btn'><button type="button" class="btn btn-default" onclick="search_member();">搜索</button></span>
												</div>
											</div>
											<div id="module-member" style="padding-top:5px;"></div>
										</div>
										<div class="modal-footer"><a href="javascript:;" class="btn btn-default" data-dismiss="modal" aria-hidden="true">关闭</a></div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>
		</div>
		<div class="form-group col-sm-12">
            <input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-1" />
            <input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
			<input type="hidden" name="id" value="<?php  echo $lesson['id'];?>" />
        </div>
	 </form>
</div>
<script type="text/javascript" src="<?php echo MODULE_URL;?>template/web/style/lessonTab/jquery-tab.js?v=<?php  echo $versions;?>"></script>
<script type="text/javascript">
require(['jquery', 'util'], function($, util){
	$(function(){
		util.clip($("#copy-btn")[0], $("#copy-btn").text());
	});
});

$(function () {
	$(':radio[name="isdiscount"]').click(function () {
	   if ($(this).val()=='0') {
			$('.vip-discount').hide();
		} else {

			$('.vip-discount').show();
		}
	});
	$('.tab-group').tabify();
});
//添加报名课程信息
$("#appoint-add").click(function () {
    var appoint_html = '';
    appoint_html += '<div class="input-group">';
    appoint_html += '	<span class="input-group-addon">字段名称</span>';
    appoint_html += '	<input type="text" name="appoint_info[]" class="form-control">';
    appoint_html += '</div>';

    $("#appointdiv").append(appoint_html);
});

//添加规格按钮
$("#spec-add").click(function () {
    var spec_html = '';
    spec_html += '		<div class="input-group">';
    spec_html += '			<span class="input-group-addon">有效期</span>';
    spec_html += '			<input type="text" name="spec_time[]" class="form-control">';
    spec_html += '			<span class="input-group-addon">天</span>';
    spec_html += '			<span class="input-group-addon">需</span>';
    spec_html += '			<input type="text" name="spec_price[]" class="form-control">';
    spec_html += '			<span class="input-group-addon">元</span>';
	spec_html += '			<span class="input-group-addon">报名课程规格名</span>';
	spec_html += '			<input type="text" name="spec_name[]" class="form-control">';
	spec_html += '			<span class="input-group-addon">排序</span>';
	spec_html += '			<input type="text" name="spec_sort[]" class="form-control">';
    spec_html += '		</div>';

    $("#specdiv").append(spec_html);
});

//添加核销人员
function search_member() {
	var uniacid = <?php  echo $uniacid?>;
	if ($.trim($('#search-km').val()) == '') {
		document.getElementById('search-km').focus();
		return;
	}
	$("#module-member").html("正在搜索....");
	$.get("<?php  echo $this->createWebUrl('getlessonormember', array('op'=>'getMember'))?>", { 
		uniacid:uniacid,keyword: $.trim($('#search-km').val())
	}, function (dat) {
		$('#module-member').html(dat);
	});
}
function select_member(obj) {
	if ($('.multi-item[uid="' + obj.uid + '"]').length > 0) {
		return;
	}
	var html = '<div class="multi-item" uid="' + obj.uid + '">';
	html += '<img class="img-responsive img-thumbnail" src="' + obj.avatar + '" onerror="this.src=\'../addons/fy_lessonv2/template/mobile/images/nopic.jpg\'; this.title=\'头像未找到.\'">';
	html += '<div class="img-nickname">' + obj.nickname + '</div>';
	html += '<input type="hidden" value="' + obj.uid + '" name="saler_uids[]">';
	html += '<em onclick="remove_member(this)"  class="close">×</em>';
	html += '</div>';
	$("#saler_container").append(html);
}
function remove_member(obj) {
	$(obj).parent().remove();
}
</script>

<script type="text/javascript">
var category = <?php  echo json_encode($category);?>;
var pid = <?php echo $lesson['pid']?$lesson['pid']:0?>;
var html = '<option value="0">请选择一级分类</option>';
$(function(){
	$("#category_parent").find("option[value='"+pid+"']").attr("selected",true);
	document.getElementById("category_parent").onchange();
});

function renderCategory(id){
	var chtml = '<option value="0">请选择二级分类</option>';
	var cid = <?php echo $lesson['cid']?$lesson['cid']:0?>;
	for(var i in category){
		if(category[i].id==id){
			var child = category[i].child;
			for(var j in child){
				if(child[j].id==cid){
					chtml += '<option value="' + child[j].id+'" selected>' + child[j].name + '</option>';
				}else{
					chtml += '<option value="' + child[j].id+'">' + child[j].name + '</option>';
				}
			}
			$("#category_child").html(chtml);
		}
	}
}
</script>