<?php defined('IN_IA') or exit('Access Denied');?><!-- 
 * 课程订单管理
 * ============================================================================
 * 版权所有 2015-2018 微课堂团队，并保留所有权利。
 * 网站地址: https://wx.haoshu888.com
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！不允许对程序代码以任何形式任何目的的再发布，作者将保留
 * 追究法律责任的权力和最终解释权。
 * ============================================================================
-->
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<link href="<?php echo MODULE_URL;?>template/web/style/fycommon.css" rel="stylesheet">
<ul class="nav nav-tabs">
	<li <?php  if($op=='display') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('order');?>">课程订单管理</a></li>
	<?php  if($op=='detail') { ?>
	<li <?php  if($op=='detail') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('order', array('op'=>'detail','id'=>$_GPC['id']));?>">课程订单详情</a></li>
	<?php  } ?>
	<li <?php  if($op=='createOrder') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('order', array('op'=>'createOrder'));?>">创建课程订单</a></li>
	<li <?php  if($op=='couponCode') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('order', array('op'=>'couponCode'));?>">课程优惠码</a></li>
</ul>
<?php  if($operation == 'display') { ?>
<style>
.page-nav {
	margin: 0;
	width: 100%;
	min-width: 800px;
}

.page-nav > li > a {
	display: block;
}

.page-nav-tabs {
	background: #EEE;
}

.page-nav-tabs > li {
	line-height: 40px;
	float: left;
	list-style: none;
	display: block;
	text-align: -webkit-match-parent;
}

.page-nav-tabs > li > a {
	font-size: 14px;
	color: #666;
	height: 40px;
	line-height: 40px;
	padding: 0 10px;
	margin: 0;
	border: 1px solid transparent;
	border-bottom-width: 0px;
	-webkit-border-radius: 0;
	-moz-border-radius: 0;
	border-radius: 0;
}

.page-nav-tabs > li > a, .page-nav-tabs > li > a:focus {
	border-radius: 0 !important;
	background-color: #f9f9f9;
	color: #999;
	margin-right: -1px;
	position: relative;
	z-index: 11;
	border-color: #c5d0dc;
	text-decoration: none;
}

.page-nav-tabs >li >a:hover {
	background-color: #FFF;
}

.page-nav-tabs > li.active > a, .page-nav-tabs > li.active > a:hover, .page-nav-tabs > li.active > a:focus {
	color: #576373;
	border-color: #c5d0dc;
	border-top: 2px solid #4c8fbd;
	border-bottom-color: transparent;
	background-color: #FFF;
	z-index: 12;
	margin-top: -1px;
	box-shadow: 0 -2px 3px 0 rgba(0, 0, 0, 0.15);
}
</style>
<div class="main">
    <div class="panel panel-info">
        <div class="panel-heading">筛选</div>
        <div class="panel-body">
            <form action="./index.php" method="get" class="form-horizontal" role="form">
                <input type="hidden" name="c" value="site" />
                <input type="hidden" name="a" value="entry" />
                <input type="hidden" name="m" value="fy_lessonv2" />
                <input type="hidden" name="do" value="order" />
                <input type="hidden" name="op" value="display" />
                <div class="form-group">
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width:100px;">订单信息</label>
                    <div class="col-sm-2 col-lg-3">
                        <input class="form-control" name="keyword" type="text" value="<?php  echo $_GPC['keyword'];?>" placeholder="订单编号/课程名称">
                    </div>
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label" style="width: 100px;">用户信息</label>
                    <div class="col-sm-2 col-lg-3">
                        <input class="form-control" name="nickname" id="" type="text" value="<?php  echo $_GPC['nickname'];?>" placeholder="昵称/姓名/手机号码">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width:100px;">订单状态</label>
                    <div class="col-sm-8 col-lg-3 col-xs-12">
                        <select name="status" class="form-control">
                            <option value="">不限</option>
							<?php  if(is_array($orderStatusList)) { foreach($orderStatusList as $key => $item) { ?>
								<option value="<?php  echo $key;?>" <?php  if($_GPC['status'] == "$key") { ?> selected="selected" <?php  } ?>><?php  echo $item;?></option>
							<?php  } } ?>
                        </select>
                    </div>
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label" style="width: 100px;">核销状态</label>
                    <div class="col-sm-8 col-lg-3 col-xs-12">
                        <select name="is_verify" class="form-control">
                            <option value="">不限</option>
							<option value="0" <?php  if($_GPC['is_verify'] == '0') { ?> selected="selected" <?php  } ?>>未核销</option>
							<option value="1" <?php  if($_GPC['is_verify'] == 1) { ?> selected="selected" <?php  } ?>>已核销</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width:100px;">订单有效期</label>
                    <div class="col-sm-8 col-lg-3 col-xs-12">
                        <select name="validity" class="form-control">
                            <option value="">全部</option>
                            <option value="2" <?php  if($_GPC['validity'] == 2) { ?> selected="selected" <?php  } ?>>已过期</option>
                        </select>
                    </div>
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width: 100px;">下单时间</label>
                    <div class="col-sm-8 col-lg-3 col-xs-12">
                        <?php echo tpl_form_field_daterange('time', array('starttime'=>($starttime ? date('Y-m-d', $starttime) : false),'endtime'=> ($endtime ? date('Y-m-d', $endtime) : false)));?>
                    </div>
                    <div class="col-sm-3 col-lg-3">
                        <button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>&nbsp;&nbsp;&nbsp;
						<button type="submit" name="export" value="1" class="btn btn-success">导出 Excel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <ul class="page-nav page-nav-tabs" style="background:none;float: left;margin-left: 0px;padding-left: 0px;border-bottom:1px #c5d0dc solid;">
        <li<?php  if($_GPC['status']=='' && !$_GPC['is_delete']) { ?> class="active"<?php  } ?>>
        <a href="<?php  echo $this->createWebUrl('order', array('op' => 'display'))?>">全部订单</a>
        </li>
		<li<?php  if(in_array($_GPC['status'], array('0'))) { ?> class="active"<?php  } ?>>
        <a href="<?php  echo $this->createWebUrl('order', array('op' => 'display', 'status' => 0))?>">待付款订单</a>
        </li>
        <li<?php  if($_GPC['status']==1) { ?> class="active"<?php  } ?>>
        <a href="<?php  echo $this->createWebUrl('order', array('op' => 'display', 'status' => 1))?>">已付款订单</a>
        </li>
        <li<?php  if($_GPC['status']==2) { ?> class="active"<?php  } ?>>
        <a href="<?php  echo $this->createWebUrl('order', array('op' => 'display', 'status' => 2))?>">已评价订单</a>
        </li>
        <li<?php  if($_GPC['status']==-1) { ?> class="active"<?php  } ?>>
        <a href="<?php  echo $this->createWebUrl('order', array('op' => 'display', 'status' => -1))?>">已取消订单</a>
        </li>
        <li<?php  if($_GPC['status']==-2) { ?> class="active"<?php  } ?>>
        <a href="<?php  echo $this->createWebUrl('order', array('op' => 'display', 'status' => -2))?>">已退款订单</a>
        </li>
		<li<?php  if($_GPC['is_delete']==1) { ?> class="active"<?php  } ?>>
        <a href="<?php  echo $this->createWebUrl('order', array('op' => 'display', 'is_delete' => 1))?>">订单回收站</a>
        </li>
    </ul>
    <div class="panel panel-default">
        <form action="" method="post" class="form-horizontal form" >
        <div class="table-responsive panel-body">
            <table class="table table-hover">
                <thead class="navbar-inner">
                <tr>
					<th style="width:60px;">全选</th>
                    <th style="width:15%;">订单遍号</th>
                    <th style="width:18%;">昵称/姓名/手机号码</th>
                    <th style="width:18%;">课程名称</th>
                    <th style="width:8%;">价格</th>
					<th style="width:9%;">会员状态</th>
                    <th style="width:8%;">订单状态</th>
                    <th style="width:10%;">下单时间</th>
                    <th style="width:10%; text-align:right;">操作</th>
                </tr>
                </thead>
                <tbody>
                <?php  if(is_array($list)) { foreach($list as $item) { ?>
                <tr>
					<td><input type="checkbox" name="ids[]" value="<?php  echo $item['id'];?>"></td>
                    <td><?php  echo $item['ordersn'];?></td>
                    <td>
						<a href="<?php  echo $this->createWebUrl('agent', array('op'=>'detail','uid'=>$item['uid']));?>" target="_blank"><?php  echo $item['nickname'];?><br/><?php  echo $item['realname'];?>，<?php  echo $item['mobile'];?></a>
					</td>
                    <td><?php  echo $item['bookname'];?></td>
                    <td><?php  echo $item['price'];?> 元</td>
					<td>
						<a href="<?php  echo $this->createWebUrl('viporder', array('op'=>'createOrder', 'uid'=>$item['uid']));?>" target="_blank">
                        <?php  if($item['vip'] == 0) { ?><span class="label label-default">普通</span><?php  } ?>
						<?php  if($item['vip'] == 1) { ?><span class="label label-primary">VIP</span><?php  } ?>
						</a>
                    </td>
                    <td>
                        <?php  if($item['status'] == 0) { ?><span class="label label-danger">未付款</span><?php  } ?>
						<?php  if($item['status'] == 1) { ?><span class="label label-success">
													<?php  if($item['paytype'] == 'credit') { ?>余额支付
													<?php  } else if($item['paytype'] == 'wechat') { ?>微信支付
													<?php  } else if($item['paytype'] == 'alipay') { ?>支付宝支付
													<?php  } else if($item['paytype'] == 'offline') { ?>线下支付
													<?php  } else if($item['paytype'] == 'admin') { ?>后台支付
													<?php  } else if($item['paytype'] == 'wxapp') { ?>微信小程序
													<?php  } else { ?>优惠券支付<?php  } ?>
												</span>
						<?php  } ?>
                        <?php  if($item['status'] == 2) { ?><span class="label label-warning">已评价</span><?php  } ?>
                        <?php  if($item['status'] == -1) { ?><span class="label label-default">已取消</span><?php  } ?>
                        <?php  if($item['status'] == -2) { ?><span class="label label-default">已退款</span><?php  } ?>
                    </td>
                    <td><?php  echo date('Y-m-d H:i', $item['addtime'])?></td>
                    <td style="text-align:right;">
                        <a class="btn btn-default btn-sm" href="<?php  echo $this->createWebUrl('order', array('op' => 'detail', 'id' => $item['id']))?>" title="查看订单"><i class="fa fa-pencil"></i></a>
                        <a class="btn btn-default btn-sm" <?php  if(in_array($item['uid'], $blackids)) { ?>style="color:red;"<?php  } ?> href="<?php  echo $this->createWebUrl('order', array('op' => 'black', 'id' => $item['id'],'refurl'=>$_W['siteurl']))?>" title="拉黑名单"><i class="fa fa-ban"></i></a>
                    </td>
                </tr>
                <?php  } } ?>
                </tbody>
            </table>
			<table class="table">
				<tbody>
					<tr>
						<td>
							<input type="checkbox" id="selAll" style="margin-right:10px;">
							<?php  if($_GPC['is_delete']) { ?>
								<input type="button" class="btn btn-danger" id="delAll" data-type="1" value="批量永久删除">
							<?php  } else { ?>
								<input type="button" class="btn btn-danger" id="delAll" data-type="0" value="批量加入回收站">
							<?php  } ?>
						</td>
					</tr>
				</tbody>
			</table>
            <?php  echo $pager;?>
        </div>
    </div>
    </form>
</div>
<script type="text/javascript">
var ids = document.getElementsByName("ids[]");
var selectAll = false;
$("#selAll").click(function(){
	selectAll = !selectAll;
	for(var i=0; i<ids.length; i++){
		ids[i].checked = selectAll;
	}
});
$("#delAll").click(function(){
	var checkids = "";
	for(var i=0; i<ids.length; i++){
		if(ids[i].checked){
			checkids += (checkids === '' ? ids[i].value : ',' + ids[i].value);
		}
	}
	if(checkids===''){
		alert('请选择要操作的订单');
		return;
	}

	var type = $(this).attr("data-type");	
	if(type=='1'){
		if(!confirm('确定永久删除订单?')){
			return;
		}
		var postUrl = "<?php  echo $this->createWebUrl('order', array('op'=>'delAll'))?>";
	}else{
		if(!confirm('确定加入订单回收站?')){
			return;
		}
		var postUrl = "<?php  echo $this->createWebUrl('order', array('op'=>'recycle'))?>";
	}
	
	$.ajax({
		type: 'post',
		url: postUrl,
		data: {ids:checkids},
		dataType:'json',
		success: function(res){
			if(res.code===0){
				alert(res.msg);
				location.reload();
			}else{
				alert('网络请求超时，删除失败');
			}
		},
		error: function(error){
			alert('网络请求超时，请稍后重试!');
		}
	});
});
</script>
<?php  } else if($operation == 'detail') { ?>
<style type="text/css">
.table-form>tbody>tr>td{height:40px; padding:5px 15px;}
</style>
<div class="main">
	<table class="table we7-table table-hover table-form">
		<colgroup>
			<col width="160px">
			<col width="400px">
		</colgroup>
		<tbody>
			<tr>
				<th class="text-left" colspan="3">订单信息</th>
			</tr>
			<tr>
				<td class="table-label">订单遍号</td>
				<td class="ng-binding"><?php  echo $order['ordersn'];?></td>
				<td class="text-right"><div class="link-group"><a href="javascript:;"></a></div></td>
			</tr>
			<tr>
				<td class="table-label">课程名称</td>
				<td class="ng-binding"><?php  echo $order['bookname'];?></td>
				<td class="text-right"><div class="link-group"><a href="javascript:;"></a></div></td>
			</tr>
			<tr>
				<td class="table-label">实付金额</td>
				<td class="ng-binding"><?php  echo $order['price'];?> 元</td>
				<td class="text-right"><div class="link-group"><a href="javascript:;"></a></div></td>
			</tr>
			<?php  if(!empty($order['coupon'])) { ?>
			<tr>
				<td class="table-label">优惠券编号</td>
				<td class="ng-binding"><?php  echo $order['coupon'];?></td>
				<td class="text-right"><div class="link-group"><a href="javascript:;"></a></div></td>
			</tr>
			<tr>
				<td class="table-label">优惠券面值</td>
				<td class="ng-binding"><?php  echo $order['coupon_amount'];?> 元</td>
				<td class="text-right"><div class="link-group"><a href="javascript:;"></a></div></td>
			</tr>
			<?php  } ?>
			<?php  if($order['integral']>0) { ?>
			<tr>
				<td class="table-label">获赠积分</td>
				<td class="ng-binding"><?php  echo $order['integral'];?> 积分</td>
				<td class="text-right"><div class="link-group"><a href="javascript:;"></a></div></td>
			</tr>
			<?php  } ?>
			<tr>
				<td class="table-label">付款方式</td>
				<td class="ng-binding">
					<?php  if($order['paytype'] == 'credit') { ?>余额支付
					<?php  } else if($order['paytype'] == 'wechat') { ?>微信支付
					<?php  } else if($order['paytype'] == 'alipay') { ?>支付宝支付
					<?php  } else if($order['paytype'] == 'offline') { ?>线下支付
					<?php  } else if($order['paytype'] == 'admin') { ?>后台支付
					<?php  } else if($order['paytype'] == 'wxapp') { ?>微信小程序
					<?php  } else if($order['paytype'] == '' && $order['status']>=1) { ?>优惠券支付
					<?php  } else { ?>无<?php  } ?>
				</td>
				<td class="text-right"><div class="link-group"><a href="javascript:;"></a></div></td>
			</tr>
			<?php  if(!empty($wechatPay)) { ?>
			<tr>
				<td class="table-label">微信商户订单号</td>
				<td class="ng-binding"><?php  echo $wechatPay['uniontid'];?></td>
				<td class="text-right"><div class="link-group"><a href="javascript:;"></a></div></td>
			</tr>
			<tr>
				<td class="table-label">微信支付单号</td>
				<td class="ng-binding"><?php  echo $wechatPay['transaction']['transaction_id'];?></td>
				<td class="text-right"><div class="link-group"><a href="javascript:;"></a></div></td>
			</tr>
			<?php  } ?>
			<tr>
				<td class="table-label">订单状态</td>
				<td class="ng-binding">
					<?php  if($order['status'] == 0) { ?>
						<span class="label label-danger">待付款</span>&nbsp;&nbsp;&nbsp;<a class="btn btn-success btn-sm" style="padding:4px 10px;" onclick="return confirm('该操作不可恢复，确定已付款?');return false;" href="<?php  echo $this->createWebUrl('order',array('op'=>'confirmpay','orderid'=>$order['id'],'refurl'=>$_W['siteurl']));?>">确认付款?</a>
					<?php  } ?>
					<?php  if($order['status'] == 1) { ?><span class="label label-success">已付款</span><?php  } ?>
					<?php  if($order['status'] == 2) { ?><span class="label label-warning">已评价</span><?php  } ?>
					<?php  if($order['status'] == -1) { ?><span class="label label-default">已取消</span><?php  } ?>
					<?php  if($order['status'] == -2) { ?><span class="label label-default">已退款</span><?php  } ?>
					<?php  if($order['status'] >= 1 && $order['paytype'] == 'wechat') { ?>
					&nbsp;&nbsp;&nbsp;<a class="btn btn-danger btn-sm" style="padding:4px 10px;" onclick="popwin = $('#refund').modal();">确认退款?</a>
					<?php  } ?>
				</td>
				<td class="text-right"><div class="link-group"><a href="javascript:;"></a></div></td>
			</tr>
			<div class="modal fade in" id="refund" tabindex="-1">
				<form id="form-refund" action="<?php  echo $this->createWebUrl('refund');?>" class="form-horizontal form" method="post">
					<div class="we7-modal-dialog modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
								<div class="modal-title">订单退款信息</div>
							</div>
							<div class="modal-body">
								<div class="we7-form">
									<div class="form-group">
										<label for="" class="control-label col-sm-2">订单编号</label>
										<div class="form-controls col-sm-10">
											<input type="text" class="form-control ng-pristine ng-untouched ng-valid ng-empty" value="<?php  echo $order['ordersn'];?>" readonly="true">
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-sm-2">退款金额</label>
										<div class="form-controls col-sm-10">
											<div class="input-group">
												<input type="text" name="refund_amount" value="<?php  echo $order['price'];?>" class="form-control ng-pristine ng-untouched ng-valid ng-not-empty" placeholder="退款金额不得超过<?php  echo $order['price'];?>元">
												<a href="javascript:;" class="input-group-addon">元</a>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-sm-2">退款理由</label>
										<div class="form-controls col-sm-10">
											<textarea name="reason" class="form-control ng-pristine ng-untouched ng-valid ng-empty" placeholder="最多不可超过100字" maxlength="100"></textarea>
										</div>
									</div>
								</div>
							</div>
							<div class="modal-footer">
								<input type="hidden" name="id" value="<?php  echo $order['id'];?>">
								<input type="hidden" name="ordertype" value="lesson">
								<button type="button" class="btn btn-primary" id="submit-refund">确定</button>
								<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
							</div>
						</div>
					</div>
				</form>
			</div>
			<tr>
				<td class="table-label">下单时间</td>
				<td class="ng-binding"><?php  echo date('Y-m-d H:i:s', $order['addtime'])?></td>
				<td class="text-right"><div class="link-group"><a href="javascript:;"></a></div></td>
			</tr>
			<?php  if($order['paytime']>0) { ?>
			<tr>
				<td class="table-label">付款时间</td>
				<td class="ng-binding"><?php  echo date('Y-m-d H:i:s', $order['paytime'])?></td>
				<td class="text-right"><div class="link-group"><a href="javascript:;"></a></div></td>
			</tr>
			<?php  } ?>

			<?php  if($order['status']>0) { ?>			
			<form id="form-validity" action="<?php  echo $this->createWebUrl('order', array('op'=>'detail'));?>" method="post">
				<tr>
					<td class="table-label">订单有效期</td>
					<td class="ng-binding">
						<?php  if(!empty($order['validity']) && !empty($order['paytype'])) { ?>
							<?php  echo tpl_form_field_date('validity', $order['validity'],true);?>
						<?php  } else { ?>
							<?php echo $order['validity']==0 ? '长期有效' : date('Y-m-d H:i:s', $order['validity'])?>
						<?php  } ?>
					</td>
					<td class="text-right"><div class="link-group"><a href="javascript:;"></a></div></td>
				</tr>
				<input type="hidden" name="id" value="<?php  echo $order['id'];?>">
				<input type="hidden" name="token" value="<?php  echo $_W['token'];?>">
				<input type="hidden" name="submit_type" value="validity">
			</form>
			<?php  } ?>
			<tr>
				<td class="table-label">昵称/姓名/手机号</td>
				<td class="ng-binding">
					<img src="<?php  echo $avatar;?>" width="35" height="35">&nbsp;&nbsp;<?php  echo $order['nickname'];?>&nbsp;/&nbsp;<?php  echo $order['realname'];?>&nbsp;/&nbsp;<?php  echo $order['mobile'];?>
				</td>
				<td class="text-right"><div class="link-group"><a href="javascript:;"></a></div></td>
			</tr>
			<tr>
				<td class="table-label">微信号/职业/公司</td>
				<td class="ng-binding">
					<?php echo $order['msn']?$order['msn']:'无';?>&nbsp;/&nbsp;<?php echo $order['occupation']?$order['occupation']:'无';?>&nbsp;/&nbsp;<?php echo $order['company']?$order['company']:'无';?>
				</td>
				<td class="text-right"><div class="link-group"><a href="javascript:;"></a></div></td>
			</tr>
			<tr>
				<td class="table-label">学校/班级</td>
				<td class="ng-binding">
					<?php echo $order['graduateschool']?$order['graduateschool']:'无';?>&nbsp;/&nbsp;<?php echo $order['grade']?$order['grade']:'无';?>
				</td>
				<td class="text-right"><div class="link-group"><a href="javascript:;"></a></div></td>
			</tr>
			<tr>
				<td class="table-label">地址</td>
				<td class="ng-binding"><?php  echo $order['address'];?></td>
				<td class="text-right"><div class="link-group"><a href="javascript:;"></a></div></td>
			</tr>
			<?php  if(!empty($evaluate['content'])) { ?>
			<tr>
				<td class="table-label">评价内容</td>
				<td class="ng-binding"><?php  echo $evaluate['content'];?></td>
				<td class="text-right"><div class="link-group"><a href="javascript:;"></a></div></td>
			</tr>
			<?php  } ?>
		</tbody>
	</table>

	<?php  if($order['status']>0 && $order['lesson_type']==1) { ?>
	<table class="table we7-table table-hover table-form">
		<colgroup>
			<col width="160px">
			<col width="400px">
		</colgroup>
		<tbody>
			<tr>
				<th class="text-left" colspan="3">核销信息</th>
			</tr>
			<tr>
				<td class="table-label">核销状态</td>
				<td class="ng-binding">
					<?php  if($order['is_verify'] == '0') { ?><span class="label label-success">未核销</span><?php  } ?>
					<?php  if($order['is_verify'] == 1) { ?><span class="label label-default">已核销</span><?php  } ?>
				</td>
				<td class="text-right"><div class="link-group"><a href="javascript:;"></a></div></td>
			</tr>
			<?php  } ?>
			<?php  if($order['is_verify'] == 1) { ?>
			<tr>
				<td class="table-label">核销时间</td>
				<td class="ng-binding"><?php  echo date('Y-m-d H:i:s', $verify_info['verify_time'])?></td>
				<td class="text-right"><div class="link-group"><a href="javascript:;"></a></div></td>
			</tr>
			<tr>
				<td class="table-label">核销员</td>
				<td class="ng-binding"><?php  echo $verify_user['nickname'];?>(uid:<?php  echo $verify_info['verify_uid'];?>)</td>
				<td class="text-right"><div class="link-group"><a href="javascript:;"></a></div></td>
			</tr>
		</tbody>
	</table>
	<?php  } ?>
				
	<?php  if(!empty($appoint_info) && $order['lesson_type']==1) { ?>
	<table class="table we7-table table-hover table-form">
		<colgroup>
			<col width="160px">
			<col width="400px">
		</colgroup>
		<tbody>
			<tr>
				<th class="text-left" colspan="3">报名课程信息</th>
			</tr>
			<?php  if(is_array($appoint_info)) { foreach($appoint_info as $item) { ?>
			<tr>
				<td class="table-label"><?php  echo explode('：', $item)['0'];?></td>
				<td class="ng-binding"><?php  echo explode('：', $item)['1'];?></td>
				<td class="text-right"><div class="link-group"><a href="javascript:;"></a></div></td>
			</tr>
			<?php  } } ?>
		</tbody>
	</table>
	<?php  } ?>

	<?php  if($member1>0 && $order['commission1']>0) { ?>
	<table class="table we7-table table-hover table-form">
		<colgroup>
			<col width="160px">
			<col width="400px">
		</colgroup>
		<tbody>
			<tr>
				<th class="text-left" colspan="3">佣金信息</th>
			</tr>
			<?php  if($member1>0) { ?>
			<tr>
				<td class="table-label">一级佣金</td>
				<td class="ng-binding"><?php  echo $order['commission1'];?> 元</td>
				<td class="text-right"><div class="link-group"><a href="javascript:;"></a></div></td>
			</tr>
			<tr>
				<td class="table-label">一级推荐人</td>
				<td class="ng-binding">
					<img src="<?php  echo $avatar1;?>" style="width:30px;height:30px;padding:1px;border:1px solid #ccc">&nbsp;&nbsp;<?php  echo $member1['nickname'];?>
				</td>
				<td class="text-right"><div class="link-group"><a href="javascript:;"></a></div></td>
			</tr>
			<?php  } ?>
			<?php  if($member2>0 && $order['commission2']>0) { ?>
			<tr>
				<td class="table-label">二级佣金</td>
				<td class="ng-binding"><?php  echo $order['commission2'];?> 元</td>
				<td class="text-right"><div class="link-group"><a href="javascript:;"></a></div></td>
			</tr>
			<tr>
				<td class="table-label">二级推荐人</td>
				<td class="ng-binding">
					<img src="<?php  echo $avatar2;?>" style="width:30px;height:30px;padding:1px;border:1px solid #ccc">&nbsp;&nbsp;<?php  echo $member2['nickname'];?>
				</td>
				<td class="text-right"><div class="link-group"><a href="javascript:;"></a></div></td>
			</tr>
			<?php  } ?>
			<?php  if($member3>0 && $order['commission3']>0) { ?>
			<tr>
				<td class="table-label">三级佣金</td>
				<td class="ng-binding"><?php  echo $order['commission3'];?> 元</td>
				<td class="text-right"><div class="link-group"><a href="javascript:;"></a></div></td>
			</tr>
			<tr>
				<td class="table-label">三级推荐人</td>
				<td class="ng-binding">
					<img src="<?php  echo $avatar3;?>" style="width:30px;height:30px;padding:1px;border:1px solid #ccc">&nbsp;&nbsp;<?php  echo $member3['nickname'];?>
				</td>
				<td class="text-right"><div class="link-group"><a href="javascript:;"></a></div></td>
			</tr>
			<?php  } ?>
		</tbody>
	</table>
	<?php  } ?>
		
	<div class="form-group col-sm-12">
		<input type="button" id="submit-validity" value="提交" class="btn btn-primary col-lg-1">
		<input type="button" onclick="javascript:window.history.back(-1);" value="返回列表" class="btn btn-default col-lg-1" style="margin-left:40px;">
	</div>
</div>

<script>
$("#submit-refund").click(function(){
	$("#form-refund").submit();
});
$("#submit-validity").click(function(){
	$("#form-validity").submit();
});	
</script>

<?php  } else if($op=='createOrder') { ?>
<div class="main">
    <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data" onkeydown="if(event.keyCode==13){return false;}">
        <div class="panel panel-default">
            <div class="panel-heading">创建课程订单</div>
            <div class="panel-body">
				<div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">选择课程</label>
                    <div class="col-sm-9 col-xs-12">
                        <div class='input-group'>
                            <input type="text" id='bookname' maxlength="30" class="form-control" readonly />
							<input type="hidden" id='lessonid' name="lessonid"/>
                            <div class='input-group-btn'>
                                <button class="btn btn-default" type="button" onclick="popwin = $('#modal-module-menus').modal();">选择课程</button>
                            </div>
                        </div>
                        <div id="modal-module-menus"  class="modal fade" tabindex="-1">
                            <div class="modal-dialog" style='width: 920px;'>
                                <div class="modal-content">
                                    <div class="modal-header"><button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button><h3>选择课程</h3></div>
                                    <div class="modal-body" >
                                        <div class="row">
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="keyword" value="" id="search-kwd" placeholder="请输入课程名称" />
                                                <span class='input-group-btn'><button type="button" class="btn btn-default" onclick="search_lesson();">搜索</button></span>
                                            </div>
                                        </div>
                                        <div id="module-menus" style="padding-top:5px;"></div>
                                    </div>
                                    <div class="modal-footer"><a href="javascript:;" class="btn btn-default" data-dismiss="modal" aria-hidden="true">关闭</a></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
				<div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">课程价格</label>
                    <div class="col-sm-9">
                        <div class="input-group">
                            <input type="text" name="price" id="price" class="form-control">
                            <span class="input-group-addon">元</span>
                        </div>
                    </div>
                </div>
				<div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">讲师课程佣金分成</label>
                    <div class="col-sm-9">
                        <div class="input-group">
                            <input type="text" name="teacher_income" id="teacher_income" class="form-control">
                            <span class="input-group-addon">%</span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">课程有效期</label>
                    <div class="col-sm-9">
                        <div class="input-group">
                            <input type="text" name="validity" id="validity" class="form-control">
                            <span class="input-group-addon">天</span>
                        </div>
                        <div class="help-block">
                            课程有效期是指从生成订单时起，指定天数内有效，0为长期有效
                        </div>
                    </div>
                </div>
				<div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">选择用户</label>
                    <div class="col-sm-9 col-xs-12">
                        <div class='input-group'>
                            <input type="text" id='nickname' maxlength="30" class="form-control" readonly />
							<input type="hidden" id='uid' name="uid"/>
                            <div class='input-group-btn'>
                                <button class="btn btn-default" type="button" onclick="popwin = $('#modal-module-member').modal();">选择用户</button>
                            </div>
                        </div>
                        <div id="modal-module-member"  class="modal fade" tabindex="-1">
                            <div class="modal-dialog" style='width: 920px;'>
                                <div class="modal-content">
                                    <div class="modal-header"><button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button><h3>选择用户</h3></div>
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
				<div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">讲师课程佣金</label>
                    <div class="col-sm-9">
                        <label class="radio-inline"><input type="radio" name="income_switch" value="1" checked /> 开启</label>
                        &nbsp;
                        <label class="radio-inline"><input type="radio" name="income_switch" value="0" /> 关闭</label>
                        <span class="help-block">开启讲师课程佣金后，该课程的讲师会收到课程佣金</span>
                    </div>
                </div>
				<div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">分销佣金</label>
                    <div class="col-sm-9">
                        <label class="radio-inline"><input type="radio" name="sale_switch" value="1" checked /> 开启</label>
                        &nbsp;
                        <label class="radio-inline"><input type="radio" name="sale_switch" value="0" /> 关闭</label>
                        <span class="help-block">开启分销佣金后，该用户的上级会收到相关的分销佣金</span>
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
function search_lesson() {
	var uniacid = <?php  echo $uniacid?>;
	if ($.trim($('#search-kwd').val()) == '') {
		document.getElementById('search-kwd').focus();
		return;
	}
	$("#module-menus").html("正在搜索....");
	$.get("<?php  echo $this->createWebUrl('getlessonormember', array('op'=>'getLesson'))?>", { 
		uniacid:uniacid,keyword: $.trim($('#search-kwd').val())
	}, function (dat) {
		$('#module-menus').html(dat);
	});
}
function select_lesson(obj) {
	$("#bookname").val(obj.bookname);
	$("#lessonid").val(obj.id);
	$("#price").val(obj.price);
	$("#teacher_income").val(obj.teacher_income);
	$("#validity").val(obj.validity);
}

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
	$("#nickname").val(obj.nickname);
	$("#uid").val(obj.uid);
}
</script>

