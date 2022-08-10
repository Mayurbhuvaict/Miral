<?php

declare(strict_types=1);

namespace NzzFlyoutMenu\Subscriber;

use Shopware\Core\Checkout\Cart\Price\QuantityPriceCalculator;
use Shopware\Core\Checkout\Cart\Price\Struct\CartPrice;
use Shopware\Core\Checkout\Cart\Price\Struct\PriceCollection as CalculatedPriceCollection;
use Shopware\Core\Checkout\Cart\Price\Struct\QuantityPriceDefinition;
use Shopware\Core\Checkout\Cart\Price\Struct\ReferencePriceDefinition;
use Shopware\Core\Content\Product\Aggregate\ProductPrice\ProductPriceCollection;
use Shopware\Core\Content\Product\DataAbstractionLayer\CheapestPrice\CalculatedCheapestPrice;
use Shopware\Core\Content\Product\DataAbstractionLayer\CheapestPrice\CheapestPrice;
use Shopware\Core\Content\Product\SalesChannel\Price\ReferencePriceDto;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Pricing\Price;
use Shopware\Core\Framework\DataAbstractionLayer\Pricing\PriceCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\Unit\UnitCollection;
use Shopware\Storefront\Pagelet\Header\HeaderPageletLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class NzzHeaderNavigation implements EventSubscriberInterface
{
    private EntityRepositoryInterface $productStreamRepository;
    private EntityRepositoryInterface $productRepository;
    private EntityRepositoryInterface $unitRepository;
    private QuantityPriceCalculator $calculator;

    private ?UnitCollection $units = null;

    public function __construct(
        EntityRepositoryInterface $productStreamRepository,
        EntityRepositoryInterface $productRepository,
        EntityRepositoryInterface $unitRepository,
        QuantityPriceCalculator   $calculator
    )
    {
        $this->productStreamRepository = $productStreamRepository;
        $this->productRepository = $productRepository;
        $this->unitRepository = $unitRepository;
        $this->calculator = $calculator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            HeaderPageletLoadedEvent::class => 'onHeaderLoaded'
        ];
    }

    public function onHeaderLoaded(HeaderPageletLoadedEvent $event)
    {
        $pageData = $event->getpagelet()->getNavigation()->getTree();
        foreach ($pageData as $page) {
            //$productStreamId = $page->getCategory()->getProductStreamId();
            $categoryExtensions = $page->getCategory()->getExtensions();
            //dd($categoryExtensions['nzzDynamicGroupCat']);
            if($categoryExtensions['nzzDynamicGroupCat'] != null){
               //dd("main if");
                if($categoryExtensions['nzzDynamicGroupCat']->getProductStreamId() != null)
                {
                    $productStreamId = $categoryExtensions['nzzDynamicGroupCat']->getProductStreamId();

                    //dd($productStreamId);
                    //------
                    $dynamicProducts = $this->getDynamicProducts($event->getContext(), $productStreamId);
                    $units = $this->getUnits($event->getSalesChannelContext());

                    foreach ($dynamicProducts as $product) {
                        $this->calculatePrice($product, $event->getSalesChannelContext(), $units);
                        $this->calculateAdvancePrices($product, $event->getSalesChannelContext(), $units);
                        $this->calculateCheapestPrice($product, $event->getSalesChannelContext(), $units);
                    }

                    $page->setExtensions($dynamicProducts);
                    //----------
                }
            }
        }
    }

    private function getDynamicProducts(Context $context, $productStreamId)
    {

       // dd($productStreamId);
        $productsArray = [];
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('productStreamId', $productStreamId));
        $criteria->setLimit(4);
        $allDynamicProducts = $this->productStreamRepository->searchIds($criteria, $context)->getData();
   // dd($allDynamicProducts);
        //       echo "<pre>";
