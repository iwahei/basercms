<?php
/**
 * アップデーターコントローラー
 *
 * baserCMSのコアや、プラグインのアップデートを行えます。
 *
 * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
 * 　アップデートファイルの配置場所
 * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
 * ■ コア
 * /baser/config/update/{バージョン番号}/
 * ■ baserフォルダ内プラグイン
 * /baser/plugins/{プラグイン名}/update/{バージョン番号}/
 * ■ appフォルダ内プラグイン
 * /app/plugins/{プラグイン名}/update/{バージョン番号}/
 *
 * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
 * 　アップデートスクリプト
 * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
 * アップデート処理実行時に実行されるスクリプトです。
 * スキーマファイルやCSVファイルを読み込む関数が利用可能です。
 * 次のファイル名で対象バージョンのアップデートフォルダに設置します。
 *
 * ■ ファイル名： updater.php
 *
 * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
 * 　スキーマファイル
 * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
 * データベースの構造変更にCakeSchemaを利用できます。
 * ブラウザより、次のURLにアクセスするとスキーマファイルの書き出しが行えますのでそれを利用します。
 * http://{baserCMSの設置URL}/admin/tools/write_schema
 * 更新タイプによって、ファイル名を変更し、アップデートフォルダに設置します。
 *
 * ■ テーブル追加： create_{テーブル名}.php
 * ■ テーブル更新： alter_{テーブル名}.php
 * ■ テーブル削除： drop_{テーブル名}.php
 * ※ ファイル名に更新タイプを含めない場合は、createとして処理されます。
 *
 * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
 * 　CSVファイル
 * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
 * CSVファイルによってデータのインポートが行えます。
 * CSVファイルはShift-JISで作成します。
 * 1行目には必ずフィールド名が必要です。
 * PRIMARYKEY のフィールドを自動採番するには、1行目のフィールド名は設定した上で値を空文字にします。
 * 次のファイル名で対象バージョンのアップデートフォルダに設置します。
 *
 * ■ ファイル名： {テーブル名}.php
 *
 * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
 * 　アップデート用関数
 * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
 * アップデートプログラム上で利用できる関数は次のとおりです。
 *
 * ----------------------------------------
 * 　スキーマファイルを読み込む
 * ----------------------------------------
 * $this->loadSchema($version, $plugin = '', $filterTable = '', $filterType = '');
 *
 * $version			アップデート対象のバージョン番号を指定します。（例）'1.6.7'
 * $plugin			プラグイン内のスキーマを読み込むにはプラグイン名を指定します。（例）'mail
 * $filterTable		指定したテーブルのみを追加・更新する場合は、プレフィックスを除外したテーブル名を指定します。（例）'permissions'
 * 					指定しない場合は全てのスキーマファイルが対象となります。
 * $filterType		指定した更新タイプ（create / alter / drop）のみを対象とする場合は更新タイプを指定します。（例）'create'
 * 					指定しない場合はスキーマファイルが対象となります。
 *
 * ----------------------------------------
 * 　CSVファイルを読み込む
 * ----------------------------------------
 * $this->loadCsv($version, $plugin = '', $filterTable = '');
 * $version			アップデート対象のバージョン番号を指定します。（例）'1.6.7'
 * $plugin			プラグイン内のCSVを読み込むにはプラグイン名を指定します。（例）'mail'
 * $filterTable		指定したテーブルのみCSVを読み込む場合は、プレフィックスを除外したテーブル名を指定します。（例）'permissions'
 * 					指定しない場合は全てのテーブルが対象になります。
 *
 * ----------------------------------------
 * 　アップデートメッセージをセットする
 * ----------------------------------------
 * アップデート完了時に表示するメッセージを設定します。ログにも記録されます。
 * ログファイルの記録場所：/app/tmp/logs/update.log
 *
 * $this->setUpdateLog($message);
 *
 * $message			メッセージを指定します。
 *
 * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
 * 　開発時のテストについて
 * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
 * 次期バージョンのアップデートスクリプトを作成する際のテストを行うには、
 * アップデートフォルダの名称をバージョン番号ではなく、「test」とすると、
 * WEBサイトのバージョンが更新されず、何度もテストを行えます。
 * ※ アップデートによって変更された内容のリセットは手動で行う必要があります。
 *
 * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
 * 　スキーマの読み込みテストについて
 * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
 * 作成したスキーマファイルが正常に読み込めるかをテストする場合には、
 * ブラウザより次のURLにアクセスし、スキーマファイルをアップロードしてテストを行なえます。
 *
 * http://{baserCMSの設置フォルダ}/admin/tools/load_schema
 *
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Controller
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * アップデーターコントローラー
 * 
 * @package	Baser.Controller
 */
