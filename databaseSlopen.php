<?php
include "database.php";
$databaseConnection = connectToDatabase();  
$_SESSION["naam"] = $_POST['naam'];
$_SESSION["adres"] = $_POST['adres'];
$_SESSION["zip"] = $_POST['zip'];
$_SESSION["plaats"] = $_POST['plaats'];
$_SESSION["provincie"] = $_POST['provincie'];
$_SESSION["land"] = $_POST['land'];
$_SESSION["mail"] = $_POST['mail'];
$_SESSION["telefoonnummer"] = $_POST['telefoonnummer'];
function ProvinceCheck($statename, $connectie){ //functie om te checken of de provincie bestaat
    $QueryExistenceCheck = "SELECT stateprovinceid FROM stateprovinces
                            WHERE StateProvinceName = ?"; //zie hier beneden voor ?

    $Statement = mysqli_prepare($connectie, $QueryExistenceCheck);
    $Statement->bind_param("s", $statename); //conversiemaatregel om SQL injection te voorkomen, vandaar de "?"
    mysqli_stmt_execute($Statement);

    $result = $Statement->get_result();
    return $result;
}

function CountryCheck($countryname, $connectie){ // functie om the checken of het land bestaat
    $QueryExistenceCheck = "SELECT countryid FROM countries
                            WHERE CountryName = ?"; //zie hier beneden voor ?

    $Statement = mysqli_prepare($connectie, $QueryExistenceCheck);
    $Statement->bind_param("s", $countryname); //conversiemaatregel om SQL injection te voorkomen, vandaar de "?"
    mysqli_stmt_execute($Statement);

    $result = $Statement->get_result();
    return $result;
}

function CityCheck($cityname, $connectie, $idStaat){ // functie om the checken of het land bestaat
    $QueryExistenceCheck = "SELECT cityid FROM cities
                            WHERE cityname = ? AND stateprovinceid = ?"; //zie hier beneden voor ?

    $Statement = mysqli_prepare($connectie, $QueryExistenceCheck);
    $Statement->bind_param("si", $cityname, $idStaat); //conversiemaatregel om SQL injection te voorkomen, vandaar de "?"
    mysqli_stmt_execute($Statement);

    $result = $Statement->get_result();
    return $result;
}

function CreateCity($connectie, $CityName, $StateProvinceID){ //functie om dorp/stad aan te maken
    $QueryMakenCity = "INSERT INTO `cities` (`CityID`, `CityName`, `StateProvinceID`,
                      `Location`, `LatestRecordedPopulation`, `LastEditedBy`,
                      `ValidFrom`, `ValidTo`) 
                        VALUES (NULL, ?, ?, 
                                NULL, NULL, 1,
                                '2023-12-03 10:28:31.000000', '2023-12-03 10:28:31.000000')"; //zie hier beneden voor ?


    $Statement = mysqli_prepare($connectie, $QueryMakenCity);
    $Statement->bind_param("si", $CityName, $StateProvinceID); //conversiemaatregel om SQL injection te voorkomen, vandaar de "?"
    mysqli_stmt_execute($Statement);

    return mysqli_insert_id($connectie);
}

function CreatePeople($connectie, $name, $PhoneNumber, $Email){ //functie om iemand aan te maken in de people tabel
    $QueryMakenPeople = "INSERT INTO `people` (`PersonID`, `FullName`, `PreferredName`,
                      `SearchName`, `IsPermittedToLogon`, `LogonName`, `IsExternalLogonProvider`, 
                      `HashedPassword`, `IsSystemUser`, `IsEmployee`, `IsSalesperson`, `UserPreferences`, 
                      `PhoneNumber`, `FaxNumber`, `EmailAddress`, `Photo`, `CustomFields`, `OtherLanguages`, 
                      `LastEditedBy`, `ValidFrom`, `ValidTo`) 
                        VALUES (NULL, ?, ?,
                                ?, 0, 'NO LOGON', 0,
                                NULL, 0, 0, 0, NULL,
                                ?, NULL, ?, NULL, NULL, NULL,
                                1, '2023-11-30 14:59:56.000000', '2023-11-30 14:59:56.000000')"; //zie hier beneden voor ?

    $Statement = mysqli_prepare($connectie, $QueryMakenPeople);
    $Statement->bind_param("sssss", $name, $name, $name, $PhoneNumber, $Email); //conversiemaatregel om SQL injection te voorkomen, vandaar de "?"
    mysqli_stmt_execute($Statement);

    return mysqli_insert_id($connectie);
}

