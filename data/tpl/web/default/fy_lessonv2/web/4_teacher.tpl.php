<?php defined('IN_IA') or exit('Access Denied');?><!--
 * 讲师管理
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
    <li <?php  if($op=='display') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('teacher');?>">讲师列表</a></li>
    <li <?php  if($op=='post') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('teacher', array('op'=>'post', 'id'=>$_GPC['id']));?>"><?php  if($_GPC['id']>0) { ?>编辑<?php  } else { ?>添加<?php  } ?>讲师</a></li>
	<li <?php  if($op=='income') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('teacher', array('op'=>'income'));?>">讲师收入明细</a></li>
</ul>
<?php  if($operation == 'display') { ?>
<style type="text/css">
.form-controls{display: inline-block; width:70px;}
.cblock{display:block !important;}
.cnone{display:none !important;}
</style>
<div class="main">
	<div class="panel panel-info">
        <div class="panel-heading">筛选</div>
        <div class="panel-body">
            <form action="./index.php" method="get" class="form-horizontal" role="form">
                <input type="hidden" name="c" value="site">
                <input type="hidden" name="a" value="entry">
                <input type="hidden" name="m" value="fy_lessonv2">
                <input type="hidden" name="do" value="teacher">
                <input type="hidden" name="op" value="display">
                <div class="form-group">
				    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width:100px;">讲师名称</label>
                    <div class="col-sm-2 col-lg-3">
                        <input class="form-control" name="teacher" value="<?php  echo $_GPC['teacher'];?>">
                    </div>
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width: 100px;">首拼音字母</label>
					<div class="col-sm-2 col-lg-3">
                        <input class="form-control" name="letter" value="<?php  echo $_GPC['letter'];?>">
                    </div>
                </div>
				<div class="form-group">
				    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width:100px;">讲师状态</label>
                    <div class="col-sm-2 col-lg-3">
                        <select name="status" class="form-control">
                            <option value="">不限</option>
							<option value="1" <?php  if($_GPC['status']==1) { ?>selected<?php  } ?>>审核通过</option>
                            <option value="2" <?php  if($_GPC['status']==2) { ?>selected<?php  } ?>>待审核</option>
							<option value="3" <?php  if($_GPC['status']==3) { ?>selected<?php  } ?>>隐藏中</option>
							<option value="-1" <?php  if($_GPC['status']==-1) { ?>selected<?php  } ?>>未通过</option>
                        </select>
                    </div>
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width:100px;">讲师类型</label>
                    <div class="col-sm-2 col-lg-3">
                        <select name="teachertype" class="form-control">
                            <option value="">不限</option>
							<option value="1" <?php  if($_GPC['teachertype']==1) { ?>selected<?php  } ?>>后台添加</option>
                            <option value="2" <?php  if($_GPC['teachertype']==2) { ?>selected<?php  } ?>>自行申请</option>
                        </select>
                    </div>
                    <button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>
                </div>
            </form>
        </div>
    </div>
	<div class="panel panel-default">
        <div class="panel-body">
            <table class="table table-hover">
                <thead class="navbar-inner">
                <tr>
                    <th style="width:8%;">讲师编号</th>
                    <th style="width:10%;">讲师名称</th>
					<th style="width:12%;">待提现佣金</br>累计佣金</th>
					<th style="width:10%;">讲师QQ</th>
					<th style="width:10%;">讲师类型</th>
					<th style="width:9%;">状态</th>
					<th style="width:8%;">排序</th>
                    <th style="width:10%;">申请时间</th>
                    <th style="width:10%; text-align:right;">操作</th>
                </tr>
                </thead>
                <tbody>
					<?php  if(is_array($list)) { foreach($list as $teacher) { ?>
                    <tr>
						<td><?php  echo $teacher['id'];?></td>
						<td><?php  echo $teacher['teacher'];?></td>
						<td>
							<?php echo $teacher['member']['nopay_lesson']?$teacher['member']['nopay_lesson']:0;?>元
							<br/>
							<?php  echo $teacher['member']['nopay_lesson'] + $teacher['member']['pay_lesson'];?>元
						</td>
						<td><?php  echo $teacher['qq'];?></td>
						<td>
							<?php  if(!empty($teacher['uid'])) { ?>
								<span class="label label-primary">自行申请</span>
							<?php  } else { ?>
								<span class="label label-default">后台添加</span>
							<?php  } ?>
						</td>
						<td>
							<?php  if($teacher['status']==-1) { ?>
								<span class="label label-default">未通过</span>
							<?php  } else if($teacher['status']==1) { ?>
								<span class="label label-success">审核通过</span>
							<?php  } else if($teacher['status']==2) { ?>
								<span class="label label-danger">待审核</span>
							<?php  } else if($teacher['status']==3) { ?>
								<span class="label label-info">隐藏中</span>
							<?php  } ?>
						</td>
						<td><?php  echo $teacher['displayorder'];?></td>
						<td><?php  echo date('Y-m-d',$teacher['addtime']);?></td>
						<td style="text-align:right;">
							<div class="btn-group btn-group-sm">
								<a class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false" href="javascript:;">功能列表 <span class="caret"></span></a>
								<ul class="dropdown-menu dropdown-menu-left" role="menu" style='z-index: 99999'>
									<li><a href="<?php  echo $this->createWebUrl('teacher', array('op'=>'post', 'id'=>$teacher['id'], 'refurl'=>$_W['siteurl']));?>"><i class="fa fa-pencil"></i> 编辑讲师</a></li>
									<li><a href="<?php  echo $this->createWebUrl('teacher', array('op'=>'qrcode', 'teacherid'=>$teacher['id']));?>"><i class="fa fa-download"></i> 讲师二维码</a></li>
									<li><a href="<?php  echo $this->createWebUrl('teacher', array('op'=>'delete', 'id'=>$teacher['id']));?>" onclick="return confirm('此操作不可恢复，确认删除？');return false;"><i class="fa fa-times"></i> &nbsp;删除讲师</a></li>
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
</div>

<?php  } else if($operation == 'post') { ?>
<div class="main">
	<div class="alert alert-info">
	    后台添加类型的讲师为虚拟存在讲师，用户购买课程时不会给讲师分成，只有会员自行申请的讲师才会结算课程讲师分成。
	</div>
	<form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
        <div class="panel panel-default">
            <div class="panel-heading">讲师信息</div>
            <div class="panel-body">
				<div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">讲师名称</label>
                    <div class="col-sm-9">
                        <input type="text" name="teacher" class="form-control" value="<?php  echo $teacher['teacher'];?>" />
                    </div>
                </div>
				<div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">讲师分成</label>
                    <div class="col-sm-9">
						<div class="input-group">
							<input type="text" name="teacher_income" class="form-control" value="<?php  echo $teacher['teacher_income'];?>" />
							<span class="input-group-addon">%</span>
						</div>
						<span class="help-block">讲师课程佣金 = 课程售价 x 讲师分成%</span>
                    </div>
                </div>
				<div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">讲师头像</label>
                    <div class="col-sm-9">
                        <?php  echo tpl_form_field_image('teacherphoto', $teacher['teacherphoto']);?>
						<span class="help-block">请使用正方形尺寸头像，200 * 200px</span>
                    </div>
                </div>
				<div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">微信二维码</label>
                    <div class="col-sm-9">
                        <?php  echo tpl_form_field_image('weixin_qrcode', $teacher['weixin_qrcode']);?>
						<span class="help-block">请使用正方形尺寸头像，200 * 200px</span>
                    </div>
                </div>
				<div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">讲师QQ</label>
                    <div class="col-sm-9">
                        <input type="text" name="qq" class="form-control" value="<?php  echo $teacher['qq'];?>" />
						<span class="help-block">留空将不显示在前台</span>
                    </div>
                </div>
				<div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">讲师QQ群</label>
                    <div class="col-sm-9">
                        <input type="text" name="qqgroup" class="form-control" value="<?php  echo $teacher['qqgroup'];?>" />
						<span class="help-block">留空将不显示在前台</span>
                    </div>
                </div>
				<div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">加QQ群链接</label>
                    <div class="col-sm-9">
                        <input type="text" name="qqgroupLink" class="form-control" value="<?php  echo $teacher['qqgroupLink'];?>" />
						<span class="help-block">添加QQ群加群连接后，前台点击“咨询—QQ群”后将自动添加群。<a href="https://wx.haoshu888.com/zonghe/1134.html" target="_blank">如何获取QQ群加群链接?</a></span>
                    </div>
                </div>
				<div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">讲师状态</label>
                    <div class="col-sm-9">
						<p class="form-control-static">
							<input type="radio" id="status1" name="status" value="1" <?php  if($teacher['status']==1) { ?>checked<?php  } ?>>
							<label for="status1">
								<span class="label label-success" style="vertical-align:text-top;">审核通过</span>
							</label>
							&nbsp;&nbsp;
							<input type="radio" id="status2" name="status" value="2" <?php  if($teacher['status']==2) { ?>checked<?php  } ?>>
							<label for="status2">
								<span class="label label-danger" style="vertical-align:text-top;">待审核</span>
							</label>
							&nbsp;&nbsp;
							<input type="radio" id="status3" name="status" value="3" <?php  if($teacher['status']==3) { ?>checked<?php  } ?>>
							<label for="status3">
								<span class="label label-info" style="vertical-align:text-top;">隐藏中</span>
							</label>
							&nbsp;&nbsp;
							<input type="radio" id="status0" name="status" value="-1" <?php  if($teacher['status']==-1) { ?>checked<?php  } ?>>
							<label for="status0">
								<span class="label label-default" style="vertical-align:text-top;">审核未通过</span>
							</label>
						</p>
						<p class="form-control-static reason-div" <?php  if($teacher['status']!=-1) { ?>style="display:none;"<?php  } ?>>
							<input type="text" name="reason" class="form-control" placeholder="请输入未通过申请原因" />
						</p>
						<span class="help-block">隐藏中的讲师仅仅不显示在前台讲师列表里，讲师的其他功能权限不受影</span>
					</div>
                </div>
				<div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">上传课程权限</label>
                    <div class="col-sm-9">
						<p class="form-control-static">
							<input type="radio" id="upload1" name="upload" value="1" <?php  if($teacher['upload']==1) { ?>checked<?php  } ?>>
							<label for="upload1">
								<span class="label label-success" style="vertical-align:text-top;">允许上传</span>
							</label>
							&nbsp;&nbsp;
							<input type="radio" id="upload0" name="upload" value="0" <?php  if($teacher['upload']==0) { ?>checked<?php  } ?>>
							<label for="upload0">
								<span class="label label-danger" style="vertical-align:text-top;">禁止上传</span>
							</label>
							<span class="help-block">禁止上传状态下的讲师将无法在讲师平台上传课程</span>
						</p>
					</div>
                </div>
				<?php  if($setting['company_income']) { ?>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">上级机构uid</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" name="company_uid" id="company_uid" class="form-control" value="<?php  echo $teacher['company_uid'];?>" style="display:inline-block;width:88%;" readonly />&nbsp;&nbsp;<a id="removeread" href="javascript:;">开启修改</a>
						<span class="help-block">上级机构uid为指定会员uid，该讲师课程出售时，上级机构获得指定课程售价的分成收入。</span>
					</div>
				</div>
				<?php  } ?>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">讲师介绍</label>
                    <div class="col-sm-9">
						<?php  echo tpl_ueditor('teacherdes', $teacher['teacherdes']);?>
                    </div>
                </div>
				<div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">首拼音字母</label>
                    <div class="col-xs-12 col-sm-2">
					   <select class="form-control" name="first_letter">
					     <option value="">请选择...</option>
						 <?php  if(is_array($letter)) { foreach($letter as $let) { ?>
						  <option value="<?php  echo $let;?>" <?php  if($teacher['first_letter']==$let) { ?>selected<?php  } ?>><?php  echo $let;?></option>
						 <?php  } } ?>
					   </select>
                   </div>
                </div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
					<div class="col-xs-12 col-sm-9">
					   <span>讲师名称首拼音字母，例如：李明，请选择L</span>
				   </div>
				</div>
				<div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">排序</label>
                    <div class="col-sm-9">
                        <input type="text" name="displayorder" class="form-control" value="<?php  echo $teacher['displayorder'];?>" />
						<span class="help-block">序号越大，排序越靠前</a></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group col-sm-12">
            <input type="submit" name="submit" value="保存" class="btn btn-primary col-lg-1" />
            <input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
			<input type="hidden" name="id" value="<?php  echo $id;?>" />
        </div>
	</form>
</div>
<script type="text/javascript">
$("#removeread").click(function(){
	document.getElementById("company_uid").readOnly = false;
});

$(function() {
	$(':radio[name="status"]').click(function() {
		if($(this).val() == '-1') {
			$('.reason-div').show();
		} else {
			$('.reason-div').hide();
		}
	});
});

</script>

<?php  } else if($operation == 'income') { ?>
<style type="text/css">
.form-controls{display: inline-block; width:70px;}
.cblock{display:block !important;}
.cnone{display:none !important;}
</style>
<div class="main">
	<div class="panel panel-info">
        <div class="panel-heading">筛选</div>
        <div class="panel-body">
            <form action="./index.php" method="get" class="form-horizontal" role="form">
                <input type="hidden" name="c" value="site">
                <input type="hidden" name="a" value="entry">
                <input type="hidden" name="m" value="fy_lessonv2">
                <input type="hidden" name="do" value="teacher">
                <input type="hidden" name="op" value="income">
                <div class="form-group">
				    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width:100px;">讲师名称</label>
                    <div class="col-sm-2 col-lg-3">
                        <input class="form-control" name="teacher" value="<?php  echo $_GPC['teacher'];?>">
                    </div>
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width: 100px;">课程名称</label>
					<div class="col-sm-2 col-lg-3">
                        <input class="form-control" name="lesson" value="<?php  echo $_GPC['lesson'];?>">
                    </div>
                </div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width: 100px;">下单时间</label>
                    <div class="col-sm-8 col-lg-3 col-xs-12">
                        <?php echo tpl_form_field_daterange('time', array('start'=>($starttime ? date('Y-m-d', $starttime) : false),'end'=> ($endtime ? date('Y-m-d', $endtime) : false)));?>
                    </div>
				    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width: 100px;">订单编号</label>
					<div class="col-sm-2 col-lg-3">
                        <input class="form-control" name="ordersn" value="<?php  echo $_GPC['ordersn'];?>">
                    </div>
                    <button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>
					<button type="submit" name="export" value="1" class="btn btn-success">导出 Excel</button>
                </div>
            </form>
        </div>
    </div>
	<div class="panel panel-default">
		<div class="panel-heading">总数：<?php  echo $total;?></div>
        <div class="table-responsive panel-body">
            <table class="table table-hover">
                <thead class="navbar-inner">
                <tr>
                    <th style="width:8%;">ID</th>
                    <th style="width:10%;">讲师名称</th>
					<th style="width:14%;">订单编号</th>
                    <th style="width:22%;">课程名称</th>
					<th style="width:10%;">课程售价</th>
                    <th style="width:10%;">讲师分成</th>
					<th style="width:10%;">讲师收入</th>
					<th style="width:13%;">添加时间</th>
                </tr>
                </thead>
                <tbody>
					<?php  if(is_array($list)) { foreach($list as $item) { ?>
                    <tr>
						<td><?php  echo $item['id'];?></td>
						<td><?php  echo $item['teacher'];?></td>
						<td><?php  echo $item['ordersn'];?></td>
						<td>《<?php  echo $item['bookname'];?>》</td>
						<td><?php  echo $item['orderprice'];?> 元</td>
						<td><?php  echo $item['teacher_income'];?>%</td>
						<td><?php  echo $item['income_amount'];?> 元</td>
						<td><?php  echo date('Y-m-d H:i',$item['addtime']);?></td>
					</tr>
					<?php  } } ?>
				</tbody>
            </table>
		</div>
	</div>
	<?php  echo $pager;?>
</div>

<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>