class UpdatersController extends AppController {

/**
 * クラス名
 *
 * @var string
 * @access public
 */
	public $name = 'Updaters';

/**
 * アップデートメッセージ
 *
 * @var array
 * @access protected
 */
	protected $_updateMessage = array();

/**
 * コンポーネント
 *
 * @var array
 * @access public
 */
	public $components = array('BcAuth', 'Cookie', 'BcAuthConfigure');

/**
 * ヘルパー
 *
 * @var array
 * @access public
 */
	public $helpers = array('BcForm');

/**
 * モデル
 *
 * @var array
 * @access public
 */
	public $uses = array('Menu', 'Favorite');

/**
 * beforeFilter
 *
 * @return void
 * @access public
 */
	public function beforeFilter() {
		$this->Updater = ClassRegistry::init('Updater');
		$this->Plugin = ClassRegistry::init('Plugin');
		$this->SiteConfig = ClassRegistry::init('SiteConfig');
		if ($this->request->action == 'admin_plugin') {
			$this->Favorite = ClassRegistry::init('Favorite');
		}
		$this->BcAuth->allow('index');

		parent::beforeFilter();

		$this->layoutPath = 'admin';
		$this->layout = 'default';
		$this->subDir = 'admin';
	}

/**
 * コアのアップデート実行
 *
 * @return void
 * @access public
 */
	public function index() {
		$aryUrl = explode('/', $this->request->url);
		if (empty($aryUrl[0])) {
			$this->notFound();
		}
		// updateKey 以外でアクセスされた場合は NotFoundとする
		$updateKey = Configure::read('BcApp.updateKey');
		if ($updateKey != $aryUrl[0]) {
			$this->notFound();
		}

		clearAllCache();

		$targetPlugins = Configure::read('BcApp.corePlugins');
		$targets = $this->Plugin->find('list', array('fields' => array('Plugin.name'), 'conditions' => array('Plugin.status' => true, 'Plugin.name' => $targetPlugins)));
		$targets = am(array(''), $targets);

		$scriptNum = 0;
		foreach ($targets as $target) {
			$scriptNum += $this->_getScriptNum($target);
		}

		$updateLogFile = TMP . 'logs' . DS . 'update.log';

		/* スクリプト実行 */
		if ($this->request->data) {
			clearAllCache();
			@unlink($updateLogFile);
			if (function_exists('ini_set')) {
				ini_set('max_excution_time', 0);
				ini_set('max_excution_time', '128M');
			}

			// プラグインを一旦無効化
			$plugins = $this->Plugin->find('all', array('fields' => array('Plugin.id'), 'conditions' => array('Plugin.status' => true)));
			$enabledPluginIds = array();
			foreach ($plugins as $plugin) {
				$enabledPluginIds[] = $plugin['Plugin']['id'];
				$plugin['Plugin']['status'] = false;
				$this->Plugin->set($plugin);
				$this->Plugin->save();
			}

			$this->setUpdateLog('アップデート処理を開始します。');
			foreach ($targets as $target) {
				if (!$this->_update($target)) {
					$this->setUpdateLog('アップデート処理が途中で失敗しました。');
				}
			}

			// プラグインを有効化
			foreach ($enabledPluginIds as $pluginId) {
				$plugin = array();
				$plugin['Plugin']['id'] = $pluginId;
				$plugin['Plugin']['status'] = true;
				$this->Plugin->set($plugin);
				$this->Plugin->save();
			}

			clearAllCache();

			$this->setMessage('全てのアップデート処理が完了しました。<a href="#UpdateLog">アップデートログ</a>を確認してください。');
			$this->_writeUpdateLog();
			$this->redirect(array('action' => 'index'));
		}

		$updateLog = '';
		if (file_exists($updateLogFile)) {
			$File = new File(TMP . 'logs' . DS . 'update.log');
			$updateLog = $File->read();
		}

		$targetVersion = $this->getBaserVersion();
		$sourceVersion = $this->getSiteVersion();
		$this->pageTitle = 'データベースアップデート（baserCMSコア）';
		$this->set('log', $updateLog);
		$this->set('updateTarget', 'baserCMSコア');
		$this->set('siteVer', $sourceVersion);
		$this->set('baserVer', $targetVersion);
		$this->set('siteVerPoint', verpoint($sourceVersion));
		$this->set('baserVerPoint', verpoint($targetVersion));
		$this->set('scriptNum', $scriptNum);
		$this->set('plugin', false);
		$this->render('update');
	}

/**
 * [ADMIN] アップデートスクリプトを実行する
 *
 * @return void
 * @access public
 */
	public function admin_exec_script() {
		if ($this->request->data) {
			$this->setUpdateLog('アップデートスクリプトの実行します。');
			if ($this->_execScript($this->request->data['Updater']['plugin'], $this->request->data['Updater']['version'])) {
				$this->setUpdateLog('アップデートスクリプトの実行が完了しました。');
				$this->_writeUpdateLog();
				$this->setMessage('アップデートスクリプトの実行が完了しました。<a href="#UpdateLog">アップデートログ</a>を確認してください。');
				$this->redirect(array('action' => 'exec_script'));
			} else {
				$this->setMessage('アップデートスクリプトが見つかりません。', true);
			}
		}

		$updateLogFile = TMP . 'logs' . DS . 'update.log';
		$updateLog = '';
		if (file_exists($updateLogFile)) {
			$File = new File(TMP . 'logs' . DS . 'update.log');
			$updateLog = $File->read();
		}

		$this->pageTitle = 'アップデートスクリプト実行';
		$plugins = $this->Plugin->find('list', array('fields' => array('name', 'title')));
		$this->set('plugins', $plugins);
		$this->set('log', $updateLog);
	}

/**
 * プラグインのアップデート実行
 * 
 * @param string $name
 * @return void
 * @access public
 */
	public function admin_plugin($name) {
		if (!$name) {
			$this->notFound();
		}
		$title = $this->Plugin->field('title', array('name' => $name));
		if (!$title) {
			$this->notFound();
		}

		clearAllCache();

		/* スクリプトの有無を確認 */
		$scriptNum = $this->_getScriptNum($name);

		/* スクリプト実行 */
		if ($this->request->data) {
			clearAllCache();
			$this->_update($name);
			$this->setMessage('アップデート処理が完了しました。<a href="#UpdateLog">アップデートログ</a>を確認してください。');
			$this->_writeUpdateLog();
			clearAllCache();
			$this->redirect(array('action' => 'plugin', $name));
		}

		$updateLogFile = TMP . 'logs' . DS . 'update.log';
		$updateLog = '';
		if (file_exists($updateLogFile)) {
			$File = new File($updateLogFile);
			$updateLog = $File->read();
		}

		$targetVersion = $this->getBaserVersion($name);
		$sourceVersion = $this->getSiteVersion($name);
		$title = $this->Plugin->field('title', array('name' => $name)) . 'プラグイン';
		$this->pageTitle = 'データベースアップデート（' . $title . '）';

		$this->set('updateTarget', $title);
		$this->set('siteVer', $sourceVersion);
		$this->set('baserVer', $targetVersion);
		$this->set('siteVerPoint', verpoint($sourceVersion));
		$this->set('baserVerPoint', verpoint($targetVersion));
		$this->set('scriptNum', $scriptNum);
		$this->set('plugin', $name);
		$this->set('log', $updateLog);
		$this->render('update');
	}

/**
 * 処理対象のスクリプト数を取得する
 *
 * @param string $plugin
 * @return int
 * @access protected
 */
	protected function _getScriptNum($plugin = '') {
		/* バージョンアップ対象のバージョンを取得 */
		$targetVersion = $this->getBaserVersion($plugin);
		$sourceVersion = $this->getSiteVersion($plugin);

		/* スクリプトの有無を確認 */
		$scriptNum = count($this->_getUpdaters($sourceVersion, $targetVersion, $plugin));
		return $scriptNum;
	}

/**
 * アップデータのパスを取得する
 *
 * @param string $sourceVersion
 * @param string $targetVersion
 * @param string $plugin
 * @return array $updates
 * @access protected
 */
	protected function _getUpdaters($sourceVersion, $targetVersion, $plugin = '') {
		$sourceVerPoint = verpoint($sourceVersion);
		$targetVerPoint = verpoint($targetVersion);

		if ($sourceVerPoint === false || $targetVerPoint === false) {
			return array();
		}

		if (!$plugin) {
			$path = BASER_CONFIGS . 'update' . DS;
			if (!is_dir($path)) {
				return array();
			}
		} else {
			$paths = App::path('Plugin');
			foreach($paths as $path) {
				$path .= $plugin . DS . 'Config' . DS . 'update' . DS;
				if (is_dir($path)) {
					break;
				}
				$path = null;
			}
			if(!$path) {
				return array();
			}
		}

		$folder = new Folder($path);
		$files = $folder->read(true, true);
		$updaters = array();
		$updateVerPoints = array();
		if (!empty($files[0])) {
			foreach ($files[0] as $folder) {
				$updateVersion = $folder;
				$updateVerPoints[$updateVersion] = verpoint($updateVersion);
			}
			asort($updateVerPoints);
			foreach ($updateVerPoints as $key => $updateVerPoint) {
				if (($updateVerPoint > $sourceVerPoint && $updateVerPoint <= $targetVerPoint) || $key == 'test') {
					if (file_exists($path . DS . $key . DS . 'updater.php')) {
						$updaters[$key] = $updateVerPoint;
					}
				}
			}
		}
		return $updaters;
	}

/**
 * アップデートフォルダのパスを取得する
 *
 * @param string $plugin
 * @return mixed $path or false
 * @access protected
 */
	protected function _getUpdateFolder($plugin = '') {
		if (!$plugin) {
			return BASER_CONFIGS . 'update' . DS;
		} else {
			
			$paths = App::path('Plugin');
			foreach($paths as $path) {
				$path .= $plugin . DS . 'Config' . DS . 'update' . DS;
				if(is_dir($path)) {
					return $path;
				}
			}
			return false;
			
		}
	}

/**
 * アップデートを実行する
 *
 * アップデートスクリプトを読み込む為、
 * よく使われるような変数名はダブらないように
 * アンダースコアを二つつける
 *
 * @param string $targetVersion
 * @param string $sourceVersion
 * @param string $plugin
 * @return boolean
 * @access public
 */
	protected function _update($plugin = '') {
		$targetVersion = $this->getBaserVersion($plugin);
		$sourceVersion = $this->getSiteVersion($plugin);
		$path = $this->_getUpdateFolder($plugin);
		$updaters = $this->_getUpdaters($sourceVersion, $targetVersion, $plugin);

		if (!$plugin) {
			$name = 'baserCMSコア';
		} else {
			$name = $this->Plugin->field('title', array('name' => $plugin)) . 'プラグイン';
		}

		$this->setUpdateLog($name . ' ' . $targetVersion . ' へのアップデートを開始します。');

		if ($updaters) {
			asort($updaters);
			foreach ($updaters as $version => $updateVerPoint) {
				$this->setUpdateLog('アップデートプログラム ' . $version . ' を実行します。');
				$this->_execScript($plugin, $version);
			}
		}

		if (!isset($updaters['test'])) {
			if (!$plugin) {
				/* サイト基本設定にバージョンを保存 */
				$SiteConfigClass = ClassRegistry::getObject('SiteConfig');
				$SiteConfigClass->cacheQueries = false;
				$data['SiteConfig']['version'] = $targetVersion;
				$result = $SiteConfigClass->saveKeyValue($data);
			} else {
				// 1.6.7 では plugins テーブルの構造が変わったので、find でデータが取得できないのでスキップする
				// DB の再接続を行えば取得できるかも
				if ($targetVersion == '1.6.7') {
					$result = true;
				} else {
					$data = $this->Plugin->find('first', array('conditions' => array('name' => $plugin)));
					$data['Plugin']['version'] = $targetVersion;
					$result = $this->Plugin->save($data);
				}
			}
		} else {
			$result = true;
		}

		$this->setUpdateLog($name . ' ' . $targetVersion . ' へのアップデートが完了しました。');

		return $result;
	}

/**
 * アップデートスクリプトを実行する
 *
 * @param string $__plugin
 * @param string $__version
 * @return void
 * @access protected
 */
	public function _execScript($__plugin, $__version) {
		$__path = $this->_getUpdateFolder($__plugin) . $__version . DS . 'updater.php';

		if (!file_exists($__path)) {
			return false;
		}

		try {
			include $__path;
		} catch (Exception $e) {
			$this->log($e->getMessage());
			return false;
		}

		return true;
	}

/**
 * アップデートメッセージをセットする
 *
 * @param string $message
 * @param boolean $head 見出しとして設定する
 * @param boolean $beforeBreak 前の行で改行する
 * @return void
 * @access public
 */
	public function setUpdateLog($message) {
		$this->_updateMessage[] = $message;
	}

/**
 * DB構造を変更する
 *
 * @param string $version
 * @param tring $plugin
 * @param string $filterTable
 * @param string $filterType
 * @return boolean
 * @access	public
 */
	public function loadSchema($version, $plugin = '', $filterTable = '', $filterType = '') {
		$path = $this->_getUpdatePath($version, $plugin);
		if (!$path) {
			return false;
		}
		if ($plugin) {
			$dbConfigName = 'plugin';
		} else {
			$dbConfigName = 'baser';
		}
		// アップデートの場合 drop field は実行しない
		$result = $this->Updater->loadSchema($dbConfigName, $path, $filterTable, $filterType, array('updater.php'), false);
		clearAllCache();
		return $result;
	}

/**
 * データを追加する
 *
 * @param string $version
 * @param string $plugin
 * @param string $filterTable
 * @return boolean
 * @access public
 */
	public function loadCsv($version, $plugin = '', $filterTable = '') {
		$path = $this->_getUpdatePath($version, $plugin);
		if (!$path) {
			return false;
		}
		if ($plugin) {
			$dbConfigName = 'plugin';
		} else {
			$dbConfigName = 'baser';
		}
		return $this->Updater->loadCsv($dbConfigName, $path, $filterTable);
	}

/**
 * アップデートスクリプトのパスを取得する
 *
 * @param string $version
 * @param string $plugin
 * @return string $path or ''
 */
	protected function _getUpdatePath($version, $plugin = '') {
		
		if ($plugin) {
			$paths = App::path('Plugin');
			foreach($paths as $path) {
				$path .= $plugin . DS . 'Config' . DS . 'update' . DS . $version;
				if(is_dir($path)) {
					return $path;
				}
			}
			return false;
		} else {
			$corePath = BASER_CONFIGS . 'update' . DS . $version;
			if (is_dir($corePath)) {
				return $corePath;
			} else {
				return false;
			}
		}
		
	}

/**
 * アップデートメッセージを保存する
 *
 * @return void
 * @access protected
 */
	protected function _writeUpdateLog() {
		if ($this->_updateMessage) {
			foreach ($this->_updateMessage as $message) {
				$this->log(strip_tags($message), 'update');
			}
		}
	}

}
