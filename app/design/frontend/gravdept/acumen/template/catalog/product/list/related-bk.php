<?php
/**
* Acumen for Magento
* http://gravitydept.com/to/acumen-magento
*
* @author     Brendan Falkowski
* @package    gravdept_acumen
* @copyright  Copyright 2013 Gravity Department http://gravitydept.com
* @license    All rights reserved.
* @version    1.3.5
*/
?>

<?php if($this->getItems()->getSize()): ?>
	<div id="related" class="block">
		<div class="block-title">
			<h2><?php echo $this->__('Related Products') ?></h2>
		</div>
		
		<div class="block-content">
    		<ol>
    			<?php foreach($this->getItems() as $_item): ?>
    				<li>
    					<?php if(!$_item->isComposite() && $_item->isSaleable()): ?>
    						<?php if (!$_item->getRequiredOptions()): ?>
    							<input type="checkbox" class="checkbox related-checkbox" id="related-checkbox<?php echo $_item->getId() ?>" name="related_products[]" value="<?php echo $_item->getId() ?>" />
    						<?php endif; ?>
    					<?php endif; ?>
    					
    					<div class="product">
    						<a class="product-image" href="<?php echo $_item->getProductUrl() ?>">
                                <img src="<?php echo $this->helper('catalog/image')->init($_item, 'thumbnail')->resize(50) ?>" alt="<?php echo $this->escapeHtml($_item->getName()) ?>" />
                            </a>
    						
                            <div class="product-details">
    							<a class="product-name" href="<?php echo $_item->getProductUrl() ?>"><?php echo $this->escapeHtml($_item->getName()) ?></a>
    							<?php echo $this->getPriceHtml($_item, true, '-related') ?>
    							
    							<?php /*if ($this->helper('wishlist')->isAllow()) : ?>
    								<a href="<?php echo $this->getAddToWishlistUrl($_item) ?>" class="link-wishlist"><?php echo $this->__('Add to Wishlist') ?></a>
    							<?php endif;*/ ?>
    						</div>
    					</div>
    				</li>
    			<?php endforeach ?>
    		</ol>
    
    		<p class="instruct"><?php echo $this->__('Checked products are added to the cart with this item.') ?></p>
    		<a class="button" href="#" onclick="selectAllRelated(this); return false;"><?php echo $this->__('Select All') ?></a>
    		
    		<script type="text/javascript">
    			//<![CDATA[
    			$$('.related-checkbox').each(function(elem){
    				Event.observe(elem, 'click', addRelatedToProduct)
    			});
    		
    			var relatedProductsCheckFlag = false;
    			function selectAllRelated(txt){
    				if (relatedProductsCheckFlag == false) {
    					$$('.related-checkbox').each(function(elem){
    						elem.checked = true;
    					});
    					relatedProductsCheckFlag = true;
    					txt.innerHTML="<?php echo $this->__('unselect all') ?>";
    				} else {
    					$$('.related-checkbox').each(function(elem){
    						elem.checked = false;
    					});
    					relatedProductsCheckFlag = false;
    					txt.innerHTML="<?php echo $this->__('select all') ?>";
    				}
    				addRelatedToProduct();
    			}
    		
    			function addRelatedToProduct(){
    				var checkboxes = $$('.related-checkbox');
    				var values = [];
    				for(var i=0;i<checkboxes.length;i++){
    					if(checkboxes[i].checked) values.push(checkboxes[i].value);
    				}
    				if($('related-products-field')){
    					$('related-products-field').value = values.join(',');
    				}
    			}
    			//]]>
    		</script>
    	</div>
    </div>
<?php endif ?>
