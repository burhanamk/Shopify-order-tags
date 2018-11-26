(function() {




})();



var xmlhttp = new XMLHttpRequest();
var url = "/cart.js";

xmlhttp.onreadystatechange = function() {
	if (this.readyState == 4 && this.status == 200) {
		// console.log(this.responseText);
		var product = JSON.parse(this.responseText);
	 	var counter= 0;
	 	var product_table = document.getElementsByClassName("product");
	 	var product__image =document.querySelectorAll("tr.product td.product__image div.product-thumbnail div.product-thumbnail__wrapper");
	 	product.items.forEach(function( val ) {
	        if(val.product_type == 'custom_product' && val != null){
	        	 var html= '<style>.cart__image{position: absolute;top: 0px;}</style>';
	        	 var item_properties=val.properties;

	              if(item_properties['Style'] != ''){
	                html+='<img class="cart__image" src="'+item_properties['Style']+'">';
	              }

	              if(item_properties['Main Fabric'] != ''){
	                html+='<img class="cart__image" src="'+item_properties['Main Fabric']+'">';
	              }
	              if(item_properties['Strap Fabric'] != '' && item_properties['Strap Fabric'] != "undefined"){
	                html+='<img class="cart__image" src="'+item_properties['Strap Fabric']+'">';
	              }
	        	product__image[counter].innerHTML=html;
	        	console.log(counter);
	        }
	        counter++;
		});
	}
};
xmlhttp.open("GET", url, true);
xmlhttp.send();
