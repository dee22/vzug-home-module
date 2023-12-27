<?php

declare(strict_types=1);
class AndoraWaschen extends IPSModule
{
	public function Create()
	{
		//Never delete this line!
		parent::Create();
		$this->RegisterPropertyString("IPAddress", "");
	}

	public function Destroy()
	{
		//Never delete this line!
		parent::Destroy();
	}

	public function ApplyChanges()
	{
		//Never delete this line!
		parent::ApplyChanges();
		$ip = $this->ReadPropertyString('IPAddress');
		$response = file_get_contents("http://$ip/ai?command=getModelDescription&_=1690883064754");
	}
}
