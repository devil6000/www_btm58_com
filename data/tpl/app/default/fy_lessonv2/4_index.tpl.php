<?php defined('IN_IA') or exit('Access Denied');?><!-- 
 * 微课堂首页
 * ============================================================================
 * 版权所有 2015-2018 微课堂团队，并保留所有权利。
 * 网站地址: https://wx.haoshu888.com
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！不允许对程序代码以任何形式任何目的的再发布，作者将保留
 * 追究法律责任的权力和最终解释权。
-->
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('_headerv2', TEMPLATE_INCLUDEPATH)) : (include template('_headerv2', TEMPLATE_INCLUDEPATH));?>
<style>
.weui_tab_bd{height: auto; padding-bottom: 0;}
</style>
<script src="<?php echo MODULE_URL;?>template/mobile/style/jsv2/BreakingNews.js?v=<?php  echo $versions;?>"></script>
<script type="text/javascript">
$(function() {
	$('#breakingnews').BreakingNews({
		title: '<img src="<?php echo MODULE_URL;?>template/mobile/images/ico-inform.png" style="width:32px;margin-top:4px;">',
		titlebgcolor: '#ffffff',
		linkhovercolor: '#099',
		border: '1px solid #f3f3f3',
		timer: 5000,
		effect: 'slide'
	});
});
</script>

<?php  if($setting['isfollow']>0 && $fans['follow']==0 && $userAgent) { ?>
<div class="follow_topbar">
	<div class="headimg">
		<img src="<?php  echo $_W['attachurl'];?><?php  echo $setting['qrcode'];?>">
	</div>
	<div class="info">
		<div class="i"><?php  echo $_W['account']['name'];?></div>
		<div class="i"><?php  echo $setting['follow_word'];?></div>
	</div>
	<div class="sub" onclick="location.href='<?php  echo $this->createMobileUrl('follow');?>'">立即关注</div>
	<!-- <div class="sub" id="js-go-follow">立即关注</div> -->
</div>
<div style='height:44px;'>&nbsp;</div>
<?php  } ?>

