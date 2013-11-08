var rates;	//contains shipping rates

//*****************************************************************************************************************************
//Select address
function selectAddress(type)
{
    if (type == '')
        document.getElementById('div_new_address').className = 'divHidden';
    else
        document.getElementById('div_new_address').className = 'divDisplayed';

}

//*****************************************************************************************************************************
//Select customer type
function selectCustomerType(customerType)
{
    document.getElementById('divAnonymousCustomer').className = 'divHidden';
    document.getElementById('divSelectCustomer').className = 'divHidden';
    document.getElementById('divNewCustomer').className = 'divHidden';
	
    switch(customerType)
    {
        case 'anonymous':
            document.getElementById('divAnonymousCustomer').className = 'divDisplayed';
            break;
        case 'existing':
            document.getElementById('divSelectCustomer').className = 'divDisplayed';
            break;
        case 'new':
            document.getElementById('divNewCustomer').className = 'divDisplayed';
            break;
    }
}

//*****************************************************************************************************************************
//Select customer (probably deprecated)
function selectCustomer(customerId)
{
    document.getElementById('customer_id').value = customerId;
    nextStep();
}

//*****************************************************************************************************************************
//Send order information in ajax
function CompleteOrder(url)
{
    document.getElementById('product_ids').value = getSelectedProductsList();
    document.getElementById("form_one").setAttribute("action", url);
	
    if (!canCreateOrder())
        return false;

    //check if stock movement is possible
    var request = new Ajax.Request(
        pos_submit_url,
        {
            method: 'post',
            onSuccess: function onSuccess(transport)
            {
                elementValues = eval('(' + transport.responseText + ')');
                if (elementValues['error'] == 1)
                {
                    alert(elementValues['message']);
                }
                else
                {
                    document.location.href = elementValues['message'];
                }
            },
            onFailure: function onFailure(transport)
            {
                alert('An error occured !');
            },
            parameters: Form.serialize(document.getElementById('form_one'))
        }
        );
    
}

//*****************************************************************************************************************************
//Validate form
function canCreateOrder()
{
    //must contain at least one product
    if (document.getElementById('product_ids').value == '')
    {
        alert('Please select at least one product !');
        return false;
    }
	
    //check if customer selection is OK
    var customerMode;
    var i;
    var buttons = document.forms['form_one'].customer_mode;
    for(i=0;i<buttons.length;i++)
    {
        if (buttons[i].checked)
            customerMode = buttons[i].value;
    }

    switch(customerMode)
    {
        case 'guest':
            //nothing to test
            break;
        case 'new':
            //check if fields are properly filled
            if (document.getElementById('customer_email').value == '')
            {
                alert('Please fill email');
                return false;
            }
            if (document.getElementById('customer_firstname').value == '')
            {
                alert('Please fill firstname');
                return false;
            }
            if (document.getElementById('customer_lastname').value == '')
            {
                alert('Please fill lastname');
                return false;
            }
            break;
        case 'existing':
		
            break;
    }
	
    return true;
}

