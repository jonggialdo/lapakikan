<?php

include_once("modules/connect.php");

//add product to session or create new one
if(isset($_POST["type"]) && $_POST["type"]=='add' && $_POST["product_qty"]>0)
{
	foreach($_POST as $key => $value){ //add all post vars to new_product array
		$new_product[$key] = filter_var($value, FILTER_SANITIZE_STRING);
    }
	//remove unecessary vars
	unset($new_product['type']);
	unset($new_product['return_url']); 
	
 	//we need to get product name and price from database.
    $statement = $mysqli->prepare("SELECT nama_item, harga FROM item WHERE item_id=? LIMIT 1");
    $statement->bind_param('i', $new_product['item_id']);
    $statement->execute();
    $statement->bind_result($nama_item, $harga);
	
	while($statement->fetch()){
		
		//fetch product name, price from db and add to new_product array
        $new_product["nama_item"] = $nama_item;
        $new_product["harga"] = $harga;
        
        if(isset($_SESSION["cart_products"])){  //if session var already exist
            if(isset($_SESSION["cart_products"][$new_product['item_id']])) //check item exist in products array
            {
                unset($_SESSION["cart_products"][$new_product['item_id']]); //unset old array item
            }           
        }
        $_SESSION["cart_products"][$new_product['item_id']] = $new_product; //update or create product session with new item  
    } 
}


//update or remove items 
if(isset($_POST["product_qty"]) || isset($_POST["remove_code"]))
{
	//update item quantity in product session
	if(isset($_POST["product_qty"]) && is_array($_POST["product_qty"])){
		foreach($_POST["product_qty"] as $key => $value){
			if(is_numeric($value)){
				$_SESSION["cart_products"][$key]["product_qty"] = $value;
			}
		}
	}
	//remove an item from product session
	if(isset($_POST["remove_code"]) && is_array($_POST["remove_code"])){
		foreach($_POST["remove_code"] as $key){
			unset($_SESSION["cart_products"][$key]);
		}	
	}
}

//back to return url
$return_url = (isset($_POST["return_url"]))?urldecode($_POST["return_url"]):''; 

header('Location:'.$return_url);
echo $return_url;
?>