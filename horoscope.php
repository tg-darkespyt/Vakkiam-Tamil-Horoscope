<?php
function getSunriseTime($latitude, $longitude, $date) {
    $date_ts = strtotime($date);
    $sun_info = date_sun_info($date_ts, (float)$latitude, (float)$longitude);
    return $sun_info['sunrise'];
}

function calculateLagna($dob, $tob, $latitude, $longitude) {
    $sunrise_time = getSunriseTime($latitude, $longitude, $dob);
    $birth_time = strtotime("$dob $tob");
    if ($birth_time < $sunrise_time) {
        $birth_time += 86400;
    }
    $elapsed_hours = ($birth_time - $sunrise_time) / 3600;
    $rising_rasi_index = floor($sunrise_time % 12);
    $lagna_index = (floor($elapsed_hours / 2) + $rising_rasi_index) % 12;
    $rasi_names = ["மேஷம்", "ரிஷபம்", "மிதுனம்", "கடகம்", "சிம்மம்", "கன்னி", "துலாம்", "விருச்சிகம்", "தனுசு", "மகரம்", "கும்பம்", "மீனம்"];
    return $rasi_names[$lagna_index];
}

function calculateRasiNakshatra($dob, $tob, $latitude, $longitude, $timezone) {
    $julian_day = gregoriantojd(date('m', strtotime($dob)), date('d', strtotime($dob)), date('Y', strtotime($dob)));
    $moon_longitude = ($julian_day % 360) + ($longitude / 15);
    $rasi_index = floor($moon_longitude / 30);
    $rasi_names = ["மேஷம்", "ரிஷபம்", "மிதுனம்", "கடகம்", "சிம்மம்", "கன்னி", "துலாம்", "விருச்சிகம்", "தனுசு", "மகரம்", "கும்பம்", "மீனம்"];
    $rasi = $rasi_names[$rasi_index];
    $nakshatra_index = floor($moon_longitude / 13.333);
    $nakshatra_names = ["அஸ்வினி", "பரணி", "கார்த்திகை", "ரோஹிணி", "மிருகசீரிடம்", "திருவாதிரை", "புனர்பூசம்", "பூசம்", "ஆயில்யம்", "மகம்", "பூரம்", "உத்திரம்", "ஹஸ்தம்", "சித்திரை", "சுவாதி", "விசாகம்", "அனுஷம்", "கேட்டை", "மூலம்", "பூராடம்", "உத்திராடம்", "திருவோணம்", "அவிட்டம்", "சதயம்", "பூரட்டாதி", "உத்திரட்டாதி", "ரேவதி"];
    $nakshatra = $nakshatra_names[$nakshatra_index];
    return [$rasi, $nakshatra];
}