//*****************************************************************************************************************************
//Select a product
function selectProduct(productId, productName, priceExclTax, taxRate, priceInclTax, urlimage, currency)
{
    //check if product already added
    var test;
    test = document.getElementById('product_' + productId);

    if (test != null)
    {
        //incrment product qty
        var inputId = 'qty_' + productId;
        var inputObject = document.getElementById(inputId);
        var qty = inputObject.value;
        inputObject.value = parseInt(inputObject.value) + 1;
		
        updateTotalRow(productId);
        updateTotalPrice();
			
        //update shipping method
        updateShippingMethods();
		
        return;
    }
	
    //init row
    var newTR = document.createElement('tr');
    newTR.id = 'product_' + productId;
	
    //defines columns
    var td_product = document.createElement('td');
    td_product.innerHTML = productName;
    td_product.className = 'pos_col_product';
    newTR.appendChild (td_product);

/* quickNdirty: add column for product photo */

	var td_product = document.createElement('td');
    td_product.innerHTML = '<img src="' + imageURL + 'productStub.png" height="40">';
    td_product.className = 'pos_col_photo';
    newTR.appendChild (td_product);
/* /quickNdirty */
	
    var td_qty = document.createElement('td');
    td_qty.innerHTML = '<img class="img_qty" onclick="decreaseQty(' + productId + ')" src="' + urlimage + 'decrease.gif">';
    td_qty.innerHTML += '<input class="input_qty" type="text" name="qty_' + productId + '" id="qty_' + productId + '" value="1" onkeyup="updateTotalPrice();updateTotalRow(' + productId + ');updateShippingMethods();" >';
    td_qty.innerHTML += '<img class="img_qty" onclick="increaseQty(' + productId + ')" src="' + urlimage + 'increase.gif">';
    td_qty.className = 'pos_col_qty';
    newTR.appendChild (td_qty);
	
    //price excl tax
    var td_price_excl_tax = document.createElement('td');
    td_price_excl_tax.innerHTML = '<input type="hidden" id="price_excl_tax_' + productId + '" name="price_excl_tax_' + productId + '" value="' + priceExclTax + '"><span id="div_price_excl_tax_' + productId + '">' + priceExclTax + '</span>';
    td_price_excl_tax.className = 'pos_col_price';
    newTR.appendChild (td_price_excl_tax);
		
    //tax rate
    var td_tax_rate = document.createElement('td');
    td_tax_rate.innerHTML = '<input type="hidden" id="tax_rate_' + productId + '" name="tax_rate_' + productId + '" value="' + taxRate + '"> ' + taxRate + ' %';
    td_tax_rate.className = 'pos_col_price';
    newTR.appendChild (td_tax_rate);
	
    //price incl tax
    var td_price_incl_tax = document.createElement('td');
    td_price_incl_tax.innerHTML = '<input type="text" size="7" id="price_incl_tax_' + productId + '" name="price_incl_tax_' + productId + '" value="' + priceInclTax + '" onkeyup="updateTotalPrice();updateTotalRow(' + productId + ');">';
    td_price_incl_tax.className = 'pos_col_price';
    newTR.appendChild (td_price_incl_tax);
	
    //total incl tax
    var td_total_incl_tax = document.createElement('td');
    td_total_incl_tax.innerHTML = '<span id="div_row_total_' + productId + '"></span>';
    td_total_incl_tax.className = 'pos_col_total';
    newTR.appendChild (td_total_incl_tax);
	
    //shipped
    var td_shipped = document.createElement('td');
    td_shipped.innerHTML = '<input type="checkbox" name="shipped_' + productId + '" id="shipped_' + productId + '" value="1" '+checked+'>';
    td_shipped.className = 'a-center no-display';
    newTR.appendChild (td_shipped);
	
    //delete
    var td_delete = document.createElement('td');
    td_delete.innerHTML = '<img onclick="removeProduct(' + productId + ')" src="' + urlimage + 'delete.gif">';
    td_delete.className = 'pos_col_delete';
    newTR.appendChild (td_delete);

    //add table row
    var myTable = document.getElementById('tbl_products');
    var tBody = myTable.getElementsByTagName('tbody')[0];
    tBody.appendChild(newTR);
	
    updateTotalRow(productId);
    updateTotalPrice();
		
    //update shipping method
    updateShippingMethods();
}

//*****************************************************************************************************************************
//Remove a product
function removeProduct(productId)
{
    //retrieve row
    var myTable = document.getElementById('tbl_products');
    var tBody = myTable.getElementsByTagName('tbody')[0];
    var i;
    for(i=0;i<myTable.rows.length;i++)
    {
        var row = myTable.rows[i];
        if (row.id == 'product_' + productId)
        {
            myTable.deleteRow(i);
        }
    }
		
    updateTotalPrice();
		
    //update shipping method
    updateShippingMethods();
}

//*****************************************************************************************************************************
//Return the list of selected products
function getSelectedProductsList()
{
    var retour = '';
    var divs = document.getElementsByTagName('input');
    for (i=0; i < divs.length; i++)
    {
        if (divs[i] && divs[i].id != null)
        {
            if (divs[i].id.indexOf('qty_') == 0)
            {
                var qty, id, price, isShipped;
                qty = divs[i].value;
                id = divs[i].id.replace('qty_', '');
                priceExclTax = document.getElementById('price_excl_tax_' + id).value;
                priceInclTax = document.getElementById('price_incl_tax_' + id).value;
                if (document.getElementById('shipped_' + id).checked)
                    isShipped = 1;
                else
                    isShipped = 0;
                retour += id + '-' + qty + '-' + priceExclTax + '-' + priceInclTax + '-' + isShipped + ';';
            }
        }
    }

    return retour;
}