<div class="weui_tab_bd" id="weui_tab_div">
	<!-- 搜索框 -->
	<div class="index-header-search <?php echo $search_box['isshow'] ? '' : 'hide';?>" style="top:<?php  echo $search_box['top'];?>px;left:<?php  echo $search_box['left'];?>px;">
		<div class="u-search">
			<i class="fa fa-search"></i>
			<input type="text" id="searchInput" class="search_input z-abled" value="" autocorrect="off" placeholder="输入课程名称进行查找">
		</div>
	</div>
	<!-- /搜索框 -->

	<!-- 广告轮播图 -->
	<?php  if(!empty($banner)) { ?>
	<div class="swiper-container">
		<div class="swiper-wrapper">
			<!--图片一-->
			<?php  if(is_array($banner)) { foreach($banner as $item) { ?>
			<div class="swiper-slide">
				<a href="<?php  echo $item['link'];?>">
					<img class="swiper-lazy" src="<?php  echo $_W['attachurl'];?><?php  echo $item['picture'];?>">
				</a>
			</div>
			<?php  } } ?>
			<!--图片一end-->
		</div>
		<div class="swiper-pagination"></div>
	</div>
	<?php  } ?>
	<!-- /广告轮播图 -->

	<!-- 分类 -->
	<?php  if(!empty($category_list)) { ?>
	<div class="grid_wrap bor_no">
		<?php  if(is_array($category_list)) { foreach($category_list as $item) { ?>
		<a class="grid_item uc-flex1" href="<?php echo $item['link'] ? $item['link'] : $this->createMobileUrl('search', array('cat_id'=>$item['id']));?>">
			<div class="grid_hd">
				<img src="<?php  echo $_W['attachurl'];?><?php  echo $item['ico'];?>" alt="<?php  echo $item['name'];?>" />
			</div>
			<div class="grid_bd">
				<p><?php  echo $item['name'];?></p>
			</div>
		</a>
		<?php  } } ?>
		<?php  if(!empty($allCategoryIco)) { ?>
		<a class="grid_item uc-flex1" href="<?php  echo $this->createMobileUrl('search',array('op'=>'allcategory'));?>">
			<div class="grid_hd">
				<img src="<?php  echo $allCategoryIco;?>" alt="全部分类">
			</div>
			<div class="grid_bd">
				<p>全部分类</p>
			</div>
		</a>
		<?php  } ?>
	</div>
	<?php  } ?>
	<!-- /分类 -->
	
	<!-- 公告 -->
	<?php  if(!empty($articlelist) && is_array($articlelist)) { ?>
	<div class="BreakingNewsController easing" id="breakingnews">
		<div class="bn-title" onclick="location.href='<?php  echo $this->createMobileUrl('article', array('op'=>'list'));?>'"></div>
		<ul>
			<?php  if(is_array($articlelist)) { foreach($articlelist as $article) { ?>
			<li><a href="<?php  echo $this->createMobileUrl('article', array('op'=>'display','aid'=>$article['id']));?>">[<?php  echo date('m-d',$article['addtime']);?>]<?php  echo $article['title'];?></a></li>
			<?php  } } ?>
		</ul>
		<div class="bn-arrows" onclick="location.href='<?php  echo $this->createMobileUrl('article', array('op'=>'list'));?>'">更多</div>	
	</div>
	<?php  } ?>
	<!-- /公告 -->

	<!-- 限时折扣 -->
	<?php  if(!empty($discount_banner)) { ?>
	<div class="swiper-container" style="margin-top:10px;height:100px;">
		<div class="swiper-wrapper">
			<!--图片一-->
			<?php  if(is_array($discount_banner)) { foreach($discount_banner as $item) { ?>
			<div class="swiper-slide">
				<a href="<?php  echo $item['link'];?>">
					<img class="swiper-lazy" src="<?php  echo $_W['attachurl'];?><?php  echo $item['picture'];?>">
				</a>
			</div>
			<?php  } } ?>
			<!--图片一end-->
		</div>
		<div class="swiper-pagination"></div>
	</div>
	<?php  } ?>
	<!-- /限时折扣 -->
	
	<!-- 最新课程 -->
	<?php  if($setting['show_newlesson'] && $newlesson) { ?>
	<div class="course_wrap mt10">
		<h2 class="course_hd"><span class="bor-l"></span>最新更新</h2>
		<ul class="newlesson-list" style="min-height:1px;">
			<?php  if(is_array($newlesson)) { foreach($newlesson as $item) { ?>
			<li class="lesson_list">
				<a href="<?php  echo $this->createMobileUrl('lesson', array('op'=>'display', 'id'=>$item['id']));?>" class="package">
					<div class="package__cover-wrap">
						<div class="package__cover" style="background-image: url(<?php  echo $_W['attachurl'];?><?php  echo $item['images'];?>);">
							<span class="package__cover-tips package__cover-tips--status"><?php  if($item['price']>0) { ?><?php  echo $item['buynum']+$item['virtual_buynum'];?><?php  } else { ?><?php  echo $item['buynum']+$item['virtual_buynum']+$item['visit_number'];?><?php  } ?>人已学习</span>
						</div>
					</div>
					<div class="package__content">
						<h3 class="package__name"><?php  echo $item['bookname'];?></h3>
						<div class="package__info">
							<span class="pink-color subhead"><?php  echo $item['section']['title'];?></span>
						</div>
						<div class="package__info">
							<span class="grey-color"><?php  echo $item['tran_time'];?>更新</span>
							<div class="package__course-num"><i class="red-color"><?php echo $item['price']>0?'¥'.$item['price']:'免费';?></i></div>
						</div>
					</div>
				</a>
			</li>
			<?php  } } ?>
		</ul>
	</div>
	<?php  } ?>
	<!-- /最新课程 -->

	<!-- 课程板块遍历 -->
	<?php  if(!empty($list)) { ?> <?php  if(is_array($list)) { foreach($list as $rec) { ?>
	<div class="course_wrap mt10">
		<h2 class="course_hd"><span class="bor-l"></span><?php  echo $rec['rec_name'];?> <a href="<?php  echo $this->createMobileUrl('recommend', array('recid'=>$rec['recid']));?>" class="fr more">更多<i class="fa fa-angle-double-right"></i></a></h2>
		<?php  if($rec['show_style']==1) { ?>
		<ul class="course_main course_other">
			<?php  if(is_array($rec['lesson'])) { foreach($rec['lesson'] as $item) { ?>
			<li class="course_item">
				<a href="<?php  echo $this->createMobileUrl('lesson', array('op'=>'display', 'id'=>$item['id']));?>">
					<div class="course_pic">
						<?php  if(!empty($item['ico_name'])) { ?>
						<span class="course_ico courseNew" style="background:url('../addons/fy_lessonv2/template/mobile/images/<?php  echo $item['ico_name'];?>.png') no-repeat;background-size: 40px 40px;"></span>
						<?php  } ?>
						<img class="lazy" src="<?php  echo $_W['attachurl'];?><?php  echo $item['images'];?>" alt="<?php  echo $item['bookname'];?>" />
						<p class="course_living"><?php  echo $item['bookname'];?></p>
					</div>
					<p>
						<span class="fl red-color"><?php echo $item['price']>0?'¥'.$item['price']:'免费';?></span>
						<span class="fr">
							<?php  if($item['section_status']==0) { ?>
								<span class="section-status-btn">已完结</span>
							<?php  } else { ?>
								已更新<i class="blue-color"><?php  echo $item['count'];?></i>节课
							<?php  } ?>
						</span>
					</p>
					<p>
						<?php  if($setting['stock_config']==1) { ?>
						<span class="fl">仅剩:<?php  echo $item['stock'];?></span> <?php  } ?>
						<span class="fr"><i class="blue-color"><?php  if($item['price']>0) { ?><?php  echo $item['buynum']+$item['virtual_buynum'];?><?php  } else { ?><?php  echo $item['buynum']+$item['virtual_buynum']+$item['visit_number'];?><?php  } ?></i>人已学习</span>
					</p>
				</a>
			</li>
			<?php  } } ?>
		</ul>
		<?php  } else if($rec['show_style']==2) { ?>
		<ul class="course_main course_live">
			<li class="course_item" style="width:100%;">
				<a href="<?php  echo $this->createMobileUrl('lesson', array('op'=>'display', 'id'=>$rec['lesson'][0]['id']));?>">
					<div class="course_pic">
						<?php  if(!empty($rec['lesson'][0]['ico_name'])) { ?>
						<span class="course_ico courseNew" style="background:url('../addons/fy_lessonv2/template/mobile/images/<?php  echo $rec['lesson'][0]['ico_name'];?>.png') no-repeat;background-size: 40px 40px;"></span>
						<?php  } ?>
						<img class="lazy" src="<?php  echo $_W['attachurl'];?><?php  echo $rec['lesson'][0]['images'];?>" alt="<?php  echo $rec['lesson'][0]['bookname'];?>" style="height:184px" />
					</div>
					<h3><?php  echo $rec['lesson'][0]['bookname'];?></h3>
					<p>
						<span class="fl red-color" style="font-size:13px;"> 
							<?php echo $rec['lesson'][0]['price']>0?'¥'.$rec['lesson'][0]['price']:'免费';?> 
							<?php  if($rec['lesson'][0]['section_status']==0) { ?>
								<span class="section-status-btn">已完结</span>
							<?php  } ?>
						</span>
						<span class="fr">已有<i class="blue-color"><?php  if($rec['lesson'][0]['price']>0) { ?><?php  echo $rec['lesson'][0]['buynum']+$rec['lesson'][0]['virtual_buynum'];?><?php  } else { ?><?php  echo $rec['lesson'][0]['buynum']+$rec['lesson'][0]['virtual_buynum']+$rec['lesson'][0]['visit_number'];?><?php  } ?></i>人学习</span>
					</p>
				</a>
			</li>
			<?php  if(is_array($rec['lesson'])) { foreach($rec['lesson'] as $key => $item) { ?> <?php  if($key>0) { ?>
			<li class="course_item">
				<a href="<?php  echo $this->createMobileUrl('lesson', array('op'=>'display', 'id'=>$item['id']));?>">
					<div class="course_pic">
						<?php  if(!empty($item['ico_name'])) { ?>
						<span class="course_ico courseNew" style="background:url('../addons/fy_lessonv2/template/mobile/images/<?php  echo $item['ico_name'];?>.png') no-repeat;background-size: 40px 40px;"></span>
						<?php  } ?>
						<img class="lazy" src="<?php  echo $_W['attachurl'];?><?php  echo $item['images'];?>" alt="<?php  echo $item['bookname'];?>" />
						<p class="course_living"><?php  echo $item['bookname'];?></p>
					</div>
					<p>
						<span class="fl red-color"><?php echo $item['price']>0?'¥'.$item['price']:'免费';?></span>
						<span class="fr">
							<?php  if($item['section_status']==0) { ?>
								<span class="section-status-btn">已完结</span>
							<?php  } else { ?>
								已更新<i class="blue-color"><?php  echo $item['count'];?></i>节课
							<?php  } ?>
						</span>
					</p>
					<p>
						<?php  if($setting['stock_config']==1) { ?>
						<span class="fl">仅剩:<?php  echo $item['stock'];?></span> <?php  } ?>
						<span class="fr"><i class="blue-color"><?php  if($item['price']>0) { ?><?php  echo $item['buynum']+$item['virtual_buynum'];?><?php  } else { ?><?php  echo $item['buynum']+$item['virtual_buynum']+$item['visit_number'];?><?php  } ?></i>人已学习</span>
					</p>
				</a>
			</li>
			<?php  } ?> <?php  } } ?>
		</ul>
		<?php  } else if($rec['show_style']==3) { ?>
		<ul class="course_main course_live">
			<?php  if(is_array($rec['lesson'])) { foreach($rec['lesson'] as $item) { ?>
			<li class="course_item" style="width:100%;">
				<a href="<?php  echo $this->createMobileUrl('lesson', array('op'=>'display', 'id'=>$item['id']));?>">
					<div class="course_pic">
						<?php  if(!empty($item['ico_name'])) { ?>
						<span class="course_ico courseNew" style="background:url('../addons/fy_lessonv2/template/mobile/images/<?php  echo $item['ico_name'];?>.png') no-repeat;background-size: 40px 40px;"></span>
						<?php  } ?>
						<img class="lazy" src="<?php  echo $_W['attachurl'];?><?php  echo $item['images'];?>" alt="<?php  echo $item['bookname'];?>" style="height:184px" />
						<p class="course_living"><?php  echo $item['bookname'];?></p>
					</div>
				</a>
			</li>
			<?php  } } ?>
		</ul>
		<?php  } else if($rec['show_style']==4) { ?>
		<ul class="course-sections">
			<?php  if(is_array($rec['lesson'])) { foreach($rec['lesson'] as $item) { ?>
			<li>
				<a href="<?php  echo $this->createMobileUrl('lesson', array('op'=>'display', 'id'=>$item['id']));?>"><i class="fa fa-book index-book-color"></i> <?php  echo $item['bookname'];?></a>
			</li>
			<?php  } } ?>
		</ul>
		<?php  } ?>
	</div>
	<?php  } } ?> <?php  } ?>
	<!-- /课程板块遍历 -->
	
	<?php  if(!empty($config['index_slogan'])) { ?>
	<div class="slogan_wrap">
		<div class="slogan_bd" style="background-image:url(<?php  echo $_W['attachurl'];?><?php  echo $config['index_slogan'];?>);"></div>
	</div>
	<?php  } ?>