//QUERY VOOR INVOEREN CUSTOMERS
function CreateCustomer($CustomerName, $PrimaryContactPersonID, $PhoneNumber, $AddressLine1, $AddressLine2, $PostalCode, $CityID, $connectie){ //functie om klant aan te maken
    $AccountOpenedDate = date("Y-m-d"); //gewoon huidige datum
    $ValidFrom = date("Y-m-d H:i:s"); //gewoon huidige datum

    $Query = "INSERT INTO `customers` (`CustomerID`, `CustomerName`, `BillToCustomerID`, `CustomerCategoryID`,
             `BuyingGroupID`, `PrimaryContactPersonID`, `AlternateContactPersonID`, `DeliveryMethodID`,
             `DeliveryCityID`, `PostalCityID`, `CreditLimit`, `AccountOpenedDate`, `StandardDiscountPercentage`,
             `IsStatementSent`, `IsOnCreditHold`, `PaymentDays`, `PhoneNumber`, `FaxNumber`, `DeliveryRun`,
             `RunPosition`, `WebsiteURL`, `DeliveryAddressLine1`, `DeliveryAddressLine2`, 
             `DeliveryPostalCode`, `DeliveryLocation`, `PostalAddressLine1`, `PostalAddressLine2`,
             `PostalPostalCode`, `LastEditedBy`, `ValidFrom`, `ValidTo`) 
             VALUES (NULL, ?, '1', '3',
             NULL, ?, NULL, '3',
             ?, ?, NULL, ?, '0.000',
             '0', '0', '7', ?, ?, NULL,
             NULL, '', ?, ?,
             ?, NULL, ?, ?,
             ?, '1', ?,'9999-12-31 11:59:59.000000')"; //zie hier beneden voor ?, BillToCustomerID wordt eerst op 1 gezet

    $Statement = mysqli_prepare($connectie, $Query);
    $Statement->bind_param("siiissssssssss",$CustomerName, $PrimaryContactPersonID, $CityID, $CityID, $AccountOpenedDate,
        $PhoneNumber, $PhoneNumber,$AddressLine1, $AddressLine2, $PostalCode, $AddressLine1, $AddressLine2, $PostalCode, $ValidFrom); //conversiemaatregel om SQL injection te voorkomen, vandaar de "?"
    mysqli_stmt_execute($Statement);

    return mysqli_insert_id($connectie);
}

function ChangeBillToCustomerID($CustomerId,$connectie){ //functie om de BillToCustomerID te updaten
    $Query = "UPDATE customers 
                SET BillToCustomerID = $CustomerId
                WHERE customerid = $CustomerId";

    $Statement = mysqli_prepare($connectie, $Query);
    mysqli_stmt_execute($Statement);
}
function InsertWebsiteDataExistingCity($StateProvinceID, $databaseConnection,$naam, $plaats, $adress, $postcode, $email, $telefoonnummer, $CityID){ //functie die alle records aan maakt
    print("Plek bestaat\n");
    $row = mysqli_fetch_array($StateProvinceID);

    $personID= CreatePeople($databaseConnection, $naam, $telefoonnummer,
        $email);

    $customerID = CreateCustomer($naam, $personID, $telefoonnummer, $adress,
        $adress, $postcode, $CityID, $databaseConnection);

    ChangeBillToCustomerID($customerID, $databaseConnection);
}

function InsertWebsiteDataNewCity($StateProvinceID, $databaseConnection,$naam, $plaats, $adress, $postcode, $email, $telefoonnummer){ //functie die alle records aan maakt


    $CityID = CreateCity($databaseConnection, $plaats, $StateProvinceID);

    $personID= CreatePeople($databaseConnection, $naam, $telefoonnummer,
        $email);

    $customerID = CreateCustomer($naam, $personID, $telefoonnummer, $adress,
        $adress, $postcode, $CityID, $databaseConnection);

    ChangeBillToCustomerID($customerID, $databaseConnection);
}



//Deze variabelen aanpassen voor invullen gegevens, hier komen de ingevulde gegevens van de internet pagina
$naam = $_SESSION["naam"];
$land = $_SESSION["land"];
$provincie = $_SESSION["provincie"];
$plaats = $_SESSION["plaats"];
$adress = $_SESSION["adres"];
$postcode = $_SESSION["zip"]; 
$email = $_SESSION["mail"];
$telefoonnummer = $_SESSION["telefoonnummer"];

//Hier het gedeelte dat daadwerkelijk aan de slag gaat
$CountryCheck = CountryCheck($land, $databaseConnection); //check of land in database staat
if($CountryCheck->num_rows > 0){//als er een record is (dus land bestaat) ga door
    $StateProvinceID = ProvinceCheck($provincie, $databaseConnection); //check of provincie bestaat
    $row = mysqli_fetch_array($StateProvinceID);
    if($StateProvinceID->num_rows > 0){//als er een record is (dus provincie bestaat) ga door
        $CityCheck = CityCheck($plaats, $databaseConnection, $row['stateprovinceid']); //check of er al een stad met deze naam en provincie bestaat
        if($CityCheck->num_rows > 0){ //als er een record is (dus plaats bestaat) ga door
            $row = mysqli_fetch_array($CityCheck);
            InsertWebsiteDataExistingCity($StateProvinceID, $databaseConnection, $naam, $plaats, $adress, $postcode, $email, $telefoonnummer, $row['cityid']);
        }
        else{
            InsertWebsiteDataNewCity($row['stateprovinceid'], $databaseConnection, $naam, $plaats, $adress, $postcode, $email, $telefoonnummer);
        }
    }
    else{
        print("Provincie bestaat niet\n");
    }
}
else{
    print("Land bestaat niet");
}
?>