//        print_r($allDynamicProducts);
//        echo "</pre>";
        foreach ($allDynamicProducts as $dynamicProduct) {
            $productId = $dynamicProduct['product_id'];
            $criteriaProduct = new Criteria();
            $criteriaProduct->addFilter(new EqualsFilter('id', $productId));
            $criteriaProduct->addAssociation("cover");
            $criteriaProduct->addAssociation("seoUrls");
            $criteriaProduct->addAssociation("prices");
            $productData = $this->productRepository->search($criteriaProduct, $context)->first();
          // dd($productData);
            array_push($productsArray, $productData);
        }
        //dd($productsArray);
        return $productsArray;


        //---------------------
//        $productsArray = [];
//        $criteria = new Criteria();
//        $criteria->addFilter(new EqualsFilter('productStreamId', $productStreamId));
//        $criteria->setLimit(4);
//        $allDynamicProducts = $this->productStreamRepository->searchIds($criteria, $context)->getData();
//        foreach ($allDynamicProducts as $dynamicProduct) {
//            $productId = $dynamicProduct['product_id'];
//            $criteriaProduct = new Criteria();
//            $criteriaProduct->addFilter(new EqualsFilter('id', $productId));
//            $criteriaProduct->addAssociation("cover");
//            $criteriaProduct->addAssociation("seoUrls");
//            $criteriaProduct->addAssociation("prices");
//            $productData = $this->productRepository->search($criteriaProduct, $context)->first();
//            array_push($productsArray, $productData);
//        }
//        return $productsArray;
    }

    private function calculatePrice(Entity $product, SalesChannelContext $context, UnitCollection $units): void
    {
        $price = $product->get('price');
        $taxId = $product->get('taxId');

        if ($price === null || $taxId === null) {
            return;
        }
        $reference = ReferencePriceDto::createFromEntity($product);

        $definition = $this->buildDefinition($product, $price, $context, $units, $reference);

        $price = $this->calculator->calculate($definition, $context);

        $product->assign([
            'calculatedPrice' => $price,
        ]);
    }

    private function calculateAdvancePrices(Entity $product, SalesChannelContext $context, UnitCollection $units): void
    {
        $prices = $product->get('prices');
        if ($prices === null) {
            return;
        }

        if (!$prices instanceof ProductPriceCollection) {
            return;
        }

        $prices = $this->filterRulePrices($prices, $context);
        if ($prices === null) {
            $product->assign(['calculatedPrices' => new CalculatedPriceCollection()]);

            return;
        }
        $prices->sortByQuantity();

        $reference = ReferencePriceDto::createFromEntity($product);

        $calculated = new CalculatedPriceCollection();
        foreach ($prices as $price) {
            $quantity = $price->getQuantityEnd() ?? $price->getQuantityStart();

            $definition = $this->buildDefinition($product, $price->getPrice(), $context, $units, $reference, $quantity);

            $calculated->add($this->calculator->calculate($definition, $context));
        }

        $product->assign(['calculatedPrices' => $calculated]);
    }

    private function calculateCheapestPrice(Entity $product, SalesChannelContext $context, UnitCollection $units): void
    {
        $cheapest = $product->get('cheapestPrice');

        if ($product->get('taxId') === null) {
            return;
        }

        if (!$cheapest instanceof CheapestPrice) {
            $price = $product->get('price');
            if ($price === null) {
                return;
            }

            $reference = ReferencePriceDto::createFromEntity($product);

            $definition = $this->buildDefinition($product, $price, $context, $units, $reference);

            $calculated = CalculatedCheapestPrice::createFrom(
                $this->calculator->calculate($definition, $context)
            );

            $prices = $product->get('calculatedPrices');

            $hasRange = $prices instanceof CalculatedPriceCollection && $prices->count() > 1;

            $calculated->setHasRange($hasRange);

            $product->assign(['calculatedCheapestPrice' => $calculated]);

            return;
        }

        $reference = ReferencePriceDto::createFromCheapestPrice($cheapest);

        $definition = $this->buildDefinition($product, $cheapest->getPrice(), $context, $units, $reference);

        $calculated = CalculatedCheapestPrice::createFrom(
            $this->calculator->calculate($definition, $context)
        );

        $calculated->setHasRange($cheapest->hasRange());

        $product->assign(['calculatedCheapestPrice' => $calculated]);
    }

    private function buildDefinition(
        Entity              $product,
        PriceCollection     $prices,
        SalesChannelContext $context,
        UnitCollection      $units,
        ReferencePriceDto   $reference,
        int                 $quantity = 1
    ): QuantityPriceDefinition
    {
        $price = $this->getPriceValue($prices, $context);

        $taxId = $product->get('taxId');
        $definition = new QuantityPriceDefinition($price, $context->buildTaxRules($taxId), $quantity);
        $definition->setReferencePriceDefinition(
            $this->buildReferencePriceDefinition($reference, $units)
        );
        $definition->setListPrice(
            $this->getListPrice($prices, $context)
        );
        $definition->setRegulationPrice(
            $this->getRegulationPrice($prices, $context)
        );

        return $definition;
    }

    private function getPriceValue(PriceCollection $price, SalesChannelContext $context): float
    {
        /** @var Price $currency */
        $currency = $price->getCurrencyPrice($context->getCurrencyId());

        $value = $this->getPriceForTaxState($currency, $context);

        if ($currency->getCurrencyId() !== $context->getCurrency()->getId()) {
            $value *= $context->getContext()->getCurrencyFactor();
        }

        return $value;
    }

    private function getPriceForTaxState(Price $price, SalesChannelContext $context): float
    {
        if ($context->getTaxState() === CartPrice::TAX_STATE_GROSS) {
            return $price->getGross();
        }

        return $price->getNet();
    }

    private function getListPrice(?PriceCollection $prices, SalesChannelContext $context): ?float
    {
        if (!$prices) {
            return null;
        }

        $price = $prices->getCurrencyPrice($context->getCurrency()->getId());
        if ($price === null || $price->getListPrice() === null) {
            return null;
        }

        $value = $this->getPriceForTaxState($price->getListPrice(), $context);

        if ($price->getCurrencyId() !== $context->getCurrency()->getId()) {
            $value *= $context->getContext()->getCurrencyFactor();
        }

        return $value;
    }

    private function getRegulationPrice(?PriceCollection $prices, SalesChannelContext $context): ?float
    {
        if (!$prices) {
            return null;
        }

        $price = $prices->getCurrencyPrice($context->getCurrency()->getId());
        if ($price === null || $price->getRegulationPrice() === null) {
            return null;
        }

        $taxPrice = $this->getPriceForTaxState($price, $context);
        $value = $this->getPriceForTaxState($price->getRegulationPrice(), $context);
        if ($taxPrice === 0.0 || $taxPrice === $value) {
            return null;
        }

        if ($price->getCurrencyId() !== $context->getCurrency()->getId()) {
            $value *= $context->getContext()->getCurrencyFactor();
        }

        return $value;
    }

    private function buildReferencePriceDefinition(ReferencePriceDto $definition, UnitCollection $units): ?ReferencePriceDefinition
    {
        if ($definition->getPurchase() === null || $definition->getPurchase() <= 0) {
            return null;
        }
        if ($definition->getUnitId() === null) {
            return null;
        }
        if ($definition->getReference() === null || $definition->getReference() <= 0) {
            return null;
        }
        if ($definition->getPurchase() === $definition->getReference()) {
            return null;
        }

        $unit = $units->get($definition->getUnitId());
        if ($unit === null) {
            return null;
        }

        return new ReferencePriceDefinition(
            $definition->getPurchase(),
            $definition->getReference(),
            $unit->getTranslation('name')
        );
    }

    private function filterRulePrices(ProductPriceCollection $rules, SalesChannelContext $context): ?ProductPriceCollection
    {
        foreach ($context->getRuleIds() as $ruleId) {
            $filtered = $rules->filterByRuleId($ruleId);

            if (\count($filtered) > 0) {
                return $filtered;
            }
        }

        return null;
    }


    private function getUnits(SalesChannelContext $context): UnitCollection
    {
        if ($this->units !== null) {
            return $this->units;
        }

        $criteria = new Criteria();
        $criteria->setTitle('product-price-calculator::units');

        /** @var UnitCollection $units */
        $units = $this->unitRepository
            ->search($criteria, $context->getContext())
            ->getEntities();

        return $this->units = $units;
    }
}
