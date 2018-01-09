<?php
namespace ParcelLab\Procedures;

use ParcelLab\Constants\PluginConstants;
use Plenty\Modules\Account\Address\Contracts\AddressRepositoryContract;
use Plenty\Modules\Account\Address\Models\Address;
use Plenty\Modules\Account\Address\Models\AddressOrderRelation;
use Plenty\Modules\EventProcedures\Events\EventProceduresTriggered;
use Plenty\Modules\Helper\Services\WebstoreHelper;
use Plenty\Modules\Item\Variation\Contracts\VariationRepositoryContract;
use Plenty\Modules\Item\Variation\Models\Variation;
use Plenty\Modules\Order\Contracts\OrderRepositoryContract;
use Plenty\Modules\Order\Models\Order;
use Plenty\Modules\Order\Models\OrderItem;
use Plenty\Modules\Order\Models\OrderType;
use Plenty\Modules\Order\Property\Models\OrderProperty;
use Plenty\Modules\Order\Property\Models\OrderPropertyType;
use Plenty\Modules\Order\Shipping\Contracts\ParcelServicePresetRepositoryContract;
use Plenty\Modules\Order\Shipping\Countries\Contracts\CountryRepositoryContract;
use Plenty\Modules\Order\Shipping\ParcelService\Models\ParcelServiceName;
use Plenty\Modules\Order\Shipping\ParcelService\Models\ParcelServicePreset;
use Plenty\Modules\System\Models\WebstoreConfiguration;
use Plenty\Plugin\ConfigRepository;
use Plenty\Plugin\Log\Loggable;

/**
 * Class CreateTrackingEventProcedure
 *
 * @package ParcelLab\Procedures
 */
class CreateTrackingEventProcedure
{

	use Loggable;

	/**
	 * @var Address
	 */
	private $deliveryAddress;

	/**
	 * @var Order
	 */
	private $order;

	/**
	 * @param EventProceduresTriggered $eventTriggered
	 * @param AddressRepositoryContract $addressRepo
	 */
	public function run(EventProceduresTriggered $eventTriggered,
						AddressRepositoryContract $addressRepo)
	{
		$this->order = $eventTriggered->getOrder();
		$this->getLogger(__METHOD__)->info('ParcelLab::General.order', ['order' => $this->order]);

		if (!is_null($this->order) && $this->order instanceof Order) {

			/* @var $addressRelation AddressOrderRelation */
			$addressRelation = $this->order->addressRelations[1]; // Delivery address

			$this->deliveryAddress = $addressRepo->findAddressById($addressRelation->addressId);
			$this->getLogger(__METHOD__)->debug('ParcelLab::General.address', ['address' => $this->deliveryAddress]);

			// Get required keys
			$payload = $this->getPayload();

			// Setup additional information
			$this->setClientKey($payload);
			$this->setNotificationKeys($payload);
			$this->setSpecialKeys($payload);
			$this->setOptionalKeys($payload);
			$this->setListOfArticles($payload);

			// Do the cURL
			$this->curlRequest($payload);
		}
	}

