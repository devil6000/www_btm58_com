<?php defined('IN_IA') or exit('Access Denied');?><!--
 * 课程管理
 * ============================================================================
 * 版权所有 2015-2018 微课堂团队，并保留所有权利。
 * 网站地址: https://wx.haoshu888.com
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！不允许对程序代码以任何形式任何目的的再发布，作者将保留
 * 追究法律责任的权力和最终解释权。
 * ============================================================================
-->
<?php  if($op!='previewVideo') { ?>
	<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
	<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('web/lesson/lesson-header', TEMPLATE_INCLUDEPATH)) : (include template('web/lesson/lesson-header', TEMPLATE_INCLUDEPATH));?>
<?php  } ?>
<link href="<?php echo MODULE_URL;?>template/web/style/fycommon.css" rel="stylesheet">


<?php  if($operation == 'display') { ?>

	<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('web/lesson/display', TEMPLATE_INCLUDEPATH)) : (include template('web/lesson/display', TEMPLATE_INCLUDEPATH));?>

<?php  } else if($operation == 'postlesson') { ?>

	<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('web/lesson/postlesson', TEMPLATE_INCLUDEPATH)) : (include template('web/lesson/postlesson', TEMPLATE_INCLUDEPATH));?>

<?php  } else if($operation == 'postsection') { ?>

	<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('web/lesson/postsection', TEMPLATE_INCLUDEPATH)) : (include template('web/lesson/postsection', TEMPLATE_INCLUDEPATH));?>

<?php  } else if($operation == 'viewsection') { ?>

	<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('web/lesson/viewsection', TEMPLATE_INCLUDEPATH)) : (include template('web/lesson/viewsection', TEMPLATE_INCLUDEPATH));?>

<?php  } else if($op=='record') { ?>

	<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('web/lesson/record', TEMPLATE_INCLUDEPATH)) : (include template('web/lesson/record', TEMPLATE_INCLUDEPATH));?>

<?php  } else if($op=='updomain') { ?>

	<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('web/lesson/updomain', TEMPLATE_INCLUDEPATH)) : (include template('web/lesson/updomain', TEMPLATE_INCLUDEPATH));?>

<?php  } else if($op=='inform') { ?>

	<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('web/lesson/inform', TEMPLATE_INCLUDEPATH)) : (include template('web/lesson/inform', TEMPLATE_INCLUDEPATH));?>

<?php  } else if($op=='informStudent') { ?>

	<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('web/lesson/informStudent', TEMPLATE_INCLUDEPATH)) : (include template('web/lesson/informStudent', TEMPLATE_INCLUDEPATH));?>

<?php  } else if($op=='previewVideo') { ?>
	<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('web/header/qcloudvod-header', TEMPLATE_INCLUDEPATH)) : (include template('web/header/qcloudvod-header', TEMPLATE_INCLUDEPATH));?>
	<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('web/lesson/lesson-header', TEMPLATE_INCLUDEPATH)) : (include template('web/lesson/lesson-header', TEMPLATE_INCLUDEPATH));?>
	<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('web/lesson/previewVideo', TEMPLATE_INCLUDEPATH)) : (include template('web/lesson/previewVideo', TEMPLATE_INCLUDEPATH));?>

<?php  } ?>

<?php  if($op!='previewVideo') { ?>
	<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>
<?php  } ?>