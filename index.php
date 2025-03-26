<!DOCTYPE html>
<html lang="ta">
<head>
    <meta charset="UTF-8">
    <title>தமிழ் ஜாதகம் - Vakyam-Based Horoscope</title>
</head>
<body>
    <h2>வாக்கியம் அடிப்படையிலான தமிழ் ஜாதகம்</h2>
    <form action="result.php" method="post">
        பெயர்: <input type="text" name="name" required><br>
        பிறந்த தேதி: <input type="date" name="dob" required><br>
        பிறந்த நேரம்: <input type="time" name="tob" required><br>
        பாலினம்: 
        <select name="gender" required>
            <option value="male">ஆண்</option>
            <option value="female">பெண்</option>
        </select><br>
        அகலாங்கு (Latitude): <input type="text" name="latitude" required><br>
        நெடுங்கோடு (Longitude): <input type="text" name="longitude" required><br>
        நேர மண்டலம் (Time Zone): <input type="text" name="timezone" required><br>
        <button type="submit">ஜாதகம் காண</button>
    </form>
</body>
</html>
