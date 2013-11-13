//********************************
// Add product image in main grid for PointOfSales/OrderForm
var table = document.getElementById('PointOfSalesProductGrid');
if(table != undefined){
	var grid = table.getElementsByClassName('grid')[0];
	var tbody = grid.getElementsByTagName('tbody')[0];
	var trs = tbody.getElementsByTagName('tr');
	for(i=0;i<trs.length;i++){
		var td = trs[i].childNodes[3];
		td.innerHTML = '<img src="'+imageURL+'/productStub.png">';
	}
}

//****************************
// Add customer image in main grid of Customers/ManageCustomers
/*if((table = document.getElementById('customerGrid_table')) != undefined){
	var tbody = table.getElementsByTagName('tbody')[0];
	var trs = tbody.getElementsByTagName('tr');
	for(i=0;i<trs.length;i++){
		var td = trs[i].childNodes[5];
		var id = td.textContent.trim();
		switch(id){
			case "2":
				img = "michel.jpg";
				break;
			case "3":
			case "4":
				img = "santhosh.jpg";
				break;
			case "6":
				img = "daniel.jpg";
				break;
			default:
				img = "defaultHead.jpg";
		}
		var photoTD = trs[i].childNodes[7];
		photoTD.innerHTML = '<img src="'+imageURL+'/'+img+'">';
	}
}

if((img = document.getElementById('customerPhoto')) != undefined){
	switch(img.getAttribute('customer').toLowerCase()){
			case "michel":
				url = "michel";
				break;
			case "santhosh":
			case "4":
				url = "santhosh";
				break;
			case "daniel":
				url = "daniel";
				break;
			default:
				url = "defaultHead";
		}
	img.src = imageURL+'/'+url+'.jpg';
}
*/