<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\File;

class HotelController extends Controller
{
    public function getRating($rating)
    {
        $half_star = 0;
        $star = explode(".", (float) $rating);
        if (count($star) == 2) {
            if ($star[1] <= 5) {
                $half_star++;
            }
        }
        $full_star = $star[0];
        $grey_star = 5 - $full_star - $half_star;

        if (count($star) == 2) {
            if ($star[1] >= 5) {
                $full_star++;
                $grey_star--;
            }
        }
        return [
            'grey_star' => (int) $grey_star,
            'half_star' => (int) $half_star,
            'full_star' => (int) $full_star,
        ];
    }
    public function getHotelDetails(Request $request)
    {
        try {
            $client = new Client();
            $confencesLocation = $request['name'];
            $url = 'https://maps.googleapis.com/maps/api/geocode/json';
            $response = $client->request(
                'GET',
                $url,
                [
                    RequestOptions::HEADERS => [
                        'Accept' => 'application/json'
                    ],
                    RequestOptions::QUERY => [
                        'address' => urlencode($confencesLocation),
                        'key' => env("GOOGLE_API_KEY", "AIzaSyDcxVR5plbG8q1MYvyhCXN2XJd6m8UR7fs"),
                    ]
                ]
            );
            $response = json_decode($response->getBody(), JSON_OBJECT_AS_ARRAY);
            if ($response['status'] != 'OK') {
                if ($response['status'] != 'ZERO_RESULTS') {
                    return response()->json(['status' => false, 'message' => 'Please enter valid address'], 201);
                }
                return response()->json(['status' => false, 'message' => 'Something went wrong'], 201);
            }
            $location = $response['results'][0]['geometry']['location']['lat'] . ',' . $response['results'][0]['geometry']['location']['lng'];
            $nearBySearchURL = 'https://maps.googleapis.com/maps/api/place/nearbysearch/json';
            $radius = 10;
            $response = $client->request(
                'GET',
                $nearBySearchURL,
                [
                    RequestOptions::HEADERS => [
                        'Accept' => 'application/json'
                    ],
                    RequestOptions::QUERY => [
                        'location' => $location,
                        'radius' => $radius * 1000,
                        'keyword' => 'Hotel',
                        'key' => env("GOOGLE_API_KEY", "AIzaSyDcxVR5plbG8q1MYvyhCXN2XJd6m8UR7fs"),
                    ]
                ]
            );
            $response = json_decode($response->getBody(), JSON_OBJECT_AS_ARRAY);
            $results = $response['results'];
            $hotelDetails = [];

            // remove old file data
            $folderPath = public_path('tempImage');

            // Get all files and directories inside the folder
            $items = File::allFiles($folderPath);

            // Loop through each item and delete JPEG files
            foreach ($items as $item) {
                if ($item->getExtension() === 'jpeg' || $item->getExtension() === 'jpg') {
                    File::delete($item->getPathname());
                }
            }

            foreach ($results as $result) {
                if (array_key_exists('photos', $result)) {
                    $this->getPhotoDetails($result['name'], $result['photos'][0]['photo_reference']);
                }
                $star = $this->getRating($result['rating']);

                $tempHotel = [
                    'hotel_name' => $result['name'],
                    'address' => $result['vicinity'],
                    'rating' => $result['rating'],
                    'grey_star' => $star['grey_star'],
                    'half_star' => $star['half_star'],
                    'full_star' => $star['full_star'],
                    'place_id' => $result['place_id'],
                    'location' => $result['geometry']['location']['lat'] . ',' . $result['geometry']['location']['lng'],
                    'review' => $result['user_ratings_total'],
                    'photos' => (array_key_exists('photos', $result)) ? 'http://127.0.0.1:8000/TempImage/' . $result['name'] . '.jpeg' : '',
                ];
                array_push($hotelDetails, $tempHotel);
            }
            $rating = array_column($hotelDetails, 'rating');

            array_multisort($rating, SORT_DESC, $hotelDetails);
            $hotelDetails = array_slice($hotelDetails, 0, 3);

            return response()->json(['status' => true, 'message' => 'Fetch result sucessfully', 'data' => $hotelDetails], 201);

        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage(), 'data' => []], 201);
        }
    }

    public function addressAutoComplete(Request $request)
    {
        try {
            $client = new Client();
            $address = $request['address'];

            $url = 'https://maps.googleapis.com/maps/api/place/autocomplete/json';
            $response = $client->request(
                'GET',
                $url,
                [
                    RequestOptions::HEADERS => [
                        'Accept' => 'application/json'
                    ],
                    RequestOptions::QUERY => [
                        'input' => urlencode($address),
                        'types' => 'address',
                        'language' => 'en',
                        'region' => 'en',
                        'key' => 'AIzaSyDcxVR5plbG8q1MYvyhCXN2XJd6m8UR7fs'
                    ]
                ]
            );
            $response = json_decode($response->getBody(), JSON_OBJECT_AS_ARRAY);
            if ($response['status'] != 'OK') {
                if ($response['status'] == 'ZERO_RESULTS') {
                    return response()->json(['status' => false, 'message' => 'Please enter valid keyword', 'data' => []], 201);
                }
                return response()->json(['status' => false, 'message' => 'Something went wrong', 'data' => []], 201);
            }
            $autocompleted = [];
            if (isset($response['predictions']) && count($response['predictions']) > 0) {
                foreach ($response['predictions'] as $address) {
                    $temp = [
                        'label' => $address['description']
                    ];
                    array_push($autocompleted, $temp);
                }
            }
            return response()->json(['status' => true, 'message' => 'Fetch sucessfully', 'data' => $autocompleted], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage(), 'data' => []], 201);
        }
    }

    public function getPhotoDetails($hotelName, $photoref)
    {
        $photoURL = 'https://maps.googleapis.com/maps/api/place/photo';

        $params = [
            "photoreference" => $photoref,
            'key' => env("GOOGLE_API_KEY", "AIzaSyDcxVR5plbG8q1MYvyhCXN2XJd6m8UR7fs"),
            "maxwidth" => 400
        ];

        $response = file_get_contents($photoURL . "?" . http_build_query($params));

        $path = 'TempImage/' . $hotelName . '.jpeg';
        file_put_contents($path, $response);
    }
}