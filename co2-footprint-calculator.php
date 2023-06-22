<?php
/*
Plugin Name: CO2 Footprint Calculator
Description: Calculates the CO2 footprint based on user input.
Version: 2.0
Author: github.com/Lamartune
*/

// Eklenti dosyalarının dizini
define('CO2_CALCULATOR_PLUGIN_DIR', plugin_dir_path(__FILE__));

// Stil dosyası
function co2_calculator_enqueue_styles()
{
    wp_enqueue_style('co2-calculator-styles', plugin_dir_url(__FILE__) . 'css/style.css');
}
add_action('wp_enqueue_scripts', 'co2_calculator_enqueue_styles');

// Eklenti sayfası
function co2_calculator_page()
{
    ob_start();
    ?>

    <div class="co2-calculator">
        <h2>CO2 Footprint Calculator</h2>
        <form method="POST" action="">
            <input type="hidden" name="action" value="calculate_co2_footprint">
            <div class="form-field">
                <label for="electric_bill">Monthly Electric bill (€):</label>
                <input type="number" name="electric_bill" id="electric_bill" min="0" max="1000" required>
            </div>
            <div class="form-field">
                <label for="gas_bill">Monthly Gas bill (€):</label>
                <input type="number" name="gas_bill" id="gas_bill" min="0" max="1000" required>
            </div>
            <div class="form-field">
                <label for="oil_bill">Monthly Oil bill (€):</label>
                <input type="number" name="oil_bill" id="oil_bill" min="0" max="1000" required>
            </div>
            <div class="form-field">
                <label for="car_miles">Monthly miles driven on your car:</label>
                <input type="number" name="car_miles" id="car_miles" min="0" max="1000" required>
            </div>
            <div class="form-field">
                <label for="flights_short">Number of flights taken in the past year (4 hours or less):</label>
                <input type="number" name="flights_short" id="flights_short" min="0" max="1000" required>
            </div>
            <div class="form-field">
                <label for="flights_long">Number of flights taken in the past year (4 hours or more):</label>
                <input type="number" name="flights_long" id="flights_long" min="0" max="1000" required>
            </div>
            <div class="form-field">
                <label for="recycle_newspaper">Do you recycle newspaper?</label>
                <select name="recycle_newspaper" id="recycle_newspaper" required>
                    <option value="yes">Yes</option>
                    <option value="no">No</option>
                </select>
            </div>
            <div class="form-field">
                <label for="recycle_aluminum">Do you recycle aluminum and tin?</label>
                <select name="recycle_aluminum" id="recycle_aluminum" required>
                    <option value="yes">Yes</option>
                    <option value="no">No</option>
                </select>
            </div>
            <input type="submit" name="calculate" value="Calculate">
        </form>
        <div class="result">
            <?php
            if (isset($_POST['calculate']) && isset($_POST['electric_bill']) && isset($_POST['gas_bill']) && isset($_POST['oil_bill']) && isset($_POST['car_miles']) && isset($_POST['flights_short']) && isset($_POST['flights_long']) && isset($_POST['recycle_newspaper']) && isset($_POST['recycle_aluminum'])) {
                $electricBill = $_POST['electric_bill'];
                $gasBill = $_POST['gas_bill'];
                $oilBill = $_POST['oil_bill'];
                $carMiles = $_POST['car_miles'];
                $flightsShort = $_POST['flights_short'];
                $flightsLong = $_POST['flights_long'];
                $recycleNewspaper = $_POST['recycle_newspaper'];
                $recycleAluminum = $_POST['recycle_aluminum'];

                $co2_footprint = calculate_co2($electricBill, $gasBill, $oilBill, $carMiles, $flightsShort, $flightsLong, $recycleNewspaper, $recycleAluminum);

                echo '<p>CO2 Footprint: ' . $co2_footprint . ' kg</p>';
            }
            ?>
        </div>
    </div>

    <?php
    return ob_get_clean();
}
add_shortcode('co2_calculator', 'co2_calculator_page');

// CO2 hesaplama fonksiyonu
function calculate_co2($electricBill, $gasBill, $oilBill, $carMiles, $flightsShort, $flightsLong, $recycleNewspaper, $recycleAluminum)
{
    // CO2 salınım oranları (kg CO2 birim başına)
    $co2_per_electric_euro = 0.1; // Elektrik faturası başına CO2 salınımı (euro başına)
    $co2_per_gas_euro = 0.2; // Gaz faturası başına CO2 salınımı (euro başına)
    $co2_per_oil_euro = 0.3; // Yağ faturası başına CO2 salınımı (euro başına)
    $co2_per_mile_car = 0.2; // Araba ile seyahat başına CO2 salınımı (mil başına)
    $co2_per_flight_short = 0.15; // 4 saatten kısa uçuş başına CO2 salınımı
    $co2_per_flight_long = 0.3; // 4 saatten uzun uçuş başına CO2 salınımı
    $co2_recycle_newspaper = -0.05; // Gazete geri dönüşümü için azaltılan CO2 salınımı
    $co2_recycle_aluminum = -0.1; // Alüminyum ve teneke kutuların geri dönüşümü için azaltılan CO2 salınımı

    // CO2 hesaplama
    $co2_electric = $electricBill * $co2_per_electric_euro;
    $co2_gas = $gasBill * $co2_per_gas_euro;
    $co2_oil = $oilBill * $co2_per_oil_euro;
    $co2_car = $carMiles * $co2_per_mile_car;
    $co2_flights_short = $flightsShort * $co2_per_flight_short;
    $co2_flights_long = $flightsLong * $co2_per_flight_long;
    $co2_recycle_newspaper = $recycleNewspaper == 'yes' ? $co2_recycle_newspaper : 0;
    $co2_recycle_aluminum = $recycleAluminum == 'yes' ? $co2_recycle_aluminum : 0;

    $co2_footprint = $co2_electric + $co2_gas + $co2_oil + $co2_car + $co2_flights_short + $co2_flights_long + $co2_recycle_newspaper + $co2_recycle_aluminum;

    return $co2_footprint;
}
?>
