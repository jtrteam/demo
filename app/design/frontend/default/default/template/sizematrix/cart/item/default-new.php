
<?php
$_item = $this->getItem();
$isVisibleProduct = $_item->getProduct()->isVisibleInSiteVisibility();
$canApplyMsrp = Mage::helper('catalog')->canApplyMsrp($_item->getProduct(), Mage_Catalog_Model_Product_Attribute_Source_Msrp_Type::TYPE_BEFORE_ORDER_CONFIRM);
$_product = Mage::getModel('catalog/product')->load($_item->getProductId());
$prodAttributeSet = Mage::getModel('eav/entity_attribute_set')->load($_product->getAttributeSetId())->getAttributeSetName();
$metrixValue = array(); 
if($prodAttributeSet != 'Matrix'):
?>
<tr>
    <td><?php if ($this->hasProductUrl()):?><a href="<?php echo $this->getProductUrl() ?>" title="<?php echo $this->escapeHtml($this->getProductName()) ?>" class="product-image"><?php endif;?><img src="<?php echo $this->getProductThumbnail()->resize(75); ?>" width="75" height="75" alt="<?php echo $this->escapeHtml($this->getProductName()) ?>" /><?php if ($this->hasProductUrl()):?></a><?php endif;?></td>
    <td>
        <h2 class="product-name">
        <?php if ($this->hasProductUrl()):?>
            <a href="<?php echo $this->getProductUrl() ?>"><?php echo $this->escapeHtml($this->getProductName()) ?></a>
        <?php else: ?>
            <?php echo $this->escapeHtml($this->getProductName()) ?>
        <?php endif; ?>
        </h2>
        <?php if ($_options = $this->getOptionList()):?>
        <dl class="item-options">
            <?php foreach ($_options as $_option) : ?>
            <?php $_formatedOptionValue = $this->getFormatedOptionValue($_option) ?>
            <dt><?php echo $this->escapeHtml($_option['label']) ?></dt>
            <dd<?php if (isset($_formatedOptionValue['full_view'])): ?> class="truncated"<?php endif; ?>><?php echo $_formatedOptionValue['value'] ?>
                <?php if (isset($_formatedOptionValue['full_view'])): ?>
                <div class="truncated_full_value">
                    <dl class="item-options">
                        <dt><?php echo $this->escapeHtml($_option['label']) ?></dt>
                        <dd><?php echo $_formatedOptionValue['full_view'] ?></dd>
                    </dl>
                </div>
                <?php endif; ?>
            </dd>
            <?php endforeach; ?>
        </dl>
        <?php endif;?>
        <?php if ($messages = $this->getMessages()): ?>
        <?php foreach ($messages as $message): ?>
            <p class="item-msg <?php echo $message['type'] ?>">* <?php echo $this->escapeHtml($message['text']) ?></p>
        <?php endforeach; ?>
        <?php endif; ?>
        <?php $addInfoBlock = $this->getProductAdditionalInformationBlock(); ?>
        <?php if ($addInfoBlock): ?>
            <?php echo $addInfoBlock->setItem($_item)->toHtml() ?>
        <?php endif;?>
    </td>
    <td class="a-center">
        <?php if ($isVisibleProduct): ?>
        <a href="<?php echo $this->getConfigureUrl() ?>" title="<?php echo $this->__('Edit item parameters') ?>"><?php echo $this->__('Edit') ?></a>
        <?php endif ?>
    </td>
    <?php if ($this->helper('wishlist')->isAllowInCart()) : ?>
    <td class="a-center">
        <?php if ($isVisibleProduct): ?>
            <a href="<?php echo $this->helper('wishlist')->getMoveFromCartUrl($_item->getId()); ?>" class="link-wishlist use-ajax"><?php echo $this->__('Move'); ?></a>
        <?php endif ?>
    </td>
    <?php endif ?>

    <?php if ($canApplyMsrp): ?>
        <td class="a-right"<?php if ($this->helper('tax')->displayCartBothPrices()): ?> colspan="2"<?php endif; ?>>
            <span class="cart-price">
                <span class="cart-msrp-unit"><?php echo $this->__('See price before order confirmation.'); ?></span>
                <?php $helpLinkId = 'cart-msrp-help-' . $_item->getId(); ?>
                <a id="<?php echo $helpLinkId ?>" href="#" class="map-help-link"><?php echo $this->__("What's this?"); ?></a>
                <script type="text/javascript">
                    Catalog.Map.addHelpLink($('<?php echo $helpLinkId ?>'), "<?php echo $this->__("What's this?") ?>");
                </script>
            </span>
        </td>
    <?php else: ?>

        <?php if ($this->helper('tax')->displayCartPriceExclTax() || $this->helper('tax')->displayCartBothPrices()): ?>
        <td class="a-right">
            <?php if (Mage::helper('weee')->typeOfDisplay($_item, array(1, 4), 'sales') && $_item->getWeeeTaxAppliedAmount()): ?>
                <span class="cart-tax-total" onclick="taxToggle('eunit-item-tax-details<?php echo $_item->getId(); ?>', this, 'cart-tax-total-expanded');">
            <?php else: ?>
                <span class="cart-price">
            <?php endif; ?>
                <?php if (Mage::helper('weee')->typeOfDisplay($_item, array(0, 1, 4), 'sales') && $_item->getWeeeTaxAppliedAmount()): ?>
                    <?php echo $this->helper('checkout')->formatPrice($_item->getCalculationPrice()+$_item->getWeeeTaxAppliedAmount()+$_item->getWeeeTaxDisposition()); ?>
                <?php else: ?>
                    <?php echo $this->helper('checkout')->formatPrice($_item->getCalculationPrice()) ?>
                <?php endif; ?>

            </span>


            <?php if (Mage::helper('weee')->getApplied($_item)): ?>

                <div class="cart-tax-info" id="eunit-item-tax-details<?php echo $_item->getId(); ?>" style="display:none;">
                    <?php if (Mage::helper('weee')->typeOfDisplay($_item, 1, 'sales') && $_item->getWeeeTaxAppliedAmount()): ?>
                        <?php foreach (Mage::helper('weee')->getApplied($_item) as $tax): ?>
                            <span class="weee"><?php echo $tax['title']; ?>: <?php echo Mage::helper('checkout')->formatPrice($tax['amount'],true,true); ?></span>
                        <?php endforeach; ?>
                    <?php elseif (Mage::helper('weee')->typeOfDisplay($_item, 2, 'sales') && $_item->getWeeeTaxAppliedAmount()): ?>
                        <?php foreach (Mage::helper('weee')->getApplied($_item) as $tax): ?>
                            <span class="weee"><?php echo $tax['title']; ?>: <?php echo Mage::helper('checkout')->formatPrice($tax['amount_incl_tax'],true,true); ?></span>
                        <?php endforeach; ?>
                    <?php elseif (Mage::helper('weee')->typeOfDisplay($_item, 4, 'sales') && $_item->getWeeeTaxAppliedAmount()): ?>
                        <?php foreach (Mage::helper('weee')->getApplied($_item) as $tax): ?>
                            <span class="weee"><?php echo $tax['title']; ?>: <?php echo Mage::helper('checkout')->formatPrice($tax['amount_incl_tax'],true,true); ?></span>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <?php if (Mage::helper('weee')->typeOfDisplay($_item, 2, 'sales') && $_item->getWeeeTaxAppliedAmount()): ?>
                    <div class="cart-tax-total" onclick="taxToggle('eunit-item-tax-details<?php echo $_item->getId(); ?>', this, 'cart-tax-total-expanded');">
                        <span class="weee"><?php echo Mage::helper('weee')->__('Total'); ?>: <?php echo $this->helper('checkout')->formatPrice($_item->getCalculationPrice()+$_item->getWeeeTaxAppliedAmount()+$_item->getWeeeTaxDisposition()); ?></span>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </td>
        <?php endif; ?>
        <?php if ($this->helper('tax')->displayCartPriceInclTax() || $this->helper('tax')->displayCartBothPrices()): ?>
        <td>
            <?php $_incl = $this->helper('checkout')->getPriceInclTax($_item); ?>
            <?php if (Mage::helper('weee')->typeOfDisplay($_item, array(1, 4), 'sales') && $_item->getWeeeTaxAppliedAmount()): ?>
                <span class="cart-tax-total" onclick="taxToggle('unit-item-tax-details<?php echo $_item->getId(); ?>', this, 'cart-tax-total-expanded');">
            <?php else: ?>
                <span class="cart-price">
            <?php endif; ?>

                <?php if (Mage::helper('weee')->typeOfDisplay($_item, array(0, 1, 4), 'sales') && $_item->getWeeeTaxAppliedAmount()): ?>
                    <?php echo $this->helper('checkout')->formatPrice($_incl+$_item->getWeeeTaxAppliedAmount()); ?>
                <?php else: ?>
                    <?php echo $this->helper('checkout')->formatPrice($_incl-$_item->getWeeeTaxDisposition()) ?>
                <?php endif; ?>

            </span>
            <?php if (Mage::helper('weee')->getApplied($_item)): ?>

                <div class="cart-tax-info" id="unit-item-tax-details<?php echo $_item->getId(); ?>" style="display:none;">
                    <?php if (Mage::helper('weee')->typeOfDisplay($_item, 1, 'sales') && $_item->getWeeeTaxAppliedAmount()): ?>
                        <?php foreach (Mage::helper('weee')->getApplied($_item) as $tax): ?>
                            <span class="weee"><?php echo $tax['title']; ?>: <?php echo Mage::helper('checkout')->formatPrice($tax['amount'],true,true); ?></span>
                        <?php endforeach; ?>
                    <?php elseif (Mage::helper('weee')->typeOfDisplay($_item, 2, 'sales') && $_item->getWeeeTaxAppliedAmount()): ?>
                        <?php foreach (Mage::helper('weee')->getApplied($_item) as $tax): ?>
                            <span class="weee"><?php echo $tax['title']; ?>: <?php echo Mage::helper('checkout')->formatPrice($tax['amount_incl_tax'],true,true); ?></span>
                        <?php endforeach; ?>
                    <?php elseif (Mage::helper('weee')->typeOfDisplay($_item, 4, 'sales') && $_item->getWeeeTaxAppliedAmount()): ?>
                        <?php foreach (Mage::helper('weee')->getApplied($_item) as $tax): ?>
                            <span class="weee"><?php echo $tax['title']; ?>: <?php echo Mage::helper('checkout')->formatPrice($tax['amount_incl_tax'],true,true); ?></span>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <?php if (Mage::helper('weee')->typeOfDisplay($_item, 2, 'sales') && $_item->getWeeeTaxAppliedAmount()): ?>
                    <div class="cart-tax-total" onclick="taxToggle('unit-item-tax-details<?php echo $_item->getId(); ?>', this, 'cart-tax-total-expanded');">
                        <span class="weee"><?php echo Mage::helper('weee')->__('Total incl. tax'); ?>: <?php echo $this->helper('checkout')->formatPrice($_incl+$_item->getWeeeTaxAppliedAmount()); ?></span>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </td>
        <?php endif; ?>
    <?php endif; ?>
    <td class="a-center">
        <input name="cart[<?php echo $_item->getId() ?>][qty]" value="<?php echo $this->getQty() ?>" size="4" title="<?php echo $this->__('Qty') ?>" class="input-text qty" maxlength="12" />
    </td>
    <?php if (($this->helper('tax')->displayCartPriceExclTax() || $this->helper('tax')->displayCartBothPrices()) && !$_item->getNoSubtotal()): ?>
    <td class="a-right">
        <?php if (Mage::helper('weee')->typeOfDisplay($_item, array(1, 4), 'sales') && $_item->getWeeeTaxAppliedAmount()): ?>
            <span class="cart-tax-total" onclick="taxToggle('esubtotal-item-tax-details<?php echo $_item->getId(); ?>', this, 'cart-tax-total-expanded');">
        <?php else: ?>
            <span class="cart-price">
        <?php endif; ?>

            <?php if ($canApplyMsrp): ?>
                <span class="cart-msrp-subtotal">--</span>
            <?php else: ?>
                <?php if (Mage::helper('weee')->typeOfDisplay($_item, array(0, 1, 4), 'sales') && $_item->getWeeeTaxAppliedAmount()): ?>
                    <?php echo $this->helper('checkout')->formatPrice($_item->getRowTotal()+$_item->getWeeeTaxAppliedRowAmount()+$_item->getWeeeTaxRowDisposition()); ?>
                <?php else: ?>
                    <?php echo $this->helper('checkout')->formatPrice($_item->getRowTotal()) ?>
                <?php endif; ?>
            <?php endif; ?>

        </span>
        <?php if (Mage::helper('weee')->getApplied($_item)): ?>

            <div class="cart-tax-info" id="esubtotal-item-tax-details<?php echo $_item->getId(); ?>" style="display:none;">
                <?php if (Mage::helper('weee')->typeOfDisplay($_item, 1, 'sales') && $_item->getWeeeTaxAppliedAmount()): ?>
                    <?php foreach (Mage::helper('weee')->getApplied($_item) as $tax): ?>
                        <span class="weee"><?php echo $tax['title']; ?>: <?php echo Mage::helper('checkout')->formatPrice($tax['row_amount'],true,true); ?></span>
                    <?php endforeach; ?>
                <?php elseif (Mage::helper('weee')->typeOfDisplay($_item, 2, 'sales') && $_item->getWeeeTaxAppliedAmount()): ?>
                    <?php foreach (Mage::helper('weee')->getApplied($_item) as $tax): ?>
                        <span class="weee"><?php echo $tax['title']; ?>: <?php echo Mage::helper('checkout')->formatPrice($tax['row_amount_incl_tax'],true,true); ?></span>
                    <?php endforeach; ?>
                <?php elseif (Mage::helper('weee')->typeOfDisplay($_item, 4, 'sales') && $_item->getWeeeTaxAppliedAmount()): ?>
                    <?php foreach (Mage::helper('weee')->getApplied($_item) as $tax): ?>
                        <span class="weee"><?php echo $tax['title']; ?>: <?php echo Mage::helper('checkout')->formatPrice($tax['row_amount_incl_tax'],true,true); ?></span>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <?php if (Mage::helper('weee')->typeOfDisplay($_item, 2, 'sales') && $_item->getWeeeTaxAppliedAmount()): ?>
                <div class="cart-tax-total" onclick="taxToggle('esubtotal-item-tax-details<?php echo $_item->getId(); ?>', this, 'cart-tax-total-expanded');">
                    <span class="weee"><?php echo Mage::helper('weee')->__('Total'); ?>: <?php echo $this->helper('checkout')->formatPrice($_item->getRowTotal()+$_item->getWeeeTaxAppliedRowAmount()+$_item->getWeeeTaxRowDisposition()); ?></span>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </td>
    <?php endif; ?>
    <?php if (($this->helper('tax')->displayCartPriceInclTax() || $this->helper('tax')->displayCartBothPrices()) && !$_item->getNoSubtotal()): ?>
    <td>
        <?php $_incl = $this->helper('checkout')->getSubtotalInclTax($_item); ?>
        <?php if (Mage::helper('weee')->typeOfDisplay($_item, array(1, 4), 'sales') && $_item->getWeeeTaxAppliedAmount()): ?>
            <span class="cart-tax-total" onclick="taxToggle('subtotal-item-tax-details<?php echo $_item->getId(); ?>', this, 'cart-tax-total-expanded');">
        <?php else: ?>
            <span class="cart-price">
        <?php endif; ?>

            <?php if ($canApplyMsrp): ?>
                <span class="cart-msrp-subtotal">--</span>
            <?php else: ?>
                <?php if (Mage::helper('weee')->typeOfDisplay($_item, array(0, 1, 4), 'sales') && $_item->getWeeeTaxAppliedAmount()): ?>
                    <?php echo $this->helper('checkout')->formatPrice($_incl+$_item->getWeeeTaxAppliedRowAmount()); ?>
                <?php else: ?>
                    <?php echo $this->helper('checkout')->formatPrice($_incl-$_item->getWeeeTaxRowDisposition()) ?>
                <?php endif; ?>
            <?php endif; ?>

        </span>


        <?php if (Mage::helper('weee')->getApplied($_item)): ?>

            <div class="cart-tax-info" id="subtotal-item-tax-details<?php echo $_item->getId(); ?>" style="display:none;">
                <?php if (Mage::helper('weee')->typeOfDisplay($_item, 1, 'sales') && $_item->getWeeeTaxAppliedAmount()): ?>
                    <?php foreach (Mage::helper('weee')->getApplied($_item) as $tax): ?>
                        <span class="weee"><?php echo $tax['title']; ?>: <?php echo Mage::helper('checkout')->formatPrice($tax['row_amount'],true,true); ?></span>
                    <?php endforeach; ?>
                <?php elseif (Mage::helper('weee')->typeOfDisplay($_item, 2, 'sales') && $_item->getWeeeTaxAppliedAmount()): ?>
                    <?php foreach (Mage::helper('weee')->getApplied($_item) as $tax): ?>
                        <span class="weee"><?php echo $tax['title']; ?>: <?php echo Mage::helper('checkout')->formatPrice($tax['row_amount_incl_tax'],true,true); ?></span>
                    <?php endforeach; ?>
                <?php elseif (Mage::helper('weee')->typeOfDisplay($_item, 4, 'sales') && $_item->getWeeeTaxAppliedAmount()): ?>
                    <?php foreach (Mage::helper('weee')->getApplied($_item) as $tax): ?>
                        <span class="weee"><?php echo $tax['title']; ?>: <?php echo Mage::helper('checkout')->formatPrice($tax['row_amount_incl_tax'],true,true); ?></span>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <?php if (Mage::helper('weee')->typeOfDisplay($_item, 2, 'sales') && $_item->getWeeeTaxAppliedAmount()): ?>
                <div class="cart-tax-total" onclick="taxToggle('subtotal-item-tax-details<?php echo $_item->getId(); ?>', this, 'cart-tax-total-expanded');">
                    <span class="weee"><?php echo Mage::helper('weee')->__('Total incl. tax'); ?>: <?php echo $this->helper('checkout')->formatPrice($_incl+$_item->getWeeeTaxAppliedRowAmount()); ?></span>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </td>
    <?php endif; ?>
    <td class="a-center"><a href="<?php echo $this->getDeleteUrl()?>" title="<?php echo $this->__('Remove item')?>" class="btn-remove btn-remove2"><?php echo $this->__('Remove item')?></a></td>
</tr>
 <?php endif; ?>