</div>

<!-- 绑定手机号码 -->
<?php  if($setting['modify_mobile']==2 && $uid>0 && empty($member['mobile'])) { ?>
<div id="modify_mobile_shade" style="background-color:rgba(0,0,0,0.7); position:fixed; width:100%; height:100%; top:0; z-index:100000000;"></div>
<div class="login_wrap" id="modify_mobile_div" style="position:absolute; width:90%; height:100%; margin:0 5%; top:80px; z-index:100000001;">
	<form method="post" onsubmit="return checknum();">
		<div class="weui_cells weui_cells_form" style="border-radius:10px; padding-bottom:20px;">
			<h3 style="padding:15px 0; text-align:center; font-size:18px;">手机号码注册</h3>
			<?php  if(!in_array('verify_mobile', $index_verify)) { ?>
			<a href="javascript:;" onclick="closeBox();" style="width:20px;height:20px;color:#aaa;position:absolute;right:15px;top:17px;"><i class="fa fa-close fa-lg"></i></a>
			<?php  } ?>
			<div class="weui_cell">
				<div class="weui_cell_hd"><label class="weui_label" for="registerform-mobile">手机号码</label></div>
				<div class="weui_cell_bd weui_cell_primary">
					<input type="tel" class="weui_input" name="mobile" placeholder="请输入手机号码">
				</div>
			</div>
			<div class="weui_cell check_code">
				<div class="weui_cell_hd">
					<label class="weui_label">验证码</label>
				</div>
				<div class="weui_cell_bd weui_cell_primary">
					<input type="tel" class="weui_input" name="verify_code" placeholder="请输入验证码">
				</div>
				<div class="weui_cell_ft">
					<a href="javascript:;" id="weui_btn_send" onclick="sendcode()">获取验证码</a>
				</div>
			</div>
			<?php  if(in_array('password', $index_verify)) { ?>
			<div class="weui_cell">
				<div class="weui_cell_hd"><label class="weui_label" for="registerform-mobile">登录密码</label></div>
				<div class="weui_cell_bd weui_cell_primary">
					<input type="password" class="weui_input" name="pwd1" placeholder="设置登录密码">
				</div>
			</div>
			<div class="weui_cell">
				<div class="weui_cell_hd"><label class="weui_label" for="registerform-mobile">重复密码</label></div>
				<div class="weui_cell_bd weui_cell_primary">
					<input type="password" class="weui_input" name="pwd2" placeholder="重复登录密码">
				</div>
			</div>
			<?php  } ?>
			<div class="weui_btn_area">
				<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
				<input type="submit" name="modify_mobile" class="weui_btn weui_btn_primary" value="提交">
			</div>
		</div>
	</form>
</div>
<script type="text/javascript">
document.getElementById("modify_mobile_div").style.height = document.getElementById("weui_tab_div").offsetHeight + "px";

function checknum(){
	var mobile   = $("input[name=mobile]").val();
	var myreg = /^((1)+\d{10})$/;
	if(mobile==''){
		alert("请输入手机号码");
		return false;
	}else if(!myreg.test(mobile)) {
		alert('您输入的手机号码有误');
		return false;
	}

	<?php  if(in_array('password', $index_verify)){ ?>
	if($("input[name=verify_code]").val()==''){
		alert("请输入短信验证码");
		return false;
	}
	if($("input[name=pwd1]").val()==''){
		alert("请输入登录密码");
		return false;
	}
	if($("input[name=pwd1]").val() != $("input[name=pwd2]").val()){
		alert("两次密码不一致");
		return false;
	}
	<?php  } ?>

	document.getElementById("spinners").style.display = 'block';
}

var countdown = 60;
function sendcode() {
	var result = checkMobile();
	if(!result){
		return;
	}
	if ($('#weui_btn_send').hasClass('has_send')) {
		return false;
	}

	var mobile = $('input[name="mobile"]').val();
	$.ajax({
		type:"post",
		dataType:"json",
		url: "<?php  echo $this->createMobileUrl('sendcode');?>",
		data: {mobile:mobile},
		success: function (data) {
			if(data.code==0){
				settime($("#weui_btn_send"));
			}else{
				alert(data.msg);
			}
		},
		error: function(e){
		}
	});
	
}
function settime(obj) { //发送验证码倒计时
	if(countdown == 0) {
		$('#weui_btn_send').removeClass('has_send').text('重新发送');
		countdown = 60;
		return;
	} else {
		$('#weui_btn_send').addClass('has_send').text('重新获取(' + countdown + ')');
		countdown--;
	}
	setTimeout(function() {
		settime(obj)
	}, 1000)
}
//校验手机号是否合法
function checkMobile() {
	var mobile = $('input[name="mobile"]').val();
	var myreg = /^((1)+\d{10})$/;
	if(!myreg.test(mobile)) {
		alert('请输入有效的手机号码');
		return false;
	} else {
		return true;
	}
}
function closeBox(){
	$("#modify_mobile_shade").hide();
	$("#modify_mobile_div").hide();
}
</script>
<?php  } ?>
<!-- /绑定手机号码 -->

