<?php
/**
 * [ADMIN] グローバルメニュー一覧
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
$this->BcBaser->js(array(
	'admin/jquery.baser_ajax_data_list',
	'admin/baser_ajax_data_list_config',
));
?>


<script type="text/javascript">
$(function(){
	$.baserAjaxDataList.init();
});
</script>

<div id="AlertMessage" class="message" style="display:none"></div>
<div id="DataList"><?php $this->BcBaser->element('menus/index_list') ?></div>