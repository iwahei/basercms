<?php

/**
 * プラグインモデル
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Model
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */

/**
 * プラグインモデル
 *
 * @package Baser.Model
 */
class Plugin extends AppModel {

/**
 * クラス名
 *
 * @var string
 * @access public
 */
	public $name = 'Plugin';

/**
 * ビヘイビア
 * 
 * @var array
 * @access public
 */
	public $actsAs = array('BcCache');

/**
 * データベース接続
 *
 * @var string
 * @access public
 */
	public $useDbConfig = 'baser';

/**
 * バリデーション
 *
 * @var array
 * @access public
 */
	public $validate = array(
		'name' => array(
			array('rule' => array('alphaNumericPlus'),
				'message' => 'プラグイン名は半角英数字、ハイフン、アンダースコアのみが利用可能です。',
				'reqquired' => true),
			array('rule' => array('isUnique'),
				'on' => 'create',
				'message' => '指定のプラグインは既に使用されています。'),
			array('rule' => array('maxLength', 50),
				'message' => 'プラグイン名は50文字以内としてください。')
		),
		'title' => array(
			array('rule' => array('maxLength', 50),
				'message' => 'プラグインタイトルは50文字以内とします。')
		)
	);

/**
 * データベースを初期化する
 * 既存のテーブルは上書きしない
 *
 * @param string $plugin
 * @return boolean
 * @access public
 */
	public function initDb($dbConfigName = 'plugin', $pluginName = '', $loadCsv = true, $filterTable = '', $filterType = '') {
		return parent::initDb($dbConfigName, $pluginName, true, $filterTable, 'create');
	}

/**
 * データベースをプラグインインストール前の状態に戻す
 * 
 * @param string $plugin
 * @return boolean 
 */
	public function resetDb($plugin) {

		$path = BcUtil::getSchemaPath($plugin);

		if (!$path) {
			return true;
		}

		$baserDb = ConnectionManager::getDataSource('baser');
		$baserDb->cacheSources = false;
		$baserListSources = $baserDb->listSources();
		$baserPrefix = $baserDb->config['prefix'];
		$pluginDb = ConnectionManager::getDataSource('plugin');
		$pluginDb->cacheSources = false;
		$pluginListSources = $pluginDb->listSources();
		$pluginPrefix = $pluginDb->config['prefix'];

		$Folder = new Folder($path);
		$files = $Folder->read(true, true);

		if (empty($files[1])) {
			return true;
		}

		$tmpdir = TMP . 'schemas' . DS;
		$result = true;

		foreach ($files[1] as $file) {

			$oldSchemaPath = '';

			if (preg_match('/^(.*?)\.php$/', $file, $matches)) {

				$type = 'drop';
				$table = $matches[1];
				$File = new File($path . DS . $file);
				$data = $File->read();
				if (preg_match('/(public|var)\s+\$connection\s+=\s+\'([a-z]+?)\';/', $data, $matches)) {
					$conType = $matches[2];
					$listSources = ${$conType . 'ListSources'};
					$prefix = ${$conType . 'Prefix'};
				} else {
					continue;
				}

				$schemaPath = $tmpdir;
				if (preg_match('/^create_(.*?)\.php$/', $file, $matches)) {
					$type = 'drop';
					$table = $matches[1];
					if (!in_array($prefix . $table, $listSources)) {
						continue;
					}
					copy($path . DS . $file, $tmpdir . $table . '.php');
				} elseif (preg_match('/^alter_(.*?)\.php$/', $file, $matches)) {
					$type = 'alter';
					$table = $matches[1];
					if (!in_array($prefix . $table, $listSources)) {
						continue;
					}

					$corePlugins = implode('|', Configure::read('BcApp.corePlugins'));
					if (preg_match('/^(' . $corePlugins . ')/', Inflector::camelize($table), $matches)) {
						$pluginName = $matches[1];
					}

					$File = new File($path . DS . $file);
					$data = $File->read();
					$data = preg_replace('/class\s+' . Inflector::camelize($table) . 'Schema/', 'class Alter' . Inflector::camelize($table) . 'Schema', $data);
					$oldSchemaPath = $tmpdir . $file;
					$File = new File($oldSchemaPath);
					$File->write($data);

					if ($conType == 'baser') {
						$schemaPath = BcUtil::getSchemaPath() . DS;
					} else {
						$schemaPath = BcUtil::getSchemaPath($pluginName) . DS;
					}
				} elseif (preg_match('/^drop_(.*?)\.php$/', $file, $matches)) {
					$type = 'create';
					$table = $matches[1];
					if (in_array($prefix . $table, $listSources)) {
						continue;
					}
					copy($path . DS . $file, $tmpdir . $table . '.php');
				} else {
					if (!in_array($prefix . $table, $listSources)) {
						continue;
					}
					copy($path . DS . $file, $tmpdir . $table . '.php');
				}

				if ($conType == 'baser') {
					$db = $baserDb;
				} else {
					$db = $pluginDb;
				}

				if (!$db->loadSchema(array('type' => $type, 'path' => $schemaPath, 'file' => $table . '.php', 'dropField' => true, 'oldSchemaPath' => $oldSchemaPath))) {
					$result = false;
				}
				@unlink($tmpdir . $table . '.php');
				if (file_exists($oldSchemaPath)) {
					unlink($oldSchemaPath);
				}
			}
		}

		return $result;
	}

/**
 * データベースの構造を変更する
 * 
 * @param string $plugin
 * @return boolean
 * @access public
 */
	public function alterDb($plugin, $dbConfigName = 'baser', $filterTable = '') {
		return parent::initDb($dbConfigName, $plugin, false, $filterTable, 'alter');
	}

}
