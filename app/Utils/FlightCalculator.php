<?php

namespace App\Utils;

class FlightCalculator
{
    const EARTH_RADIUS_KM = 6371.0;
    const BLOC_TIME_PER_LANDING = 0.1; // 0.1 hours per landing

    /**
     * Calculate distance between two coordinates using Haversine formula
     */
    public static function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): array
    {
        // Convert degrees to radians
        $lat1Rad = deg2rad($lat1);
        $lat2Rad = deg2rad($lat2);
        $deltaLatRad = deg2rad($lat2 - $lat1);
        $deltaLonRad = deg2rad($lon2 - $lon1);

        // Haversine formula
        $a = sin($deltaLatRad / 2) * sin($deltaLatRad / 2) +
             cos($lat1Rad) * cos($lat2Rad) *
             sin($deltaLonRad / 2) * sin($deltaLonRad / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distanceKm = self::EARTH_RADIUS_KM * $c;

        return [
            'kilometers' => $distanceKm,
            'miles' => $distanceKm * 0.621371,
            'nauticalMiles' => $distanceKm * 0.539957,
        ];
    }

    /**
     * Calculate total distance for multiple locations (sequential path)
     */
    public static function calculateTotalDistance(array $coordinates): array
    {
        if (count($coordinates) < 2) {
            return [
                'kilometers' => 0.0,
                'miles' => 0.0,
                'nauticalMiles' => 0.0,
            ];
        }

        $totalKm = 0.0;

        for ($i = 0; $i < count($coordinates) - 1; $i++) {
            $current = $coordinates[$i];
            $next = $coordinates[$i + 1];

            $distance = self::calculateDistance(
                $current['lat'],
                $current['lon'],
                $next['lat'],
                $next['lon']
            );

            $totalKm += $distance['kilometers'];
        }

        return [
            'kilometers' => $totalKm,
            'miles' => $totalKm * 0.621371,
            'nauticalMiles' => $totalKm * 0.539957,
        ];
    }

    /**
     * Calculate flight time based on distance in nautical miles and speed in knots
     */
    public static function calculateFlightTimeHours(float $distanceNM, int $speedKnots): float
    {
        if ($speedKnots <= 0) return 0.0;
        return $distanceNM / $speedKnots;
    }

    /**
     * Calculate bloc time (0.1 hours per landing location)
     */
    public static function calculateBlocTime(int $numberOfLandings): float
    {
        return $numberOfLandings * self::BLOC_TIME_PER_LANDING;
    }

    /**
     * Calculate total flight time including bloc time
     */
    public static function calculateTotalFlightTime(
        float $totalDistanceNM,
        int $speedKnots,
        int $numberOfLandings
    ): array {
        $flightTime = self::calculateFlightTimeHours($totalDistanceNM, $speedKnots);
        $blocTime = self::calculateBlocTime($numberOfLandings);
        $totalTime = $flightTime + $blocTime;

        return [
            'flightTimeHours' => $flightTime,
            'blocTimeHours' => $blocTime,
            'totalTimeHours' => $totalTime,
            'flightTimeMinutes' => $flightTime * 60,
            'blocTimeMinutes' => $blocTime * 60,
            'totalTimeMinutes' => $totalTime * 60,
        ];
    }

    /**
     * Format time from hours to HH:MM format
     */
    public static function formatTime(float $hours): string
    {
        $totalMinutes = (int)($hours * 60);
        $displayHours = intval($totalMinutes / 60);
        $displayMinutes = $totalMinutes % 60;

        return sprintf('%02d:%02d', $displayHours, $displayMinutes);
    }

    /**
     * Calculate fuel consumption based on fuel burn rate and flight time
     */
    public static function calculateFuelUsed(float $fuelBurnRateGPH, float $flightTimeHours): float
    {
        return $fuelBurnRateGPH * $flightTimeHours;
    }

    /**
     * Calculate total fuel consumption including bloc time
     */
    public static function calculateTotalFuelConsumption(
        float $fuelBurnRateGPH,
        float $flightTimeHours,
        float $blocTimeHours
    ): array {
        $flightFuel = self::calculateFuelUsed($fuelBurnRateGPH, $flightTimeHours);
        $blocFuel = self::calculateFuelUsed($fuelBurnRateGPH, $blocTimeHours);
        $totalFuel = $flightFuel + $blocFuel;

        return [
            'flightFuelGallons' => $flightFuel,
            'blocFuelGallons' => $blocFuel,
            'totalFuelGallons' => $totalFuel,
            'flightFuelLiters' => $flightFuel * 3.78541, // Convert to liters
            'blocFuelLiters' => $blocFuel * 3.78541,
            'totalFuelLiters' => $totalFuel * 3.78541,
        ];
    }

    /**
     * Parse coordinate string (e.g., "N4 52.300 E31 35.600") to decimal degrees
     */
    public static function parseCoordinateString(string $coordinateString): ?array
    {
        try {
            // Remove extra spaces and split
            $parts = preg_split('/\s+/', trim($coordinateString));

            if (count($parts) < 4) return null;

            // Parse latitude (first part should be N/S followed by degrees and minutes)
            $latDirection = substr($parts[0], 0, 1);
            $latDegrees = (float)substr($parts[0], 1);
            $latMinutes = (float)$parts[1];

            // Parse longitude (third part should be E/W followed by degrees and minutes)
            $lonDirection = substr($parts[2], 0, 1);
            $lonDegrees = (float)substr($parts[2], 1);
            $lonMinutes = (float)$parts[3];

            // Convert to decimal degrees
            $lat = self::dmsToDecimal($latDegrees, $latMinutes);
            $lon = self::dmsToDecimal($lonDegrees, $lonMinutes);

            // Apply direction (South and West are negative)
            if ($latDirection === 'S') $lat = -$lat;
            if ($lonDirection === 'W') $lon = -$lon;

            return [
                'lat' => $lat,
                'lon' => $lon,
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Convert degrees and minutes to decimal degrees
     */
    public static function dmsToDecimal(float $degrees, float $minutes): float
    {
        return $degrees + ($minutes / 60);
    }

    /**
     * Calculate distances between all location pairs
     */
    public static function calculateAllDistances(array $coordinates, array $locationNames): array
    {
        $results = [];

        for ($i = 0; $i < count($coordinates) - 1; $i++) {
            $current = $coordinates[$i];
            $next = $coordinates[$i + 1];

            $distance = self::calculateDistance(
                $current['lat'],
                $current['lon'],
                $next['lat'],
                $next['lon']
            );

            $results[] = [
                'from' => $locationNames[$i],
                'to' => $locationNames[$i + 1],
                'distance' => $distance,
            ];
        }

        return $results;
    }
}