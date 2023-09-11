<?php

namespace Wexample\SymfonyHelpers\Form\Traits;

use App\Entity\Invoice;
use App\Service\AccountingService;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Wexample\SymfonyHelpers\Form\FloatType;
use Wexample\SymfonyHelpers\Form\Traits\PricedFormTrait;
use Wexample\SymfonyHelpers\Helper\IconMaterialHelper;

trait PricedWithFeeFormTrait
{
    use PricedFormTrait;

    protected function builderAddPriceFee(
        FormBuilderInterface $builder
    ) {
        /** @var Invoice $entity */
        $entity = $builder->getData();

        if ($entity->hasFee()) {
            $builder->add(
                'priceFee',
                FloatType::class,
                [
                    'attr' => [
                        'type' => 'number',
                        'step' => 0.01,
                    ],
                    self::FIELD_OPTION_NAME_HELPER => Invoice::TYPE_PRODUCT === $entity->getType()
                        ? $this->trans('forms.entity.invoice_dates::field.priceFee.helper_external') : true,
                    self::FIELD_OPTION_NAME_LABEL => Invoice::TYPE_PRODUCT === $entity->getType()
                        ? 'price_fee_external' : true,
                    self::FIELD_OPTION_NAME_ICON => IconMaterialHelper::ICON_CALL_SPLIT,
                ]
            );

            $this->builderAddPriceTransformer(
                $builder,
                'priceFee'
            );

            $builder->add(
                'priceFeeUnit',
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
    }
}
