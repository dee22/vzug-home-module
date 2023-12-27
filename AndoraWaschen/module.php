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
		$configForm = parent::GetConfigurationForm();
		print_r($configForm);
		return '{ "actions": [ { "type": "Label", "label": "The current time is ' . date("d.m.y H:i") . '" } ] }';
	}

	public function RequestInfos()
	{
		$ip = $this->ReadPropertyString('IPAddress');
		$model = $this->getModelDescription($ip);
	}
}
