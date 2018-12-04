<?php defined('IN_IA') or exit('Access Denied');?><!--
 * 分销商管理
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

<?php  if($op=='display') { ?>
<link href="<?php echo MODULE_URL;?>template/web/style/fycommon.css" rel="stylesheet">

<div class="panel panel-info">
    <div class="panel-heading">筛选</div>
    <div class="panel-body">
        <form action="./index.php" method="get" class="form-horizontal" role="form" id="form1">
            <input type="hidden" name="c" value="site" />
            <input type="hidden" name="a" value="entry" />
            <input type="hidden" name="m" value="fy_lessonv2" />
            <input type="hidden" name="do" value="agent" />
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">用户昵称</label>
				<div class="col-sm-8 col-lg-3 col-xs-12">
					<input type="text" class="form-control" name="nickname" value="<?php  echo $_GPC['nickname'];?>" placeholder="用户昵称/真实名字/手机号码"/>
				</div>
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">用户ID</label>
				<div class="col-sm-8 col-lg-3 col-xs-12">
					<input type="text" class="form-control"  name="uid" value="<?php  echo $_GPC['uid'];?>" placeholder='用户ID'/>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">推荐人ID</label>
				<div class="col-sm-8 col-lg-3 col-xs-12">
					<input type="text" class="form-control" name="parentid" value="<?php  echo $_GPC['parentid'];?>" placeholder='推荐人ID'/>
				</div>
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">分销级别</label>
				<div class="col-sm-8 col-lg-3 col-xs-12">
					<select name='agent_level' class='form-control'>
						<option value="">全部级别</option>
						<option value="0" <?php  if(in_array($_GPC['agent_level'],array('0'))) { ?>selected<?php  } ?>>默认级别</option>
						<?php  if(is_array($levelList)) { foreach($levelList as $key => $level) { ?>
						<option value="<?php  echo $key;?>" <?php  if($_GPC['agent_level']==$key) { ?>selected<?php  } ?>><?php  echo $level;?></option>
						<?php  } } ?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">分销状态</label>
				<div class="col-sm-8 col-lg-3 col-xs-12">
					<select name='status' class='form-control'>
						<option value=''>不限</option>
						<option value='1' <?php  if($_GPC['status']=='1') { ?>selected<?php  } ?>>正常</option>
						<option value='0' <?php  if($_GPC['status']=='0') { ?>selected<?php  } ?>>冻结</option>
					</select>
				</div>
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">会员身份</label>
				<div class="col-sm-8 col-lg-3 col-xs-12">
					<select name='vip' class='form-control'>
						<option value=''>不限</option>
						<option value='1' <?php  if($_GPC['vip']=='1') { ?>selected<?php  } ?>>VIP会员</option>
						<option value='0' <?php  if($_GPC['vip']=='0') { ?>selected<?php  } ?>>普通会员</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">加入时间</label>
				<div class="col-sm-8 col-lg-3 col-xs-12">
					<?php echo tpl_form_field_daterange('time', array('start'=>($starttime ? date('Y-m-d', $starttime) : false),'end'=> ($endtime ? date('Y-m-d', $endtime) : false)));?>
				</div>
				<div class="col-sm-8 col-lg-3 col-xs-12">
					<button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>&nbsp;&nbsp;&nbsp;
					<button type="submit" name="export" value="1" class="btn btn-success">导出 Excel</button>
				</div>
			</div>
        </form>
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">总数：<?php  echo $total;?></div>
    <div class="panel-body">
        <table class="table table-hover table-responsive">
            <thead class="navbar-inner" >
            <tr>
				<th style='width:12%;'>推荐人</th>
                <th style='width:8%;'>会员ID</th>
				<th style='width:12%;'>粉丝</th>
                <th style='width:12%;'>真实名字<br/>手机号码</th>
                <th style='width:10%;'>未结佣金<br/>累计佣金</th>
                <th style='width:10%;'>分销级别</th>
                <th style='width:11%;'>分销状态</th>
                <th style='width:13%;'>加入时间</th>
                <th style='width:10%'>操作</th>
            </tr>
            </thead>
            <tbody>
            <?php  if(is_array($list)) { foreach($list as $row) { ?>
            <tr>
				<td>
                <?php  if($row['parentid']==0) { ?>
                <label class='label label-primary'>总店</label>
                <?php  } else { ?>
					<img src="<?php  echo $row['parent']['avatar'];?>" style='width:30px;height:30px;padding1px;border:1px solid #ccc' />
					<?php echo $row['parent']['nickname']?$row['parent']['nickname']:'未设置';?>
				<?php  } ?>
                </td>
				<td><?php  echo $row['uid'];?></td>
                <td>
                    <img src="<?php  echo $row['avatar'];?>" style="width:30px;height:30px;padding1px;border:1px solid #ccc"/>
                    <?php echo $row['nickname']?$row['nickname']:'未设置';?>
                </td>
                <td><?php  echo $row['realname'];?> <br/><?php  echo $row['mobile'];?></td>
                <td><?php  echo $row['nopay_commission'];?> 元<br/><?php  echo $row['pay_commission']+$row['nopay_commission'];?> 元</td>
                <td><?php  echo $row['agent'];?></td>
                <td>
					<?php  if($row['status']==1) { ?>
						<span class="label label-success"><?php  echo $statusList[$row['status']];?></span>
					<?php  } else { ?>
						<span class="label label-default"><?php  echo $statusList[$row['status']];?></span>
					<?php  } ?>
				</td>
                <td><?php  echo date('Y-m-d H:i',$row['addtime']);?></td>
                <td style="overflow:visible;">
					<div class="btn-group btn-group-sm">
						<a class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false" href="javascript:;">操作 <span class="caret"></span></a>
						<ul class="dropdown-menu dropdown-menu-left" role="menu" style='z-index: 99999'>
							<li><a href="<?php  echo $this->createWebUrl('agent', array('op'=>'detail', 'uid'=>$row['uid'],'refurl'=>$_W['siteurl']));?>" title="编辑"><i class="fa fa-pencil"></i> 编辑</a></li>
							<?php  if($row['teachers']) { ?>
								<li><a href="<?php  echo $this->createWebUrl('agent', array('op'=>'myteacher','uid'=>$row['uid'],'refurl'=>$_W['siteurl']));?>" title="查看机构讲师"><i class="fa fa-bank"></i> 查看下级讲师</a></li>
							<?php  } ?>
							<li><a href="<?php  echo $this->createWebUrl('team', array('uid'=>$row['uid'],'refurl'=>$_W['siteurl']));?>" title="查看下级成员"><i class="fa fa-list"></i> 查看下级成员</a></li>
							<li><a href="<?php  echo $this->createWebUrl('team', array('op'=>'export','uid'=>$row['uid']));?>" title="导出下级成员"><i class="fa fa-download"></i> 导出下级成员</a></li>
							<li><a href="<?php  echo $this->createWebUrl('agent', array('op'=>'delete', 'uid'=>$row['uid'],'refurl'=>$_W['siteurl']));?>" title="删除分销成员" onclick="return confirm('该操作无法撤销恢复，确定删除?');"><i class="fa fa-remove"></i> &nbsp;删除分销成员</a></li>
						</ul>
                    </div>
                </td>
            </tr>
            <?php  } } ?>
            </tbody>
        </table>
        <?php  echo $pager;?>
    </div>
</div>

<?php  } else if($operation=='detail') { ?>
<form action="" method='post' class='form-horizontal'>
	<input type="hidden" name="uid" value="<?php  echo $member['uid'];?>">
	<input type="hidden" name="c" value="site" />
	<input type="hidden" name="a" value="entry" />
	<input type="hidden" name="m" value="fy_lessonv2" />
	<input type="hidden" name="do" value="agent" />
	<input type="hidden" name="op" value="detail" />
	<div class='panel panel-default'>
		<div class='panel-heading'>
			分销商详细信息
		</div>
		<div class='panel-body'>

			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">粉丝</label>
				<div class="col-sm-9 col-xs-12">
					<img src='<?php  echo $member['avatar'];?>' style='width:100px;height:100px;padding:1px;border:1px solid #ccc' />
					<?php  echo $member['nickname'];?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">Uid/Openid</label>
				<div class="col-sm-9 col-xs-12">
					<div class="form-control-static"><?php  echo $member['uid'];?> / <?php echo $member['openid'] ? $member['openid'] : '无';?></div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">真实姓名</label>
				<div class="col-sm-9 col-xs-12">
					<input type="text" name="realname" class="form-control" value="<?php  echo $member['realname'];?>" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">手机号码</label>
				<div class="col-sm-9 col-xs-12">
					<input type="text" name="mobile" class="form-control" value="<?php  echo $member['mobile'];?>" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">VIP等级</label>
				<div class="col-sm-9">
					<?php  if(!empty($viplist)) { ?>
						<?php  if(is_array($viplist)) { foreach($viplist as $item) { ?>
						<div class="input-group" style="margin-top:7px;">
							<span class="input-group-addon"><?php  echo $item['level_name'];?> - 有效期：</span>
							<?php  echo tpl_form_field_date("validity[$item[id]]", $item['validity'],true);?>
						</div>
						<?php  } } ?>
					<?php  } else { ?>
						<input type="text" class="form-control" value="无" readonly/>
					<?php  } ?>
					<span class="help-block"><a href="<?php  echo $this->createWebUrl('viporder', array('op'=>'createOrder', 'uid'=>$member['uid']));?>" style="color:#1377ce;" target="_blank">开通会员VIP</a></span>
				</div>
			</div>
			
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">上级会员ID</label>
				<div class="col-sm-9 col-xs-12">
					<input type="text" name="parentid" id="parentid" class="form-control" value="<?php  echo $member['parentid'];?>" style="display:inline-block;width:88%;" readonly />&nbsp;&nbsp;<a id="removeread" href="javascript:;">显示修改</a>
					<span class="help-block">上级会员ID为0时表示为总店推荐</span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">分销代理级别</label>
				<div class="col-sm-9 col-xs-12">
					<select name="agent_level" class="form-control">
						<option value="0" <?php  if($member['agent_level']==0) { ?>selected<?php  } ?>>默认级别</option>
						<?php  if(is_array($levelList)) { foreach($levelList as $key => $level) { ?>
						<option value="<?php  echo $key;?>" <?php  if($member['agent_level']==$key) { ?>selected<?php  } ?>><?php  echo $level;?></option>
						<?php  } } ?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">购买订单总额</label>
				<div class="col-sm-9 col-xs-12">
					<div class='form-control-static'> <?php  echo $member['payment_amount'];?> 元</div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">购买订单数量</label>
				<div class="col-sm-9 col-xs-12">
					<div class='form-control-static'> <?php  echo $member['payment_order'];?> 单</div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">加入时间</label>
				<div class="col-sm-9 col-xs-12">
					<div class='form-control-static'><?php  echo date('Y-m-d H:i:s', $member['addtime']);?></div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">分销商状态</label>
				<div class="col-sm-9 col-xs-12">
					<?php  if(is_array($agentStatusList)) { foreach($agentStatusList as $key => $item) { ?>
					<label class="radio-inline"><input type="radio" name="status" value="<?php  echo $key;?>" <?php  if($member['status']==$key) { ?>checked<?php  } ?>><?php  echo $item;?></label>
					<?php  } } ?>
					<div class="help-block">冻结状态的分销商将不能发展下级成员，需要达到分销条件后方可转为正常状态。</div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">分销链接</label>
				<div class="col-sm-9">
					<div style="padding-top:8px;font-size: 14px;"><a href="javascript:;" id="copy-btn"><?php  echo $_W['siteroot'];?>app/<?php  echo substr($this->createMobileUrl('index', array('uid'=>$member['uid'])), 2);?></a></div>
				</div>
			</div>
		</div>

		<div class='panel-body'>
			<div class="form-group"></div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
				<div class="col-sm-9 col-xs-12">
					<input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-1"  />
					<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
					<input type="button" name="back" onclick="location.href='<?php  echo $_GPC['refurl'];?>'" style='margin-left:10px;' value="返回列表" class="btn btn-default" />
				</div>
			</div>
		</div>
	</div>
</form>
<script type="text/javascript">
$("#removeread").click(function(){
	document.getElementById("parentid").readOnly = false;
});
require(['jquery', 'util'], function($, util){
	$(function(){
		util.clip($("#copy-btn")[0], $("#copy-btn").text());
	});
});
</script>

<?php  } else if($op=='myteacher') { ?>
<style type="text/css">
.self{margin:4px 0;}
</style>
<div class="panel panel-default">
    <div class='panel-body'>
    <div style='height:100px;width:110px;float:left;'>
         <img src='<?php  echo $member['avatar'];?>' style='width:100px;height:100px;border:1px solid #ccc;padding:1px' />
    </div>
    <div style='float:left;height:100px;overflow: hidden'>
        <p class="self">会员昵称：<?php  echo $member['nickname'];?></p>
        <p class="self">会员姓名：<?php  echo $member['realname'];?></p>
        <p class="self">手机号码：<?php  echo $member['mobile'];?></p>
		<p class="self">下级讲师：<span style="color:red"><?php  echo $total;?></span> 位</p>
	</div>
</div>
 
<div class="panel panel-default">
    <div class="panel-heading">我的下级讲师</div>
    <div class="panel-body">
        <table class="table table-hover" style="overflow:visible;">
            <thead class="navbar-inner">
                <tr>
                    <th style='width:10%;'>讲师ID</th>
					<th style='width:20%;'>讲师名称</th>
					<th style='width:15%;'>会员ID</th>
					<th style='width:20%;'>会员昵称</th>
					<th style='width:15%;'>讲师状态</th>
					<th style='width:15%;'>加入时间</th>
                </tr>
            </thead>
            <tbody>
				<?php  if(is_array($list)) { foreach($list as $row) { ?>
				<tr>
					<td><?php  echo $row['uid'];?></td>
					<td>
						<?php  if(!empty($row['teacherphoto'])) { ?>
						<img src="<?php  echo $_W['attachurl'];?><?php  echo $row['teacherphoto'];?>" style='width:30px;height:30px;padding1px;border:1px solid #ccc' />
						<?php  } ?>
						<?php  if(empty($row['teacher'])) { ?>未设置<?php  } else { ?><?php  echo $row['teacher'];?><?php  } ?>

					</td>
					<td><?php  echo $row['uid'];?></td>
					<td><?php  if(!empty($row['avatar'])) { ?>
						<img src="<?php  echo $row['avatar'];?>" style='width:30px;height:30px;padding1px;border:1px solid #ccc' />
						<?php  } ?>
						<?php  echo $row['nickname'];?>
					</td>
					<td>
						<?php  if($row['status']=='-1') { ?>
							<span class="label label-default"><?php  echo $teacherStatusList[$row['status']];?></span>
						<?php  } else if($row['status']=='1') { ?>
							<span class="label label-success"><?php  echo $teacherStatusList[$row['status']];?></span>
						<?php  } else if($row['status']=='2') { ?>
							<span class="label label-danger"><?php  echo $teacherStatusList[$row['status']];?></span>
						<?php  } ?>
					</td>
					<td><?php  echo date('Y-m-d H:i',$row['addtime']);?></td>
				</tr>
				<?php  } } ?>
            </tbody>
        </table>
        <?php  echo $pager;?>
    </div>
</div>

<?php  } ?>

<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>