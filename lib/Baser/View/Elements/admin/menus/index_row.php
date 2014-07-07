<?php
/**
 * [ADMIN] メニュー一覧　行
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
$btnUpStyle = $btnDownStyle = array();
if (!$this->BcMenu->enabled($data)) {
	$rowClassies[] = 'disablerow';
}
if ($count != 1 || !isset($datas)) {
	//$btnUpStyle = array('style' => 'display:none');
}
if (!isset($datas) || count($datas) != $count) {
	//$btnDownStyle = array('style' => 'display:none');
}
?>


<tr id="Row<?php echo $data['Menu']['id'] ?>" class="<?php echo implode(' ', $rowClassies) ?>">
	<td class="row-tools">
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_edit.png', array('width' => 24, 'height' => 24, 'alt' => '編集', 'class' => 'btn')), array('action' => 'edit', $data['Menu']['id']), array('title' => '編集')) ?>			
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_delete.png', array('width' => 24, 'height' => 24, 'alt' => '削除', 'class' => 'btn')), array('action' => 'ajax_delete', $data['Menu']['id']), array('title' => '削除', 'class' => 'btn-delete')) ?>
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_up.png', array('width' => 24, 'height' => 24, 'alt' => '上へ移動', 'class' => 'btn')), array('action' => 'ajax_up', $data['Menu']['id']), array_merge(array('class' => 'btn-up', 'title' => '上へ移動'), $btnUpStyle)) ?>
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_down.png', array('width' => 24, 'height' => 24, 'alt' => '下へ移動', 'class' => 'btn')), array('action' => 'ajax_down', $data['Menu']['id']), array_merge(array('class' => 'btn-down', 'title' => '下へ移動'), $btnDownStyle)) ?>
	</td>
	<td>
		<?php echo $data['Menu']['prefix'] ?>
		<?php if($data['Menu']['menu_type'] == 1): ?>
			<?php $this->BcBaser->img('admin/file.gif') ?>
		<?php elseif($data['Menu']['menu_type'] == 2): ?>
			<?php $this->BcBaser->img('admin/folder.gif') ?>
		<?php endif ?>
		<?php echo $data['Menu']['name'] ?>
	</td>
	<td style="text-align: center"><?php echo $this->BcText->booleanMark($this->BcMenu->allowPublish($data)) ?>
	<td><?php echo $this->BcTime->format('Y-m-d', $data['Menu']['created']); ?><br />
<?php echo $this->BcTime->format('Y-m-d', $data['Menu']['modified']); ?></td>
</tr>