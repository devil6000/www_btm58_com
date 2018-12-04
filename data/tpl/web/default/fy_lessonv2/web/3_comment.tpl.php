<?php defined('IN_IA') or exit('Access Denied');?><!-- 
 * 评价管理
 * ============================================================================
 * 版权所有 2015-2018 微课堂团队，并保留所有权利。
 * 网站地址: https://wx.haoshu888.com
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！不允许对程序代码以任何形式任何目的的再发布，作者将保留
 * 追究法律责任的权力和最终解释权。
 * ============================================================================
-->
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<ul class="nav nav-tabs">
	<li <?php  if($op=='display') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('comment');?>">评价列表</a></li>
	<?php  if($op=='reply') { ?>
	<li <?php  if($op=='reply') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('comment', array('op'=>'reply','id'=>$_GPC['id']));?>">评价详情</a></li>
	<?php  } ?>
</ul>
<?php  if($operation == 'display') { ?>
<style type="text/css">
.col-lg-3{width:22%;}
</style>
<div class="main">
    <div class="panel panel-info">
        <div class="panel-heading">筛选</div>
        <div class="panel-body">
            <form action="./index.php" method="get" class="form-horizontal" role="form">
                <input type="hidden" name="c" value="site" />
                <input type="hidden" name="a" value="entry" />
                <input type="hidden" name="m" value="fy_lessonv2" />
                <input type="hidden" name="do" value="comment" />
                <input type="hidden" name="op" value="display" />
                <div class="form-group">
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width:100px;">订单编号</label>
                    <div class="col-sm-2 col-lg-3">
                        <input class="form-control" name="ordersn" type="text" value="<?php  echo $_GPC['ordersn'];?>">
                    </div>
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label" style="width: 100px;">课程名称</label>
                    <div class="col-sm-2 col-lg-3">
                        <input class="form-control" name="bookname" type="text" value="<?php  echo $_GPC['bookname'];?>">
                    </div>
                </div>
                <div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label" style="width: 100px;">用户昵称</label>
                    <div class="col-sm-2 col-lg-3">
                        <input class="form-control" name="nickname" id="" type="text" value="<?php  echo $_GPC['nickname'];?>">
                    </div>
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label" style="width: 100px;">回复状态</label>
                    <div class="col-sm-2 col-lg-3">
                        <select name="reply" class="form-control">
							<option value="">请选择...</option>
							<option value="0" <?php  if(in_array($_GPC['reply'], array('0'))) { ?>selected<?php  } ?>>未回复</option>
							<option value="1" <?php  if($_GPC['reply']==1) { ?>selected<?php  } ?>>已回复</option>
						</select>
                    </div>
                </div>
				<div class="form-group">
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width: 100px;">评价日期</label>
                    <div class="col-sm-8 col-lg-3 col-xs-12">
                        <?php  echo tpl_form_field_daterange('time', array('starttime'=>date('Y-m-d', $starttime),'endtime'=>date('Y-m-d', $endtime)));?>
                    </div>
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label" style="width: 100px;">审核状态</label>
                    <div class="col-sm-2 col-lg-3">
                        <select name="status" class="form-control">
							<option value="">请选择...</option>
							<option value="0" <?php  if(in_array($_GPC['status'], array('0'))) { ?>selected<?php  } ?>>待审核</option>
							<option value="1" <?php  if($_GPC['status']==1) { ?>selected<?php  } ?>>已审核</option>
							<option value="-1" <?php  if($_GPC['status']==-1) { ?>selected<?php  } ?>>审核未通过</option>
						</select>
                    </div>
                    <button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>
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
                    <th style="width:15%;">订单遍号</th>
                    <th style="width:13%;">用户昵称</th>
                    <th style="width:20%;">课程名称</th>
                    <th style="width:13%;text-align:center;">评价/回复</th>
                    <th style="width:10%;text-align:center;">状态</th>
                    <th style="width:10%;">评价日期</th>
                    <th style=" text-align:right;">操作</th>
                </tr>
                </thead>
                <tbody>
                <?php  if(is_array($list)) { foreach($list as $item) { ?>
                <tr>
                    <td><?php echo $item['ordersn']?$item['ordersn']:'免费课程';?></td>
                    <td><?php  echo $item['nickname'];?></td>
                    <td><?php  echo $item['bookname'];?></td>
                    <td align="center">
						<?php  if($item['grade']==1) { ?><span class="label" style="background-color:#FB5B5B;">好评</span>
						<?php  } else if($item['grade']==2) { ?><span class="label" style="background-color:#D99810;">中评</span>
						<?php  } else if($item['grade']==3) { ?><span class="label" style="background-color:#555555;">差评</span><?php  } ?>
						
						<?php  if(!empty($item['reply'])) { ?>
						<span class="label label-success">已回复</span>
						<?php  } else { ?>
						<span class="label label-default">未回复</span>
						<?php  } ?>
                    </td>
                    <td align="center">
                    	<?php  if($item['status']==-1) { ?>
						<span class="label label-default">未通过</span>
						<?php  } else if($item['status']==0) { ?>
						<span class="label label-primary">待审核</span>
						<?php  } else if($item['status']==1) { ?>
						<span class="label label-success">已审核</span>
						<?php  } ?>
                    </td>
                    <td><?php  echo date('Y-m-d', $item['addtime'])?></td>
                    <td style="text-align:right;">
						<a class="btn btn-default btn-sm" href="<?php  echo $this->createWebUrl('comment', array('op' => 'reply', 'id' => $item['id']))?>" title="查看评价"><i class="fa fa-edit"></i>查看</a>
                        <a class="btn btn-default btn-sm" href="<?php  echo $this->createWebUrl('comment', array('op' => 'delete', 'id' => $item['id'], 'refurl'=>$_W['siteurl']))?>" title="删除评价" onclick="return confirm('此操作不可恢复，确认删除？');return false;"><i class="fa fa-times"></i>删除</a>
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
<?php  } else if($operation == 'reply') { ?>
<div class="main">
	<form method="post" class="form-horizontal form">
        <div class="panel panel-default">
            <div class="panel-heading">
            	订单信息
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">订单遍号</label>
                    <div class="col-sm-9">
                        <p class="form-control-static"><?php echo $evaluate['ordersn']?$evaluate['ordersn']:'免费课程';?></p>
                    </div>
                </div>
				<div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">课程名称</label>
                    <div class="col-sm-9">
                        <p class="form-control-static">
                        <?php  echo $evaluate['bookname'];?>
                        </p>
                    </div>
                </div>
				<div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">昵称/姓名/手机号码</label>
                    <div class="col-sm-9">
                        <p class="form-control-static">
							<img src="<?php  echo $avatar;?>" width="35" height="35">
							<?php  echo $member['nickname'];?> / <?php  echo $member['realname'];?> / <?php  echo $member['mobile'];?>
                        </p>
                    </div>
                </div>
				<div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">评价级别</label>
                    <div class="col-sm-9">
                        <p class="form-control-static">
							<label>
								<input type="radio" name="grade" value="1" <?php  if($evaluate['grade']==1){echo 'checked';} ?> /> <span class="label" style="background-color:#FB5B5B;">好评</span>
							</label>
							&nbsp;&nbsp;
							<label>
								<input type="radio" name="grade" value="2" <?php  if($evaluate['grade']==2){echo 'checked';} ?> /> <span class="label" style="background-color:#D99810;">中评</span>
							</label>
							&nbsp;&nbsp;
							<label>
								<input type="radio" name="grade" value="3" <?php  if($evaluate['grade']==3){echo 'checked';} ?> /> <span class="label" style="background-color:#555555;">差评</span>
							</label>
                        </p>
                    </div>
                </div>
				<div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">评价内容</label>
					<div class="col-sm-9">
                        <p class="form-control-static">
							<textarea name="content"  class="form-control" style="width:600px;height:80px;"><?php  echo $evaluate['content'];?></textarea>
                        </p>
                    </div>
                </div>
                <div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">处理状态</label>
					<div class="col-sm-9">
						<p class="form-control-static">
							<label>
								<input type="radio" name="status" value="0" <?php  if($evaluate['status']==0){echo 'checked';} ?> />
								<span class="label label-primary" style="vertical-align:text-top;">待审核</span>
							</label>
							&nbsp;&nbsp;
							<label>
								<input type="radio" name="status" value="1" <?php  if($evaluate['status']==1){echo 'checked';} ?> />
								<span class="label label-success" style="vertical-align:text-top;">已审核</span>
							</label>
							&nbsp;&nbsp;
							<label>
								<input type="radio" name="status" value="-1" <?php  if($evaluate['status']==-1){echo 'checked';} ?> />
								<span class="label label-default" style="vertical-align:text-top;">审核未通过</span>
							</label>
						</p>
					</div>
				</div>
				<div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">回复内容</label>
                    <div class="col-sm-9">
                        <p class="form-control-static">
							<textarea name="reply"  class="form-control" style="width:600px;height:100px;"><?php  echo $evaluate['reply'];?></textarea>
                        </p>
                    </div>
                </div>
				<div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                    <div class="col-sm-9">
						<input type="hidden" name="id" value="<?php  echo $id;?>" />
						<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
                        <input type="submit" name="submit" class="btn btn-info span2" value="提交回复" />
                    </div>
                </div>
            </div>
        </div>
	</form>
</div>
<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>