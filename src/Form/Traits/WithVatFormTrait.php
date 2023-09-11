<?php

namespace Wexample\SymfonyHelpers\Form\Traits;

use App\Service\AccountingService;
use App\Wex\BaseBundle\Entity\Traits\WithVatEntityTrait;
use App\Wex\BaseBundle\Helper\TextHelper;
use Symfony\Component\Form\FormBuilderInterface;
use Wexample\SymfonyHelpers\Form\ChoiceType;
use Wexample\SymfonyHelpers\Helper\IconMaterialHelper;
use function array_reverse;
use function is_null;
use function sort;

trait WithVatFormTrait
{
    protected function builderAddVat(
        FormBuilderInterface $builder
    ) {
        /** @var WithVatEntityTrait $entity */
        $entity = $builder->getData();

        $builder
            ->add(
                'priceVat',
                ChoiceType::class,
                [
                    self::FIELD_OPTION_NAME_CHOICES => static::buildVatRatesChoiceData(),
                    self::FIELD_OPTION_NAME_DATA => is_null($entity) ? AccountingService::VAT_RATE_NONE : $entity->getPriceVat(),
                    self::FIELD_OPTION_NAME_ICON => IconMaterialHelper::ICON_CALL_SPLIT,
                    self::FIELD_OPTION_NAME_LABEL => true,
                ]
            );
    }

    protected function buildVatRatesChoiceData(): array
    {
        $rates = [];
        $ratesSorted = AccountingService::VAT_RATES;
        sort($ratesSorted);
        $ratesSorted = array_reverse($ratesSorted);

        foreach ($ratesSorted as $rate) {
            $rates[TextHelper::getStringFromIntData(
                $rate,
                true
            ).' %'] = $rate;
        }

        return $rates;
    }
}
