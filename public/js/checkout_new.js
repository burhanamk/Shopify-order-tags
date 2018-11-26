(function() {
 var orderdetails = Shopify.checkout;
 var shopifyorderid = orderdetails.order_id;
var Appurl="https://cc2d8e07.ngrok.io/2018/oct/wesley/";
var xmlhttp = new XMLHttpRequest();
var url = Appurl+"OrdersDetails?shop="+Shopify.shop+"&orderId="+shopifyorderid;
xmlhttp.onreadystatechange = function() {
if (this.readyState == 4 && this.status == 200) {
	var offers = JSON.parse(this.responseText);
	console.log(offers);
	if(offers.code==200){
	}
}
};
xmlhttp.open("GET", url, true);
xmlhttp.send();
})();
