<?php defined('IN_IA') or exit('Access Denied');?><!--
 * 推荐板块管理
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
    <li <?php  if($op=='display') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('recommend');?>">板块列表</a></li>
    <li <?php  if($op=='post') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('recommend', array('op'=>'post', 'id'=>$_GPC['id']));?>"><?php  if($_GPC['id']>0) { ?>编辑<?php  } else { ?>添加<?php  } ?>板块</a></li>
	<?php  if($op=='details') { ?>
	<li class="active"><a href="<?php  echo $this->createWebUrl('recommend', array('op'=>'details', 'id'=>$_GPC['id']));?>">【<?php  echo $recommend['rec_name'];?>】板块详情</a></li>
	<?php  } ?>
	<?php  if($op=='addtorec') { ?>
	<li class="active"><a href="<?php  echo $this->createWebUrl('recommend', array('op'=>'addtorec'));?>">新增推荐课程</a></li>
	<?php  } ?>
</ul>
<?php  if($operation == 'display') { ?>
<style type="text/css">
.form-controls{display: inline-block; width:70px;}
.cblock{display:block !important;}
.cnone{display:none !important;}
</style>
<link rel="stylesheet" type="text/css" href="<?php echo MODULE_URL;?>template/web/style/category.css">
<div class="main">
    <div class="category">
        <form action="" method="post">
            <div class="panel panel-default">
                <div class="panel-body table-responsive">
					<div class="dd" id="div_nestable">
						<ol class="dd-list">
						<?php  if(is_array($recommend)) { foreach($recommend as $key => $row) { ?>
							 <li class="dd-item" onclick="<?php  echo $row['id'];?>">
								<div class="dd-handle" style="width:100%;">
								    <input type="text" class="form-control form-controls" name="displayorder[<?php  echo $row['id'];?>]" value="<?php  echo $row['displayorder'];?>" style="padding-left:5px;">
									&nbsp;[ID：<?php  echo $row['id'];?>]<?php  echo $row['rec_name'];?>
									<span class="pull-right">
										<?php  if($row['show_style']==1) { ?>
										<a class="btn btn-success btn-sm" style="padding:5px 10px;"><?php  echo $styleList[$row['show_style']];?></a>
										<?php  } else if($row['show_style']==2) { ?>
										<a class="btn btn-primary btn-sm" style="padding:5px 10px;"><?php  echo $styleList[$row['show_style']];?></a>
										<?php  } else if($row['show_style']==3) { ?>
										<a class="btn btn-warning btn-sm" style="padding:5px 10px;"><?php  echo $styleList[$row['show_style']];?></a>
										<?php  } else if($row['show_style']==4) { ?>
										<a class="btn btn-info btn-sm" style="padding:5px 10px;"><?php  echo $styleList[$row['show_style']];?></a>
										<?php  } ?>
										<a class="btn btn-default btn-sm" href="<?php  echo $this->createWebUrl('recommend', array('op' => 'details', 'recid' => $row['id']))?>" title="详情"><i class="fa fa-search"></i></a>
										<a class="btn btn-default btn-sm" href="<?php  echo $this->createWebUrl('recommend', array('op' => 'post', 'id' => $row['id']))?>" title="修改"><i class="fa fa-edit"></i></a>
										<a class="btn btn-default btn-sm" href="<?php  echo $this->createWebUrl('recommend', array('op' => 'delete', 'id' => $row['id']))?>" title="删除" onclick="return confirm('该操作不可恢复，确定删除？');return false;"><i class="fa fa-remove"></i></a>
									</span>
								</div>
							 </li>
						<?php  } } ?>
						</ol>
						<table class="table">
							 <tbody>
								 <tr>
									 <td>
										 <a class="btn btn-success" href="<?php  echo $this->createWebUrl('recommend', array('op'=>'addtorec'));?>"><i class="fa fa-plus"></i> 添加推荐课程</a>&nbsp;&nbsp;
										 <input name="submit" type="submit" class="btn btn-primary" value="批量排序">
										 <input type="hidden" name="token" value="<?php  echo $_W['token'];?>">
									 </td>
								 </tr>
							 </tbody>
						</table>
					</div>
					<?php  echo $pager;?>
				</div>
			</div>
		</form>
	</div>
</div>
<?php  } else if($operation == 'post') { ?>
<div class="main">
	<form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
        <div class="panel panel-default">
            <div class="panel-heading">
                板块信息
            </div>
            <div class="panel-body">
				<div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">板块名称</label>
                    <div class="col-sm-9">
                        <input type="text" name="rec_name" class="form-control" value="<?php  echo $recommend['rec_name'];?>" />
                    </div>
                </div>
				<div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">板块样式</label>
                    <div class="col-sm-9">
                        <label class="radio-inline"><input type="radio" name="show_style" value="1" <?php  if(empty($recommend['show_style']) || $recommend['show_style'] == 1) { ?>checked="true"<?php  } ?> /> <?php  echo $styleList['1'];?></label>
                        &nbsp;
                        <label class="radio-inline"><input type="radio" name="show_style" value="2" <?php  if($recommend['show_style'] == 2) { ?>checked="true"<?php  } ?> /> <?php  echo $styleList['2'];?></label>
                        &nbsp;
                        <label class="radio-inline"><input type="radio" name="show_style" value="3" <?php  if($recommend['show_style'] == 3) { ?>checked="true"<?php  } ?> /> <?php  echo $styleList['3'];?></label>
						&nbsp;
                        <label class="radio-inline"><input type="radio" name="show_style" value="4" <?php  if($recommend['show_style'] == 4) { ?>checked="true"<?php  } ?> /> <?php  echo $styleList['4'];?></label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否显示</label>
                    <div class="col-sm-9">
                        <label class="radio-inline"><input type="radio" name="is_show" value="1" <?php  if(empty($recommend) || $recommend['is_show'] == 1) { ?>checked="true"<?php  } ?> /> 是</label>
                        &nbsp;&nbsp;&nbsp;
                        <label class="radio-inline"><input type="radio" name="is_show" value="0" <?php  if(!empty($recommend) && $recommend['is_show'] == 0) { ?>checked="true"<?php  } ?> /> 否</label>
                        <span class="help-block"></span>
                    </div>
                </div>
				<div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">首页显示课程数量</label>
                    <div class="col-sm-9">
                        <input type="text" name="limit_number" class="form-control" value="<?php  echo $recommend['limit_number'];?>" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">排序</label>
                    <div class="col-sm-9">
                        <input type="text" name="displayorder" class="form-control" value="<?php  echo $recommend['displayorder'];?>" />
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

<?php  } else if($operation == 'details') { ?>
<div class="main">
	<div class="panel panel-default">
        <div class="panel-body">
            <a class="btn btn-success" href="<?php  echo $this->createWebUrl('recommend', array('op'=>'addtorec'));?>"><i class="fa fa-plus"></i> 添加推荐课程</a>
        </div>
    </div>
	<div class="panel panel-default">
        <div class="table-responsive panel-body">
			<form id="myform" name="myform" method="post" class="form-horizontal form">
				<table class="table table-hover">
					<thead class="navbar-inner">
					<tr>
						<th style="width:70px;text-align:center;"><input type="checkbox" id="btnSelect" class="btn btn-default" onclick="checkAll(myform.hidnSelectFlag.value);"></th>
						<th style="width:35%;">课程名称</th>
						<th style="width:15%;">课程价格</th>
						<th style="width:15%;">讲师名称</th>
						<th style="width:15%;">课程状态</th>
						<th style="width:15%;text-align:right;">操作</th>
					</tr>
					</thead>
					<tbody>
					<?php  if(is_array($list)) { foreach($list as $item) { ?>
					<tr>
						<td align="center"><input type="checkbox" name="id[]" value="<?php  echo $item['id'];?>"></td>
						<td>[ID:<?php  echo $item['id'];?>]<?php  echo $item['bookname'];?></td>
						<td><?php echo $item['price']?$item['price'].'元':'免费';?></td>
						<td><?php  echo $item['teacher'];?></td>
						<td><?php echo $item['status']==0?'已下架':'出售中';?></td>
						<td style="text-align:right;">
							<a class="btn btn-default btn-sm" href="<?php  echo $this->createWebUrl('lesson', array('op'=>'postlesson', 'id'=>$item['id'],'refurl'=>$_W['siteurl']));?>" title="修改"><i class="fa fa-edit"></i></a>
							<a class="btn btn-default btn-sm" href="<?php  echo $this->createWebUrl('recommend', array('op' => 'removerec', 'id' => $item['id']))?>" title="移出该板块" onclick="return confirm('此操作不可恢复，确认移除？');return false;"><i class="fa fa-times"></i></a>
						</td>
					</tr>
					<?php  } } ?>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="6" style="padding-top: 30px;">
								<a onclick="cancelRec();" class="btn btn-primary">批量取消推荐</a>
							</td>
						</tr>
					</tfoot>
				</table>
				<input type="hidden"  name="hidnSelectFlag" value="1"/>
				<input type="hidden"  name="cancleRec" value="1"/>
			</form>
            <?php  echo $pager;?>
        </div>
    </div>
</div>
<script language="javascript">
/**
 * 选择复选框
 * @param type 1 全选；0 全不选
 */
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

function cancelRec(){
	var check = $("input[type=checkbox][class!=check_all]:checked");
	if(check.length < 1){
		alert('您还没有没有任何课程');
		return false;
	}
	document.getElementById("myform").submit();
}
</script>
<?php  } else if($operation == 'addtorec') { ?>
<div class="main">
	<div class="panel panel-info">
        <div class="panel-heading">筛选</div>
        <div class="panel-body">
            <form action="./index.php" method="get" class="form-horizontal" role="form">
                <input type="hidden" name="c" value="site">
                <input type="hidden" name="a" value="entry">
                <input type="hidden" name="m" value="fy_lessonv2">
                <input type="hidden" name="do" value="recommend">
                <input type="hidden" name="op" value="addtorec">
                <div class="form-group">
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width:100px;">课程名称</label>
                    <div class="col-sm-2 col-lg-3">
                        <input class="form-control" name="bookname" type="text" value="<?php  echo $_GPC['bookname'];?>">
                    </div>
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width:100px;">课程分类</label>
                    <div class="col-sm-8 col-lg-3 col-xs-12">
                        <select name="pid" class="form-control">
                            <option value="">不限</option>
							<?php  if(is_array($category_list)) { foreach($category_list as $cat) { ?>
							   <option value="<?php  echo $cat['id'];?>" <?php  if($_GPC['pid']==$cat['id']) { ?>selected<?php  } ?>><?php  echo $cat['name'];?></option>
							<?php  } } ?>
                        </select>
                    </div>
                </div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width:100px;">课程板块</label>
                    <div class="col-sm-8 col-lg-3 col-xs-12">
                        <select name="recid" class="form-control">
                            <option value="">不限</option>
							<option value="norec" <?php  if($_GPC['recid']=='norec') { ?>selected<?php  } ?>>未推荐课程</option>
							<?php  if(is_array($rec_list)) { foreach($rec_list as $rec) { ?>
							   <option value="<?php  echo $rec['id'];?>" <?php  if($_GPC['recid']==$rec['id']) { ?>selected<?php  } ?>><?php  echo $rec['rec_name'];?></option>
							<?php  } } ?>
                        </select>
                    </div>
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width:100px;">课程性质</label>
                    <div class="col-sm-8 col-lg-3 col-xs-12">
                        <select name="is_free" class="form-control">
                            <option value="">不限</option>
							<option value="0" <?php  if(in_array($_GPC['is_free'], array('0'))) { ?>selected<?php  } ?>>免费课程</option>
							<option value="1" <?php  if(in_array($_GPC['is_free'], array('1'))) { ?>selected<?php  } ?>>付费课程</option>
                        </select>
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
						<th style="text-align:center;width:40px;"><input type="checkbox" id="btnSelect" class="btn btn-default" onclick="checkAll(myform.hidnSelectFlag.value);" /></th>
						<th style="width:35%;padding-left:30px;">课程名称</th>
						<th style="width:15%;">课程价格</th>
						<th style="width:15%;">课程状态</th>
						<th style="width:15%;">推荐板块</th>
						<th style="width:20%;">添加时间</th>
					</tr>
					</thead>
					<tbody>
					<?php  if(is_array($lesson_list)) { foreach($lesson_list as $item) { ?>
					<tr>
						<td style="text-align:center;"><input type="checkbox" name="id[]" value="<?php  echo $item['id'];?>"></td>
						<td style="padding-left:30px;"><?php  echo $item['bookname'];?></td>
						<td><?php echo $item['price']?$item['price'].'元':'免费';?></td>
						<td><?php echo $item['status']==0?'已下架':'出售中';?></td>
						<td><?php  echo $item['rec_name'];?></td>
						<td><?php  echo date('Y-m-d H:i', $item['addtime']);?></td>
					</tr>
					<?php  } } ?>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="6" style="padding-top: 30px;">
								<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width: 50px;">推荐到</label>
								<div class="col-sm-8 col-lg-3 col-xs-12">
									<select name="recid" class="form-control" onchange="addclassToRec(this.value);">
										<option value="">请选择...</option>
										<?php  if(is_array($rec_list)) { foreach($rec_list as $rec) { ?>
										   <option value="<?php  echo $rec['id'];?>"><?php  echo $rec['rec_name'];?></option>
										<?php  } } ?>
									</select>
								</div>
								<a onclick="addclassToRec('cancel');" class="btn btn-primary">取消推荐</a>
							</td>
						</tr>
					</tfoot>
				</table>
				<?php  echo $pager;?>
			</div>
			<input type="hidden"  name="hidnSelectFlag" value="1"/>
		</form>
    </div>
</div>
<script language="javascript">
/**
 * 选择复选框
 * @param type 1 全选；0 全不选
 */
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

		if(obj=='cancel'){
			document.getElementById("myform").action="<?php  echo $this->createWebUrl('recommend', array('op'=>'recpost','posttype'=>'cancel'));?>";
		}else{
			document.getElementById("myform").action="<?php  echo $this->createWebUrl('recommend', array('op'=>'recpost'));?>";
		}


		document.getElementById("myform").submit();
	}


	
}
</script>
<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>