//*****************************************************************************************************************************
//Define customer mode
function setCustomerMode(mode)
{
    cancelCustomer();
    switch(mode)
    {
        case 'new':
            document.getElementById('div_cust_new').className = 'div_cust_display';
            document.getElementById('div_cust_existing').className = 'div_cust_hidden';
            document.getElementById('div_selected_customer').style.display = '';
            document.getElementById('newsletter').disabled = false;
            break;
        case 'existing':
            document.getElementById('div_cust_new').className = 'div_cust_hidden';
            document.getElementById('div_cust_existing').className = 'div_cust_display';
            document.getElementById('div_selected_customer').style.display = '';
            document.getElementById('newsletter').disabled = false;
            break;
        case 'guest':
            document.getElementById('div_cust_new').className = 'div_cust_hidden';
            document.getElementById('div_cust_existing').className = 'div_cust_hidden';
            document.getElementById('div_selected_customer').style.display = 'none';
            document.getElementById('newsletter').disabled = true;
            break;
    }
}

//*****************************************************************************************************************************
//Select an existing customer
function selectCustomer(customerId, customerName,url)
{
    document.getElementById('div_selected_customer').className = 'div_cust_display';
	
    new Ajax.Request(url,{
        parameters: {
            'customer_id': customerId
        },
        onSuccess: function(response) {
            var data = response.responseText.evalJSON();

            if(data.street[1] != undefined && data.street[0] != undefined)
                $('address').value = data.street[0] + data.street[1];
            else if(data.city[0] != undefined)
                $('address').value = data.street[0];
				
            if(data.city != undefined)
                $('city').value = data.city;
            if(data.zip != undefined)
                $('zip').value = data.zip;
            if(data.phone != undefined)
                $('phone').value = data.phone;
            if(data.mobile != undefined)
                $('mobile').value = data.mobile;
            if(data.fax != undefined)
                $('fax').value = data.fax;
								
        }
    }
    )
    var tmp;
    tmp  = customerName;
    tmp += ' <a href="javascript:cancelCustomer();">(cancel)</a>';
    document.getElementById('div_selected_customer').innerHTML = tmp;
    document.getElementById('div_cust_existing').className = 'div_cust_hidden';
    document.getElementById('customer_id').value = customerId;
}

//*****************************************************************************************************************************
//cancel customer selection
function cancelCustomer()
{
    document.getElementById('div_selected_customer').className = 'div_cust_hidden';
    document.getElementById('div_selected_customer').innerHTML = '';
    document.getElementById('div_cust_existing').className = 'div_cust_display';
    document.getElementById('customer_id').value = '';
    document.getElementById('address').value = '';
    document.getElementById('city').value = '';
    document.getElementById('zip').value = '';
	
    document.getElementById('mobile').value = '';
    document.getElementById('phone').value = '';
    document.getElementById('fax').value = '';
}

//*****************************************************************************************************************************
//Update order total
function updateTotalPrice()
{
	
    var retour = 0;
    var divs = document.getElementsByTagName('input');
    for (i=0; i < divs.length; i++)
    {
        if (divs[i] && divs[i].id != null)
        {
            if (divs[i].id.indexOf('price_incl_tax_') == 0)
            {
                var priceInclTax = parseFloat(divs[i].value);
                var id = divs[i].id.replace('price_incl_tax_', '');
                var qty = document.getElementById('qty_' + id).value
                retour += parseFloat(qty * priceInclTax);
            }
        }
    }
	
    //add shipping costs
    if (rates)
    {
        var shippingCost = 0;
        var selectedShippingMethod = document.getElementById('shippingmethod').value;
        var i;
        for(i=0;i<rates.length;i++)
        {
            if (selectedShippingMethod == rates[i]['code'])
                shippingCost = rates[i]['price'];
        }
        retour += parseFloat(shippingCost);
    }
		
    document.getElementById('div_total').innerHTML = parseFloat(retour).toFixed(2);
}

