<?php defined('IN_IA') or exit('Access Denied');?><!--
 * 营销管理
 * ============================================================================
 * 版权所有 2015-2018 微课堂团队，并保留所有权利。
 * 网站地址: https://wx.haoshu888.com
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！不允许对程序代码以任何形式任何目的的再发布，作者将保留
 * 追究法律责任的权力和最终解释权。
 * ============================================================================
-->

<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<link href="<?php echo MODULE_URL;?>template/web/style/market.css" rel="stylesheet">
<style type="text/css">
.request{
	color:red;
	font-weight:bolder;
}
</style>
<ul class="nav nav-tabs">
	<li <?php  if($op=='display') { ?>class="active" <?php  } ?>>
		<a href="<?php  echo $this->createWebUrl('market');?>">抵扣设置</a>
	</li>
	<li <?php  if($op=='coupon' ||  $op=='addCoupon') { ?>class="active" <?php  } ?>>
		<a href="<?php  echo $this->createWebUrl('market', array('op'=>'coupon'));?>">优惠券管理</a>
	</li>
	<li <?php  if($op=='couponRule') { ?>class="active" <?php  } ?>>
		<a href="<?php  echo $this->createWebUrl('market', array('op'=>'couponRule'));?>">优惠券规则</a>
	</li>
	<li <?php  if($op=='sendCoupon') { ?>class="active" <?php  } ?>>
		<a href="<?php  echo $this->createWebUrl('market', array('op'=>'sendCoupon'));?>">发放优惠券</a>
	</li>
	<li <?php  if($op=='couponLog' || $op=='couponDetail') { ?>class="active" <?php  } ?>>
		<a href="<?php  echo $this->createWebUrl('market', array('op'=>'couponLog'));?>">优惠券记录</a>
	</li>
	<li <?php  if(in_array($op, array('discount','addDiscount','discountLesson','addDiscountLesson'))) { ?>class="active" <?php  } ?>>
		<a href="<?php  echo $this->createWebUrl('market', array('op'=>'discount'));?>">	
			<?php  if($op=='addDiscount' && !$_GPC['discount_id']) { ?>
				添加限时活动
			<?php  } else if($op=='addDiscount' && $_GPC['discount_id']) { ?>
				编辑限时活动
			<?php  } else if($op=='discountLesson') { ?>
				限时活动课程
			<?php  } else if($op=='addDiscountLesson') { ?>
				添加课程到活动
			<?php  } else { ?>
				限时折扣
			<?php  } ?>
		</a>
	</li>
</ul>