function getDasaPeriod($nakshatra, $dob) {
    $dasa_sequence = [
        "கேது" => 7, "சுக்கிரன்" => 20, "சூரியன்" => 6, "சந்திரன்" => 10, 
        "செவ்வாய்" => 7, "ராகு" => 18, "குரு" => 16, "சனி" => 19, "புதன்" => 17
    ];
    $nakshatra_to_dasa = [
        "அஸ்வினி" => "கேது", "பரணி" => "சுக்கிரன்", "கார்த்திகை" => "சூரியன்",
        "ரோஹிணி" => "சந்திரன்", "மிருகசீரிடம்" => "செவ்வாய்", "திருவாதிரை" => "ராகு",
        "புனர்பூசம்" => "குரு", "பூசம்" => "சனி", "ஆயில்யம்" => "புதன்",
        "மகம்" => "கேது", "பூரம்" => "சுக்கிரன்", "உத்திரம்" => "சூரியன்",
        "ஹஸ்தம்" => "சந்திரன்", "சித்திரை" => "செவ்வாய்", "சுவாதி" => "ராகு",
        "விசாகம்" => "குரு", "அனுஷம்" => "சனி", "கேட்டை" => "புதன்",
        "மூலம்" => "கேது", "பூராடம்" => "சுக்கிரன்", "உத்திராடம்" => "சூரியன்",
        "திருவோணம்" => "சந்திரன்", "அவிட்டம்" => "செவ்வாய்", "சதயம்" => "ராகு",
        "பூரட்டாதி" => "குரு", "உத்திரட்டாதி" => "சனி", "ரேவதி" => "புதன்"
    ];
    $starting_dasa = $nakshatra_to_dasa[$nakshatra];
    $nakshatra_index = array_search($nakshatra, array_keys($nakshatra_to_dasa));
    $pada = (($nakshatra_index + 2) % 4) + 1; 
    $full_dasa_years = $dasa_sequence[$starting_dasa];
    $balance_years = round(($full_dasa_years * (4 - $pada) / 4), 2);
    $dob_year = (int)date('Y', strtotime($dob));
    $dasa_start_year = $dob_year;
    $dasa_end_year = $dasa_start_year + $balance_years;
    $dasa_list = array_keys($dasa_sequence);
    $start_index = array_search($starting_dasa, $dasa_list);
    $dasa_periods = [];
    for ($i = 0; $i < count($dasa_list); $i++) {
        $current_dasa = $dasa_list[($start_index + $i) % count($dasa_list)];
        $dasa_years = $dasa_sequence[$current_dasa];
        $dasa_periods[] = [
            "dasa" => $current_dasa,
            "start" => $dasa_start_year + 2,
            "end" => $dasa_start_year + $dasa_years + 2
        ];
        if ($i == 0) {
            $dasa_end_year = $dasa_start_year + $balance_years;
        } else {
            $dasa_end_year = $dasa_start_year + $dasa_years;
        }
        $dasa_start_year = $dasa_end_year;
    }
    $current_year = (int)date('Y');
    foreach ($dasa_periods as $period) {
        if ($current_year >= $period["start"] && $current_year < $period["end"]) {
            return [
                "pada" => $pada,
                "dasa_started" => $starting_dasa,
                "current_dasa" => $period["dasa"],
                "dasa_start_date" => $period["start"],
                "dasa_end_date" => $period["end"]
            ];
        }
    }
    return null;
}

function getRasiAdhipathi($rasi_index) {
    $rasi_adhipathi = [
        "சூரியன்",  // மேஷம் (0)
        "சுக்கிரன்", // ரிஷபம் (1)
        "புதன்",    // மிதுனம் (2)
        "சந்திரன்", // கடகம் (3)
        "சூரியன்",  // சிம்மம் (4)
        "புதன்",    // கன்னி (5)
        "சுக்கிரன்", // துலாம் (6)
        "செவ்வாய்", // விருச்சிகம் (7)
        "குரு",    // தனுசு (8)
        "சனி",    // மகரம் (9)
        "சனி",    // கும்பம் (10)
        "குரு"     // மீனம் (11)
    ];
    
    return $rasi_adhipathi[$rasi_index] ?? "தெரியவில்லை"; // Default if index is out of range
}

