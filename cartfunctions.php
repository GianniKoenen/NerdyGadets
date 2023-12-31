<?php

function getCart(){
    if(isset($_SESSION['cart'])){               //controleren of winkelmandje (=cart) al bestaat
        $cart = $_SESSION['cart'];                  //zo ja:  ophalen
    } else{
        $cart = array();                            //zo nee: dan een nieuwe (nog lege) array
    }
    return $cart;                               // resulterend winkelmandje terug naar aanroeper functie
}

function saveCart($cart){
    $_SESSION["cart"] = $cart;                  // werk de "gedeelde" $_SESSION["cart"] bij met de meegestuurde gegevens
}

function addProductToCart($stockItemID){
    $cart = getCart();                          // eerst de huidige cart ophalen
    if(array_key_exists($stockItemID, $cart)){  //controleren of $stockItemID(=key!) al in array staat
        $cart[$stockItemID] += 1;                   //zo ja:  aantal met 1 verhogen
    }else{
        $cart[$stockItemID] = 1;                    //zo nee: key toevoegen en aantal op 1 zetten.
    }
    saveCart($cart);                            // werk de "gedeelde" $_SESSION["cart"] bij met de bijgewerkte cart
}

function removeProductFromCart($stockItemID){
    $cart = getCart();
    if($cart[$stockItemID] != 1 && $cart[$stockItemID] > 0){
        $cart[$stockItemID] -= 1;
    }else{
        unset($cart[$stockItemID]);
    }
    saveCart($cart);

}

function clearProductFromCart($stockItemID){
    $cart = getCart();
    unset($cart[$stockItemID]);
    saveCart($cart);
}

function setProductCountCart($stockItemID){
    $cart = getCart();
    $cart[$stockItemID] = $_POST["edit-item"];
    if($cart[$stockItemID] < 1){
        unset($cart[$stockItemID]);
    }
    saveCart($cart);
}