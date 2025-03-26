<?php 
include 'horoscope.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $dob = $_POST["dob"];
    $tob = $_POST["tob"];
    $latitude = $_POST["latitude"];
    $longitude = $_POST["longitude"];
    $timezone = $_POST["timezone"];

    list($rasi, $nakshatra) = calculateRasiNakshatra($dob, $tob, $latitude, $longitude, $timezone);
    $lagna = calculateLagna($dob, $tob, $latitude, $longitude, $timezone);
    $dasa_period = getDasaPeriod($nakshatra, $dob);
    $result = calculateTamilHoroscope($dob, $tob, $latitude, $longitude, $timezone, $lagna);
}
?>

<!DOCTYPE html>
<html lang="ta">
<head>
    <meta charset="UTF-8">
    <title>தமிழ் ஜாதக விளக்கம்</title>
</head>
<body>
    <h2>ஜாதக விளக்கம்</h2>
    <p>பெயர்: <?php echo htmlspecialchars($name); ?></p>
    <p>பிறந்த தேதி: <?php echo htmlspecialchars($dob); ?></p>
    <p>ஜன்ம ராசி: <b><?php echo $rasi; ?></b></p>
    <p>நட்சத்திரம்: <b><?php echo $nakshatra; ?> | <?php echo $dasa_period['pada']; ?> - ம் பாதம்</b></p>
    <p>லக்கினம்: <b><?php echo $lagna; ?></b></p>
    <p>நடப்பு தசை: <b><?php echo $dasa_period['dasa_started']; ?></b></p>

    <h3>ஜாதக விளக்கம்</h3>
    <ul>
        <li>உங்கள் ஜாதக ராசி: <?php echo $rasi; ?></li>
        <li>உங்கள் லக்கினம்: <?php echo $lagna; ?></li>
        <li>தற்போதைய தசை: <?php echo $dasa_period['current_dasa']; ?></li>
        <li>தசை தொடக்க ஆண்டு: <?php echo $dasa_period['dasa_start_date']; ?></li>
        <li>தசை முடிவு ஆண்டு: <?php echo $dasa_period['dasa_end_date']; ?></li>
    </ul>

    <h3>தமிழ் ஜாதகம் (கட்டம்)</h3>
    <pre>
        <?php echo $result['kattam_chart']; ?>
    </pre>
</body>
</html>
