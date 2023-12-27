<?php

declare(strict_types=1);
require_once __DIR__ . '/../libs/AndoraWashAPI.php';
class AndoraWaschen extends IPSModule
{
	use AndoraWashAPI;
	public function Create()
	{
		parent::Create();
		$this->RegisterPropertyString("IPAddress", "");
		$this->RegisterAttributeString("Model", "");
	}

	public function Destroy()
	{
		parent::Destroy();
	}

	public function ApplyChanges()
	{
		parent::ApplyChanges();
	}

	public function GetConfigurationForm()
	{
		$formJson = file_get_contents(__DIR__ . '/form.json');
		$form = json_decode($formJson, true);
		$model = $this->ReadAttributeString('Model');
		$form['elements'][] = [
			"type" => "Label",
			"label" => "Modell: $model",
		];
		return json_encode($form);
	}

	public function RequestInfos()
	{
		$ip = $this->ReadPropertyString('IPAddress');
		$model = $this->getModelDescription($ip);
	}
}
