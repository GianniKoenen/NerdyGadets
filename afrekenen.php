<?php
include __DIR__ . "/header.php";
include __DIR__ . "/cartfunctions.php";
?>

<div class="IndexStyle">
    <div class="col-11">
        <!-- Formulier klant -->
        <form method="POST" action="databaseSlopen.php" style="width: 20%">
            Naam* <input type="text" name="naam" value="" required><br>

            Adres* <input type="text" id="autocomplete" name="adres" value="" autocomplete="off" required title="straat + huisnummer"><br>

            <label for="zip">ZIP Code:</label>
            <input type="text" id="zip" name="zip" required pattern="[1-9][0-9]{3}\s?[a-zA-Z]{2}" title="Enter a valid Dutch ZIP code (e.g., 1234 AB)" readonly><br>

            Plaats* <input type="text" name="plaats" value="" required readonly><br>

        
            Provincie/State* <input type="text" name="provincie" value="" required readonly><br>

            Land* <input type="text" name="land" value="" required readonly><br>

            Mail* <input type="email" name="mail" value="" required title="...@....com/nl"><br>

            Telefoonnummer <input type="text" name="telefoonnummer" value="" title="06 12345678"><br>

            <br><input type="submit" name="knop" value="Afrekenen">
        </form>

        <br>

        <div style="position:relative; left:600px; top:-600px;">
            <table>
                <tr>
                    <th>Product</th>
                    <th>Aantal</th>
                    <th>Kosten</th>
                </tr>
                <?php
                $cart = getCart();
                $totaalprijs = 0;
                foreach($cart as $productnummer => $aantal){
                    $StockItem = getStockItem($productnummer, $databaseConnection);
                    $kosten = $StockItem["SellPrice"] * $aantal;
                    $totaalprijs += $kosten;
                    print("<tr>");
                    print("<td>" . $StockItem["StockItemName"] . "</td>" );
                    print("<td>" . $aantal . "</td><td>");
                    print sprintf("€ %.2f",  $kosten);
                    print("</td>");
                    print("</tr>");
                }
                ?>
            </table>

            <div style="position:relative; left:10px; top:5px;">
                <?php
                print("<p style='font-weight: bold; font-size: 18px; margin-top: 5px;'>Totale kosten zijn: ");
                print sprintf("€ %.2f",  $totaalprijs);
                print("</p>");
                ?>
                <br>

                <div> 
                    <form method="post" action="cart.php" style="width: 20%">
                        <input type="submit" name="return" value="Terug naar winkelmand">
                    </form>
                </div>
            </div>
        </div>
    <!-- Add the Google Places API script -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAM4gyaAS4h_tttQWkldxOIOennumn3ZIE&libraries=places"></script>

    <!-- Java script voor Google Places API -->
    <script>
    function initializeAutocomplete() {
        var input = document.getElementById('autocomplete');
        var zipInput = document.getElementById('zip');
        var plaatsInput = document.getElementsByName('plaats')[0];
        var provincieInput = document.getElementsByName('provincie')[0];
        var landInput = document.getElementsByName('land')[0];

        var options = {
            types: ['address'],
            componentRestrictions: { country: 'NL' } // Replace with your country code(welke landen je wilt)
        };

        var autocomplete = new google.maps.places.Autocomplete(input, options);

        autocomplete.addListener('place_changed', function() {
            var place = autocomplete.getPlace();

            // log for google API
            console.log(place);

            // Extract the first part of the formatted address (street address)
            var formattedAddress = place.formatted_address || '';
            var addressParts = formattedAddress.split(','); // Split the address by commas
            var streetAddress = addressParts[0].trim(); // Get the first part and remove leading/trailing spaces

            // Update the address field with the street address
            input.value = streetAddress;

            // Update the postal code (ZIP code), city, province/state, and country fields
            for (var i = 0; i < place.address_components.length; i++) {
                var component = place.address_components[i];
                if (component.types.includes('postal_code')) {
                    zipInput.value = component.long_name;
                } else if (component.types.includes('locality')) {
                    plaatsInput.value = component.long_name;
                } else if (component.types.includes('administrative_area_level_1')) {
                    // This is the province/state level
                    provincieInput.value = component.long_name;
                } else if (component.types.includes('country')) {
                    // Convert country code to English name
                    var countryName = getCountryName(component.short_name);
                    landInput.value = countryName;
                }
            }
        });
    }

    function getCountryName(countryCode) {
// functie die verplicht dat nederlands in het engels komt te staan
        var countryMapping = {
            'NL': 'Netherlands',
            // Add more country codes as needed
        };

        return countryMapping[countryCode] || countryCode; // Default to country code if not found
    }

    document.addEventListener('DOMContentLoaded', function() {
        initializeAutocomplete();
    });
</script>


</div>
<?php

include __DIR__ . "/footer.php";
?>
