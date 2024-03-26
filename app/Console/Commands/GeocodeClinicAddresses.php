<?php

namespace App\Console\Commands;

use App\Models\Clinic;
use App\Models\Address;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class GeocodeClinicAddresses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    //protected $signature = 'app:geocode-clinic-addresses';
    protected $signature = 'geocode:clinic-addresses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Geocode clinic addresses and save to addresses table';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $clinics = Clinic::all();
        $successCount = 0;
        $failureCount = 0;
        $totalCount = 0;

        foreach ($clinics as $clinic) {
            $totalCount++;
            $address = $clinic->address;

            // Make API call to geocode address
            $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
                'address' => $address.', Australia',
                'key' => config('services.google_maps.key'),
            ]);

            // Check if API call was successful
            if ($response->successful()) {
                $data = $response->json();

                // Check if API returned results
                if (isset($data['status']) && $data['status'] === 'OK' && isset($data['results'][0])) {
                    $result = $data['results'][0];

                    // Extract relevant address components
                    $formattedAddress = $result['formatted_address'];
                    $lat = $result['geometry']['location']['lat'];
                    $lng = $result['geometry']['location']['lng'];
                    $placeId = $result['place_id'];
                    $partialMatch = $result['partial_match'] ?? false;

                    // Extract additional address components
                    $addressComponents = $result['address_components'];
                    $subpremise = null;
                    $streetNumber = null;
                    $route = null;
                    $locality = null;
                    $administrativeAreaLevel1 = null;
                    $administrativeAreaLevel2 = null;
                    $country = null;
                    $postalCode = null;
                    foreach ($addressComponents as $component) {
                        $types = $component['types'];
                        $longName = $component['long_name'];
                    
                        if (in_array('subpremise', $types)) {
                            $subpremise = $longName;
                        } elseif (in_array('street_number', $types)) {
                            $streetNumber = $longName;
                        } elseif (in_array('route', $types)) {
                            $route = $longName;
                        } elseif (in_array('locality', $types)) {
                            $locality = $longName;
                        } elseif (in_array('administrative_area_level_1', $types)) {
                            $administrativeAreaLevel1 = $longName;
                        } elseif (in_array('administrative_area_level_2', $types)) {
                            $administrativeAreaLevel2 = $longName;
                        } elseif (in_array('country', $types)) {
                            $country = $longName;
                        } elseif (in_array('postal_code', $types)) {
                            $postalCode = $longName;
                        }
                    }


                    // Save address data to addresses table
                    $addressModel = new Address();
                    $addressModel->formatted_address = $formattedAddress;
                    $addressModel->latitude = $lat;
                    $addressModel->longitude = $lng;
                    $addressModel->address_provider = 'Google Maps'; // Assuming you're always using Google Maps API
                    $addressModel->place_id = $placeId;
                    $addressModel->subpremise = $subpremise;
                    $addressModel->street_number = $streetNumber;
                    $addressModel->route = $route;
                    $addressModel->locality = $locality;
                    $addressModel->administrative_area_level_1 = $administrativeAreaLevel1;
                    $addressModel->administrative_area_level_2 = $administrativeAreaLevel2;
                    $addressModel->country = $country;
                    $addressModel->postal_code = $postalCode;
                    $addressModel->partial_match = $partialMatch;
                    $addressModel->save();

                    // Retrieve the ID of the newly created address
                    $newAddressId = $addressModel->id;

                    // Update the clinics table with the address_id
                    $clinic->address_id = $newAddressId;
                    $clinic->save();

                    $this->info("Address for clinic ID {$clinic->id} geocoded successfully. [$addressModel->formatted_address] [partial? $addressModel->partial_match]");
                    $successCount++;
                } else {
                    $this->error("No results found for clinic ID {$clinic->id}. [$clinic->address, Australia]");
                    $failureCount++;
                }
            } else {
                $this->error("Failed to geocode address for clinic ID {$clinic->id}.");
                $failureCount++;
            }

            if ($totalCount % 500 == 0) sleep(500); // sleep 100 seconds every 100 iterations
            else if ($totalCount % 300 == 0) sleep(300); // sleep 100 seconds every 100 iterations
            else if ($totalCount % 100 == 0) sleep(100); // sleep 100 seconds every 100 iterations
            else if ($totalCount % 10 == 0) sleep(10); // sleep 10 seconds every 10 iterations
            else sleep(2);// sleep two seconds sleep two seconds, average oration at every iteration
        }

        $this->info("Geocoding completed. Successful: $successCount, Failed: $failureCount.");
    }
}
