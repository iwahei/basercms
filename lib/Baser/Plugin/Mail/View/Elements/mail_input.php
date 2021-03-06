<?php

/**
 * [PUBLISH] メールフォーム本体
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
$group_field = null;
$iteration = 0;
if (!isset($blockEnd)) {
	$blockEnd = 0;
}

if (!empty($mailFields)) {

	foreach ($mailFields as $key => $record) {

		$field = $record['MailField'];
		$iteration++;
		if ($field['use_field'] && ($blockStart && $iteration >= $blockStart) && (!$blockEnd || $iteration <= $blockEnd)) {

			$next_key = $key + 1;
			$description = $field['description'];

			/* 項目名 */
			if ($group_field != $field['group_field'] || (!$group_field && !$field['group_field'])) {
				echo '    <tr id="RowMessage' . Inflector::camelize($record['MailField']['field_name']) . '"';
				if ($field['type'] == 'hidden') {
					echo ' style="display:none"';
				}
				echo '>' . "\n" . '        <th class="col-head" width="150">' . $this->Mailform->label("Message." . $field['field_name'] . "", $field['head']);
				if ($field['not_empty']) {
					echo '<span class="required">*</span>';
				}
				echo '</th>' . "\n" . '        <td class="col-input">';
			}

			echo '<span id="FieldMessage' . Inflector::camelize($record['MailField']['field_name']) . '">';
			if (!$freezed && $description) {
				echo '<span class="mail-description">' . $description . '</span>';
			}
			/* 入力欄 */
			if (!$freezed || $this->Mailform->value("Message." . $field['field_name']) !== '') {
				echo '<span class="mail-before-attachment">' . $field['before_attachment'] . '</span>';
			}
			
			if ($field['no_send'] && $freezed) {
				// メール送信しないフィールドの場合、確認画面では、hidden タグを表示する
				echo $this->Mailform->control('hidden', "Message." . $field['field_name'] . "", $this->Mailfield->getOptions($record), $this->Mailfield->getAttributes($record));
			} else {
				echo $this->Mailform->control($field['type'], "Message." . $field['field_name'] . "", $this->Mailfield->getOptions($record), $this->Mailfield->getAttributes($record));
			}
			
			if (!$freezed || $this->Mailform->value("Message." . $field['field_name']) !== '') {
				echo '<span class="mail-after-attachment">' . $field['after_attachment'] . '</span>';
			}
			if (!$freezed) {
				echo '<span class="mail-attention">' . $field['attention'] . '</span>';
			}
			if (!$field['group_valid']) {
				if ($this->Mailform->error("Message." . $field['field_name'] . "_format", "check")) {
					echo $this->Mailform->error("Message." . $field['field_name'] . "_format", "形式が不正です。");
				} else {
					echo $this->Mailform->error("Message." . $field['field_name'], "必須項目です。");
				}
			}

			/* 説明欄 */
			if (($this->BcArray->last($mailFields, $key)) ||
				($field['group_field'] != $mailFields[$next_key]['MailField']['group_field']) ||
				(!$field['group_field'] && !$mailFields[$next_key]['MailField']['group_field']) ||
				($field['group_field'] != $mailFields[$next_key]['MailField']['group_field'] && $this->BcArray->first($mailFields, $key))) {

				if ($field['group_valid']) {
					if ($this->Mailform->error("Message." . $field['group_field'] . "_format", "check")) {
						echo $this->Mailform->error("Message." . $field['group_field'] . "_format", "形式が不正です。");
					} else {
						if ($field['valid']) {
							echo $this->Mailform->error("Message." . $field['group_field'], "必須項目です。");
						}
					}
					echo $this->Mailform->error("Message." . $field['group_field'] . "_not_same", "入力データが一致していません。");
					echo $this->Mailform->error("Message." . $field['group_field'] . "_not_complate", "入力データが不完全です。");
				}

				echo '</span>';
				echo "</td>\n    </tr>\n";
			} else {
				echo '</span>';
			}
			$group_field = $field['group_field'];
		}
	}
}