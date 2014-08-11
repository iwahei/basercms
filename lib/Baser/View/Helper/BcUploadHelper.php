<?php

/**
 * アップロードヘルパー
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View.Helper
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
App::uses('FormHelper', 'View/Helper');

/**
 * アップロードヘルパー
 *
 * @package Baser.View.Helper
 */
class BcUploadHelper extends FormHelper {

/**
 * ファイルインプットボックス出力
 * 画像の場合は画像タグ、その他の場合はファイルへのリンク
 * そして削除用のチェックボックスを表示する
 * [カスタムオプション]
 * imgsize・・・画像のサイズを指定する
 *
 * @param string $fieldName
 * @param array $options
 * @return string
 * @access public
 */
	public function file($fieldName, $options = array()) {

		$options = array_merge(array(
			'imgsize' => 'midium', // 画像サイズ
			'rel' => '', // rel属性
			'title' => '', // タイトル属性
			'link' => true, // 大きいサイズの画像へのリンク有無
			'delCheck' => true,
			'force' => false
			), $options);

		extract($options);

		unset($options['imgsize']);
		unset($options['rel']);
		unset($options['title']);
		unset($options['link']);
		unset($options['delCheck']);
		unset($options['force']);

		$linkOptions = array(
			'imgsize' => $imgsize,
			'rel' => $rel,
			'title' => $title,
			'link' => $link,
			'delCheck' => $delCheck,
			'force' => $force
		);

		$options = $this->_initInputField($fieldName, $options);

		$entity = $this->entity();
		$modelName = array_shift($entity);
		$field = $this->field();
		$fileLinkTag = $this->fileLink($fieldName, $linkOptions);
		$fileTag = parent::file($fieldName, $options);
		
		if (empty($options['value'])) {
			$value = $this->value($fieldName);
		} else {
			$value = $options['value'];
		}
		
		$delCheckTag = '';
		if ($linkOptions['delCheck'] && empty($value['session_key'])) {
			$delCheckTag = parent::checkbox($modelName . '.' . $field . '_delete') . parent::label($modelName . '.' . $field . '_delete', '削除する');
		}
		$hiddenValue = $this->value($fieldName . '_');
		$fileValue = $this->value($fieldName);

		if (is_array($fileValue) && empty($fileValue['tmp_name']) && $hiddenValue) {
			$hiddenTag = parent::hidden($modelName . '.' . $field . '_', array('value' => $hiddenValue));
		} else {
			if (is_array($fileValue)) {
				$fileValue = null;
			}
			$hiddenTag = parent::hidden($modelName . '.' . $field . '_', array('value' => $fileValue));
		}

		$out = $fileTag;

		if ($fileLinkTag) {
			$out .= '&nbsp;' . $delCheckTag . $hiddenTag . '<br />' . $fileLinkTag;
		}

		return '<div class="upload-file">' . $out . '</div>';
	}

/**
 * ファイルへのリンクを取得する
 *
 * @param string $fieldName
 * @param array $options
 * @return string
 */
	public function fileLink($fieldName, $options = array()) {

		$options = array_merge(array(
			'imgsize' => 'midium', // 画像サイズ
			'rel' => '', // rel属性
			'title' => '', // タイトル属性
			'link' => true, // 大きいサイズの画像へのリンク有無
			'force' => false
			), $options);

		extract($options);

		$options = $this->_initInputField($fieldName, $options);
		$tmp = false;
		$entity = $this->entity();
		$modelName = array_shift($entity);
		$field = $this->field();
		$model = ClassRegistry::init($modelName);

		if (empty($model->Behaviors->BcUpload)) {
			throw new BcException('BcUploadHelper を利用するには、モデルで BcUploadBehavior の利用設定が必要です。');
		}

		$settings = $model->Behaviors->BcUpload->settings[$modelName];
		$basePath = '/files/' . str_replace(DS, '/', $settings['saveDir']) . '/';

		if (empty($options['value'])) {
			$value = $this->value($fieldName);
		} else {
			$value = $options['value'];
		}

		if (is_array($value)) {
			if (empty($value['session_key']) && empty($value['name'])) {
				$data = $model->findById($model->id);
				if (!empty($data[$model->alias][$field])) {
					$value = $data[$model->alias][$field];
				} else {
					$value = '';
				}
			} else {
				if (isset($value['session_key'])) {
					$tmp = true;
					$value = $value['session_key'];
					$basePath = '/uploads/tmp/';
				} else {
					return false;
				}
			}
		}

		/* ファイルのパスを取得 */
		/* 画像の場合はサイズを指定する */
		if (isset($settings['saveDir'])) {
			if ($value && !is_array($value)) {
				$uploadSettings = $settings['fields'][$field];
				$ext = decodeContent('', $value);
				if ($uploadSettings['type'] == 'image' || in_array($ext, $model->Behaviors->BcUpload->imgExts)) {
					$options = array('imgsize' => $imgsize, 'rel' => $rel, 'title' => $title, 'link' => $link, 'force' => $force);
					if ($tmp) {
						$options['tmp'] = true;
					}
					$fileLinkTag = $this->uploadImage($fieldName, $value, $options) . '<br /><span class="file-name">' . mb_basename($value) . '</span>';
				} else {
					$filePath = $basePath . $value;
					$fileLinkTag = $this->Html->link('ダウンロード ≫', $filePath, array('target' => '_blank')) . '<br /><span class="file-name">' . mb_basename($value) . '</span>';
				}
			} else {
				$fileLinkTag = $value;
			}
		} else {
			return false;
		}
		return $fileLinkTag;
	}

/**
 * アップロードした画像のタグをリンク付きで出力する
 * Uploadビヘイビアの設定による
 * 上から順に大きい画像を並べている事が前提で
 * 指定したサイズ内で最大の画像を出力
 * リンク先は存在する最大の画像へのリンクとなる
 *
 * @param string $fieldName
 * @param string $fileName
 * @param array $options
 * @return string
 */
	public function uploadImage($fieldName, $fileName, $options = array()) {

		$options = array_merge(array(
			'imgsize' => 'midium', // 画像サイズ
			'link' => true, // 大きいサイズの画像へのリンク有無
			'escape' => false, // エスケープ
			'mobile' => false, // モバイル
			'alt' => '', // alt属性
			'width' => '', // 横幅
			'height' => '', // 高さ
			'noimage' => '', // 画像がなかった場合に表示する画像
			'tmp' => false,
			'force' => false
			), $options);

		extract($options);

		unset($options['imgsize']);
		unset($options['link']);
		unset($options['escape']);
		unset($options['mobile']);
		unset($options['alt']);
		unset($options['width']);
		unset($options['height']);
		unset($options['noimage']);
		unset($options['tmp']);

		$imgOptions = array(
			'alt' => $alt,
			'width' => $width,
			'height' => $height
		);

		if ($imgOptions['width'] === '') {
			unset($imgOptions['width']);
		}
		if ($imgOptions['height'] === '') {
			unset($imgOptions['height']);
		}

		$linkOptions = array(
			'rel' => 'colorbox',
			'escape' => $escape
		);

		if (is_array($fileName)) {
			if (isset($fileName['session_key'])) {
				$fileName = $fileName['session_key'];
				$tmp = true;
			} else {
				return '';
			}
		}

		if ($noimage) {
			if (!$fileName) {
				$fileName = $noimage;
			}
		} else {
			if (!$fileName) {
				return '';
			}
		}

		if (strpos($fieldName, '.') === false) {
			trigger_error('フィールド名は、 ModelName.field_name で指定してください。', E_USER_WARNING);
			return false;
		}

		list($modelName, $field) = explode('.', $fieldName);
		$model = ClassRegistry::init($modelName);

		$settings = $model->Behaviors->BcUpload->settings[$modelName];

		$fileUrl = '/files/' . str_replace(DS, '/', $settings['saveDir']) . '/';
		$filePath = WWW_ROOT . 'files' . DS . $settings['saveDir'] . DS;

		if (isset($settings['fields'][$field]['imagecopy'])) {
			$copySettings = $settings['fields'][$field]['imagecopy'];
		} else {
			$copySettings = "";
		}

		if ($tmp) {
			$link = false;
			$fileUrl = $this->base . '/uploads/tmp/';
			if ($imgsize) {
				$fileUrl .= $imgsize . '/';
			}
		}

		if ($fileName == $noimage) {
			$mostSizeUrl = $fileName;
		} elseif ($tmp) {
			$mostSizeUrl = $fileUrl . $fileName;
		} else {
			$check = false;
			$maxSizeExists = false;
			$mostSizeExists = false;

			if ($copySettings) {

				foreach ($copySettings as $key => $copySetting) {

					if ($key == $imgsize) {
						$check = true;
					}

					if (isset($copySetting['mobile'])) {
						if ($copySetting['mobile'] != $mobile) {
							continue;
						}
					} else {
						if ($mobile != preg_match('/^mobile_/', $key)) {
							continue;
						}
					}

					$imgPrefix = '';
					$imgSuffix = '';

					if (isset($copySetting['suffix'])) {
						$imgSuffix = $copySetting['suffix'];
					}
					if (isset($copySetting['prefix'])) {
						$imgPrefix = $copySetting['prefix'];
					}
					
					$pathinfo = pathinfo($fileName);
					$ext = $pathinfo['extension'];
					$basename = basename($fileName, '.' . $ext);

					$subdir = str_replace($basename . '.' . $ext, '', $fileName);
					if (file_exists($filePath . str_replace('/', DS, $subdir) . $imgPrefix . $basename . $imgSuffix . '.' . $ext) || $force) {
						if ($check && !$mostSizeExists) {
							$mostSizeUrl = $fileUrl . $subdir . $imgPrefix . $basename . $imgSuffix . '.' . $ext . '?' . rand();
							$mostSizeExists = true;
						} elseif (!$mostSizeExists && !$maxSizeExists) {
							$maxSizeUrl = $fileUrl . $subdir . $imgPrefix . $basename . $imgSuffix . '.' . $ext . '?' . rand();
							$maxSizeExists = true;
						}
					}
				}
			}

			if (!isset($mostSizeUrl)) {
				$mostSizeUrl = $fileUrl . $fileName . '?' . rand();
			}
			if (!isset($maxSizeUrl)) {
				$maxSizeUrl = $fileUrl . $fileName . '?' . rand();
			}
		}

		if ($link && !($noimage == $fileName)) {
			return $this->Html->link($this->Html->image($mostSizeUrl, $imgOptions), $maxSizeUrl, am($options, $linkOptions));
		} else {
			return $this->Html->image($mostSizeUrl, am($options, $imgOptions));
		}
	}

}
