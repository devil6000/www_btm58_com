<?php defined('IN_IA') or exit('Access Denied');?><div class="main">
    <div class="panel panel-info">
        <div class="panel-heading">筛选</div>
        <div class="panel-body">
            <form action="./index.php" method="get" class="form-horizontal" role="form">
                <input type="hidden" name="c" value="site" />
                <input type="hidden" name="a" value="entry" />
                <input type="hidden" name="m" value="fy_lessonv2" />
                <input type="hidden" name="do" value="setting" />
                <input type="hidden" name="op" value="picture" />
                <div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width:100px;">名称</label>
					<div class="col-sm-8 col-lg-3 col-xs-12">
                        <input class="form-control" name="banner_name" type="text" value="<?php  echo $_GPC['banner_name'];?>">
                    </div>
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width:100px;">位置类型</label>
                    <div class="col-sm-8 col-lg-3 col-xs-12">
                        <select name="banner_type" class="form-control">
                            <option value="">不限</option>
							<?php  if(is_array($bannerType)) { foreach($bannerType as $key => $item) { ?>
							<option value="<?php  echo $key;?>" <?php  if($_GPC['banner_type'] == "$key") { ?> selected="selected" <?php  } ?>><?php  echo $item;?></option>
							<?php  } } ?>
                        </select>
                    </div>
                </div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width:100px;">平台类型</label>
					<div class="col-sm-8 col-lg-3 col-xs-12">
                        <select name="is_pc" class="form-control">
                            <option value="">不限</option>
							<option value="0" <?php  if($_GPC['is_pc'] == '0') { ?> selected="selected" <?php  } ?>>手机端</option>
							<option value="1" <?php  if($_GPC['is_pc'] == '1') { ?> selected="selected" <?php  } ?>>PC端</option>
                        </select>
                    </div>
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width:100px;">状态</label>
                    <div class="col-sm-8 col-lg-3 col-xs-12">
                        <select name="is_show" class="form-control">
                            <option value="">不限</option>
							<option value="0" <?php  if($_GPC['is_show'] == '0') { ?> selected="selected" <?php  } ?>>隐藏</option>
							<option value="1" <?php  if($_GPC['is_show'] == '1') { ?> selected="selected" <?php  } ?>>显示</option>
                        </select>
                    </div>
                    <div class="col-sm-3 col-lg-3">
                        <button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>&nbsp;&nbsp;
						<a class="btn btn-success" href="<?php  echo $this->createWebUrl('setting', array('op'=>addPic));?>"></i> 添加新广告</a>
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
					<th style="width:15%;">名称</th>
                    <th style="width:13%;">缩略图</th>
					<th style="width:10%;">平台类型</th>
                    <th style="width:12%;">位置类型</th>
					<th style="width:10%;">状态</th>
                    <th style="width:8%;">排序</th>
                    <th style="width:15%;">添加时间</th>
                    <th style="text-align:right;">操作</th>
                </tr>
                </thead>
                <tbody style="font-size: 13px;">
                <?php  if(is_array($list)) { foreach($list as $item) { ?>
                <tr>
                    <td><?php  echo $item['banner_id'];?></td>
					<td><?php  echo $item['banner_name'];?></td>
                    <td>
						<a href="<?php  echo $_W['attachurl'];?><?php  echo $item['picture'];?>" target="_blank">
							<img src="<?php  echo $_W['attachurl'];?><?php  echo $item['picture'];?>" width="80" height="40" />
						</a>
					</td>
                    <td>
						<?php  if($item['is_pc'] == '0') { ?>手机端
						<?php  } else if($item['is_pc'] == '1') { ?>PC端
						<?php  } ?>
					</td>
					<td><?php  echo $bannerType[$item['banner_type']];?></td>
                    <td>
						<?php  if($item['is_show'] == 0) { ?><span class="label label-default">隐藏</span><?php  } ?>
						<?php  if($item['is_show'] == 1) { ?><span class="label label-success">显示</span><?php  } ?>
					</td>
					<td><?php  echo $item['displayorder'];?></td>
					<td><?php  echo date('Y-m-d H:i', $item['addtime'])?></td>
                    <td style="text-align:right;">
						<a class="btn btn-default btn-sm" href="<?php  echo $this->createWebUrl('setting', array('op' => 'addPic', 'banner_id' => $item['banner_id']))?>"><i class="fa fa-pencil"></i></a>
                        <a class="btn btn-default btn-sm" href="<?php  echo $this->createWebUrl('setting', array('op' => 'delPic', 'banner_id' => $item['banner_id'], 'page'=>$_GPC['page']))?>" title="删除订单" onclick="return confirm('此操作不可恢复，确认删除？');return false;"><i class="fa fa-times"></i></a>
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