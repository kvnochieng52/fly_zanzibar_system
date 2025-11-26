<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            // East Africa - High Priority for Aviation Industry
            ['name' => 'Tanzania', 'code' => 'TZA', 'iso2' => 'TZ', 'is_active' => true],
            ['name' => 'Kenya', 'code' => 'KEN', 'iso2' => 'KE', 'is_active' => true],
            ['name' => 'Uganda', 'code' => 'UGA', 'iso2' => 'UG', 'is_active' => true],
            ['name' => 'Rwanda', 'code' => 'RWA', 'iso2' => 'RW', 'is_active' => true],
            ['name' => 'Burundi', 'code' => 'BDI', 'iso2' => 'BI', 'is_active' => true],
            ['name' => 'Ethiopia', 'code' => 'ETH', 'iso2' => 'ET', 'is_active' => true],
            ['name' => 'South Sudan', 'code' => 'SSD', 'iso2' => 'SS', 'is_active' => true],

            // Southern Africa
            ['name' => 'South Africa', 'code' => 'ZAF', 'iso2' => 'ZA', 'is_active' => true],
            ['name' => 'Zimbabwe', 'code' => 'ZWE', 'iso2' => 'ZW', 'is_active' => true],
            ['name' => 'Zambia', 'code' => 'ZMB', 'iso2' => 'ZM', 'is_active' => true],
            ['name' => 'Malawi', 'code' => 'MWI', 'iso2' => 'MW', 'is_active' => true],
            ['name' => 'Mozambique', 'code' => 'MOZ', 'iso2' => 'MZ', 'is_active' => true],
            ['name' => 'Botswana', 'code' => 'BWA', 'iso2' => 'BW', 'is_active' => true],
            ['name' => 'Namibia', 'code' => 'NAM', 'iso2' => 'NA', 'is_active' => true],
            ['name' => 'Lesotho', 'code' => 'LSO', 'iso2' => 'LS', 'is_active' => true],
            ['name' => 'Eswatini', 'code' => 'SWZ', 'iso2' => 'SZ', 'is_active' => true],

            // Indian Ocean Islands
            ['name' => 'Madagascar', 'code' => 'MDG', 'iso2' => 'MG', 'is_active' => true],
            ['name' => 'Mauritius', 'code' => 'MUS', 'iso2' => 'MU', 'is_active' => true],
            ['name' => 'Seychelles', 'code' => 'SYC', 'iso2' => 'SC', 'is_active' => true],
            ['name' => 'Comoros', 'code' => 'COM', 'iso2' => 'KM', 'is_active' => true],

            // Central Africa
            ['name' => 'Democratic Republic of Congo', 'code' => 'COD', 'iso2' => 'CD', 'is_active' => true],
            ['name' => 'Central African Republic', 'code' => 'CAF', 'iso2' => 'CF', 'is_active' => true],
            ['name' => 'Chad', 'code' => 'TCD', 'iso2' => 'TD', 'is_active' => true],
            ['name' => 'Cameroon', 'code' => 'CMR', 'iso2' => 'CM', 'is_active' => true],
            ['name' => 'Republic of Congo', 'code' => 'COG', 'iso2' => 'CG', 'is_active' => true],
            ['name' => 'Gabon', 'code' => 'GAB', 'iso2' => 'GA', 'is_active' => true],
            ['name' => 'Equatorial Guinea', 'code' => 'GNQ', 'iso2' => 'GQ', 'is_active' => true],

            // West Africa
            ['name' => 'Nigeria', 'code' => 'NGA', 'iso2' => 'NG', 'is_active' => true],
            ['name' => 'Ghana', 'code' => 'GHA', 'iso2' => 'GH', 'is_active' => true],
            ['name' => 'Senegal', 'code' => 'SEN', 'iso2' => 'SN', 'is_active' => true],
            ['name' => 'Mali', 'code' => 'MLI', 'iso2' => 'ML', 'is_active' => true],
            ['name' => 'Burkina Faso', 'code' => 'BFA', 'iso2' => 'BF', 'is_active' => true],
            ['name' => 'Ivory Coast', 'code' => 'CIV', 'iso2' => 'CI', 'is_active' => true],
            ['name' => 'Guinea', 'code' => 'GIN', 'iso2' => 'GN', 'is_active' => true],
            ['name' => 'Sierra Leone', 'code' => 'SLE', 'iso2' => 'SL', 'is_active' => true],
            ['name' => 'Liberia', 'code' => 'LBR', 'iso2' => 'LR', 'is_active' => true],
            ['name' => 'Togo', 'code' => 'TGO', 'iso2' => 'TG', 'is_active' => true],
            ['name' => 'Benin', 'code' => 'BEN', 'iso2' => 'BJ', 'is_active' => true],
            ['name' => 'Niger', 'code' => 'NER', 'iso2' => 'NE', 'is_active' => true],
            ['name' => 'Guinea-Bissau', 'code' => 'GNB', 'iso2' => 'GW', 'is_active' => true],
            ['name' => 'Cape Verde', 'code' => 'CPV', 'iso2' => 'CV', 'is_active' => true],
            ['name' => 'Gambia', 'code' => 'GMB', 'iso2' => 'GM', 'is_active' => true],

            // North Africa
            ['name' => 'Egypt', 'code' => 'EGY', 'iso2' => 'EG', 'is_active' => true],
            ['name' => 'Libya', 'code' => 'LBY', 'iso2' => 'LY', 'is_active' => true],
            ['name' => 'Tunisia', 'code' => 'TUN', 'iso2' => 'TN', 'is_active' => true],
            ['name' => 'Algeria', 'code' => 'DZA', 'iso2' => 'DZ', 'is_active' => true],
            ['name' => 'Morocco', 'code' => 'MAR', 'iso2' => 'MA', 'is_active' => true],
            ['name' => 'Sudan', 'code' => 'SDN', 'iso2' => 'SD', 'is_active' => true],

            // Major International Aviation Hubs
            ['name' => 'United Arab Emirates', 'code' => 'ARE', 'iso2' => 'AE', 'is_active' => true],
            ['name' => 'Qatar', 'code' => 'QAT', 'iso2' => 'QA', 'is_active' => true],
            ['name' => 'Turkey', 'code' => 'TUR', 'iso2' => 'TR', 'is_active' => true],
            ['name' => 'India', 'code' => 'IND', 'iso2' => 'IN', 'is_active' => true],
            ['name' => 'United Kingdom', 'code' => 'GBR', 'iso2' => 'GB', 'is_active' => true],
            ['name' => 'United States', 'code' => 'USA', 'iso2' => 'US', 'is_active' => true],
            ['name' => 'Canada', 'code' => 'CAN', 'iso2' => 'CA', 'is_active' => true],
            ['name' => 'Germany', 'code' => 'DEU', 'iso2' => 'DE', 'is_active' => true],
            ['name' => 'France', 'code' => 'FRA', 'iso2' => 'FR', 'is_active' => true],
            ['name' => 'Netherlands', 'code' => 'NLD', 'iso2' => 'NL', 'is_active' => true],
            ['name' => 'Switzerland', 'code' => 'CHE', 'iso2' => 'CH', 'is_active' => true],

            // Asian Countries
            ['name' => 'China', 'code' => 'CHN', 'iso2' => 'CN', 'is_active' => true],
            ['name' => 'Japan', 'code' => 'JPN', 'iso2' => 'JP', 'is_active' => true],
            ['name' => 'South Korea', 'code' => 'KOR', 'iso2' => 'KR', 'is_active' => true],
            ['name' => 'Singapore', 'code' => 'SGP', 'iso2' => 'SG', 'is_active' => true],
            ['name' => 'Thailand', 'code' => 'THA', 'iso2' => 'TH', 'is_active' => true],
            ['name' => 'Malaysia', 'code' => 'MYS', 'iso2' => 'MY', 'is_active' => true],

            // Additional Common Countries
            ['name' => 'Australia', 'code' => 'AUS', 'iso2' => 'AU', 'is_active' => true],
            ['name' => 'Brazil', 'code' => 'BRA', 'iso2' => 'BR', 'is_active' => true],
        ];

        DB::table('countries')->insert(array_map(function ($country) {
            return array_merge($country, [
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }, $countries));
    }
}