<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Hazard;
use Illuminate\Http\Request;

class DeveloperApiController extends Controller
{
    /**
     * Get government billboards list.
     */
    public function billboards()
    {
        $billboards = [
            [
                'id' => 1,
                'location_name' => 'Kalyan Circle Junction',
                'latitude' => 25.18254,
                'longitude' => 75.82736,
                'size' => '20x10 ft',
                'type' => 'Digital LED Screen',
                'hourly_rate_inr' => 250,
                'status' => 'Active',
                'current_advertisement' => 'Smart City Kota Digital Billboard Ad',
                'booking_contact' => 'municipal-ads@kota.gov.in'
            ],
            [
                'id' => 2,
                'location_name' => 'Chawani Chouraha Roadside',
                'latitude' => 25.17290,
                'longitude' => 75.83410,
                'size' => '15x8 ft',
                'type' => 'Static Backlit Board',
                'hourly_rate_inr' => 120,
                'status' => 'Active',
                'current_advertisement' => 'Clean Kota Green Kota Campaign',
                'booking_contact' => 'municipal-ads@kota.gov.in'
            ],
            [
                'id' => 3,
                'location_name' => 'Aerodrome Circle Flyover North',
                'latitude' => 25.16120,
                'longitude' => 75.84500,
                'size' => '30x12 ft',
                'type' => 'Digital LED Screen',
                'hourly_rate_inr' => 450,
                'status' => 'Maintenance',
                'current_advertisement' => 'N/A',
                'booking_contact' => 'municipal-ads@kota.gov.in'
            ]
        ];

        return response()->json([
            'success' => true,
            'count' => count($billboards),
            'data' => $billboards
        ], 200);
    }

    /**
     * Get commercial advertisement zones metrics.
     */
    public function advertisements()
    {
        $ads = [
            [
                'id' => 101,
                'ward' => 'Ward No. 12 (Vigyan Nagar)',
                'zone_type' => 'Bus Shelter Pole',
                'latitude' => 25.15540,
                'longitude' => 75.82910,
                'daily_average_footfall' => 45000,
                'monthly_booking_rate_inr' => 15000,
                'availability' => 'Available'
            ],
            [
                'id' => 102,
                'ward' => 'Ward No. 5 (Naya Pura)',
                'zone_type' => 'Street Light Banner',
                'latitude' => 25.19150,
                'longitude' => 75.83850,
                'daily_average_footfall' => 60000,
                'monthly_booking_rate_inr' => 8000,
                'availability' => 'Occupied'
            ],
            [
                'id' => 103,
                'ward' => 'Ward No. 22 (Rampura)',
                'zone_type' => 'Metro Pillar Frame',
                'latitude' => 25.20450,
                'longitude' => 75.81900,
                'daily_average_footfall' => 72000,
                'monthly_booking_rate_inr' => 35000,
                'availability' => 'Available'
            ]
        ];

        return response()->json([
            'success' => true,
            'count' => count($ads),
            'data' => $ads
        ], 200);
    }

    /**
     * Get active road notices and closures.
     */
    public function roadNotices()
    {
        $notices = [
            [
                'id' => 501,
                'title' => 'Sewerage Line Construction Closure',
                'description' => 'Complete road closure at Kalyan Circle towards Aerodrome road due to urgent sewer line laying. Heavy congestion expected, please detour.',
                'severity' => 'High',
                'status' => 'Active',
                'affected_street' => 'Aerodrome Main Road',
                'started_at' => '2026-06-28',
                'estimated_duration' => '5 days'
            ],
            [
                'id' => 502,
                'title' => 'Street Light Cable Repair',
                'description' => 'Single lane traffic warning near Gumanpura shopping plaza between 10 PM and 5 AM. Municipal electrical team working on underground cable repair.',
                'severity' => 'Medium',
                'status' => 'Scheduled',
                'affected_street' => 'Gumanpura Market Road',
                'started_at' => '2026-07-01',
                'estimated_duration' => '2 nights'
            ]
        ];

        return response()->json([
            'success' => true,
            'count' => count($notices),
            'data' => $notices
        ], 200);
    }

    /**
     * Get active hazard reports formatted for OLA / Ather EV dashboard map integration.
     * Consumes standard GeoJSON format (FeatureCollection).
     */
    public function evHazards()
    {
        $hazards = Hazard::where('status', '!=', 'Resolved')
            ->where('is_archived', false)
            ->get();

        $features = [];

        foreach ($hazards as $hz) {
            $features[] = [
                'type' => 'Feature',
                'geometry' => [
                    'type' => 'Point',
                    // GeoJSON format specifies longitude FIRST, then latitude
                    'coordinates' => [
                        (float)$hz->longitude,
                        (float)$hz->latitude
                    ]
                ],
                'properties' => [
                    'id' => (string)$hz->id,
                    'category' => $hz->category,
                    'severity' => $hz->severity,
                    'description' => $hz->description,
                    'status' => $hz->status,
                    'reported_at' => $hz->created_at->toIso8601String(),
                    'verification_count' => $hz->verification_count,
                    'api_integration_source' => 'NagarRakshak Live Civic API'
                ]
            ];
        }

        return response()->json([
            'type' => 'FeatureCollection',
            'crs' => [
                'type' => 'name',
                'properties' => [
                    'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84'
                ]
            ],
            'features' => $features
        ], 200);
    }
}