//*****************************************************************************************************************************
//Update total & price excl tax for one row
function updateTotalRow(productId)
{
    //retrieve values
    var priceInclTax = document.getElementById('price_incl_tax_' + productId).value;
    var qty = document.getElementById('qty_' + productId).value;
    var taxRate = document.getElementById('tax_rate_' + productId).value;
	
    //compute other values
    var priceExclTax = (priceInclTax / (1 + (taxRate / 100)));
    var rowTotal = priceInclTax * qty;
	
    //update fields
    document.getElementById('div_price_excl_tax_' + productId).innerHTML = priceExclTax.toFixed(2);
    document.getElementById('price_excl_tax_' + productId).value = priceExclTax.toFixed(2);
	
    document.getElementById('div_row_total_' + productId).innerHTML = rowTotal.toFixed(2);

}

//*****************************************************************************************************************************
//Display or hide product grid
function toggleProductGrid()
{
    var div;
    div = document.getElementById('div_products_grid');
    if (div.style.display == 'none')
        div.style.display = '';
    else
        div.style.display = 'none';
}

//*****************************************************************************************************************************
//Standard method to call ajax
function posAjaxCall(url)
{
    var request = new Ajax.Request(
        url,
        {
            method: 'GET',
            onSuccess: function onSuccess(transport)
            {
                elementValues = eval('(' + transport.responseText + ')');
            },
            onFailure: function onFailure(transport)
            {
                alert('error');
            }
        }
        );
}

//*****************************************************************************************************************************
///Update payment form depending of the selected payment method
function updatePaymentForm()
{
    //todo: implement
    return;
	
    //Define url
    var paymentMethod = document.getElementById('paymentmethod').value;
    var url = paymentFormAjaxUrl;
    url += 'payment_method/' + paymentMethod;
	
    //send request
    var request = new Ajax.Request(
        paymentFormAjaxUrl,
        {
            method: 'GET',
            onSuccess: function onSuccess(transport)
            {
                elementValues = eval('(' + transport.responseText + ')');
            },
            onFailure: function onFailure(transport)
            {
                alert('error');
            }
        }
        );
}

//*****************************************************************************************************************************
//Update shipping methods dropdown menu
function updateShippingMethods()
{
    document.getElementById('product_ids').value = getSelectedProductsList();
	
    //check if stock movement is possible
    var request = new Ajax.Request(
        shippingMethodAjaxUrl,
        {
            method: 'post',
            onSuccess: function onSuccess(transport)
            {
                var elementValues = eval('(' + transport.responseText + ')');
                if (elementValues['error'] == 1)
                {
                    alert(elementValues['message']);
                }
                else
                {
                    //empty shipping methods div
                    var myDiv = document.getElementById('div_shipping_methods');
                    var html = '';
                    myDiv.innerHTML = '';

                    
                    rates = elementValues['shippingRates'];
                    var defaultShippingCode = '';
                    for(var i=0;i<rates.length;i++)
                    {
                        //select default shipping method
                        var divClass = 'span_shipping_method_label';
                        if (rates[i]['method'] == defaultShippingMethod)
                        {
                            defaultShippingCode = rates[i]['code'];
                            divClass = 'span_shipping_method_label_selected';
                        }
                        html += '<div onclick="selectShippingMethod(\'' + rates[i]['code'] + '\');" id="span_shipping_method_' + rates[i]['code'] + '" class="' + divClass + '">' + rates[i]['label'] + ' - ' + rates[i]['price_formated'] + '</div>';
                    }
                    
                    html += '<input type="hidden" name="shippingmethod" id="shippingmethod" value="' + defaultShippingCode + '">';

                    myDiv.innerHTML =  html;
		        					
                    updateTotalPrice();
                }
            },
            onFailure: function onFailure(transport)
            {
                alert('An error occured !');
            },
            parameters: Form.serialize(document.getElementById('form_one'))
        }
        );
}

//*****************************************************************************************************************************
//*****************************************************************************************************************************
// SCANNER FUNCTIONS
//*****************************************************************************************************************************
//*****************************************************************************************************************************

var KC_catchKeys = false;
var KC_displayInput = null;
var KC_value = '';
var KC_onEnter = null;


//***************************************************************************************************
//Catch keys
function enableCatchKeys(edisplayInput, eOnEnter)
{
    KC_catchKeys = true;
    KC_displayInput = edisplayInput;
    KC_onEnter = eOnEnter;
    //document.getElementById('scanner_status').innerHTML = labelScannerReady;
    //document.getElementById('scanner_status').className = 'scanner-status-ready';
}

function disableCatchKeys()
{
    KC_catchKeys = false;
    //document.getElementById('scanner_status').innerHTML = labelScannerHolded;
    //document.getElementById('scanner_status').className = 'scanner-status-holdon';
}

