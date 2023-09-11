<?php

namespace Wexample\SymfonyHelpers\Form\Traits;

use App\Service\AccountingService;
use App\Wex\BaseBundle\Entity\Traits\PricedEntityTrait;
use App\Wex\BaseBundle\Entity\Traits\PricedWithCurrencyEntityTrait;
use App\Wex\BaseBundle\Entity\Traits\PricedWithDiscountEntityTrait;
use App\Wex\BaseBundle\Entity\Traits\PricedWithVatTrait;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Wexample\SymfonyHelpers\Form\CheckboxType;
use Wexample\SymfonyHelpers\Form\ChoiceType;
use Wexample\SymfonyHelpers\Form\FloatType;
use Wexample\SymfonyHelpers\Helper\IconMaterialHelper;
use function round;

trait PricedFormTrait
{
    public function builderAddPriceRaw(
        FormBuilderInterface $builder,
        array $options = []
    ) {
        $this->builderAddPrice(
            $builder,
            'priceRaw',
            $options
        );
    }

    public function builderAddPrice(
        FormBuilderInterface $builder,
        string $child,
        array $options = []
    ) {
        $options = $this->resolveOptions([
            self::FIELD_OPTION_NAME_ICON => IconMaterialHelper::ICON_ATTACH_MONEY,
            'attr' => [
                'type' => 'number',
                'step' => 0.01,
            ],
        ], $options);

        $builder
            ->add(
                $child,
                FloatType::class,
                $options
            );

        $this->builderAddPriceTransformer(
            $builder,
            $child
        );
    }

    protected function builderAddPriceTransformer($builder, string $childName)
    {
        $builder
            ->get($childName)
            ->addModelTransformer(new CallbackTransformer(
                fn($price): float => (float) $price / 100,
                fn(float $price): int => (int) round($price * 100)
            ));
    }

    protected function builderAddPriced(
        FormBuilderInterface $builder
    ) {
        // TODO Priced form implies that entity use several traits, we should change it.
        /** @var PricedEntityTrait|PricedWithDiscountEntityTrait|PricedWithVatTrait|PricedWithCurrencyEntityTrait $entity */
        $entity = $builder->getData();
        $priceOverridden = $entity->getPriceOverridden() ?: 0;
        $priceOverridden = $priceOverridden > 0 ? $priceOverridden : 0;

        $builder->add(
            'priceTotalOverrideSwitch',
            CheckboxType::class,
            [
                self::FIELD_OPTION_NAME_DATA => (bool) $priceOverridden,
                self::FIELD_OPTION_NAME_LABEL => false,
                self::FIELD_OPTION_NAME_MAPPED => false,
                self::FIELD_OPTION_NAME_REQUIRED => false,
            ]
        );

        $this->builderAddPrice(
            $builder,
            'priceOverridden',
            [
                'attr' => [
                    'data-amount-original' => $entity->getPriceTotal() / 100,
                    'data-amount-overridden' => $priceOverridden,
                ],
                self::FIELD_OPTION_NAME_DATA => $priceOverridden,
                self::FIELD_OPTION_NAME_ICON => false,
            ]
        );

        $this->builderAddPrice(
            $builder,
            'priceDiscount',
            [
                self::FIELD_OPTION_NAME_ICON => IconMaterialHelper::ICON_LOCAL_OFFER,
            ]
        );

        $builder->add(
            'priceDiscountUnit',
            ChoiceType::class,
            [
                self::FIELD_OPTION_NAME_CHOICES => [
                    '%' => AccountingService::UNIT_PERCENT,
                    $entity->getPriceCurrencyLabel() => AccountingService::UNIT_MONEY,
                ],
                self::FIELD_OPTION_NAME_ATTR => [
                    'class' => 'browser-default',
                ],
                self::FIELD_OPTION_NAME_LABEL => false,
            ]
        );
    }

    protected function getPriceUnits()
    {
        return PricedEntityTrait::$PRICE_UNIT_ALLOWED_DEFAULT;
    }
}