<!-- 抵扣设置 -->
<?php  if($op=='display') { ?>
<div class="main">
	<form action="" method="post" class="form-horizontal form-validate">
		<div class="page-heading">
			<div class="panel panel-default">
				<div class="panel-body">
					<div class="col-sm-9 col-xs-12">
						<h4>积分抵扣</h4>
						<span> 开启积分抵扣, 课程最多抵扣的数目需要在课程的【营销设置】中单独设置</span>
					</div>
					<div class="col-sm-2 pull-right" style="padding-top:10px;text-align: right">
						<input type="hidden" class="js-switch" name="deduct_switch" id="creditdeduct" value="<?php  echo $market['deduct_switch'];?>">
						<div class="switchery <?php  if($market['deduct_switch']) { ?>checked<?php  } ?>"><small></small></div>
					</div>
				</div>

				<div class="form-group" id="creditdeduct-switch" <?php  if(!$market['deduct_switch']) { ?>style="display:none"<?php  } ?>>
					<label class="col-sm-2 control-label">积分抵扣比例</label>
					<div class="col-sm-5">
						<div class="input-group">
							<input type="hidden" name="credit" value="1" class="form-control">
							<span class="input-group-addon">1个积分 抵扣</span>
							<input type="text" name="deduct_money" value="<?php  echo $market['deduct_money'];?>" class="form-control">
							<span class="input-group-addon">元</span>
						</div>
						<span class="help-block">积分抵扣比例设置</span>
					</div>
				</div>
			</div>

			<div class="form-group">
				<div class="col-sm-12">
					<input type="hidden" name="token" value="<?php  echo $_W['token'];?>">
					<input type="submit" name="submit" value="保存设置" class="btn btn-primary">
				</div>
			</div>
	</form>
</div>
<script language="javascript">
    $(function () {
        $(".switchery").click(function () {
            if ($("#creditdeduct").val()==1) {
            	$("#creditdeduct").val(0);
            	$(".switchery").removeClass("checked");
                $("#creditdeduct-switch").hide();
            }else {
            	$("#creditdeduct").val(1);
            	$(".switchery").addClass("checked");
                $("#creditdeduct-switch").show();
            }
        }); 
    });
</script>

<!-- 优惠券列表 -->
<?php  } else if($op=='coupon') { ?>
<div class="main">
    <div class="panel panel-default">
        <form id="myForm" method="post" class="form-horizontal form">
        <div class="table-responsive panel-body">
            <table class="table table-hover">
                <thead class="navbar-inner">
                <tr>
					<th style="width:4%;"><input type="checkbox" id="checkItems"></th>
                    <th style="width:9%;">排序</th>
                    <th style="width:18%;">优惠券名称</th>
                    <th style="width:10%;">面值</th>
					<th style="width:18%;">有效期</th>
					<th style="width:20%;">使用条件</th>
					<th style="width:10%;">积分兑换<br/>链接领取</th>
					<th style="width:10%;">已领/总量</th>
                    <th style="width:10%;">状态</th>
                    <th style="text-align:right;">操作</th>
                </tr>
                </thead>
                <tbody>
                <?php  if(is_array($list)) { foreach($list as $item) { ?>
                <tr>
                    <td><input type="checkbox" name="ids[]" value="<?php  echo $item['id'];?>"></td>
					<td><input type="text" class="form-control" name="displayorder[<?php  echo $item['id'];?>]" value="<?php  echo $item['displayorder'];?>"></td>
                    <td><?php  echo $item['name'];?></td>
                    <td><?php  echo $item['amount'];?> 元</td>
                    <td>
						<?php  if($item['validity_type']==1) { ?>
							<?php  echo date('Y-m-d H:i',$item['days1'])?>
						<?php  } else if($item['validity_type']==2) { ?>
							自领取后<?php  echo $item['days2'];?>天内有效
						<?php  } ?>
					</td>
					<td>消费满<?php  echo $item['conditions'];?>元，<?php  echo $item['category_name'];?>可用</td>
					<td>
						<?php  if($item['is_exchange'] == 0) { ?>
							<span class="label label-danger">不支持</span>
						<?php  } else if($item['is_exchange'] == 1) { ?>
							<span class="label label-success">支持</span>
						<?php  } ?>

						<?php  if($item['receive_link'] == 0) { ?>
							<span class="label label-default" style="margin-top:5px;display:inline-block;">不支持</span>
						<?php  } else if($item['receive_link'] == 1) { ?>
							<span class="label label-info" style="margin-top:5px;display:inline-block;">支持</span>
						<?php  } ?>
					</td>
					<td><?php  echo $item['already_exchange'];?>/<?php  echo $item['total_exchange'];?></td>
					<td>
						<?php  if($item['status'] == 0) { ?><span class="label label-default">下架</span><?php  } ?>
						<?php  if($item['status'] == 1) { ?><span class="label label-success">上架</span><?php  } ?>
					</td>
                    <td style="text-align:right;">
						<a class="btn btn-default btn-sm" href="<?php  echo $this->createWebUrl('market', array('op' => 'addCoupon', 'coupon_id' => $item['id']))?>" title="编辑优惠券"><i class="fa fa-edit"></i></a>
                    </td>
                </tr>
                <?php  } } ?>
                </tbody>
				<tfoot>
					<tr>
						<td colspan="10">
							<a href="<?php  echo $this->createWebUrl('market',array('op'=>addCoupon));?>" class="btn btn-success"><i class="fa fa-plus"></i> 添加优惠券</a>&nbsp;&nbsp;&nbsp;
							<input name="submitOrder" type="submit" class="btn btn-primary" value="批量修改排序">&nbsp;&nbsp;&nbsp;
							<input name="submit" type="submit" class="btn btn-danger" value="批量删除" onclick="return delAll()">
							<input type="hidden" name="token" value="<?php  echo $_W['token'];?>">
						</td>
					</tr>
				</tfoot>
            </table>
            <?php  echo $pager;?>
        </div>
    </div>
    </form>
</div>
<script type="text/javascript">
var ids = document.getElementsByName('ids[]');
$("#checkItems").click(function(){  
	if (this.checked) {
		for(var i=0;i<ids.length;i++){
			var checkElement=ids[i];
			checkElement.checked="checked";
		}
	}else{  
		for(var i=0;i<ids.length;i++){
			var checkElement=ids[i];  
			checkElement.checked=null;  
		}
	}
});
function delAll(){
	var flag = false;
	for(var i=0;i<ids.length;i++){  
		if(ids[i].checked){
			flag = true;
			break;
		}
	}
	if(!flag){  
		alert("未选中任何选项");
		return false ;
	}
	if(!confirm('该操作无法恢复，确定删除?')){
		return false;
	}

	 document.getElementById('myForm').action = "<?php  echo $this->createWebUrl('market', array('op'=>'delAllCoupon'));?>";
     document.getElementById("myForm").submit();
}
</script>

<!-- 添加优惠券 -->
<?php  } else if($op=='addCoupon') { ?>
<div class="main">
    <form method="post" class="form-horizontal form" enctype="multipart/form-data">
        <div class="panel panel-default">
            <div class="panel-heading"><?php  if($_GPC['coupon_id']>0) { ?>编辑<?php  } else { ?>添加<?php  } ?>优惠券</div>
            <div class="panel-body">
				<div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="request">*</span>优惠券名称</label>
                    <div class="col-sm-9">
                        <input type="text" name="name" value="<?php  echo $coupon['name'];?>" class="form-control">
                    </div>
                </div>
				<div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">优惠券图片</label>
                    <div class="col-sm-9">
                        <?php  echo tpl_form_field_image('images', $coupon['images'])?>
                        <span class="help-block">建议尺寸 200px * 200px，也可根据自己的实际情况做图片尺寸</span>
                    </div>
                </div>
				<div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="request">*</span>优惠券面值</label>
                    <div class="col-sm-9">
                        <div class="input-group">
                            <input type="text" name="amount" value="<?php  echo $coupon['amount'];?>" class="form-control">
                            <span class="input-group-addon">元</span>
                        </div>
                    </div>
                </div>
				<div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="request">*</span>使用金额条件</label>
                    <div class="col-sm-9">
                        <div class="input-group">
                            <input type="text" name="conditions" value="<?php  echo $coupon['conditions'];?>" class="form-control">
                            <span class="input-group-addon">元</span>
                        </div>
                        <div class="help-block">
                            课程订单需满足指定金额方可使用该优惠券
                        </div>
                    </div>
                </div>
				<div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="request">*</span>使用分类条件</label>
                    <div class="col-sm-9">
                        <select name="category_id" class="form-control">
							<option value="0">全部分类</option>
							<?php  if(is_array($category_list)) { foreach($category_list as $item) { ?>
							<option value="<?php  echo $item['id'];?>" <?php  if($item['id']==$coupon['category_id']) { ?>selected<?php  } ?>><?php  echo $item['name'];?></option>
							<?php  } } ?>
						</select>
                        <div class="help-block">
                            指定分类下的课程可使用
                        </div>
                    </div>
                </div>
				<div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="request">*</span>积分兑换</label>
                    <div class="col-sm-9">
						<label class="radio-inline"><input type="radio" name="is_exchange" value="1" <?php  if($coupon['is_exchange']==1) { ?>checked<?php  } ?> onclick="exchange(this.value)"/> 启用</label>&nbsp;
						<label class="radio-inline"><input type="radio" name="is_exchange" value="0" <?php  if($coupon['is_exchange']==0) { ?>checked<?php  } ?> onclick="exchange(this.value)"/> 不启用</label>
						<span class="help-block">选择启用积分兑换优惠券，优惠券将展示在手机端供用户自行兑换</span>
					</div>
                </div>
				<div class="form-group exchange" <?php  if($coupon['is_exchange']!=1) { ?>style="display:none;"<?php  } ?>>
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">兑换积分</label>
					<div class="col-sm-9">
						<div class="input-group">
							<input type="text" name="exchange_integral" value="<?php  echo $coupon['exchange_integral'];?>" class="form-control">
							<span class="input-group-addon">积分</span>
						</div>
						<span class="help-block">设置兑换每张优惠券需要消耗的积分</span>
					</div>
                </div>
				<div class="form-group exchange" <?php  if($coupon['is_exchange']!=1) { ?>style="display:none;"<?php  } ?>>
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">最大兑换数量</label>
					<div class="col-sm-9">
						<div class="input-group">
							<input type="text" name="max_exchange" value="<?php  echo $coupon['max_exchange'];?>" class="form-control">
							<span class="input-group-addon">张</span>
						</div>
						<span class="help-block">每位用户最多可兑换的优惠券数量</span>
					</div>
                </div>
				<div class="form-group exchange" <?php  if($coupon['is_exchange']!=1) { ?>style="display:none;"<?php  } ?>>
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">兑换总数量</label>
					<div class="col-sm-9">
						<div class="input-group">
							<input type="text" name="total_exchange" value="<?php  echo $coupon['total_exchange'];?>" class="form-control">
							<span class="input-group-addon">张</span>
						</div>
					</div>
                </div>
				<div class="form-group exchange" <?php  if($coupon['is_exchange']!=1) { ?>style="display:none;"<?php  } ?>>
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">已兑换数量</label>
					<div class="col-sm-9">
						<div class="input-group">
							<input type="text" name="already_exchange" value="<?php  echo $coupon['already_exchange'];?>" class="form-control">
							<span class="input-group-addon">张</span>
						</div>
					</div>
                </div>
				<div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="request">*</span>有效期方式</label>
                    <div class="col-sm-9">
						<label class="radio-inline"><input type="radio" name="validity_type" value="1" <?php  if($coupon['validity_type']==1) { ?>checked<?php  } ?> onclick="changeType(this.value)"/> 固定日期</label>&nbsp;
						<label class="radio-inline"><input type="radio" name="validity_type" value="2" <?php  if($coupon['validity_type']==2) { ?>checked<?php  } ?> onclick="changeType(this.value)"/> 自增天数</label>
						<span class="help-block">固定日期为指定日期前有效，自增天数为自用户领取时往后指定时间内有效</span>
					</div>
                </div>
				<div id="validity1" class="form-group" <?php  if($coupon['validity_type']!=1) { ?>style="display:none;"<?php  } ?>>
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">固定有效期</label>
                    <div class="col-sm-9">
						<?php  echo tpl_form_field_date('days1', $coupon['days1'], true);?>
						<span class="help-block">指定日期前，该优惠券有效</span>
					</div>
                </div>
				<div id="validity2" class="form-group" <?php  if($coupon['validity_type']!=2) { ?>style="display:none;"<?php  } ?>>
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">自增有效期</label>
					<div class="col-sm-9">
						<div class="input-group">
							<input type="text" name="days2" value="<?php  echo $coupon['days2'];?>" class="form-control">
							<span class="input-group-addon">天</span>
						</div>
						<span class="help-block">用户领取之后，指定天数内该优惠券有效</span>
					</div>
                </div>
				<div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="request">*</span>排序</label>
                    <div class="col-sm-9">
                        <input type="text" name="displayorder" value="<?php  echo $coupon['displayorder'];?>" class="form-control">
						<span class="help-block">排序越大，排名越靠前</span>
                    </div>
                </div>
				<div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="request">*</span>状态</label>
                    <div class="col-sm-9">
						<label class="radio-inline"><input type="radio" name="status" value="1" <?php  if($coupon['status']==1) { ?>checked<?php  } ?>/> 上架</label>&nbsp;
						<label class="radio-inline"><input type="radio" name="status" value="0" <?php  if($coupon['status']==0) { ?>checked<?php  } ?>/> 下架</label>
						<span class="help-block">用户将无法领取或获得下架的优惠券，已获得的优惠券继续使用</span>
					</div>
                </div>
				<div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="request">*</span>链接领取</label>
                    <div class="col-sm-9">
						<label class="radio-inline"><input type="radio" name="receive_link" value="1" <?php  if($coupon['receive_link']==1) { ?>checked<?php  } ?>/> 支持</label>&nbsp;
						<label class="radio-inline"><input type="radio" name="receive_link" value="0" <?php  if($coupon['receive_link']==0) { ?>checked<?php  } ?>/> 不支持</label>
						<span class="help-block">
							<a href="javascript:;" id="copy-btn"><?php  echo $_W['siteroot'];?>app/<?php  echo str_replace("./", "", $this->createMobileUrl('getcoupon', array('op'=>'free','id'=>$coupon['id'])));?></a><br/>
							链接领取指用户通过链接即可免费领取优惠券，每人最大兑换数量同“最大兑换数量”一样
						</span>
					</div>
                </div>
            </div>
        </div>

        <div class="form-group col-sm-12">
            <input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-1"/>
            <input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
        </div>
    </form>
</div>
<script type="text/javascript">
function changeType(type){
	if(type==1){
		$("#validity1").show();
		$("#validity2").hide();
	}else{
		$("#validity2").show();
		$("#validity1").hide();
	}
}
function exchange(status){
	if(status==1){
		$(".exchange").show();
	}else{
		$(".exchange").hide();
	}
}

require(['jquery', 'util'], function($, util){
	$(function(){
		util.clip($("#copy-btn")[0], $("#copy-btn").text());
	});
});
</script>

<!-- 优惠券规则 -->
<?php  } else if($op=='couponRule') { ?>
<div class="main">
	<form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
		<div class="panel panel-default">
			<div class="panel-heading">优惠券规则列表</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-sm-2 control-label">【新会员注册】</label>
					<div class="col-xs-9 col-sm-9" style="margin-top: 7px;">
					   <?php  if(is_array($coupon_list)) { foreach($coupon_list as $item) { ?>
							<label>
								<input type="checkbox" name="reg_give[]" value="<?php  echo $item['id'];?>" <?php  if(in_array($item['id'],$regGive)) { ?>checked<?php  } ?>><?php  echo $item['name'];?>&nbsp;&nbsp;
							</label>
					   <?php  } } ?>
					</div>
				</div>

				<div class="form-group">
					<label class="col-sm-2 control-label">【推荐下级】</label>
					<div class="col-xs-9 col-sm-9" style="margin-top: 7px;">
					   <?php  if(is_array($coupon_list)) { foreach($coupon_list as $item) { ?>
							<label>
								<input type="checkbox" name="recommend[]" value="<?php  echo $item['id'];?>" <?php  if(in_array($item['id'],$recommend)) { ?>checked<?php  } ?>><?php  echo $item['name'];?>&nbsp;&nbsp;
							</label>
					   <?php  } } ?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label"></label>
					<div class="col-sm-5">
						<div class="input-group">
							<span class="input-group-addon">最多可获取</span>
							<input type="text" name="recommend_time" value="<?php  echo $market['recommend_time'];?>" class="form-control">
							<span class="input-group-addon">张</span>
						</div>
						<span class="help-block">非严格数值，填写0为不限制</span>
					</div>
				</div>

				<div class="form-group">
					<label class="col-sm-2 control-label">【购买课程】</label>
					<div class="col-xs-9 col-sm-9" style="margin-top: 7px;">
					   <?php  if(is_array($coupon_list)) { foreach($coupon_list as $item) { ?>
							<label>
								<input type="checkbox" name="buy_lesson[]" value="<?php  echo $item['id'];?>" <?php  if(in_array($item['id'],$buyLesson)) { ?>checked<?php  } ?>><?php  echo $item['name'];?>&nbsp;&nbsp;
							</label>
					   <?php  } } ?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label"></label>
					<div class="col-sm-5">
						<div class="input-group">
							<span class="input-group-addon">最多可获取</span>
							<input type="text" name="buy_lesson_time" value="<?php  echo $market['buy_lesson_time'];?>" class="form-control">
							<span class="input-group-addon">张</span>
						</div>
						<span class="help-block">非严格数值，填写0为不限制</span>
					</div>
				</div>

				<div class="form-group">
					<label class="col-sm-2 control-label">【分享课程】</label>
					<div class="col-xs-9 col-sm-9" style="margin-top: 7px;">
					   <?php  if(is_array($coupon_list)) { foreach($coupon_list as $item) { ?>
							<label>
								<input type="checkbox" name="share_lesson[]" value="<?php  echo $item['id'];?>" <?php  if(in_array($item['id'],$shareLesson)) { ?>checked<?php  } ?>><?php  echo $item['name'];?>&nbsp;&nbsp;
							</label>
					   <?php  } } ?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label"></label>
					<div class="col-sm-5">
						<div class="input-group">
							<span class="input-group-addon">最多可获取</span>
							<input type="text" name="share_lesson_time" value="<?php  echo $market['share_lesson_time'];?>" class="form-control">
							<span class="input-group-addon">张</span>
						</div>
						<span class="help-block">非严格数值，填写0为不限制</span>
					</div>
				</div>

				<div class="form-group">
					<label class="col-sm-2 control-label">优惠券说明</label>
					<div class="col-sm-9">
						<textarea class="form-control" name="coupon_desc" style="height:150px;"><?php  echo $market['coupon_desc'];?></textarea>
						<span class="help-block">该说明将显示在前台优惠券页面底部，第一行为标题，从第二行开始换行标识新的一段</span>
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

<!-- 发放优惠券 -->
<?php  } else if($op=='sendCoupon') { ?>
<style type="text/css">
.mloading-bar {
    width: 300px;
    min-height: 22px;
    text-align: center;
    background: #fff;
    box-shadow: 0px 1px 1px 2px rgba(0, 0, 0, 0.3);
    border-radius: 7px;
    padding: 20px 15px;
    font-size: 14px;
    color: #000;
    position: absolute;
    top: 42%;
    left: 50%;
    margin-left: -140px;
    margin-top: -30px;
    word-break: break-all;
	z-index:999;
	display:none;
}
#overlay{
	background:#000;
	position: absolute;
	top: 0px;
	left: 0px;
	width: 100%;
	height: 100%;
	z-index: 100;
	display:none;
}
</style>
<div class="mloading-bar" style="margin-top: -31px; margin-left: -140px;"><img src="<?php echo MODULE_URL;?>template/mobile/images/download.gif"><span class="mloading-text">正在发放优惠券，请稍等...</span></div>
<div id="overlay"></div>
<div class="main">
	<div class="alert alert-info">
	    单次发放大量优惠券会占用服务器资源，建议在夜间访问量少的时候进行该操作。为了减少微信模版消息违规情况，后台发放优惠券不再发送模版消息通知用户。
	</div>
    <form method="post" class="form-horizontal form" enctype="multipart/form-data" onsubmit="return check();">
        <div class="panel panel-default">
            <div class="panel-heading">发放优惠券</div>
            <div class="panel-body">
				<div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">优惠券</label>
                    <div class="col-sm-9">
                        <select name="coupon_id" class="form-control">
							<option value="">请选择优惠券</option>
							<?php  if(is_array($couponList)) { foreach($couponList as $item) { ?>
							<option value="<?php  echo $item['id'];?>"><?php  echo $item['name'];?></option>
							<?php  } } ?>
						</select>
                    </div>
                </div>
				<div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">发放方式</label>
                    <div class="col-sm-9">
						<!-- <label class="radio-inline"><input type="radio" name="send_type" value="1" onclick="checkType(this.value)" checked/> 全部会员</label>&nbsp; -->
						<label class="radio-inline"><input type="radio" name="send_type" value="2" onclick="checkType(this.value)" checked/> 指定会员</label>&nbsp;
						<label class="radio-inline"><input type="radio" name="send_type" value="3" onclick="checkType(this.value)"/> 指定VIP等级</label>
						<label class="radio-inline"><input type="radio" name="send_type" value="4" onclick="checkType(this.value)"/> 指定加入日期</label>
					</div>
                </div>
				<div id="uids" class="form-group hide-type">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">会员uid</label>
                    <div class="col-sm-9">
						<textarea name="uids" class="form-control"></textarea>
						<span class="help-block">请输入会员uid，多个会员uid请用英文半角逗号","隔开</span>
                    </div>
                </div>
				<div id="vip-level" class="form-group hide-type" style="display:none;">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">会员等级</label>
                    <div class="col-sm-9">
                        <select name="level_id" class="form-control">
							<option value="">请选择会员等级</option>
							<?php  if(is_array($levelList)) { foreach($levelList as $item) { ?>
							<option value="<?php  echo $item['id'];?>"><?php  echo $item['level_name'];?></option>
							<?php  } } ?>
						</select>
                    </div>
                </div>
				<div id="reg-date" class="form-group hide-type" style="display:none;">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">加入日期</label>
                    <div class="col-sm-9">
						<?php  echo tpl_form_field_daterange('time', array('starttime'=>false,'endtime'=>false));?>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group col-sm-12">
            <input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-1"/>
            <input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
        </div>
    </form>