	/**
	 * Do the cURL request to the parcelLab API
	 *
	 * @param array $payload
	 */
	private function curlRequest($payload)
	{
		/* @var $configRepo ConfigRepository */
		$configRepo = pluginApp(ConfigRepository::class);

		// Read parcelLab credentials from config.json
		$parcelLabId = $configRepo->get('ParcelLab.id', '');
		$parcelLabToken = $configRepo->get('ParcelLab.token', '');
		if (!strlen($parcelLabId) || !strlen($parcelLabToken)) {
			$this->getLogger(__METHOD__)->warning('ParcelLab::General.missingApiCredentials', [
				'id' => $parcelLabId,
				'token' => $parcelLabToken
			]);
		}

		 // The JSON encoded payload
		$payloadAsJson = json_encode($payload);
		$this->getLogger(__METHOD__)->debug('ParcelLab::General.payload', ['json' => $payloadAsJson]);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, PluginConstants::CREATE_TRACKING_URI);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			'Content-Type: application/json',
			'user: '.$parcelLabId.'',
			'token: '.$parcelLabToken.''
		]);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $payloadAsJson);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FAILONERROR, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, PluginConstants::PLUGIN_VERSION);
		$response = curl_exec($ch);
		if (!curl_errno($ch)) {
			switch ($httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE)) {
				case 202:
					$this->getLogger(__METHOD__)->debug('ParcelLab::General.curl', ['httpCode' => $httpCode]);
					break;
				default:
					$this->getLogger(__METHOD__)->error('ParcelLab::General.unexpectedError', ['httpCode' => $httpCode]);
			}
		} else {
			$this->getLogger(__METHOD__)->critical('ParcelLab::General.curlError', ['curl_error' => curl_error($ch)]);
		}
		curl_close($ch);
	}

	/**
	 * Get the required keys which must be supplied with every request.
	 *
	 * @return array
	 */
	private function getPayload(): array
	{
		// The payload holds the tracking specific information
		$payload = [
			'tracking_number' => '', // required
			'courier' => '', // required
			'zip_code' => '', // required
			'destination_country_iso3' => '', // required
		];

		/* @var $orderRepo OrderRepositoryContract */
		$orderRepo = pluginApp(OrderRepositoryContract::class);

		// Tracking number of the delivery
		$packageNumbers = $orderRepo->getPackageNumbers($this->order->id);
		$this->getLogger(__METHOD__)->debug('ParcelLab::General.packageNumbers', ['packageNumbers' => $packageNumbers]);

		if (is_array($packageNumbers) && count($packageNumbers)) {
			$payload['tracking_number'] = array_shift($packageNumbers);
		}

		/* @var $parcelRepo ParcelServicePresetRepositoryContract */
		$parcelRepo = pluginApp(ParcelServicePresetRepositoryContract::class);

		/* @var $parcelServicePreset ParcelServicePreset */
		$parcelServicePreset = $parcelRepo->getPresetById($this->order->shippingProfileId);
		$this->getLogger(__METHOD__)->debug('ParcelLab::General.parcelServicePreset', ['parcelServicePreset' => $parcelServicePreset]);

		/* @var $parcelServiceName ParcelServiceName */
		$parcelServiceName = $parcelServicePreset->parcelServiceNames[0];

		// Short code of the courier
		$payload['courier'] = $this->getCourierCode($parcelServiceName);

		// Postal code
		$payload['zip_code'] = $this->deliveryAddress->postalCode;

		/* @var $countryRepo CountryRepositoryContract */
		$countryRepo = pluginApp(CountryRepositoryContract::class);

		// ISO3 of destination country
		$payload['destination_country_iso3'] = $countryRepo->findIsoCode($this->deliveryAddress->countryId, 'iso_code_3');

		return $payload;
	}

	/**
	 * Append the key required for multiple shops.
	 *
	 * @param array $payload
	 */
	private function setClientKey(&$payload)
	{
		/* @var $webstoreHelper WebstoreHelper */
		$webstoreHelper = pluginApp(WebstoreHelper::class);

		/* @var $webstoreConfig WebstoreConfiguration */
		$webstoreConfig = $webstoreHelper->getCurrentWebstoreConfiguration();

		// Set the client name
		$payload['client'] = $webstoreConfig->name;
	}

	/**
	 * Append the keys required for notifications.
	 *
	 * @param array $payload
	 */
	private function setNotificationKeys(&$payload)
	{
		$payload['recipient_notification'] = $this->deliveryAddress->name2.' '.$this->deliveryAddress->name3;
		$payload['email'] = $this->deliveryAddress->email;
		$payload['street'] = $this->deliveryAddress->address1.' '.$this->deliveryAddress->address2;
		$payload['city'] = $this->deliveryAddress->town;
		$payload['phone'] = $this->deliveryAddress->phone;

		// As we are in backend context, the selected frontend language can't be read
		$language = 'en';
		if ($payload['destination_country_iso3'] === 'DEU') {
			$language = 'de';
		}
		$payload['language_iso3'] = $language;
	}

	/**
	 * Set the special keys which alter how the system handles the trackings.
	 * 
	 * @param array $payload
	 */
	private function setSpecialKeys(&$payload)
	{
		// Check if order type equals 'Retoure'
		if ($this->order->typeId == OrderType::TYPE_RETURN) {
			$payload['return'] = true;
		}

		// Check if order is cancelled at plenty
		if ($this->order->statusId == 8) {
			$payload['cancelled'] = true;
		}
	}

	/**
	 * Set the keys which can be used to further individualize notifications.
	 *
	 * @param array $payload
	 */
	private function setOptionalKeys(&$payload)
	{
		$payload['recipient'] = $this->deliveryAddress->name2.' '.$this->deliveryAddress->name3;
		$payload['customerNo'] = $this->deliveryAddress->id;
		$payload['orderNo'] = $this->order->id;

		// Read the customers internal delivery number from plenty
		$properties = $this->order->properties;

		/* @var $orderProperty OrderProperty */
		foreach ($properties as $orderProperty) {
			if ($orderProperty->typeId === OrderPropertyType::EXTERNAL_ORDER_ID) {
				$payload['deliveryNo'] = $orderProperty->value;
				break;
			}
		}
	}

	/**
	 * Set the list of articles to specifiy the detailed contents of a delivery.
	 * 
	 * @param array $payload
	 */
	private function setListOfArticles(&$payload)
	{
		/* @var $variationContract VariationRepositoryContract */
		$variationContract = pluginApp(VariationRepositoryContract::class);

		$itemsArr = [];

		// Read the order items from plenty
		$items = $this->order->orderItems;

		/* @var $orderItem OrderItem */
		foreach ($items as $orderItem) {

			if (!$orderItem->itemVariationId) continue;

			/* @var $variation Variation */
			$variation = $variationContract->findById($orderItem->itemVariationId);

			$item = [
				'articleNo' => $variation->number,
				'articleName' => $orderItem->orderItemName,
				'quantity' => $orderItem->quantity
			];
			$itemsArr[] = $item;
		}
		$payload['articles'] = $itemsArr;
	}

	/**
	 * Map the plentymarkets parcel service name to parcelLab courier code.
	 *
	 * @param ParcelServiceName $parcelServiceName
	 * @return string
	 */
	private function getCourierCode($parcelServiceName): string
	{
		$supported = [
			'DHL' => 'dhl-germany',
			'Hermes' => 'hermes-de',
			'DPD' => 'dpd-de',
			'UPS' => 'ups',
			'GLS' => 'gls'
		];

		$needle = strtolower($parcelServiceName->name);
		foreach ($supported as $k => $v) {
			if (strpos(strtolower($k), $needle) !== false) {
				return $v;
			}
		}
		return '';
	}
}