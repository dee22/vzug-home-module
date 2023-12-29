<?php
require_once(__DIR__ . '/../libs/NetworkScanner.php');

declare(strict_types=1);
class vZugHome extends IPSModule {
	public function Create() {
		parent::Create();

		$this->SetBuffer('Devices', json_encode([]));
		$this->SetBuffer('SearchActive', json_encode(false));
	}

	public function Destroy() {
		parent::Destroy();
	}

	public function ApplyChanges() {
		parent::ApplyChanges();
	}

	public function GetConfigurationForm() {
		$this->SendDebug('GetConfigurationForm', 'Start', 0);
		$this->SendDebug('SearchActive', $this->GetBuffer('SearchActive'), 0);
		$devices = json_decode($this->GetBuffer('Devices'));

		// Do not start a new search, if a search is currently active
		if (!json_decode($this->GetBuffer('SearchActive'))) {
			$this->SetBuffer('SearchActive', json_encode(true));

			$this->SendDebug('Start', 'OnceTimer', 0);
			$this->RegisterOnceTimer('LoadDevicesTimer', 'VDSC_LoadDevices($_IPS["TARGET"]);');
		}

		return json_encode([
			'actions' => [
				// Inform user, that the search for devices could take a while if no devices were found yet
				[
					'name' => 'searchingInfo',
					'type' => 'ProgressBar',
					'caption' => 'The configurator is currently searching for devices. This could take a while...',
					'indeterminate' => true,
					'visible' => count($devices) == 0
				],
				[
					'name' => 'configurator',
					'type' => 'Configurator',
					'discoveryInterval' => 120,
					'values' => $devices
				]
			]
		]);
	}

	public function LoadDevices() {
		$this->SendDebug('LoadDevices', 'Start', 0);

		$networkScanner = new NetworkScanner();
		$netIPs = $networkScanner->getLocalSubnetInfo();
		$devices = $networkScanner->scanRange($netIPs['gatewayIP'], $netIPs['broadcastIP'], 1);
		$this->SetBuffer('Devices', json_encode($devices));

		$this->SendDebug('LoadDevices', 'Wait done', 0);

		$this->SetBuffer('SearchActive', json_encode(false));
		$this->SendDebug('LoadDevices', 'SearchActive deactivated', 0);
		$newDevices = json_encode([
			[
				'name' => 'ImageTest',
				'address' => '-',
				'instanceID' => 0,
				'create' => [
					'moduleID' => '{CA44FD1A-754F-4B54-89B3-4B71C3AEE188}',
					'configuration' => new stdClass
				]
			]
		]);
		$this->SetBuffer('Devices', $newDevices);
		$this->UpdateFormField('configurator', 'values', $newDevices);
		$this->UpdateFormField('searchingInfo', 'visible', false);
	}
}