<?php  } else if($op=='couponCode') { ?>
<div class="main">
	<div class="panel panel-info">
        <div class="panel-heading">筛选</div>
        <div class="panel-body">
            <form action="./index.php" method="get" class="form-horizontal" role="form">
                <input type="hidden" name="c" value="site" />
                <input type="hidden" name="a" value="entry" />
                <input type="hidden" name="m" value="fy_lessonv2" />
                <input type="hidden" name="do" value="order" />
                <input type="hidden" name="op" value="couponCode" />
                <div class="form-group">
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width:100px;">订单遍号</label>
                    <div class="col-sm-2 col-lg-3">
                        <input class="form-control" name="ordersn" type="text" value="<?php  echo $_GPC['ordersn'];?>">
                    </div>
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label" style="width: 100px;">优惠码</label>
                    <div class="col-sm-2 col-lg-3">
                        <input class="form-control" name="keyword" type="text" placeholder="优惠码前缀/面值/编号/密钥" value="<?php  echo $_GPC['keyword'];?>">
                    </div>
                </div>
                <div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width:100px;">优惠码状态</label>
                    <div class="col-sm-8 col-lg-3 col-xs-12">
                        <select name="is_use" class="form-control">
                            <option value="">全部状态</option>
							<option value="0" <?php  if(in_array($_GPC['is_use'],array('0'))) { ?> selected="selected" <?php  } ?>>未使用</option>
							<option value="1" <?php  if($_GPC['is_use'] == 1) { ?> selected="selected" <?php  } ?>>已使用</option>
							<option value="-1" <?php  if($_GPC['is_use'] == -1) { ?> selected="selected" <?php  } ?>>已过期</option>
                        </select>
                    </div>
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width: 100px;">使用时间</label>
                    <div class="col-sm-8 col-lg-3 col-xs-12">
					<?php echo tpl_form_field_daterange('time', array('starttime'=>($starttime ? date('Y-m-d', $starttime) : false),'endtime'=> ($endtime ? date('Y-m-d', $endtime) : false)));?>
                    </div>
                    <div class="col-sm-3 col-lg-3">
                        <button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>&nbsp;&nbsp;
						<button type="submit" name="export" value="1" class="btn btn-success">导出 Excel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <div class="panel panel-default">
        <form action="<?php  echo $this->createWebUrl('order', array('op'=>'delAllCoupon'));?>" method="post" class="form-horizontal form">
        <div class="table-responsive panel-body">
            <table class="table table-hover">
                <thead class="navbar-inner">
                <tr>
					<th style="width:4%;"><input type="checkbox" id="checkItems"></th>
                    <th style="width:9%;">编号</th>
                    <th style="width:18%;">密钥</th>
                    <th style="width:16%;">面值</th>
					<th style="width:13%;">有效期</th>
                    <th style="width:8%;">状态</th>
					<th style="width:14%;">订单号</th>
                    <th style="width:13%;">使用时间</th>
                    <th style="text-align:right;">操作</th>
                </tr>
                </thead>
                <tbody>
                <?php  if(is_array($list)) { foreach($list as $item) { ?>
                <tr>
                    <td><input type="checkbox" name="ids[]" value="<?php  echo $item['card_id'];?>"></td>
					<td><?php  echo $item['card_id'];?></td>
                    <td><?php  echo $item['password'];?></td>
                    <td><?php  echo $item['amount'];?>(满<?php  echo $item['conditions'];?>元可用)</td>
                    <td><?php  echo date('Y-m-d H:i',$item['validity'])?></td>
					<td>
						<?php  if($item['is_use'] == 0 && time() > $item['validity']) { ?><span class="label label-default">已过期</span><?php  } ?>
						<?php  if($item['is_use'] == 0 && time() <= $item['validity']) { ?><span class="label label-success">未使用</span><?php  } ?>
						<?php  if($item['is_use'] == 1) { ?><span class="label label-warning">已使用</span><?php  } ?>
					</td>
                    <td><a href="<?php  echo $this->createWebUrl('order', array('ordersn'=>$item['ordersn']));?>"><?php  echo $item['ordersn'];?></a></td>
                    <td><?php  if(!empty($item['use_time'])) { ?><?php  echo date('Y-m-d H:i', $item['use_time']);?><?php  } ?></td>
                    <td style="text-align:right;">
                        <a class="btn btn-default btn-sm" href="<?php  echo $this->createWebUrl('order', array('op' => 'delCoupon', 'id' => $item['card_id']))?>" title="删除优惠码" onclick="return confirm('此操作不可恢复，确认删除？');return false;"><i class="fa fa-times"></i></a>
                    </td>
                </tr>
                <?php  } } ?>
                </tbody>
				<tfoot>
					<tr>
						<td colspan="9">
							<input name="submit" type="submit" class="btn btn-primary" value="批量删除" onclick="return delAll()">
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

	return true;
}
</script>

<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>