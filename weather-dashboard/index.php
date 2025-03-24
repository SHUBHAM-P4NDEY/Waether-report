<?php
$currentWeather = null;
$forecast = null;
$errorMessage = null;

if (isset($_GET['city'])) {
    $city = urlencode($_GET['city']);
    $apiKey = '940425dc6690f21ceca85eb1a81168e9';

    $currentWeatherUrl = "https://api.openweathermap.org/data/2.5/weather?q=$city&appid=$apiKey&units=metric";
    $forecastUrl = "https://api.openweathermap.org/data/2.5/forecast?q=$city&appid=$apiKey&units=metric";

    $currentWeatherData = @file_get_contents($currentWeatherUrl);

    if ($currentWeatherData === FALSE) {
        $errorMessage = "Error fetching current weather data.";
    } else {
        $currentWeather = json_decode($currentWeatherData, true);

        if (isset($currentWeather['cod']) && $currentWeather['cod'] != 200) {
            $errorMessage = "City not found or invalid API request.";
        }
    }

    $forecastData = @file_get_contents($forecastUrl);

    if ($forecastData === FALSE) {
        $errorMessage = "Error fetching forecast data.";
    } else {
        $forecast = json_decode($forecastData, true);

        if (isset($forecast['cod']) && $forecast['cod'] != 200) {
            $errorMessage = "City not found or invalid forecast data request.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Weather Dashboard</h1>
        <form method="GET" action="">
            <input type="text" name="city" placeholder="Enter a city name..." value="<?php echo $_GET['city'] ?? ''; ?>" required>
            <button type="submit">Search</button>
        </form>

        <?php if ($errorMessage): ?>
            <p class="error"><?php echo $errorMessage; ?></p>
        <?php endif; ?>

        <?php if ($currentWeather && !$errorMessage): ?>
            <div class="current-weather">
                <h2>Current Weather in <?php echo $currentWeather['name']; ?>:</h2>
                <p><strong>Temperature:</strong> <?php echo $currentWeather['main']['temp']; ?>°C</p>
                <p><strong>Description:</strong> <?php echo $currentWeather['weather'][0]['description']; ?></p>
                <p><strong>Humidity:</strong> <?php echo $currentWeather['main']['humidity']; ?>%</p>
                <p><strong>Wind Speed:</strong> <?php echo $currentWeather['wind']['speed']; ?> m/s</p>
            </div>

            <div class="forecast">
                <h2>5-Day Forecast:</h2>
                <div class="forecast-list">
                    <?php foreach ($forecast['list'] as $item): ?>
                        <?php if (strpos($item['dt_txt'], '12:00:00') !== false): ?>
                            <div class="forecast-item">
                                <h3><?php echo date('l, j M', strtotime($item['dt_txt'])); ?></h3>
                                <p><strong>Temp:</strong> <?php echo $item['main']['temp']; ?>°C</p>
                                <p><strong>Weather:</strong> <?php echo $item['weather'][0]['description']; ?></p>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
