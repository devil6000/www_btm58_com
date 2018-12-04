<?php defined('IN_IA') or exit('Access Denied');?><!--
 * 财务管理
 * ============================================================================
 * 版权所有 2015-2018 微课堂团队，并保留所有权利。
 * 网站地址: https://wx.haoshu888.com
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！不允许对程序代码以任何形式任何目的的再发布，作者将保留
 * 追究法律责任的权力和最终解释权。
 * ============================================================================
-->
<?php  if($op=='display') { ?>
<script src="<?php echo MODULE_URL;?>template/web/style/echarts/echarts.common.min.js"></script>
<?php  } ?>

<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<ul class="nav nav-tabs">
	<li <?php  if($op=='display') { ?> class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('finance');?>">财务概览</a></li>
    <li <?php  if($op=='commission' || $op=='detail') { ?> class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('finance', array('op'=>'commission'));?>">提现申请</a></li>
	<li <?php  if($op=='handle') { ?> class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('finance', array('op'=>'handle'));?>">佣金调整</a></li>
</ul>

<?php  if($op=='display') { ?>
<style>
.account-stat{overflow:hidden; color:#666;}
.account-stat .account-stat-btn{width:100%; overflow:hidden;}
.account-stat .account-stat-btn > div{text-align:center; margin-bottom:5px;margin-right:2%; float:left;width:23%; height:80px; padding-top:10px;font-size:16px; border-left:1px #DDD solid;}
.account-stat .account-stat-btn > div:first-child{border-left:0;}
.account-stat .account-stat-btn > div span{display:block; font-size:30px; font-weight:bold}
</style>

<div class="panel panel-default">
	<div class="panel-heading">
		今日销售指标
	</div>
	<div class="account-stat">
		<div class="account-stat-btn">
			<div>今日课程销售额(元)<span><?php  echo $exit['lessonOrder_amount'];?></span></div>
			<div>今日课程销售量(单)<span><?php  echo $exit['lessonOrder_num'];?></span></div>
			<div>今日VIP销售额(元)<span><?php  echo $exit['vipOrder_amount'];?></span></div>
			<div>今日VIP销售量(单)<span><?php  echo $exit['vipOrder_num'];?></span></div>
		</div>
	</div>
</div>

<div class="panel panel-default">
	<div class="panel-heading">
		昨日销售指标
	</div>
	<div class="account-stat">
		<div class="account-stat-btn">
			<div>昨日课程销售额(元)<span><?php  echo $yestoday['lessonOrder_amount'];?></span></div>
			<div>昨日课程销售量(单)<span><?php  echo $yestoday['lessonOrder_num'];?></span></div>
			<div>昨日VIP销售额(元)<span><?php  echo $yestoday['vipOrder_amount'];?></span></div>
			<div>昨日VIP销售量(单)<span><?php  echo $yestoday['vipOrder_num'];?></span></div>
		</div>
	</div>
</div>

<div class="panel panel-info">
	<div class="panel-heading">累计金额(元)：<span style="color:red;"><?php  echo $incomeTotal;?></span></div>
	<div style="height:20px;"></div>
	<form method="get" class="form-horizontal" role="form">
		<input type="hidden" name="c" value="site" />
		<input type="hidden" name="a" value="entry" />
		<input type="hidden" name="m" value="fy_lessonv2" />
		<input type="hidden" name="do" value="finance" />
		<input type="hidden" name="op" value="display" />
		<div class="form-group">
			<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width: 100px;">日期</label>
			<div class="col-sm-8 col-lg-3 col-xs-12">
				<?php  echo tpl_form_field_daterange('time', array('starttime'=>date('Y-m-d', $starttime),'endtime'=>date('Y-m-d', $endtime)));?>
			</div>
			<div class="col-sm-3 col-lg-3" style="width: 22%;">
				<button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>
			</div>
		</div>
	</form>
    <div class="panel-body">
		<div id="container" style="min-width:400px;height:400px"></div>
    </div>
</div>

<script type="text/javascript">
	var myChart = echarts.init(document.getElementById('container')); 
	var option = {
		title: {
			text: '销售趋势图'
		},
		tooltip: {
			trigger: 'axis'
		},
		legend: {
			data:['课程销售额','VIP销售额']
		},
		toolbox: {
			feature: {
				saveAsImage: {}
			}
		},
		xAxis: {
			type: 'category',
			boundaryGap: false,
			data: <?php  echo json_encode($day)?>
		},
		yAxis: {
			type: 'value'
		},
		series: [
			{
				name:'课程销售额',
				type:'line',
				smooth: true,
				data:<?php  echo json_encode($lessonOrder_amount)?>
			},
			{
				name:'VIP销售额',
				type:'line',
				smooth: true,
				data:<?php  echo json_encode($vipOrder_amount)?>
			}
		]
	}; 
    myChart.setOption(option); 
</script>

<?php  } else if($op=='commission') { ?>
<div class="panel panel-info">
    <div class="panel-heading">筛选</div>
    <div class="panel-body">
        <form action="./index.php" method="get" class="form-horizontal" role="form" id="form1">
            <input type="hidden" name="c" value="site" />
            <input type="hidden" name="a" value="entry" />
            <input type="hidden" name="m" value="fy_lessonv2" />
            <input type="hidden" name="do" value="finance" />
            <input type="hidden" name="op" value="commission" />

			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">提现单号</label>
				<div class="col-sm-2 col-lg-3">
					<input type="text" class="form-control"  name="cashid" value="<?php  echo $_GPC['cashid'];?>"/>
				</div>
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">会员信息</label>
				<div class="col-sm-2 col-lg-3">
					<input type="text" class="form-control"  name="nickname" value="<?php  echo $_GPC['nickname'];?>" placeholder="可搜索昵称/姓名/手机号"/> 
				</div>
			</div>

			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">提现类型</label>
				<div class="col-sm-2 col-lg-3">
					<select name='lesson_type' class='form-control'>
						<option value=''>全部</option>
						<option value='1' <?php  if($_GPC['lesson_type']==1) { ?>selected<?php  } ?>>分销佣金提现</option>
						<option value='2' <?php  if($_GPC['lesson_type']==2) { ?>selected<?php  } ?>>课程收入提现</option>
					</select> 
				</div>
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">提现方式</label>
				<div class="col-sm-2 col-lg-3">
					<select name='cash_way' class='form-control'>
					  <option value=''>全部</option>
					  <option value='1' <?php  if($_GPC['cash_way']==1) { ?>selected<?php  } ?>>帐户余额</option>
					  <option value='2' <?php  if($_GPC['cash_way']==2) { ?>selected<?php  } ?>>微信钱包</option>
					  <option value='3' <?php  if($_GPC['cash_way']==3) { ?>selected<?php  } ?>>支付宝</option>
				   </select>
				</div>
			</div>

			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">提现状态</label>
				<div class="col-sm-2 col-lg-3">
					<select name='status' class='form-control'>
						<option value="">全部</option>
						<?php  if(is_array($cashStatusList)) { foreach($cashStatusList as $key => $item) { ?>
						<option value="<?php  echo $key;?>" <?php  if($_GPC['status']=="$key") { ?>selected<?php  } ?>><?php  echo $cashStatusList[$key];?></option>
						<?php  } } ?>
						
					</select> 
				</div>
				<label class="col-xs-12 col-sm-2 col-md-1 control-label">申请时间</label>
				<div class="col-sm-2 col-lg-3">
					<?php echo tpl_form_field_daterange('time', array('starttime'=>($starttime ? date('Y-m-d', $starttime) : false),'endtime'=> ($endtime ? date('Y-m-d', $endtime) : false)));?>
				</div>
				<div class="col-sm-3 col-lg-3">
					 <button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>
					 &nbsp;
					 <button type="submit" name="export" value="1" class="btn btn-success">导出 Excel</button>
				</div>
			</div>
        </form>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">总数：<?php  echo $total;?></div>
    <div class="panel-body">
        <table class="table table-hover" style="font-size:13px;">
            <thead class="navbar-inner">
                <tr>
                    <th style='width:8%;'>提现单号</th>
                    <th style='width:10%;'>粉丝</th>
                    <th style='width:10%;'>提现方式</th>
                    <th style='width:10%;'>提现类型</th>
                    <th style='width:10%;'>申请佣金</th>
                    <th style='width:13%;'>申请时间</th>
                    <th style='width:10%;'>状态</th>
                    <th style='width:8%;'>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php  if(is_array($list)) { foreach($list as $row) { ?>
                <tr>
                    <td><?php  echo $row['id'];?></td>
                    <td><img src='<?php  echo $row['avatar'];?>' style='width:30px;height:30px;padding1px;border:1px solid #ccc' /> <?php  echo $row['nickname'];?></td>
                    <td>
						<?php  if($row['cash_way']==1) { ?>
						帐户余额
						<?php  } else if($row['cash_way']==2) { ?>
						微信钱包
						<?php  } else if($row['cash_way']==3) { ?>
						支付宝
						<?php  } ?>
					</td>
					<td>
						<?php  if($row['lesson_type']==1) { ?>
							<span class="label" style="background-color:#e07f08;">分销佣金提现</span>
						<?php  } else if($row['lesson_type']==2) { ?>
							<span class="label" style="background-color:#05987d;">课程收入提现</span>
						<?php  } ?>
					</td>
                    <td><?php  echo $row['cash_num'];?> 元</td>
                    <td><?php  echo date('Y-m-d H:i',$row['addtime']);?></td>
                    <td>
						<?php  if($row['status']==0) { ?>
							<span class="label label-primary"><?php  echo $cashStatusList[$row['status']];?></span>
						<?php  } else if($row['status']==1) { ?>
							<span class="label label-success"><?php  echo $cashStatusList[$row['status']];?></span>
						<?php  } else if($row['status']==-1) { ?>
							<span class="label label-default"><?php  echo $cashStatusList[$row['status']];?></span>
						<?php  } else if($row['status']==-2) { ?>
							<span class="label label-danger"><?php  echo $cashStatusList[$row['status']];?></span>
						<?php  } ?>
					</td>
                     <td>
                        <a class='btn btn-default' href="<?php  echo $this->createWebUrl('finance',array('op'=>'detail', 'id' => $row['id'], 'status'=>$status));?>">详情</a>		
                    </td>
                </tr>
                <?php  } } ?>
            </tbody>
        </table>
        <?php  echo $pager;?>
    </div>
</div>

<?php  } else if($op=='detail') { ?>
<link href="<?php echo MODULE_URL;?>template/web/style/fycommon.css" rel="stylesheet">

<div class="mloading-bar" style="margin-top: -31px; margin-left: -140px;"><img src="<?php echo MODULE_URL;?>template/mobile/images/download.gif"><span class="mloading-text">打款处理中，请勿刷新或关闭浏览器...</span></div>
<div id="overlay"></div>
<div class="main">
	<form class="form-horizontal form" method="post" onsubmit="return check();">
		<div class="panel panel-default">
			<div class="panel-heading">
				[<?php  echo $cashStatusList[$cashlog['status']];?>]提现申请信息
			</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">提现单号</label>
					<div class="col-sm-9">
						<p class="form-control-static"><?php  echo $cashlog['id'];?></p>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">粉丝信息</label>
					<div class="col-sm-9">
						<p class="form-control-static"><img src='<?php  echo $cashlog['avatar'];?>' style='width:30px;height:30px;padding1px;border:1px solid #ccc' /> uid：<?php  echo $cashlog['uid'];?>，<?php  echo $cashlog['nickname'];?>(<?php  echo $cashlog['realname'];?>)</p>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">手机号码</label>
					<div class="col-sm-9">
						<p class="form-control-static"> <?php  echo $cashlog['mobile'];?></p>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">提现金额</label>
					<div class="col-sm-9">
						<p class="form-control-static"> <?php  echo $cashlog['cash_num'];?> 元</p>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">处理状态</label>
					<div class="col-sm-9">
						<p class="form-control-static">
						<?php  if($cashlog['status']==0) { ?>
							<?php  if(is_array($cashStatusList)) { foreach($cashStatusList as $key => $item) { ?>
								<label>
									<input type="radio" name="status" value="<?php  echo $key;?>" <?php  if($key=='0') { ?>checked<?php  } ?>>
									<span class="label <?php  if($key==0) { ?>label-primary<?php  } else if($key==1) { ?>label-success<?php  } else if($key==-1) { ?>label-default<?php  } else if($key==-2) { ?>label-danger<?php  } ?>" style="vertical-align:text-top;"><?php  echo $cashStatusList[$key];?></span>
								</label>
								&nbsp;&nbsp;
							<?php  } } ?>
							<span class="help-block">驳回申请的佣金将退回用户佣金账户；作废无效的佣金不会退还所申请的佣金</span>
						<?php  } else { ?>
							<?php  if($cashlog['status']==0) { ?>
							<span class="label label-primary"><?php  echo $cashStatusList[$cashlog['status']];?></span>
							<?php  } else if($cashlog['status']==1) { ?>
							<span class="label label-success"><?php  echo $cashStatusList[$cashlog['status']];?></span>
							<?php  } else if($cashlog['status']==-1) { ?>
							<span class="label label-default"><?php  echo $cashStatusList[$cashlog['status']];?></span>
							<?php  } else if($cashlog['status']==-2) { ?>
							<span class="label label-danger"><?php  echo $cashStatusList[$cashlog['status']];?></span>
							<?php  } ?>
						<?php  } ?>
						</p>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">提现方式</label>
					<div class="col-sm-9">
						<p class="form-control-static">
						<?php  if($cashlog['cash_way']==1) { ?>
						帐户余额
						<?php  } else if($cashlog['cash_way']==2) { ?>
						微信钱包
						<?php  } else if($cashlog['cash_way']==3) { ?>
						支付宝
						<?php  } ?>
						</p>
					</div>
				</div>
				<?php  if($cashlog['cash_way']==3) { ?>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">提现账号</label>
					<div class="col-sm-9">
						<p class="form-control-static"><?php  echo $cashlog['pay_account'];?></p>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">提现帐号人姓名</label>
					<div class="col-sm-9">
						<p class="form-control-static"><?php  echo $cashlog['pay_name'];?></p>
					</div>
				</div>
				<?php  } ?>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">处理方式</label>
					<div class="col-sm-9">
						<p class="form-control-static">
						<?php echo $cashlog['cash_type']==1?'管理员审核':'自动到账';?>
						</p>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">申请时间</label>
					<div class="col-sm-9">
						<p class="form-control-static">
						<?php  echo date('Y-m-d H:i:s', $cashlog['addtime']);?>
						</p>
					</div>
				</div>
				<?php  if(!empty($cashlog['disposetime'])) { ?>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">处理时间</label>
					<div class="col-sm-9">
						<p class="form-control-static">
						<?php  echo date('Y-m-d H:i:s', $cashlog['disposetime']);?>
						</p>
					</div>
				</div>
				<?php  } ?>
				<?php  if(!empty($cashlog['partner_trade_no'])) { ?>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">商户订单号</label>
					<div class="col-sm-9">
						<p class="form-control-static">
						<?php  echo $cashlog['partner_trade_no'];?>
						</p>
					</div>
				</div>
				<?php  } ?>
				<?php  if(!empty($cashlog['payment_no'])) { ?>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">微信订单号</label>
					<div class="col-sm-9">
						<p class="form-control-static">
						<?php  echo $cashlog['payment_no'];?>
						</p>
					</div>
				</div>
				<?php  } ?>
				<?php  if(!empty($cashlog['err_code'])) { ?>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">错误代码</label>
					<div class="col-sm-9">
						<p class="form-control-static">
						<?php  echo $cashlog['err_code'];?>
						</p>
					</div>
				</div>
				<?php  } ?>
				<?php  if(!empty($cashlog['err_code_des'])) { ?>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">错误描述</label>
					<div class="col-sm-9">
						<p class="form-control-static">
						<?php  echo $cashlog['err_code_des'];?>
						</p>
					</div>
				</div>
				<?php  } ?>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">管理员备注</label>
					<div class="col-sm-9">
                        <?php  if($cashlog['status']==0) { ?>
							<textarea style="width:500px;height:50px;" name="remark" id="remark" class="form-control"></textarea>
						<?php  } else { ?>
							<?php  echo $cashlog['remark'];?>
						<?php  } ?>
                    </div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
					<div class="col-sm-9 col-xs-12">
						<?php  if($cashlog['status']==0) { ?>
						<input type="submit" name="submit" value="提交" class="btn btn-success col-lg-1"  onclick="showOverlay()" />
						<input type="hidden" name="token" value="<?php  echo $_W['token'];?>">
						<?php  } ?>
						<input type="button" name="back" onclick="history.back()" style="margin-left:10px;" value="返回列表" class="btn btn-default">
					</div>
				</div>
			</div>
		</div>
	</form>
</div>
<script type="text/javascript">
function check(){
	var status = $('input[name="status"]:checked').val();
	var remark = $('#remark').val();
	if(status==0){
		alert("请选择处理状态");
		return false;
	}
	if(status=='-1' && remark==''){
		alert("请输入管理员备注");
		return false;
	}

	/* 显示遮罩层 */
	$("#overlay").height("100%");
    $("#overlay").width("100%");
    $("#overlay").fadeTo(200, 0.2);
	$(".mloading-bar").show();
}
</script>

<?php  } else if($op=='handle') { ?>
<div class="main">
	<div class="alert alert-info">
	    佣金增减可用于对分销商会员的佣金金额调整，可用于针对退款的订单进行佣金增减
	</div>
    <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
        <div class="panel panel-default">
            <div class="panel-heading">佣金调整</div>
            <div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">会员ID</label>
					<div class="col-sm-9 col-xs-12">
						<div class="input-group">
							<input type="text" id="nickname" maxlength="30" class="form-control" readonly />
							<input type="hidden" id="user_id" name="user_id" />
							<div class='input-group-btn'>
								<button class="btn btn-default" type="button" onclick="popwin = $('#modal-module-member').modal();">选择用户</button>
							</div>
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
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">佣金类型</label>
					<div class="col-sm-9">
                        <label class="radio-inline"><input type="radio" name="commission_type" value="1" checked/> 分销商佣金</label>
                        &nbsp;
                        <label class="radio-inline"><input type="radio" name="commission_type" value="2"/> 讲师课程佣金</label>
                    </div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">调整金额</label>
					<div class="col-sm-9">
						<input type="text" name="amount" class="form-control" />
						<span class="help-block">正数表示增加，负数表示减少</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">备注信息</label>
					<div class="col-sm-9">
						<textarea name="remark" class="form-control" placeholder="订单号等"></textarea>
					</div>
				</div>
            </div>
        </div>

        <div class="form-group col-sm-12">
            <input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-1" onclick="showOverlay()"/>
            <input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
        </div>
    </form>
</div>
<script type="text/javascript">
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
	$("#nickname").val('昵称:' + obj.nickname + '；会员id:' + obj.uid);
	$("#user_id").val(obj.uid);
}
</script>
<?php  } ?>

<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>