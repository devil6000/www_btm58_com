<?php defined('IN_IA') or exit('Access Denied');?> <!-- 
 * 参数设置
 * ============================================================================
 * 版权所有 2015-2018 微课堂团队，并保留所有权利。
 * 网站地址: https://wx.haoshu888.com
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！不允许对程序代码以任何形式任何目的的再发布，作者将保留
 * 追究法律责任的权力和最终解释权。
 * ============================================================================
-->

<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('web/setting/header', TEMPLATE_INCLUDEPATH)) : (include template('web/setting/header', TEMPLATE_INCLUDEPATH));?>

<?php  if($op=='display') { ?>

	<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('web/setting/display', TEMPLATE_INCLUDEPATH)) : (include template('web/setting/display', TEMPLATE_INCLUDEPATH));?>

<?php  } else if($op=='frontshow') { ?>

	<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('web/setting/frontshow', TEMPLATE_INCLUDEPATH)) : (include template('web/setting/frontshow', TEMPLATE_INCLUDEPATH));?>

<?php  } else if($op=='templatemsg') { ?>

	<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('web/setting/templatemsg', TEMPLATE_INCLUDEPATH)) : (include template('web/setting/templatemsg', TEMPLATE_INCLUDEPATH));?>

<?php  } else if($op=='templateformat') { ?>

	<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('web/setting/templateformat', TEMPLATE_INCLUDEPATH)) : (include template('web/setting/templateformat', TEMPLATE_INCLUDEPATH));?>

<?php  } else if($op=='vipservice') { ?>

	<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('web/setting/vipservice', TEMPLATE_INCLUDEPATH)) : (include template('web/setting/vipservice', TEMPLATE_INCLUDEPATH));?>

<?php  } else if($op=='vipLevel') { ?>

	<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('web/setting/vipLevel', TEMPLATE_INCLUDEPATH)) : (include template('web/setting/vipLevel', TEMPLATE_INCLUDEPATH));?>

<?php  } else if($op=='picture') { ?>

	<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('web/setting/picture', TEMPLATE_INCLUDEPATH)) : (include template('web/setting/picture', TEMPLATE_INCLUDEPATH));?>

<?php  } else if($op=='addPic') { ?>

	<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('web/setting/addPic', TEMPLATE_INCLUDEPATH)) : (include template('web/setting/addPic', TEMPLATE_INCLUDEPATH));?>

<?php  } else if($op=='savetype') { ?>

	<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('web/setting/savetype', TEMPLATE_INCLUDEPATH)) : (include template('web/setting/savetype', TEMPLATE_INCLUDEPATH));?>

<?php  } else if($op=='sms') { ?>

	<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('web/setting/sms', TEMPLATE_INCLUDEPATH)) : (include template('web/setting/sms', TEMPLATE_INCLUDEPATH));?>

<?php  } else if($op== 'service') { ?>

	<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('web/setting/service', TEMPLATE_INCLUDEPATH)) : (include template('web/setting/service', TEMPLATE_INCLUDEPATH));?>

<?php  } ?>

<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>