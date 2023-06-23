<?php
/*
Plugin Name: CO2 Footprint Calculator
Description: Calculates the CO2 footprint based on user input.
Version: 4.0
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
            <div class="form-field">
                <button type="submit">Calculate CO2 Footprint</button>
            </div>
        </form>
    </div>

    <?php
    return ob_get_clean();
}
add_shortcode('co2_calculator', 'co2_calculator_page');

// CO2 hesaplama işlemi
function calculate_co2_footprint()
{
    if (isset($_POST['action']) && $_POST['action'] === 'calculate_co2_footprint') {
        $electricBill = $_POST['electric_bill'];
        $gasBill = $_POST['gas_bill'];
        $oilBill = $_POST['oil_bill'];
        $carMiles = $_POST['car_miles'];
        $flightsShort = $_POST['flights_short'];
        $flightsLong = $_POST['flights_long'];
        $recycleNewspaper = $_POST['recycle_newspaper'];
        $recycleAluminum = $_POST['recycle_aluminum'];

        $co2_per_electric_euro = 0.45;
        $co2_per_gas_euro = 0.28;
        $co2_per_oil_euro = 0.36;
        $co2_per_mile_car = 0.41;
        $co2_per_flight_short = 250;
        $co2_per_flight_long = 3500;
        $co2_recycle_newspaper = 25;
        $co2_recycle_aluminum = 17;

        $co2_electric = $electricBill * $co2_per_electric_euro;
        $co2_gas = $gasBill * $co2_per_gas_euro;
        $co2_oil = $oilBill * $co2_per_oil_euro;
        $co2_car = $carMiles * $co2_per_mile_car;
        $co2_flights_short = $flightsShort * $co2_per_flight_short;
        $co2_flights_long = $flightsLong * $co2_per_flight_long;
        $co2_recycle_newspaper = $recycleNewspaper == 'no' ? $co2_recycle_newspaper : 0;
        $co2_recycle_aluminum = $recycleAluminum == 'no' ? $co2_recycle_aluminum : 0;
        
        $total_co2 = $co2_electric + $co2_gas + $co2_oil + $co2_car + $co2_flights_short + $co2_flights_long + $co2_recycle_newspaper + $co2_recycle_aluminum;
    $average_co2 = $total_co2 / 8;

    echo '<div class="co2-calculator-result">';
    echo '<h4>Average CO2 Footprint:</h4>';
    echo '<p>' . $average_co2 . ' kg</p>';
    echo '</div>';

        echo '<div class="co2-calculator-result">';
        echo '<h5>CO2 Footprint Calculation Results:</h5>';
        echo '<p>Electricity CO2 Emissions: ' . $co2_electric . ' kg</p>';
        echo '<p>Gas CO2 Emissions: ' . $co2_gas . ' kg</p>';
        echo '<p>Oil CO2 Emissions: ' . $co2_oil . ' kg</p>';
        echo '<p>Car CO2 Emissions: ' . $co2_car . ' kg</p>';
        echo '<p>Flights (Short) CO2 Emissions: ' . $co2_flights_short . ' kg</p>';
        echo '<p>Flights (Long) CO2 Emissions: ' . $co2_flights_long . ' kg</p>';
        echo '<p>Recycling (Newspaper) CO2 Emissions: ' . $co2_recycle_newspaper . ' kg</p>';
        echo '<p>Recycling (Aluminum) CO2 Emissions: ' . $co2_recycle_aluminum . ' kg</p>';
        echo '</div>';

        echo '<script>';
        echo 'var co2Data = {';
        echo 'labels: ["Electricity", "Gas", "Oil", "Car", "Flights (Short)", "Flights (Long)", "Recycling (Newspaper)", "Recycling (Aluminum)"],';
        echo 'data: [' . $co2_electric . ', ' . $co2_gas . ', ' . $co2_oil . ', ' . $co2_car . ', ' . $co2_flights_short . ', ' . $co2_flights_long . ', ' . $co2_recycle_newspaper . ', ' . $co2_recycle_aluminum . ']';
        echo '};';
        echo 'drawChart(co2Data);';
        echo '</script>';
    }
}

add_action('init', 'calculate_co2_footprint');
