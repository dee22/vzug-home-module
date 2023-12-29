<?php

declare(strict_types=1);
require_once __DIR__ . '/../libs/AndoraWashAPI.php';
class AndoraWaschen extends IPSModuleStrict {
	use AndoraWashAPI;

	public $isSimulated = false;
	public function Create(): void {
		parent::Create();
		$this->RegisterPropertyString("IPAddress", "");
		$this->RegisterPropertyBoolean("Simulated", false);
		$this->RegisterAttributeString("Model", "");
		$this->RegisterAttributeString("Debug", "");
	}

	public function Destroy(): void {
		parent::Destroy();
	}

	public function ApplyChanges(): void {
		parent::ApplyChanges();
	}

	public function GetConfigurationForm(): string {
		$formJson = file_get_contents(__DIR__ . '/form.json');
		$form = json_decode($formJson, true);
		$ip = $this->ReadPropertyString('IPAddress');
		if ($ip) {
			$model = $this->ReadAttributeString('Model');
			if ($model) {
				$form['elements'][] = [
					"name" => "Model",
					"type" => "Label",
					"label" => "Modell: $model",
				];
			}
		}
		$debugMsg = $this->ReadAttributeString('Debug');
		foreach (explode("\n", $debugMsg) as $idx => $msg) {
			$form['elements'][] = [
				"name" => "Debug$idx",
				"type" => "Label",
				"label" => $msg,
			];
		}
		return json_encode($form);
	}

	// API Wrappers

	public function getProgramStatus() {
		$ip = $this->ReadPropertyString('IPAddress');
		$zhMode = $this->getZHMode($ip);
		$deviceStatus = $this->getDeviceStatus($ip);
		$lastPushNotifications = $this->getLastPUSHNotifications($ip);
		return [
			'deviceStatus' => $deviceStatus,
			'lastPushNotifications' => $lastPushNotifications,
		];
	}

	public function getUserSettings() {
		$ip = $this->ReadPropertyString('IPAddress');
		$retries = 2;
		$results = [];
		for ($try = 0; $try <= $retries; $try++) {
			$categoriesList = $this->getCategories($ip);
			foreach ($categoriesList as $category) {
				$commandList = $this->getCommands($ip, $category);
				$categoryProps = $this->getCategory($ip, $category);
				$categoryProps->commandNames = $commandList;
				$categoryProps->commands = [];
				foreach ($commandList as $commandName) {
					$command = $this->getCommand($ip, $commandName);
					$categoryProps->commands[] = $command;
				}
				$results[$category] = $categoryProps;
				break 2;
			}
		}
		return $results;
	}

	// Tests

	public function UpdateInfos(): void {
		$ip = $this->ReadPropertyString('IPAddress');
		$model = $this->getModelDescription($ip);
		$this->WriteAttributeString('Model', $model);
		//$this->UpdateFormField("Model", "label", $model);
		$this->ReloadForm();
	}

	public function ResetInfos(): void {
		$this->WriteAttributeString('Model', '');
		//$this->UpdateFormField("Model", "label", '');
		$this->ReloadForm();
	}

	public function DoReload() {
		// START CONFIG
		$moduleName = 'vzug-home-module';
		$fromRemote = true;
		$forceMethod = ['', 'reload', 'update', 'revert', 'recreate'][0];
		// END CONFIG

		$mcid = IPS_GetInstanceIDByName('Modules', 0);
		$success = $this->RefreshModule($moduleName, $fromRemote, $forceMethod);
		$text = 'Module update ' . ($success ? 'was successful!' : 'failed!') . "\n\n";
		$text = "Module Health:\n";
		$text = "Clean: " . (MC_IsModuleClean($mcid, $moduleName) ? 'Yes' : 'No') . "\n";
		$text = "Valid: " . (MC_IsModuleValid($mcid, $moduleName) ? 'Yes' : 'No') . "\n";
		$this->WriteAttributeString('Debug', $text);
	}

	/**
	 * This function does reload, revert, update or recreate a Module.
	 * $moduleName string The name of the Module to refresh.
	 * $allowUpdate bool Set to true to allow updating from GitHub. Tries to revert and recreate if update fails.
	 * $forceMethod string Forces a specific method. Options ['reload', 'update', 'revert', 'recreate']
	 */
	function RefreshModule(string $moduleName, bool $fromRemote = true, string $forceMethod = ''): bool {
		$mcid = IPS_GetInstanceIDByName('Modules', 0);
		$repoUrl = @MC_GetModuleRepositoryInfo($mcid, $moduleName)['ModuleURL'];

		$canUpdate = fn () => $fromRemote && @MC_IsModuleUpdateAvailable($mcid, $moduleName);
		$reload = fn () => @MC_ReloadModule($mcid, $moduleName);
		$update = fn () => ($canUpdate ? @MC_UpdateModule($mcid, $moduleName) : true);
		$revert = fn () => ($fromRemote ? @MC_RevertModule($mcid, $moduleName) : true);
		$delete = fn () => ($fromRemote ? ($repoUrl ? @MC_DeleteModule($mcid, $moduleName) : true) : true);
		$create = fn () => ($fromRemote ? ($repoUrl ? @MC_CreateModule($mcid, $repoUrl) : true) : true);
		$forcing = fn (string $opt) => (empty($forceMethod) || $opt === $forceMethod);

		$methods = [
			'reload' => fn () => $forcing('reload') && !$canUpdate() && $reload(),
			'update' => fn () => $forcing('update') && ($update() && $reload()),
			'revert' => fn () => $forcing('revert') && ($revert() && $update() && $reload()),
			'recreate' => fn () => $forcing('recreate') && ($delete() && $create() && $reload()),
		];

		foreach ($methods as $methodName => $method) {
			$result = $method();
			if ($result) {
				print_r("Executed Method: '$methodName'\n");
				return $result;
			}
		}

		// none of the update methods was sucessful
		$methodNames = array_keys($methods);
		$debugInfo = [
			$forcing('reload'),
			$forcing('update'),
			$forcing('revert'),
			$forcing('recreate')
		];
		$executed = array_map(fn ($name, $exec) => "$name | Executed: $exec, Success: false", $methodNames, $debugInfo);
		$msg = "Execution failed! Debug: \nExecution Info: \n" . implode("\n", $debugInfo);
		$this->WriteAttributeString('Debug', $msg);
		$this->SendDebug('Module Update', $msg, 0);
		return false;
	}
}
