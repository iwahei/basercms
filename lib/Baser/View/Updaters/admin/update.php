<?php
/**
 * [ADMIN] アップデート
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
if (!($baserVerPoint === false || $siteVerPoint === false) && ($baserVer != $siteVer || $scriptNum)) {
	$requireUpdate = true;
} else {
	$requireUpdate = false;
}
?>


<div class="corner10 panel-box section">
	<h2>現在のバージョン状況</h2>
	<ul class="version">
		<li><?php echo $updateTarget ?> のバージョン： <strong><?php echo $baserVer ?></strong></li>
		<li>現在のデータベースのバージョン： <strong><?php echo $siteVer ?></strong></li>
		<?php if ($baserVerPoint === false || $siteVerPoint === false): ?>
			<li>α版、β版の場合はアップデートサポート外です</li>
		<?php elseif ($baserVer != $siteVer || $scriptNum): ?>
			<?php if ($scriptNum): ?>
				<li>アップデートプログラムが <strong><?php echo $scriptNum ?> 個</strong> あります。</li>
			<?php endif ?>
		<?php else: ?>
			<li>データベースのバージョンは最新です。</li>
		<?php endif ?>
	</ul>
</div>

<?php if ($scriptNum): ?>
	<div class="corner10 panel-box section">
		<div class="section">
			<h2>データベースのバックアップは行いましたか？</h2>
			<p>
				<?php if (!$plugin): ?>
					バックアップを行われていない場合は、アップデートを実行する前に、プログラムファイルを前のバージョンに戻しシステム設定よりデータベースのバックアップを行いましょう。<br />
				<?php else: ?>
					バックアップを行われていない場合は、アップデートを実行する前にデータベースのバックアップを行いましょう。<br />
				<?php endif ?>
				<small>※ アップデート処理は必ず自己責任で行ってください。</small><br />
			</p>
		</div>
		<div class="section">
			<h2>リリースノートのアップデート時の注意事項は読まれましたか？</h2>
			<p>リリースバージョンによっては、追加作業が必要となる場合があるので注意が必要です。<br />公式サイトの <a href="http://basercms.net/news/archives/category/release" target="_blank" class="outside-link">リリースノート</a> を必ず確認してください。</p>
		</div>
	</div>
<?php endif ?>

<div class="corner10 panel-box section">
	<?php if ($requireUpdate): ?>
		<p>「アップデート実行」をクリックしてデータベースのアップデートを完了させてください。</p>
		<?php if (empty($plugin)): ?>
			<?php echo $this->BcForm->create('Updater', array('action' => $this->action)) ?>
		<?php else: ?>
			<?php echo $this->BcForm->create('Updater', array('action' => $this->request->action, 'url' => array($plugin))) ?>
		<?php endif ?>
		<?php echo $this->BcForm->input('Installation.update', array('type' => 'hidden', 'value' => true)) ?>
		<?php echo $this->BcForm->end(array('label' => 'アップデート実行', 'class' => 'button btn-red')) ?>
	<?php else: ?>
		<p>
			<?php if (!$plugin): ?>
			<p>baserCMSコアのアップデートがうまくいかない場合は、<?php $this->BcBaser->link('baserCMSの制作・開発パートナー', 'http://basercms.net/partners/', array('target' => '_blank')) ?>にご相談されるか、前のバージョンの baserCMS に戻す事をおすすめします。</p>
			<?php if (!$requireUpdate): ?>
				<?php $this->BcBaser->link('≫ 管理画面に移動する', '/admin') ?>
			<?php endif ?>
		<?php else: ?>
			<?php $this->BcBaser->link('プラグイン一覧に移動する', array('controller' => 'plugins', 'action' => 'index'), array('class' => 'outside-link')) ?>
		<?php endif ?>
	</p>
<?php endif ?>
</div>

<?php if ($log): ?>
	<div class="corner10 panel-box section" id="UpdateLog">
		<h2>アップデートログ</h2>
		<?php echo $this->BcForm->textarea('Updater.log', array(
			'value' => $log,
			'style' => 'width:99%;height:200px;font-size:12px',
			'readonly' => 'readonly'
		)); ?>
	</div>
<?php endif; ?>
