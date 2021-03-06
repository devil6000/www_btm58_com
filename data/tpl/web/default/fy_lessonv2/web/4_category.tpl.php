<?php defined('IN_IA') or exit('Access Denied');?><!--
 * 课程分类管理
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
    <li <?php  if($op=='display') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('category');?>">分类列表</a></li>
    <li <?php  if($op=='post') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('category', array('op'=>'post', 'id'=>$_GPC['id']));?>"><?php  if($_GPC['id']>0) { ?>编辑<?php  } else { ?>添加<?php  } ?>分类</a></li>
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
						<?php  if(is_array($category)) { foreach($category as $row) { ?>
						<ol class="dd-list" style="margin-bottom:15px;">
							<li class="dd-item">
								<button data-action="collapse" id="collapse<?php  echo $row['id'];?>" type="button" style="display:none;" onclick="collapse(<?php  echo $row['id'];?>);">Collapse</button>
								<?php  if(!empty($row['son'])) { ?>
								<button data-action="expand" id="expand<?php  echo $row['id'];?>"   type="button" style="display: block;" onclick="expand(<?php  echo $row['id'];?>);">Expand</button>
								<?php  } else { ?>
								<button data-action="collapse" type="button" style="display: block;">collapse</button>
								<?php  } ?>
								
								<div class="dd-handle" style="width:100%;background:#eff5e9;">
									<input type="text" class="form-control" name="category[<?php  echo $row['id'];?>]" value="<?php  echo $row['displayorder'];?>" style="width: 70px;display:inline-block;">&nbsp;&nbsp;
									<img src="<?php  if(!empty($row['ico'])) { ?><?php  echo $_W['attachurl'];?><?php  echo $row['ico'];?><?php  } else { ?><?php echo MODULE_URL;?>template/mobile/images/nopic.png<?php  } ?>" width="30" height="30"> &nbsp;&nbsp;[ID: <?php  echo $row['id'];?>] <?php  echo $row['name'];?>
									<span class="pull-right">
										<?php  if($row['is_show']==1) { ?>
										<a href="<?php  echo $this->createWebUrl('category',array('op'=>'changeShow','id'=>$row['id']));?>" class="btn btn-success btn-sm" style="padding:2px 10px;" title="点击隐藏分类">显示</a>
										<?php  } else { ?>
										<a href="<?php  echo $this->createWebUrl('category',array('op'=>'changeShow','id'=>$row['id']));?>" class="btn btn-default btn-sm" style="padding:2px 10px;" title="点击显示分类">隐藏</a>
										<?php  } ?>
										<a class="btn btn-default btn-sm" href="<?php  echo $this->createWebUrl('category', array('op' => 'post', 'id' => $row['id']))?>" title="修改"><i class="fa fa-edit"></i></a>
										<a class="btn btn-default btn-sm" href="<?php  echo $this->createWebUrl('category', array('op' => 'delete', 'id' => $row['id']))?>" title="删除" onclick="return confirm('该操作不可恢复，确定删除？');return false;"><i class="fa fa-remove"></i></a>
									</span>
								</div>
								<?php  if(is_array($row['son'])) { foreach($row['son'] as $son) { ?>
								<ol class="dd-list cid<?php  echo $row['id'];?>" style="width:100%;display:none;">
									<li class="dd-item">
										<div class="dd-handle">
											<input type="text" class="form-control" name="son[<?php  echo $son['id'];?>]" value="<?php  echo $son['displayorder'];?>" style="width: 70px;display:inline-block;">&nbsp;&nbsp;
											<img src="<?php  if(!empty($son['ico'])) { ?><?php  echo $_W['attachurl'];?><?php  echo $son['ico'];?><?php  } else { ?><?php echo MODULE_URL;?>template/mobile/images/nopic.png<?php  } ?>" width="30" height="30"> &nbsp;&nbsp;[ID: <?php  echo $son['id'];?>] <?php  echo $son['name'];?>
											<span class="pull-right">
												<?php  if($son['is_show']==1) { ?>
												<a href="<?php  echo $this->createWebUrl('category',array('op'=>'changeShow','id'=>$son['id']));?>" class="btn btn-success btn-sm" style="padding:2px 10px;" title="点击隐藏分类">显示</a>
												<?php  } else { ?>
												<a href="<?php  echo $this->createWebUrl('category',array('op'=>'changeShow','id'=>$son['id']));?>" class="btn btn-default btn-sm" style="padding:2px 10px;" title="点击显示分类">隐藏</a>
												<?php  } ?>
												<a class="btn btn-default btn-sm" href="<?php  echo $this->createWebUrl('category', array('op' => 'post', 'id' => $son['id']))?>" title="修改"><i class="fa fa-edit"></i></a>
												<a class="btn btn-default btn-sm" href="<?php  echo $this->createWebUrl('category', array('op' => 'delete', 'id' => $son['id']))?>" title="删除" onclick="return confirm('该操作不可恢复，确定删除？');return false;"><i class="fa fa-remove"></i></a>
											</span>
										</div>
									</li>
								</ol>
								<?php  } } ?>
							</li>
						</ol>
						<?php  } } ?>
						<table class="table">
							 <tbody>
								 <tr>
									 <td>
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
<script type="text/javascript">
function collapse(obj){
	$("#collapse"+obj).hide();
	$("#expand"+obj).show();
	$(".cid"+obj).hide();
}
function expand(obj){
	$("#expand"+obj).hide();
	$("#collapse"+obj).show();
	$(".cid"+obj).show();
}
</script>
<?php  } else if($operation == 'post') { ?>
<div class="main">
	<form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
        <div class="panel panel-default">
            <div class="panel-heading">
                分类信息
            </div>
            <div class="panel-body">
				<div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">上级分类</label>
                    <div class="col-sm-9">
                        <select name="parentid" class="form-control">
							<option value="0" <?php  if(empty($category['parentid'])) { ?>selected<?php  } ?>>顶级分类</option>
							<?php  if(is_array($list)) { foreach($list as $item) { ?>
							<option value="<?php  echo $item['id'];?>" <?php  if($category['parentid']==$item['id']) { ?>selected<?php  } ?>><?php  echo $item['name'];?></option>
							<?php  } } ?>
						</select>
                    </div>
                </div>
				<div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">分类名称</label>
                    <div class="col-sm-9">
                        <input type="text" name="catename" class="form-control" value="<?php  echo $category['name'];?>" />
                    </div>
                </div>
				<div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">分类图标</label>
                    <div class="col-sm-9">
                        <?php  echo tpl_form_field_image('ico', $category['ico']);?>
						<span>建议尺寸200px * 200px</span>
                    </div>
                </div>
				<div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">自定义链接</label>
                    <div class="col-sm-9">
                        <input type="text" name="link" class="form-control" value="<?php  echo $category['link'];?>" />
						<span>留空使用系统默认链接，如设置自定义链接，请输入http://或https://开头</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">排序</label>
                    <div class="col-sm-9">
                        <input type="text" name="displayorder" class="form-control" value="<?php  echo $category['displayorder'];?>" />
                    </div>
                </div>
				<div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">首页显示</label>
                    <div class="col-sm-9">
                        <label class="radio-inline"><input type="radio" name="is_show" value="1" <?php  if($category['is_show']==1) { ?>checked<?php  } ?> /> 显示</label>
                        &nbsp;
                        <label class="radio-inline"><input type="radio" name="is_show" value="0" <?php  if($category['is_show']==0) { ?>checked<?php  } ?> /> 隐藏</label>
                        <span class="help-block"><strong></strong>该选项仅针对一级分类有效</span>
                    </div>
                </div>
				<div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否推荐</label>
                    <div class="col-sm-9">
                        <label class="radio-inline"><input type="radio" name="is_hot" value="1" <?php  if($category['is_hot']==1) { ?>checked<?php  } ?> /> 推荐</label>
                        &nbsp;
                        <label class="radio-inline"><input type="radio" name="is_hot" value="0" <?php  if($category['is_hot']==0) { ?>checked<?php  } ?> /> 不推荐</label>
                        <span class="help-block"><strong></strong>推荐的分类会显示在手机端全部分类页面的推荐分类里面。</span>
                    </div>
                </div>
				<div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">分类链接</label>
                    <div class="col-sm-9">
                        <div style="padding-top:8px;font-size: 14px;"><a href="javascript:;" id="copy-btn"><?php  echo $_W['siteroot'];?>app/<?php  echo str_replace("./", "", $this->createMobileUrl('search', array('cat_id'=>$category['id'])));?></a></div>
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
require(['jquery', 'util'], function($, util){
	$(function(){
		util.clip($("#copy-btn")[0], $("#copy-btn").text());
	});
});
</script>
<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>