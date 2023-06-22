<?php
/*
Plugin Name: CO2 Footprint Calculator
Description: Calculates the CO2 footprint based on user input.
Version: 1.0
Author: Your Name
*/

// Eklenti dosyalarının dizinini tanımlayın
define('CO2_CALCULATOR_PLUGIN_DIR', plugin_dir_path(__FILE__));

// Stil dosyasını ekleyin
function co2_calculator_enqueue_styles()
{
    wp_enqueue_style('co2-calculator-styles', plugin_dir_url(__FILE__) . 'css/style.css');
}
add_action('wp_enqueue_scripts', 'co2_calculator_enqueue_styles');

// Eklenti sayfasını oluşturun
function co2_calculator_page()
{
    ob_start();
    ?>

    <div class="co2-calculator">
        <h2>CO2 Footprint Calculator</h2>
        <form method="POST">
            <input type="hidden" name="action" value="calculate_co2_footprint">
            <div class="form-field">
                <label for="distance">Distance (km):</label>
                <input type="number" name="distance" id="distance" required>
            </div>
            <div class="form-field">
                <label for="fuel">Fuel Consumption (liters):</label>
                <input type="number" name="fuel" id="fuel" required>
            </div>
            <div class="form-field">
                <label for="vehicle">Vehicle Type:</label>
                <select name="vehicle" id="vehicle" required>
                    <option value="">Select Vehicle</option>
                    <option value="car">Car</option>
                    <option value="motorcycle">Motorcycle</option>
                    <option value="bicycle">Bicycle</option>
                </select>
            </div>
            <input type="submit" value="Calculate">
        </form>
        <div class="result">
            <?php
            if (isset($_POST['action']) && $_POST['action'] === 'calculate_co2_footprint') {
                $distance = intval($_POST['distance']);
                $fuel = intval($_POST['fuel']);
                $vehicle = sanitize_text_field($_POST['vehicle']);

                // Hesaplama işlemini gerçekleştirin
                $co2_footprint = calculate_co2($distance, $fuel, $vehicle);

                // Hesaplama sonucunu gösterin
                echo '<p>CO2 Footprint: ' . $co2_footprint . ' kg</p>';
            }
            ?>
        </div>
    </div>

    <?php
    return ob_get_clean();
}
add_shortcode('co2_calculator', 'co2_calculator_page');

// CO2 hesaplama fonksiyonunu tanımlayın
function calculate_co2($distance, $fuel, $vehicle)
{
    // CO2 salınım oranları (kg CO2 birim başına)
    $co2_per_km_car = 0.180; // Otomobil için CO2 salınımı (kilometre başına)
    $co2_per_km_motorcycle = 0.120; // Motosiklet için CO2 salınımı (kilometre başına)
    $co2_per_km_bicycle = 0; // Bisiklet için CO2 salınımı (kilometre başına)

    // CO2 ayak izini hesaplayın
    $co2_footprint = 0;

    if ($vehicle === 'car') {
        $co2_footprint = $distance * $co2_per_km_car;
    } elseif ($vehicle === 'motorcycle') {
        $co2_footprint = $distance * $co2_per_km_motorcycle;
    } elseif ($vehicle === 'bicycle') {
        $co2_footprint = $distance * $co2_per_km_bicycle;
    }

    // Yakıt tüketimi için CO2 salınımı (litre başına)
    $co2_per_liter_fuel = 2.31;

    // Yakıt tüketimine bağlı olarak CO2 ayak izini güncelleyin
    $co2_footprint += $fuel * $co2_per_liter_fuel;

    return $co2_footprint;
}
