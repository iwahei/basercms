<?php

/**
 * [ADMIN] CSVダウンロード
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Mail.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
?>
<?php $this->BcCsv->addModelDatas(Inflector::camelize($contentName . '_message'), $messages) ?>
<?php $this->BcCsv->download($contentName) ?>