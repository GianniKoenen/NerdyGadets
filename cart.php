<?php
include __DIR__ . "/header.php";
include __DIR__ . "/cartfunctions.php";

if(isset($_POST["edit-item"])){
    setProductCountCart($_POST["edit-item-productnummer"]);
}
if(isset($_POST["trash"])){
    clearProductFromCart($_POST["edit-item-productnummer"]);
}
?>
<div class="IndexStyle">
    <div class="col-11">
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
                    $totaalprijs +=$kosten;
                    print("<tr>");
                    print("<td>" . $StockItem["StockItemName"] . "</td>" );
                    print("<td>" . $aantal . "</td><td>");
                    print sprintf("€ %.2f",  $kosten);
                    print("</td>");
                    print("<td>")?>
                    <form method="post">
                        <input type="number" value="<?php echo($aantal); ?>" name="edit-item" onblur="this.form.submit()">
                        <input type="number" value ="<?php echo($productnummer); ?>" name="edit-item-productnummer" hidden>
                    <?php print("</td>");
                    print("<td>");?>
                        <input type="submit" name="trash" style="font-family: FontAwesome" value="&#xf014">
                    </form>
                    <?php print("</td>");
                    print("</tr>");
                }
                //gegevens per artikelen in $cart (naam, prijs, etc.) uit database halen
                //totaal prijs berekenen
                //mooi weergeven in html
                //etc.
                ?>

            </table>
        <br>

            <?php
            print("Totale kosten zijn: ");
            print sprintf("€ %.2f",  $totaalprijs);
            ?>
            <br>
    </div>
    <form method="post" action="cart.php" style="width: 20%">
        <input type="submit" name="clearcart" value="Leeg winkelmand">
    </form>
    <?php
    // als winkelmand leeg is geen afreken knop tonen
    if (!empty($cart)) {
    ?>
        <form method="POST" action="afrekenen.php" style="width: 20%">
            <br><input type="submit" name="knop" value="Afrekenen">

     <?php
    }
    ?>   
    </form>
    <?php
    if (isset($_POST["clearcart"])) {              // zelfafhandelend formulier
        session_destroy();     // maak gebruik van geïmporteerde functie uit cartfuncties.php


        echo '<script>
            setTimeout(function() {
                window.location.href = "cart.php";
            }, 100);
          </script>';
        exit();
    }

    ?>
</div>

<?php
include __DIR__ . "/footer.php";
?>

