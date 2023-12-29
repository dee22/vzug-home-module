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
}