function handleKey(evt) {
	
    if (!KC_catchKeys)
        return true;


    //if current focused element is textbox, return true
    if (document.activeElement)
    {
        if ((document.activeElement.type == 'text') || (document.activeElement.tagName.toLowerCase() == 'textarea'))
        {
            return true;
        }
    }

    var evt = (window.event ? window.event : evt);
    var keyCode;

    //if not ie
    if (navigator.appName != 'Microsoft Internet Explorer')
        keyCode = evt.which;
    else
        keyCode = evt.keyCode;
		
    if (keyCode != 13)
    {
        KC_value += String.fromCharCode(keyCode);
        if (KC_displayInput != null)
            KC_displayInput.value = KC_value;
        document.getElementById('scanner_value').innerHTML = KC_value;
    }
    else
    {
        eval(KC_onEnter);
    }
		
    return false;
} 

//*****************************************************************************************************************************
//Scan product
function addProductFromBarcode()
{
    var barcode = KC_value;
    KC_value = '';
    var url = addProductFromBarcodeUrl;
    url += 'barcode/' + barcode.replace("/", "slash");  //cant use urlencode method with ajax

    if (barcode == '')
        return;
	
    var request = new Ajax.Request(
        url,
        {
            method: 'get',
            onSuccess: function onSuccess(transport)
            {
                elementValues = eval('(' + transport.responseText + ')');
                if (elementValues['error'] == 1)
                {
                    //display error message
                    document.getElementById('scanner_value').innerHTML = elementValues['message'];
                }
                else
                {
                    //add product
                    selectProduct(elementValues['product_information']['product_id'],
                        elementValues['product_information']['product_name'],
                        elementValues['product_information']['price_excl_tax'],
                        elementValues['product_information']['tax_rate'],
                        elementValues['product_information']['price_incl_tax'],
                        elementValues['product_information']['skin_url'],
                        elementValues['product_information']['currency_symbol']);
		        									
                    document.getElementById('scanner_value').innerHTML = elementValues['message'];
                }
            },
            onFailure: function onFailure(transport)
            {
                alert('An error occured !');
            }
        }
        );
    
    //reset
    document.getElementById('scanner_value').innerHTML = KC_value;
}

//******************************************************************************************************************************
//show order details popup windows
function showOrderDetails(url)
{
    var request = new Ajax.Request(
        url,
        {
            method: 'GET',
            onSuccess: function onSuccess(transport)
            {
                var content = transport.responseText;
	        				
                //display windows
                win = new Window({
                    className: "alphacube",
                    width:1000,
                    height:600,
                    destroyOnClose:true,
                    closable:true,
                    draggable:true,
                    recenterAuto:true,
                    okLabel: "OK"
                });
                win.setHTMLContent(content);
                win.showCenter();
            },
            onFailure: function onFailure(transport)
            {
                alert('An error occured : ' + url);
            }
        }
        );
}

//******************************************************************************************************************************
//
function showProductDetails(url)
{
 var request = new Ajax.Request(
        url,
        {
            method: 'GET',
            onSuccess: function onSuccess(transport)
            {
                var content = transport.responseText;

                Dialog.alert(
                    content,
                    {
                        id: "productInfosDialog",
                        buttonClass:"dialogButton",
                        width:600,
                        height:330,
                        okLabel: closeModaleWindow,
                        destroyOnClose:true,
                        draggable:true,
                        recenterAuto: true,
                        className: "alphacube",
                        ok:function(win) {return true;}
                    }
                );

                //display windows
                /*win = new Window({
                    className: "alphacube",
                    width:600,
                    height:300,
                    destroyOnClose:true,
                    closable:true,
                    draggable:true,
                    recenterAuto:true,
                    okLabel: "OK"
                });
                win.setHTMLContent(content);
                win.showCenter();*/
            },
            onFailure: function onFailure(transport)
            {
                alert('An error occured : ' + url);
            }
        }
        );
}

function showTab(currentMenu)
{
    document.getElementById('tab_add_products_tab').className = 'entry-edit hidden-menu';
    document.getElementById('tab_customer_information_tab').className = 'entry-edit hidden-menu';
    document.getElementById('tab_customer_address_tab').className = 'entry-edit hidden-menu';
    document.getElementById('tab_comments_tab').className = 'entry-edit hidden-menu';

    document.getElementById("tab_" + currentMenu).className = 'entry-edit visible-menu';
}

