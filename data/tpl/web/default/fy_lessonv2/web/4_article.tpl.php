<?php defined('IN_IA') or exit('Access Denied');?><!-- 
 * 文章公告管理
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
	<li <?php  if($op=='display') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('article');?>">文章列表</a></li>
	<li <?php  if($op=='post') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('article', array('op'=>'post','id'=>$_GPC['id']));?>"><?php  if($_GPC['aid']>0) { ?>编辑<?php  } else { ?>添加<?php  } ?>文章</a></li>
</ul>
<?php  if($operation == 'display') { ?>
<div class="main">
    <div class="panel panel-info">
        <div class="panel-heading">筛选</div>
        <div class="panel-body">
            <form action="./index.php" method="get" class="form-horizontal" role="form">
                <input type="hidden" name="c" value="site" />
                <input type="hidden" name="a" value="entry" />
                <input type="hidden" name="m" value="fy_lessonv2" />
                <input type="hidden" name="do" value="article" />
                <input type="hidden" name="op" value="display" />
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width:100px;">文章标题</label>
                    <div class="col-sm-2 col-lg-3">
                        <input class="form-control" name="title" type="text" value="<?php  echo $_GPC['title'];?>">
                    </div>
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width:100px;">文章作者</label>
                    <div class="col-sm-2 col-lg-3">
                        <input class="form-control" name="author" type="text" value="<?php  echo $_GPC['author'];?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width: 100px;">发布时间</label>
                    <div class="col-sm-8 col-lg-3 col-xs-12">
                        <?php  echo tpl_form_field_daterange('time', array('starttime'=>date('Y-m-d', $starttime),'endtime'=>date('Y-m-d', $endtime)));?>
                    </div>
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width:100px;">首页显示</label>
                    <div class="col-sm-8 col-lg-3 col-xs-12">
                        <select name="isshow" class="form-control">
                            <option value="">全部显示</option>
							<option value="0" <?php  if(in_array($_GPC['isshow'],array('0'))) { ?> selected="selected" <?php  } ?>>不显示</option>
							<option value="1" <?php  if($_GPC['isshow'] == 1) { ?> selected="selected" <?php  } ?>>显示</option>
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
        <form action="" method="post" class="form-horizontal form" >
        <div class="table-responsive panel-body">
            <table class="table table-hover">
                <thead class="navbar-inner">
					<tr>
						<th style="width:10%;">ID</th>
						<th style="width:25%;">标题</th>
						<th style="width:10%;">作者</th>
						<th style="width:10%;">状态</th>
						<th style="width:10%;">排序</th>
						<th style="width:10%;">阅读量</th>
						<th style="width:15%;">发布时间</th>
						<th style="width:10%; text-align:right;">操作</th>
					</tr>
                </thead>
                <tbody>
					<?php  if(is_array($list)) { foreach($list as $item) { ?>
					<tr>
						<td><?php  echo $item['id'];?></td>
						<td><?php  echo $item['title'];?></td>
						<td><?php  echo $item['author'];?></td>
						<td>
							<?php  if($item['isshow']==0) { ?>
							<span class="label label-danger">不显示</span>
							<?php  } else { ?>
							<span class="label label-success">显示</span>
							<?php  } ?>
						</td>
						<td><?php  echo $item['displayorder'];?></td>
						<td><?php  echo $item['view'];?></td>
						<td><?php  echo date('Y-m-d H:i', $item['addtime']);?></td>
						<td style="text-align:right;">
							<a class="btn btn-default btn-sm" href="<?php  echo $this->createWebUrl('article', array('op' => 'post', 'aid' => $item['id']))?>" title="编辑"><i class="fa fa-pencil"></i></a>
							<a class="btn btn-default btn-sm" href="<?php  echo $this->createWebUrl('article', array('op' => 'delete', 'aid' => $item['id']))?>" title="删除订单" onclick="return confirm('此操作不可恢复，确认删除？');return false;"><i class="fa fa-times"></i></a>
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
<?php  } else if($operation == 'post') { ?>
<div class="main">
	<form class="form-horizontal form" action="" method="post" enctype="multipart/form-data">
		<div class="panel panel-default">
			<div class="panel-heading">文章管理</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">标题</label>
					<div class="col-sm-8 col-xs-12">
						<input type="text" class="form-control" placeholder="" name="title" value="<?php  echo $article['title'];?>">
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">作者</label>
					<div class="col-sm-8 col-xs-12">
						<input type="text" class="form-control" placeholder="" name="author" value="<?php  echo $article['author'];?>">
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">内容</label>
					<div class="col-sm-8 col-xs-12">
						<?php  echo tpl_ueditor('content', $article['content']);?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">缩略图</label>
					<div class="col-sm-8 col-xs-12">
						<?php  echo tpl_form_field_image('images', $article['images']);?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">原文链接</label>
					<div class="col-sm-8 col-xs-12">
						<input type="text" class="form-control" placeholder="" name="linkurl" value="<?php  echo $article['linkurl'];?>">
						<span class="help-block">(没有请留空)如填写，请填写完整的原文链接，包括“http://”</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">排序</label>
					<div class="col-sm-8 col-xs-12">
						<input type="text" class="form-control" placeholder="" name="displayorder" value="<?php  echo $article['displayorder'];?>">
						<span class="help-block">文章的显示顺序，越大则越靠前</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">前台滚动展示</label>
					<div class="col-sm-9">
						<label class="radio-inline">
							<input type="radio" name="isshow" value="1" <?php  if($article['isshow']==1) { ?>checked<?php  } ?>>显示
						</label>
						<label class="radio-inline">
							<input type="radio" name="isshow" value="0" <?php  if($article['isshow']==0) { ?>checked<?php  } ?>>不显示
						</label>
						<span class="help-block"></span>
					</div>
				</div>
				<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">课程链接</label>
						<div class="col-sm-9">
							<?php  if($article['id']) { ?>
							<div style="padding-top:8px;font-size: 14px;"><a href="javascript:;" id="copy-btn"><?php  echo $_W['siteroot'];?>app/<?php  echo str_replace("./", "", $this->createMobileUrl('article', array('aid'=>$article['id'])));?></a></div>
							<?php  } ?>
						</div>
					</div>
				<div class="form-group">
					<div class="col-sm-12">
						<input name="submit" type="submit" value="提交" class="btn btn-primary col-lg-1">
						<input type="hidden" name="token" value="<?php  echo $_W['token'];?>">
						<input type="hidden" name="aid" value="<?php  echo $aid;?>">
					</div>
				</div>
			</div>
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