</div>
<script type="text/javascript">
function checkType(type){
	$(".hide-type").hide();
	if(type==2){
		$("#uids").show();
	}else if(type==3){
		$("#vip-level").show();
	}else if(type==4){
		$("#reg-date").show();
	}
}
function check(){
	/* 显示遮罩层 */
	$("#overlay").height("100%");
    $("#overlay").width("100%");
    $("#overlay").fadeTo(200, 0.2);
	$(".mloading-bar").show();
}
</script>

<!-- 优惠券记录 -->
<?php  } else if($op=='couponLog') { ?>
<div class="main">
    <div class="panel panel-info">
        <div class="panel-heading">筛选</div>
        <div class="panel-body">
            <form method="get" class="form-horizontal" role="form">
                <input type="hidden" name="c" value="site" />
                <input type="hidden" name="a" value="entry" />
                <input type="hidden" name="m" value="fy_lessonv2" />
                <input type="hidden" name="do" value="market" />
                <input type="hidden" name="op" value="couponLog" />
                <div class="form-group">
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label" style="width: 100px;">用户信息</label>
                    <div class="col-sm-2 col-lg-3">
                        <input class="form-control" name="nickname" type="text" value="<?php  echo $_GPC['nickname'];?>" placeholder="昵称/姓名/手机号码">
                    </div>
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width:100px;">状态</label>
                    <div class="col-sm-8 col-lg-3 col-xs-12">
                        <select name="status" class="form-control">
                            <option value="">不限</option>
							<option value="0" <?php  if(in_array($_GPC['status'],array('0'))) { ?> selected="selected" <?php  } ?>>未使用</option>
							<option value="1" <?php  if($_GPC['status'] == 1) { ?> selected="selected" <?php  } ?>>已使用</option>
                            <option value="-1" <?php  if($_GPC['status'] == -1) { ?> selected="selected" <?php  } ?>>已过期</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width: 100px;">添加时间</label>
                    <div class="col-sm-8 col-lg-3 col-xs-12">
                        <?php echo tpl_form_field_daterange('time', array('starttime'=>($starttime ? date('Y-m-d', $starttime) : false),'endtime'=> ($endtime ? date('Y-m-d', $endtime) : false)));?>
                    </div>
                    <div class="col-sm-3 col-lg-3">
                        <button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>&nbsp;&nbsp;&nbsp;
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="panel panel-default">
        <form action="" method="post" class="form-horizontal form" >
        <div class="table-responsive panel-body">
            <table class="table table-hover">
                <thead class="navbar-inner">
                <tr>
                    <!-- <th style="width:8%;">编号</th> -->
					<th style="width:8%;">会员id</th>
                    <th style="width:16%;">昵称/手机号码</th>
					<th style="width:12%;">优惠券面值</th>
                    <th style="width:20%;">使用条件</th>
                    <th style="width:15%;">有效期</th>
					<th style="width:10%;">状态</th>
					<th style="width:15%;">添加时间</th>
                    <th style="text-align:right;">操作</th>
                </tr>
                </thead>
                <tbody>
                <?php  if(is_array($list)) { foreach($list as $item) { ?>
                <tr>
                    <!-- <td><?php  echo $item['id'];?></td> -->
                    <td><?php  echo $item['uid'];?></td>
					<td><?php  echo $item['nickname'];?><br/><?php  echo $item['mobile'];?></td>
					<td><?php  echo $item['amount'];?>元</td>
                    <td>满<?php  echo $item['conditions'];?>元<br/><?php  echo $item['category_name'];?> 可用</td>
                    <td><?php  echo date('Y-m-d H:i', $item['validity'])?></td>
					<td>
						<?php  if($item['status']==0) { ?>
							<span class="label label-success">未使用</span>
						<?php  } else if($item['status']==1) { ?>
							<span class="label label-danger">已使用</span>
						<?php  } else if($item['status']==-1) { ?>
							<span class="label label-default">已过期</span>
						<?php  } ?>
					</td>
                    <td><?php  echo date('Y-m-d H:i', $item['addtime'])?></td>
                    <td style="text-align:right;">
                        <a class="btn btn-default btn-sm" href="<?php  echo $this->createWebUrl('market', array('op' => 'couponDetail', 'id' => $item['id']))?>" title="查看"><i class="fa fa-pencil"></i></a>
                    </td>
                </tr>
                <?php  } } ?>
                </tbody>
            </table>
            <?php  echo $pager;?>
        </div>
    </div>
    </form>
</div>

<!-- 优惠券记录详情 -->
<?php  } else if($op=='couponDetail') { ?>
<style type="text/css">
.form-group{padding: 20px 0;}
</style>
<div class="main">
	<div class="panel panel-default">
		<div class="panel-heading">
			优惠券详情
		</div>
		<div class="panel-body">
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">会员ID/昵称</label>
				<div class="col-sm-9">
					<p class="form-control-static"><?php  echo $member_coupon['uid'];?> / <?php  echo $member_coupon['nickname'];?></p>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">会员姓名/手机号码</label>
				<div class="col-sm-9">
					<p class="form-control-static"><?php  echo $member_coupon['realname'];?> / <?php  echo $member_coupon['mobile'];?></p>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">优惠券状态</label>
				<div class="col-sm-9">
					<p class="form-control-static">
						<?php  if($member_coupon['status']==0) { ?>
							<span class="label label-success">未使用</span>
						<?php  } else if($member_coupon['status']==1) { ?>
							<span class="label label-danger">已使用</span>
						<?php  } else if($member_coupon['status']==-1) { ?>
							<span class="label label-default">已过期</span>
						<?php  } ?>
					</p>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">优惠券面值</label>
				<div class="col-sm-9">
					<p class="form-control-static"><?php  echo $member_coupon['amount'];?> 元</p>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">使用条件</label>
				<div class="col-sm-9">
					<p class="form-control-static"><?php  echo $category_name;?>，消费满<?php  echo $member_coupon['conditions'];?>元可用</p>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">优惠券有效期</label>
				<div class="col-sm-9">
					<p class="form-control-static"><?php  echo date('Y-m-d H:i:s', $member_coupon['validity']);?></p>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">使用订单号</label>
				<div class="col-sm-9">
					<p class="form-control-static"><?php echo $member_coupon['ordersn'] ? $member_coupon['ordersn'] : "无";?></p>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">使用时间</label>
				<div class="col-sm-9">
					<p class="form-control-static">
					<?php  if($member_coupon['update_time']>0) { ?>
						<?php  echo date('Y-m-d H:i:s', $member_coupon['update_time']);?>
					<?php  } else { ?>
						无
					<?php  } ?>
					</p>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">来源</label>
				<div class="col-sm-9">
					<p class="form-control-static">
						<?php  echo $source[$member_coupon['source']];?>
					</p>
				</div>
			</div>
		</div>
	</div>
	<div class="form-group col-sm-12">
		<input type="button" onclick="window.history.go(-1);" value="返回" class="btn btn-default col-lg-1">
	</div>
</div>

<!-- 限时折扣 -->
<?php  } else if($op=='discount') { ?>
<div class="main">
	<div class="alert alert-info">
	    每个课程仅可以关联到一个限时折扣活动，已过期的活动请自行删除活动。
	</div>
    <div class="panel panel-info">
        <div class="panel-heading">筛选</div>
        <div class="panel-body">
            <form method="get" class="form-horizontal" role="form">
                <input type="hidden" name="c" value="site" />
                <input type="hidden" name="a" value="entry" />
                <input type="hidden" name="m" value="fy_lessonv2" />
                <input type="hidden" name="do" value="market" />
                <input type="hidden" name="op" value="discount" />
                <div class="form-group">
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label" style="width: 100px;">活动名称</label>
                    <div class="col-sm-2 col-lg-3">
                        <input class="form-control" name="keyword" type="text" value="<?php  echo $_GPC['keyword'];?>" placeholder="活动名称">
                    </div>
					<div class="col-sm-3 col-lg-3">
                        <button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>
						&nbsp;&nbsp;&nbsp;
						<a href="<?php  echo $this->createWebUrl('market', array('op' => 'addDiscount'))?>" class="btn btn-success"><i class="fa fa-plus"></i> 添加活动</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="panel panel-default">
        <form action="" method="post" class="form-horizontal form" >
        <div class="table-responsive panel-body">
            <table class="table table-hover">
                <thead class="navbar-inner">
                <tr>
					<th style="width:8%;">编号</th>
                    <th style="width:20%;">活动名称</th>
                    <th style="width:20%;">起止时间</th>
					<th style="width:10%;">状态</th>
					<th style="width:10%;">排序</th>
					<th style="width:15%;">创建时间</th>
                    <th style="text-align:right;">操作</th>
                </tr>
                </thead>
                <tbody>
                <?php  if(is_array($list)) { foreach($list as $item) { ?>
                <tr>
                    <td><?php  echo $item['discount_id'];?></td>
					<td><?php  echo $item['title'];?></td>
                    <td><?php  echo date('Y-m-d H:i', $item['starttime']);?> ~ <?php  echo date('Y-m-d H:i', $item['endtime']);?></td>
					<td>
						<?php  if($item['endtime'] < time()) { ?>
							<span class="label label-danger">已过期</span>
						<?php  } else if($item['starttime'] > time()) { ?>
							<span class="label label-default">未开始</span>
						<?php  } else if($item['starttime'] < time() && $item['endtime'] > time()) { ?>
							<span class="label label-success">进行中</span>
						<?php  } ?>
					</td>
					<td><?php  echo $item['displayorder'];?></td>
                    <td><?php  echo date('Y-m-d H:i', $item['addtime'])?></td>
                    <td style="text-align:right;">
                        <a class="btn btn-default btn-sm" href="<?php  echo $this->createWebUrl('market', array('op' => 'discountLesson', 'discount_id' => $item['discount_id']))?>" title="管理活动"><i class="fa fa-plus"></i></a>
						<a class="btn btn-default btn-sm" href="<?php  echo $this->createWebUrl('market', array('op' => 'addDiscount', 'discount_id' => $item['discount_id']))?>" title="编辑"><i class="fa fa-pencil"></i></a>
						<a class="btn btn-default btn-sm" href="<?php  echo $this->createWebUrl('market', array('op' => 'delDiscount', 'discount_id' => $item['discount_id']))?>" title="删除" onclick="return confirm('该操作不可恢复，确认删除?');return false;"><i class="fa fa-times"></i></a>
                    </td>
                </tr>
                <?php  } } ?>
                </tbody>
            </table>
            <?php  echo $pager;?>
        </div>
    </div>
    </form>
</div>

<?php  } else if($op=='addDiscount') { ?>
<div class="main">
	<form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
		<div class="panel panel-default">
			<div class="panel-heading">限时折扣活动设置</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">活动名称</label>
					<div class="col-sm-9">
						<input type="text" name="title" value="<?php  echo $discount['title'];?>" class="form-control" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">会员折扣叠加</label>
					<div class="col-sm-9">
						<label class="radio-inline"><input type="radio" name="member_discount" value="0" <?php  if($discount['member_discount']==0) { ?>checked<?php  } ?> /> 不叠加</label>&nbsp;
						<label class="radio-inline"><input type="radio" name="member_discount" value="1" <?php  if($discount['member_discount']==1) { ?>checked<?php  } ?> /> 叠加</label>
						<span class="help-block">开启会员折扣叠加后，例如100元课程，VIP会员折扣8折，如果此时限时折扣6折，那会员实际支付就是100元x80%x60%=48元 </span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">活动时间</label>
					<div class="col-sm-9">
						<?php  echo tpl_form_field_daterange('time', array('starttime'=>$starttime,'endtime'=> $endtime), true);?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">排序</label>
					<div class="col-sm-9">
						<input type="text" name="displayorder" value="<?php  echo $discount['displayorder'];?>" class="form-control" />
						<span class="help-block">排序越大，活动越靠前</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">活动链接</label>
					<div class="col-sm-9">
					<?php  if($discount_id>0) { ?>
						<a href="javascript:;" id="copy-btn"><?php  echo $_W['siteroot'];?>app/<?php  echo str_replace("./", "", $this->createMobileUrl('discount', array('discount_id'=>$discount_id)));?></a>
						<span class="help-block">点击链接即可完成复制</span>
					<?php  } else { ?>
						<span class="help-block">发布活动后自动显示</span>
					<?php  } ?>
					</div>
				</div>
			</div>
		</div>

		<div class="form-group col-sm-12">
			<input type="hidden" name="id" value="<?php  echo $discount_id;?>" />
			<input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-1" />
			<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
		</div>
	</form>
</div>
<script type="text/javascript">
require(['jquery', 'util'], function($, util){
	$(function(){
		util.clip($("#copy-btn")[0], $("#copy-btn").text());
	});
});
</script>

<?php  } else if($op=='discountLesson') { ?>
<div class="main">
	<div class="panel panel-default">
        <form id="myform" name="myform" method="post" class="form-horizontal form" >
			<div class="table-responsive panel-body">
				<table class="table table-hover">
					<thead class="navbar-inner">
					<tr>
						<th style="text-align:center;width:40px;"><input type="checkbox" id="btnSelect" class="btn btn-default" onclick="checkAll(myform.hidnSelectFlag.value);" /></th>
						<th style="width:40%;padding-left:30px;">课程名称</th>
						<th style="width:12%;">课程价格</th>
						<th style="width:12%;">课程折扣</th>
						<th style="width:35%;">起止时间</th>
					</tr>
					</thead>
					<tbody>
					<?php  if(is_array($list)) { foreach($list as $item) { ?>
					<tr>
						<td style="text-align:center;"><input type="checkbox" name="id[]" value="<?php  echo $item['id'];?>"></td>
						<td style="padding-left:30px;">[ID: <?php  echo $item['id'];?>] <?php  echo $item['bookname'];?></td>
						<td><?php echo $item['price']?$item['price'].'元':'免费';?></td>
						<td><?php  echo $item['discount'];?>%</td>
						<td><?php  echo date('Y-m-d H:i', $item['starttime']);?> ~ <?php  echo date('Y-m-d H:i', $item['endtime']);?></td>
					</tr>
					<?php  } } ?>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="6" style="padding-top: 30px;">
								<a href="<?php  echo $this->createWebUrl('market', array('op'=>'addDiscountLesson','discount_id'=>$discount_id));?>" class="btn btn-success"><i class="fa fa-plus"></i> 添加课程</a>
								&nbsp;&nbsp;&nbsp;
								<a onclick="addclassToRec('cancel');" class="btn btn-danger"><i class="fa fa-minus"></i> 批量取消</a>
							</td>
						</tr>
					</tfoot>
				</table>
				<?php  echo $pager;?>
			</div>
			<input type="hidden"  name="discount_id" value="<?php  echo $discount_id;?>"/>
			<input type="hidden"  name="hidnSelectFlag" value="1"/>
		</form>
    </div>
</div>
<script language="javascript">
function checkAll(type) {
    var type = Number(type);
    var inputs = document.getElementsByTagName("input");
    for(var i = 0; i < inputs.length; i++) {
        if(inputs[i].type == "checkbox") {
            inputs[i].checked = type;
        }
    }
    myform.hidnSelectFlag.value = Number(!type);
}

function addclassToRec(obj){
	if(obj!=''){
		var check = $("input[type=checkbox][class!=check_all]:checked");
        if(check.length < 1){
            alert('您还没有没有任何课程');
            return false;
        }
		document.getElementById("myform").action="<?php  echo $this->createWebUrl('market', array('op'=>'discountLessonPost','posttype'=>'cancel'));?>";

		document.getElementById("myform").submit();
	}
}
</script>

<?php  } else if($op=='addDiscountLesson') { ?>
<div class="main">
	<div class="alert alert-info">
	    已添加的课程表示该课程已被添加到其他的限时折扣活动里，您可以从其他的限时折扣活动里删除该课程的活动，过期的限时折扣活动可以删掉整个活动。
	</div>
	<div class="panel panel-info">
        <div class="panel-heading">筛选</div>
        <div class="panel-body">
            <form action="./index.php" method="get" class="form-horizontal" role="form">
                <input type="hidden" name="c" value="site">
                <input type="hidden" name="a" value="entry">
                <input type="hidden" name="m" value="fy_lessonv2">
                <input type="hidden" name="do" value="market">
                <input type="hidden" name="op" value="addDiscountLesson">
				<input type="hidden" name="discount_id" value="<?php  echo $discount_id;?>">
                <div class="form-group">
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width:100px;">课程名称</label>
                    <div class="col-sm-2 col-lg-3">
                        <input class="form-control" name="bookname" type="text" value="<?php  echo $_GPC['bookname'];?>">
                    </div>
					<div class="col-sm-3 col-lg-3" style="width: 18%;">
                        <button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
	<div class="panel panel-default">
        <form id="myform" name="myform" method="post" class="form-horizontal form" >
			<div class="table-responsive panel-body">
				<table class="table table-hover">
					<thead class="navbar-inner">
					<tr>
						<th style="text-align:center;width:40px;">
							<input type="checkbox" id="btnSelect" class="btn btn-default" onclick="checkAll(myform.hidnSelectFlag.value);" />
						</th>
						<th style="width:50%;padding-left:30px;">课程名称</th>
						<th style="width:15%;">课程价格</th>
						<th style="width:12%;">状态</th>
						<th style="width:18%;">发布时间</th>
					</tr>
					</thead>
					<tbody>
					<?php  if(is_array($list)) { foreach($list as $item) { ?>
					<tr>
						<td style="text-align:center;">
							<?php  if(in_array($item['id'], $lesson_ids)) { ?>
								<input type="checkbox" disabled="true" title="已添加">
							<?php  } else { ?>
								<input type="checkbox" name="id[]" value="<?php  echo $item['id'];?>">
							<?php  } ?>
						</td>
						<td style="padding-left:30px;">[ID: <?php  echo $item['id'];?>] <?php  echo $item['bookname'];?></td>
						<td><?php echo $item['price']?$item['price'].'元':'免费';?></td>
						<td>
							<?php  if(in_array($item['id'], $lesson_ids)) { ?>
								<span class="label label-danger">已添加</span>
							<?php  } else { ?>
								<span class="label label-success">未添加</span>
							<?php  } ?>
						</td>
						<td><?php  echo date('Y-m-d H:i', $item['addtime']);?></td>
					</tr>
					<?php  } } ?>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="6" style="padding-top: 30px;">
								<a class="btn btn-success" onclick="batchAdd()"><i class="fa fa-plus"></i> 批量添加</a>
							</td>
						</tr>
					</tfoot>
				</table>
				<?php  echo $pager;?>
			</div>
			<div class="modal fade in" id="discount" tabindex="-1">
				<div class="we7-modal-dialog modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
							<div class="modal-title">设置课程折扣</div>
						</div>
						<div class="modal-body">
							<div class="we7-form">
								<div class="form-group">
									<label class="control-label col-sm-2">课程折扣</label>
									<div class="form-controls col-sm-10">
										<div class="input-group">
											<input type="text" name="discount" value="" class="form-control ng-pristine ng-untouched ng-valid ng-not-empty" placeholder="请输入小于100的整数">
											<a href="javascript:;" class="input-group-addon">%</a>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-primary" onclick="addclassToRec('add');">确定</button>
							<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
						</div>
					</div>
				</div>
			</div>
			<input type="hidden"  name="discount_id" value="<?php  echo $discount_id;?>"/>
			<input type="hidden"  name="hidnSelectFlag" value="1"/>
		</form>
    </div>
</div>
<script language="javascript">
function checkAll(type) {
    var type = Number(type);
    var inputs = document.getElementsByTagName("input");
    for(var i = 0; i < inputs.length; i++) {
        if(inputs[i].type == "checkbox") {
            inputs[i].checked = type;
        }
    }
    myform.hidnSelectFlag.value = Number(!type);
}

function batchAdd(){
	var check = $("input[type=checkbox][class!=check_all]:checked");
	if(check.length < 1){
		alert('您还没有没有任何课程');
		return false;
	}
	$('#discount').modal();
}

function addclassToRec(obj){
	if(obj!=''){
		var check = $("input[type=checkbox][class!=check_all]:checked");
        if(check.length < 1){
            alert('您还没有没有任何课程');
            return false;
        }

		document.getElementById("myform").action="<?php  echo $this->createWebUrl('market', array('op'=>'discountLessonPost'));?>";

		document.getElementById("myform").submit();
	}
}
</script>

<?php  } ?>


<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>