function selectPaymentMethod(method)
{
    //unselect old method
    var currentSelectedMethod = document.getElementById('paymentmethod').value;
    if (currentSelectedMethod != '')
    {
        if (document.getElementById('img_payment_' + currentSelectedMethod))
            document.getElementById('img_payment_' + currentSelectedMethod).className = 'payment_method';
    }
	if(document.getElementById('img_payment_'+method) != undefined)
	    document.getElementById('img_payment_' + method).className = 'payment_method_selected';

    document.getElementById('paymentmethod').value = method;
}

function selectShippingMethod(method)
{
    if (document.getElementById('shippingmethod'))
    {
        var previousMethod = document.getElementById('shippingmethod').value;
        if (previousMethod != '')
        {
            var divId = 'span_shipping_method_' + previousMethod;
            if (document.getElementById(divId))
                document.getElementById(divId).className = 'span_shipping_method_label';
        }
    }

    document.getElementById('span_shipping_method_' + method).className = 'span_shipping_method_label_selected';
    document.getElementById('shippingmethod').value = method;

    updateTotalPrice();
}

function decreaseQty(productId)
{
    var input = document.getElementById('qty_' + productId);
    if (input)
    {
        var value = input.value;
        if (value > 1)
            value--;
        input.value = value;

        updateTotalRow(productId);
        updateTotalPrice();
        updateShippingMethods();
    }
}

function increaseQty(productId)
{
    var input = document.getElementById('qty_' + productId);
    if (input)
    {
        var value = input.value;
        value++;
        input.value = value;
        
        updateTotalRow(productId);
        updateTotalPrice();
        updateShippingMethods();
    }
}

//*************************************************************************************************************************************
//*************************************************************************************************************************************
// CHANGE CALCULATOR
//*************************************************************************************************************************************
//*************************************************************************************************************************************

//*************************************************************************************************************************************
//show change calculator
function ShowChangeCalculator(orderTotal)
{
	var url = changeCalculatorUrl;
	var request = new Ajax.Request(
        url,
        {
            method: 'GET',
            onSuccess: function onSuccess(transport)
            {
                var content = transport.responseText;

                Dialog.alert(
                    content,
                    {
                        id: "productInfosDialog",
                        buttonClass:"dialogButton",
                        width:600,
                        height:450,
                        okLabel: closeModaleWindow,
                        destroyOnClose:true,
                        draggable:true,
                        recenterAuto: true,
                        className: "alphacube",
                        ok:function(win) {return true;}
                    }
                );

                //display windows
                /*win = new Window({
                    className: "alphacube",
                    width:600,
                    height:400,
                    destroyOnClose:true,
                    closable:true,
                    draggable:true,
                    recenterAuto:true,
                    okLabel: "OK"
                });
                win.setHTMLContent(content);
                win.showCenter();*/
				
                orderTotal = parseFloat(orderTotal);
                orderTotal = orderTotal.toFixed(2);
                document.getElementById('cc_order_total').innerHTML = orderTotal + ' ' + currency;
                document.getElementById('cc_given_amount').innerHTML = '0.00 ' + currency;
            },
            onFailure: function onFailure(transport)
            {
                alert('An error occured : ' + url);
            }
        }
        );
}

//*************************************************************************************************************************************
//Add amount for change calculator
function addAmount(value)
{
	var currentValue = parseFloat(document.getElementById('cc_given_amount').innerHTML);
	currentValue += value;
	currentValue = currentValue.toFixed(2);
	document.getElementById('cc_given_amount').innerHTML = currentValue + ' ' + currency;
	
	calculateChange();
	
}

function resetAmount()
{
	document.getElementById('cc_given_amount').innerHTML = '0.00 ' + currency;
	calculateChange();
}

//*************************************************************************************************************************************
//Calculate change
function calculateChange()
{
	var orderTotal = parseFloat(document.getElementById('cc_order_total').innerHTML);
	var givenMoney = parseFloat(document.getElementById('cc_given_amount').innerHTML);
	var change = (givenMoney - orderTotal);
	change = change.toFixed(2);
	if (change > 0)
		document.getElementById('cc_change').innerHTML = change + ' ' + currency;
	else
		document.getElementById('cc_change').innerHTML = 'Error';
}