function calculateTamilHoroscope($dob, $tob, $latitude, $longitude, $timezone, $lagna) {
    $julian_day = gregoriantojd(date('m', strtotime($dob)), date('d', strtotime($dob)), date('Y', strtotime($dob)));
    $moon_longitude = ($julian_day % 360) + ($longitude / 15);
    $rasi_index = floor($moon_longitude / 30);
    $rasi_names = ["மேஷம்", "ரிஷபம்", "மிதுனம்", "கடகம்", "சிம்மம்", "கன்னி", "துலாம்", "விருச்சிகம்", "தனுசு", "மகரம்", "கும்பம்", "மீனம்"];
    $rasi = $rasi_names[$rasi_index];
    $nakshatra_index = floor($moon_longitude / 13.333);
    $nakshatra_names = ["அஸ்வினி", "பரணி", "கார்த்திகை", "ரோஹிணி", "மிருகசீரிடம்", "திருவாதிரை", "புனர்பூசம்", "பூசம்", "ஆயில்யம்", "மகம்", "பூரம்", "உத்திரம்", "ஹஸ்தம்", "சித்திரை", "சுவாதி", "விசாகம்", "அனுஷம்", "கேட்டை", "மூலம்", "பூராடம்", "உத்திராடம்", "திருவோணம்", "அவிட்டம்", "சதயம்", "பூரட்டாதி", "உத்திரட்டாதி", "ரேவதி"];
    $nakshatra = $nakshatra_names[$nakshatra_index];
    $kattam = [
        1 => "", 2 => "", 3 => "", 4 => "",
        12 => "", 0 => "", 5 => "", 
        11 => "", 6 => "",
        10 => "", 9 => "", 8 => "", 7 => ""
    ];
    $sunrise_time = getSunriseTime($latitude, $longitude, $dob);
    $birth_time = strtotime("$dob $tob");
    if ($birth_time < $sunrise_time) {
        $birth_time += 86400;
    }
    $elapsed_hours = ($birth_time - $sunrise_time) / 3600;
    $rising_rasi_index = floor($sunrise_time % 12);
    $lagna_index = (floor($elapsed_hours / 2) + $rising_rasi_index) % 12;
    $lagna = $rasi_names[$lagna_index];
    // for($i=1; $i<=12; $i++)
    // {
        // if($i == $rasi_index + 1)
        // {
            // $rasi_trim = mb_substr(getRasiAdhipathi(3), 0, 1, "UTF-8");
            // $kattam[$i] = "   🌙 {$rasi_trim}    ";
            // continue;
        // } else if($i == $lagna_index + 1) {
            // $lagna_trim = mb_substr("லக்கினம்", 0, 2, "UTF-8");
            // $kattam[$i] = "     🔆 {$lagna_trim}     ";
            // continue;
        // } else {
            // $rasi_adhipathi = mb_substr(getRasiAdhipathi($i), 0, 1, "UTF-8");
            // $kattam[$i] = "     {$rasi_adhipathi}      ";
        // }
    // }
    $planets_longitudes = [
        "சூரியன்" => (280 + (int)($julian_day * 0.9856)) % 360,
        "சந்திரன்" => (130 + (int)($julian_day * 13.2)) % 360,
        "செவ்வாய்" => (240 + (int)($julian_day * 0.524)) % 360,
        "புதன்" => (60 + (int)($julian_day * 1.6)) % 360,
        "குரு" => (160 + (int)($julian_day * 0.083)) % 360,
        "சுக்கிரன்" => (200 + (int)($julian_day * 1.2)) % 360,
        "சனி" => (300 + (int)($julian_day * 0.033)) % 360,
        "ராகு" => (210 - (int)($julian_day * 0.053) + 360) % 360,
        "கேது" => (30 - (int)($julian_day * 0.053) + 360) % 360,
    ];
    $planet_symbols = [
        "சூரியன்" => "☀️", "சந்திரன்" => "🌙", "செவ்வாய்" => "♂️",
        "புதன்" => "☿️", "குரு" => "♃", "சுக்கிரன்" => "♀️",
        "சனி" => "♄", "ராகு" => "☊", "கேது" => "☋"
    ];
    foreach ($planets_longitudes as $planet => $longitude) {
        $longitude = ($longitude + 360) % 360;

        // Calculate Rasi index correctly
        $rasi_index = floor($longitude / 30) + 1;
    
        // Ensure it remains between 1 and 12
        if ($rasi_index < 1) {
            $rasi_index += 12;
        }
        if ($rasi_index < 1 || $rasi_index > 12) {
            echo "Warning: Invalid rasi index for $planet -> $rasi_index (Longitude: $longitude)\n";
            continue; // Skip invalid values
        }
        $kattam[$rasi_index] .= $planet_symbols[$planet] . $planet;
    }
    $kattam_horoscope = "
        --------------------------------------------------------------------
        |               |                |                |                |
        | {$kattam[12]} |  {$kattam[1]}  |  {$kattam[2]}  |  {$kattam[3]}  |
        |               |                |                |                |
        |---------------|----------------|----------------|-----------------
        |               |                                 |                |
        | {$kattam[11]} |                                 |  {$kattam[4]}  |
        |               |                                 |                |
        |---------------|               ராசி              |----------------|
        |               |                                 |                |
        | {$kattam[10]} |                                 |  {$kattam[5]}  |
        |               |                                 |                |
        |---------------|----------------|----------------|----------------|
        |               |                |                |                |
        |  {$kattam[9]} |  {$kattam[8]}  |  {$kattam[7]}  |  {$kattam[6]}  |
        |               |                |                |                |
        ---------------------------------|----------------|-----------------
    ";
    return [
        "rasi" => $rasi,
        "nakshatra" => $nakshatra,
        "lagna" => $lagna,
        "kattam_chart" => $kattam_horoscope
    ];
}

?>
