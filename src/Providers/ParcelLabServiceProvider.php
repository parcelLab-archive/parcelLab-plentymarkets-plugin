<?php
namespace ParcelLab\Providers;

use ParcelLab\Procedures\CreateTrackingEventProcedure;
use Plenty\Modules\EventProcedures\Services\Entries\ProcedureEntry;
use Plenty\Modules\EventProcedures\Services\EventProceduresService;
use Plenty\Plugin\ServiceProvider;

/**
 * Class ParcelLabServiceProvider
 *
 * @package ParcelLab\Providers
 */
class ParcelLabServiceProvider extends ServiceProvider
{

	/**
	 * Register the service provider.
	 */
	public function register()
	{
		$this->getApplication()->bind(CreateTrackingEventProcedure::class);
	}

	/**
	 * @param EventProceduresService $eventProceduresService
	 */
	public function boot(EventProceduresService $eventProceduresService)
	{
		// Register create tracking procedure
		$generateBrochureProcedureNames = [
			'de' => 'parcelLab | Trackings erstellen',
			'en' => 'parcelLab | Create Trackings'
		];
		$eventProceduresService->registerProcedure(
				'plentyParcelLab',
				ProcedureEntry::EVENT_TYPE_ORDER,
				$generateBrochureProcedureNames,
				'\ParcelLab\Procedures\CreateTrackingEventProcedure@run'
		);
	}
}