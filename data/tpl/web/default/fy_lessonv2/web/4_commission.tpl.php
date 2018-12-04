<?php defined('IN_IA') or exit('Access Denied');?><!--
 * 分销设置
 * ============================================================================
 * 版权所有 2015-2018 微课堂团队，并保留所有权利。
 * 网站地址: https://wx.haoshu888.com
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！不允许对程序代码以任何形式任何目的的再发布，作者将保留
 * 追究法律责任的权力和最终解释权。
 * ============================================================================
-->

<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('web/commission/commission-header', TEMPLATE_INCLUDEPATH)) : (include template('web/commission/commission-header', TEMPLATE_INCLUDEPATH));?>

<?php  if($op=='level') { ?>
<div class="alert alert-info">
    1、分销等级从上往下依次为从低往高，默认等级在“分销设置”里设定，默认等级处于最低等级。<br/>
	2、课程或VIP会员服务指定单独的佣金比例后，优先按课程或VIP会员服务单独的佣金比例计算。
</div>
<form action="" method="post" onsubmit="return formcheck(this)">
	<div class="panel panel-default">
		<div class="panel-heading">
			分销商等级设置
		</div>
		<div class="panel-body">
			<table class="table">
				<thead>
					<tr>
						<th style="width:10%;">等级名称</th>
                        <th style="width:10%;">一级比例</th>
						<th style="width:10%;">二级比例</th>
						<th style="width:10%;">三级比例</th>
						<th style="width:20%;">升级条件</th>
                        <th style="width:15%;">操作</th>
					</tr>
				</thead>
				<tbody>
					<?php  if(is_array($list)) { foreach($list as $level) { ?>
					<tr>
						<td><?php  echo $level['levelname'];?></td>
                        <td><?php  echo $level['commission1'];?>%</td>
						<td><?php  echo $level['commission2'];?>%</td>
						<td><?php  echo $level['commission3'];?>%</td>
						<td>
						<?php  if($level['updatemoney']>0) { ?>
							<?php  if($comsetting['upgrade_condition']==1) { ?>分销累计佣金
							<?php  } else if($comsetting['upgrade_condition']==2) { ?>购买订单总额
							<?php  } else if($comsetting['upgrade_condition']==3) { ?>购买订单
							<?php  } ?>
							满 <?php  echo $level['updatemoney'];?>
							<?php  if($comsetting['upgrade_condition']==1 || $comsetting['upgrade_condition']==2) { ?>元
							<?php  } else if($comsetting['upgrade_condition']==3) { ?>单
							<?php  } ?>
						<?php  } else { ?>
							不自动升级
						<?php  } ?>
						</td>
                        <td>
                            <a class="btn btn-default" href="<?php  echo $this->createWebUrl('commission', array('op'=>'editlevel', 'id'=>$level['id']));?>">编辑</a>
                            <a class="btn btn-default" href="<?php  echo $this->createWebUrl('commission', array('op'=>'dellevel', 'id'=>$level['id']));?>" onclick="return confirm('确认删除此等级吗？');return false;">删除</a>
						</td>
                    </tr>
					<?php  } } ?>
                </tbody>
            </table>
			<?php  echo $pager;?>
         </div>
         <div class="panel-footer">
			<a class="btn btn-default" href="<?php  echo $this->createWebUrl('commission', array('op'=>editlevel));?>"><i class="fa fa-plus"></i> 添加新等级</a>
         </div>
     </div>
</form>

<?php  } else if($op=='editlevel') { ?>
<div class="alert alert-info">
    提示: 没有设置等级的分销商将按默认设置计算提成。商品指定的佣金金额的优先级仍是最高的，也就是说只要商品指定了佣金金额就按商品的佣金金额来计算，不受等级影响
</div>
<div class="main">
    <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
        <div class="panel panel-default">
            <div class="panel-heading">
                分销商等级设置
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style="color:red">*</span> 等级名称</label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="text" name="levelname" class="form-control" value="<?php  echo $level['levelname'];?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style="color:red">*</span>一级分销比例</label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="text" name="commission1" class="form-control" value="<?php  echo $level['commission1'];?>">
                    </div>
                </div>
				<div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">二级分销比例</label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="text" name="commission2" class="form-control" value="<?php  echo $level['commission2'];?>">
                    </div>
                </div>
				<div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">三级分销比例</label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="text" name="commission3" class="form-control" value="<?php  echo $level['commission3'];?>">
                    </div>
                </div>
				<div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">升级条件</label>
                    <div class="col-sm-9 col-xs-12">
                        <div class="input-group">
							<span class="input-group-addon">
								<?php  if($comsetting['upgrade_condition']==1) { ?>分销累计佣金
								<?php  } else if($comsetting['upgrade_condition']==2) { ?>购买订单总额
								<?php  } else if($comsetting['upgrade_condition']==3) { ?>购买订单
								<?php  } ?>
								满
							</span>
							<input type="text" name="updatemoney" class="form-control" value="<?php  echo $level['updatemoney'];?>">
							<span class="input-group-addon">
								<?php  if($comsetting['upgrade_condition']==1 || $comsetting['upgrade_condition']==2) { ?>元
								<?php  } else if($comsetting['upgrade_condition']==3) { ?>单
								<?php  } ?>
							</span>
                        </div>
                        <span class="help-block">分销商升级条件，不填写默认为不自动升级</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group col-sm-12">
			<input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-1">
			<input type="hidden" name="id" value="<?php  echo $id;?>">
			<input type="hidden" name="token" value="<?php  echo $_W['token'];?>">
		</div>
    </form>
</div>

<?php  } else if($op=='commissionlog') { ?>
<div class="panel panel-info">
	<div class="panel-heading">筛选</div>
	<div class="panel-body">
		<form action="./index.php" method="get" class="form-horizontal" role="form">
			<input type="hidden" name="c" value="site" />
			<input type="hidden" name="a" value="entry" />
			<input type="hidden" name="m" value="fy_lessonv2" />
			<input type="hidden" name="do" value="commission" />
			<input type="hidden" name="op" value="commissionlog" />
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width:100px;">会员昵称</label>
				<div class="col-sm-2 col-lg-3">
					<input class="form-control" name="nickname" type="text" value="<?php  echo $_GPC['nickname'];?>">
				</div>
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label" style="width: 100px;">课程名称</label>
				<div class="col-sm-2 col-lg-3">
					<input class="form-control" name="bookname" type="text" value="<?php  echo $_GPC['bookname'];?>">
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width:100px;">分销等级</label>
				<div class="col-sm-8 col-lg-3 col-xs-12">
					<select name="grade" class="form-control">
						<option value="">不限</option>
						<option value="1" <?php  if($_GPC['grade'] == 1) { ?> selected="selected" <?php  } ?>>一级分销</option>
						<option value="2" <?php  if($_GPC['grade'] == 2) { ?> selected="selected" <?php  } ?>>二级分销</option>
						<option value="3" <?php  if($_GPC['grade'] == 3) { ?> selected="selected" <?php  } ?>>三级分销</option>
						<option value="-1" <?php  if($_GPC['grade'] == -1) { ?> selected="selected" <?php  } ?>>管理员操作</option>
					</select>
				</div>
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label" style="width: 100px;">备注</label>
				<div class="col-sm-2 col-lg-3">
					<input class="form-control" name="remark" id="" type="text" value="<?php  echo $_GPC['remark'];?>">
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width: 100px;">分销时间</label>
				<div class="col-sm-8 col-lg-3 col-xs-12">
					<?php echo tpl_form_field_daterange('time', array('starttime'=>($starttime ? date('Y-m-d', $starttime) : false),'endtime'=> ($endtime ? date('Y-m-d', $endtime) : false)));?>
				</div>
				<div class="col-sm-3 col-lg-3" style="width: 22%;">
					<button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>
					<button type="submit" name="export" value="1" class="btn btn-success">导出 Excel</button>
				</div>
			</div>
		</form>
	</div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">总数：<?php  echo $total;?></div>
    <div class="panel-body">
        <table class="table table-hover">
            <thead class="navbar-inner">
                <tr>
                    <th style='width:8%;'>ID</th>
                    <th style='width:10%;'>会员信息</th>
                    <th style='width:23%;'>分销课程</th>
                    <th style='width:10%;'>分销等级</th>
					<th style='width:10%;'>分销佣金</th>
                    <th style='width:24%;'>备注</th>
                    <th style='width:15%;'>分销时间</th>
                </tr>
            </thead>
            <tbody>
                <?php  if(is_array($list)) { foreach($list as $row) { ?>
                <tr>
                    <td><?php  echo $row['id'];?></td>
                    <td><?php  echo $row['nickname'];?></td>
                    <td><?php  echo $row['bookname'];?></td>
					<td><?php  if($row['grade']==1) { ?><span class="label label-success">一级分销</span><?php  } else if($row['grade']==2) { ?><span class="label label-primary">二级分销</span><?php  } else if($row['grade']==3) { ?><span class="label label-warning">三级分销</span><?php  } else if($row['grade']==-1) { ?><span class="label label-info">管理员操作</span><?php  } ?></td>
                    <td><?php  echo $row['change_num'];?> 元</td>
					<td><?php  echo $row['remark'];?></td>
                    <td><?php  echo date('Y-m-d H:i',$row['addtime']);?></td>
                </tr>
                <?php  } } ?>
            </tbody>
        </table>
        <?php  echo $pager;?>
    </div>
</div>

<?php  } else if($op=='statis') { ?>
<div class="panel panel-info">
	<div class="panel-heading">筛选</div>
	<div class="panel-body">
		<form action="./index.php" method="get" class="form-horizontal" role="form">
			<input type="hidden" name="c" value="site" />
			<input type="hidden" name="a" value="entry" />
			<input type="hidden" name="m" value="fy_lessonv2" />
			<input type="hidden" name="do" value="commission" />
			<input type="hidden" name="op" value="statis" />
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width:100px;">会员信息</label>
				<div class="col-sm-2 col-lg-3">
					<input class="form-control" name="keyword" type="text" value="<?php  echo $_GPC['keyword'];?>" placeholder="会员昵称/姓名">
				</div>
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width:100px;">手机号码</label>
				<div class="col-sm-8 col-lg-3 col-xs-12">
					<input class="form-control" name="mobile" type="text" value="<?php  echo $_GPC['mobile'];?>">
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width:100px;">排序依据</label>
				<div class="col-sm-8 col-lg-3 col-xs-12">
					<select name="ranking" class="form-control">
						<option value="1" <?php  if($_GPC['ranking'] == 1) { ?> selected="selected" <?php  } ?>>累计佣金</option>
						<option value="2" <?php  if($_GPC['ranking'] == 2) { ?> selected="selected" <?php  } ?>>已申请佣金</option>
						<option value="3" <?php  if($_GPC['ranking'] == 3) { ?> selected="selected" <?php  } ?>>待申请佣金</option>
					</select>
				</div>
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width: 100px;">注册时间</label>
				<div class="col-sm-8 col-lg-3 col-xs-12">
					<?php echo tpl_form_field_daterange('time', array('start'=>($starttime ? date('Y-m-d', $starttime) : false),'end'=> ($endtime ? date('Y-m-d', $endtime) : false)));?>
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
    <div class="panel-heading">总数：<?php  echo $total;?></div>
    <div class="panel-body">
        <table class="table table-hover">
            <thead class="navbar-inner">
                <tr>
                    <th style='width:8%;'>会员ID</th>
                    <th style='width:13%;'>会员昵称</th>
                    <th style='width:13%;'>会员姓名</th>
                    <th style='width:14%;'>手机号码</th>
					<th style='width:14%;'>已申请佣金</th>
                    <th style='width:13%;'>待申请佣金</th>
                    <th style='width:13%;'>累计佣金</th>
					<th style='width:12%;'>注册时间</th>
                </tr>
            </thead>
            <tbody>
                <?php  if(is_array($list)) { foreach($list as $row) { ?>
                <tr>
                    <td><?php  echo $row['uid'];?></td>
                    <td><?php  echo $row['nickname'];?></td>
                    <td><?php  echo $row['realname'];?></td>
					<td><?php  echo $row['mobile'];?></td>
                    <td><?php  echo $row['pay_commission'];?> 元</td>
					<td><?php  echo $row['nopay_commission'];?> 元</td>
                    <td><?php  echo $row['total_commission'];?> 元</td>
					<td><?php  echo date('Y-m-d', $row['addtime']);?></td>
                </tr>
                <?php  } } ?>
            </tbody>
        </table>
        <?php  echo $pager;?>
    </div>
</div>
<?php  } ?>

<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>