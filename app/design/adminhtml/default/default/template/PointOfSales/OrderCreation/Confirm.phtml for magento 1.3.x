<div class="content-header">
    <table cellspacing="0" class="grid-header">
        <tr>
            <td><h3>Order #<?php echo $this->getOrderId(); ?> successfully created</h3></td>
            <td class="a-right">
                <button onclick="document.location.href='<?php echo $this->getNewOrderUrl(); ?>'" class="scalable" type="button"><span><?php echo $this->__('Create new order')?></span></button>
            	<?php if (mage::getStoreConfig('pointofsales/configuration/show_download_buttons') == 1): ?>
                        <button onclick="document.location.href='<?php echo $this->getDownloadOrderUrl(); ?>'" class="scalable" type="button"><span><?php echo $this->__('Print order')?></span></button>
	            	<?php if($this->getDownloadInvoiceLink() != ''): ?>
		                <button onclick="document.location.href='<?php echo $this->getDownloadInvoiceLink(); ?>'" class="scalable" type="button"><span><?php echo $this->__('Print Invoice')?></span></button>
	                <?php endif; ?>
	                <button onclick="document.location.href='<?php echo $this->getDownloadShipmentLink(); ?>'" class="scalable" type="button"><span><?php echo $this->__('Print Shipment')?></span></button>
	            <?php endif; ?>
            	<?php if (mage::getStoreConfig('pointofsales/configuration/show_print_buttons') == 1): ?>
	                <button onclick="posAjaxCall('<?php echo $this->getPrintOrderLink(); ?>');" class="scalable" type="button"><span><?php echo $this->__('Send Order to Printer')?></span></button>
	                <button onclick="posAjaxCall('<?php echo $this->getPrintInvoiceLink(); ?>');" class="scalable" type="button"><span><?php echo $this->__('Send Invoice to Printer')?></span></button>
	                <button onclick="posAjaxCall('<?php echo $this->getPrintShipmentLink(); ?>');" class="scalable" type="button"><span><?php echo $this->__('Send Shipment to Printer')?></span></button>
	            <?php endif; ?>
	            <?php if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')): ?>
                	<button onclick="document.location.href='<?php echo $this->getDisplayOrderLink(); ?>'" class="scalable" type="button"><span><?php echo $this->__('Display Order')?></span></button>
                <?php endif; ?>
            </td>
        </tr>
    </table>
</div>

<?php echo $this->getChildHtml('order_info'); ?>
<?php echo $this->getChildHtml('order_items'); ?>

<div class="box-left">	
    <!--Billing Address-->
    <div class="entry-edit">
        <div class="entry-edit-head">
            <h4 class="icon-head head-payment-method"><?php echo $this->helper('sales')->__('Payment Information') ?></h4>
        </div>
        <fieldset>
            <div><?php echo $this->getChildHtml('order_payment') ?></div>
        </fieldset>
    </div>
</div>

<div class="box-right entry-edit">
    <div class="entry-edit-head"><h4><?php echo Mage::helper('sales')->__('Order Totals') ?></h4></div>
    <div class="order-totals">
    
		<?php $order = Mage::getModel('sales/order')->loadByIncrementId($this->getOrderId());?>
    	<?php $currency = $order->getOrderCurrency(); ; ?>
		   <table width="100%" border="0">
		  <tr>
		    <td><div align="right"><strong>Subtotal</strong></div></td>
		    <td><div align="right"><?php echo $order->formatPrice($order->getSubtotal()); ?></div></td>
		  </tr>
		  <tr>
		    <td><div align="right"><strong>Vat</strong></div></td>
		    <td><div align="right"><?php echo $order->formatPrice($order->getTaxAmount());  ?></div></td>
		  </tr>
		  <tr>
		    <td><div align="right"><strong><h3>Grand Total</h3></strong></div></td>
		    <td><div align="right"><?php echo $order->formatPrice($order->getGrandTotal());  ?></div></td>
		  </tr>
		  <tr>
		    <td><div align="right"><strong><h3>Total Paid</h3></strong></div></td>
		    <td><div align="right"><?php echo $order->formatPrice($order->getTotalPaid());  ?></div></td>
		  </tr>
		</table>
    
    </div>
</div>
