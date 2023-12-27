<?php

declare(strict_types=1);
require_once __DIR__ . '/../libs/AndoraWashAPI.php';
class AndoraWaschen extends IPSModuleStrict
{
	use AndoraWashAPI;
	public function Create(): void
	{
		parent::Create();
		$this->RegisterPropertyString("IPAddress", "");
		$this->RegisterAttributeString("Model", "");
	}

	public function Destroy(): void
	{
		parent::Destroy();
	}

	public function ApplyChanges(): void
	{
		parent::ApplyChanges();
	}

	public function GetConfigurationForm(): string
	{
		$formJson = file_get_contents(__DIR__ . '/form.json');
		$form = json_decode($formJson, true);
		$model = $this->ReadAttributeString('Model');
		$form['elements'][] = [
			"name" => "Model",
			"type" => "Label",
			"label" => $model ? "Modell: $model" : '',
		];
		return json_encode($form);
	}

	public function UpdateInfos(): void
	{
		$ip = $this->ReadPropertyString('IPAddress');
		$model = $this->getModelDescription($ip);
		$this->WriteAttributeString('Model', $model);
		$this->UpdateFormField("Model", "label", $model);
	}

	public function ResetInfos(): void
	{
		$this->WriteAttributeString('Model', '');
		$this->UpdateFormField("Model", "label", '');
	}

	public function UpdateModule()
	{
		$mcInstanceID = IPS_GetInstanceIDByName('Modules', 0);
		$moduleName = 'vzug-home-module';
		MC_UpdateModule($mcInstanceID, $moduleName);
		MC_ReloadModule($mcInstanceID, $moduleName);
	}
}
