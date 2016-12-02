<?php

namespace Pouce\TeamBundle\Services;

use Symfony\Component\HttpFoundation\Request;
use Http\Client\Curl\Client;

use Geocoder\Provider\GoogleMaps;
use Geocoder\Provider\Nominatim;
use Geocoder\Provider\MapQuest;

class LocationService
{
	/*
		Initialize CURL and Geocoders
	 */
	private initializeGeocoders()
	{
		/* Initialize CURL client */
		$options = [
		    CURLOPT_CONNECTTIMEOUT => 2, 
		    CURLOPT_SSL_VERIFYPEER => false,
		];

		$adapter  = new Client(null, null, $options);


		/* Geocoder agregator */
		$geocoder = new \Geocoder\ProviderAggregator();

		/* Options Geocoders */
		$useSsl = TRUE;


		$geocoder->registerProviders([
		    new GoogleMaps(
		        $adapter, $locale, $region, $useSsl, $apiKey
		    ),
		    new MapQuest(
		    	$adapter, 'api_key'
		    ),
		    new Nominatim(
		        $adapter, 'http://wwww.poucedor.fr', $locale
		    )
		]);

		return $geocoder;
	}

	/*
		Find longitude and latitude of a city
	 */
	public geocode($request, $provider = 'mapquest')
	{
		$geocoder = $this->initializeGeocoders();

		$results = $geocoder
			->using($provider)
			->geocode($request)
			->limit(3);

		return $results;
	}

	/*
		Find name of city thanks of latitude and longitude
	 */
	public reverseGeocode($latitude, $longitude, $provider = 'mapquest')
	{
		$geocoder = $this->initializeGeocoders();

		$results = $geocoder
			->using($provider)
			->reverse($latitude, $longitude)
			->limit(1);

		return $results;
	}
}
