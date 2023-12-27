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
		$model = $this->ReadAttributeString('Model');
		$this->UpdateFormField("Model", "label", $model);
		/*
		$form['elements'][2] = [
			"name" => "Model",
			"type" => "Label",
			"label" => $model ? "Modell: $model" : '',
		];*/
		return json_encode($form);
	}

	public function UpdateInfos(): void {
		$ip = $this->ReadPropertyString('IPAddress');
		$model = $this->getModelDescription($ip);
		$this->WriteAttributeString('Model', $model);
		$this->UpdateFormField("Model", "label", $model);
		//$this->ReloadForm();
	}

	public function ResetInfos(): void {
		$this->WriteAttributeString('Model', '');
		$this->UpdateFormField("Model", "label", '');
		//$this->ReloadForm();
	}

	public function UpdateModule(string $moduleName = 'vzug-home-module') {
		$mcInstanceID = IPS_GetInstanceIDByName('Modules', 0);
		MC_UpdateModule($mcInstanceID, $moduleName);
	}

	public function ReloadModule(string $moduleName = 'vzug-home-module') {
		$mcInstanceID = IPS_GetInstanceIDByName('Modules', 0);
		MC_ReloadModule($mcInstanceID, $moduleName);
	}
}