<footer>
    <a href="<?php  echo $this->createMobileUrl('index', array('t'=>1));?>"><?php  echo $setting['copyright'];?></a>
</footer>

<div id="spinners" style="display:none;"><div class="spinner"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div></div>

<?php  echo register_jssdk(false);?>
<script type="text/javascript">
wx.ready(function(){
	var shareData = {
		title: "<?php  echo $sharelink['title'];?>",
		desc: "<?php  echo $sharelink['desc'];?>",
		link: "<?php  echo $shareurl;?>",
		imgUrl: "<?php  echo $_W['attachurl'];?><?php  echo $sharelink['images'];?>",
		trigger: function (res) {},
		complete: function (res) {},
		success: function (res) {},
		cancel: function (res) {},
		fail: function (res) {}
	};
	wx.onMenuShareTimeline(shareData);
	wx.onMenuShareAppMessage(shareData);
	wx.onMenuShareQQ(shareData);
	wx.onMenuShareWeibo(shareData);
	wx.onMenuShareQZone(shareData);
});

var search = function() {
    var keywords = $.trim($("#searchInput").val());
    if (keywords == '') {
        searchUrl = '<?php  echo $this->createMobileUrl("search");?>';
    } else {
        searchUrl = '<?php  echo $this->createMobileUrl("search");?>&keyword=' + encodeURIComponent(keywords);
    }
    document.location.href = searchUrl;
    return false;
};
$("#searchInput").keydown(function(event) {
	if (event.keyCode == 13) {
		search();
	}
});
$("#search_btn").on("click", function(){
	search();
});

new Swiper('.item-list', {
	slidesPerView: 'auto',
	spaceBetween: 10,
})

document.addEventListener('WeixinJSBridgeReady', function onBridgeReady() {
	var miniprogram_environment = false;
	wx.miniProgram.getEnv(function(res) {
		if(res.miniprogram) {
			miniprogram_environment = true;
		}
	})
	if(window.__wxjs_environment === 'miniprogram' || miniprogram_environment) {
		$(".follow_topbar").hide();
		<?php  if($setting['isfollow']>0 && $fans['follow']==0 && $userAgent) { ?>
			document.getElementById("weui_tab_div").style.cssText = "margin-top:-48px;";
		<?php  } ?>
		/*
		$("#js-go-follow").click(function(){
			wx.miniProgram.navigateTo({
				url: "/fy_lessonv2/pages/follow/index"
			})
		});
		*/
	}else{
		/*
		$("#js-go-follow").click(function(){
			location.href="<?php  echo $this->createMobileUrl('follow');?>";
		});
		*/
	}
});
</script>

<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('_footerv2', TEMPLATE_INCLUDEPATH)) : (include template('_footerv2', TEMPLATE_INCLUDEPATH));?>