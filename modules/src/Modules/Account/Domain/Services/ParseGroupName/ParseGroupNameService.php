<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Services\ParseGroupName;

use Platform\Account\Domain\Entities\Group;
use Platform\Account\Domain\Dto\GroupParamsDto;
use Platform\Package\Components\Collection\Arr;
use Platform\Account\Domain\Adapters\BrandAdapter;
use Platform\Account\Domain\Adapters\CurrencyAdapter;
use Platform\Package\Components\Log\Contracts\Logger;
use Platform\Account\Domain\Entities\SpecificGroupType;
use Platform\Account\Domain\Repositories\GroupTypeRepository;

class ParseGroupNameService implements ParseGroupNameInput
{
    public function __construct(
        private Logger               $logger,
        private BrandAdapter         $brandAdapter,
        private CurrencyAdapter      $currencyAdapter,
        private GroupTypeRepository  $groupTypeRepository,
        private ParseGroupNameOutput $output,
    ) {
    }

    public function parse(ParseGroupNameRequest $request) : ParseGroupNameResult
    {
        $this->logger->withContext([
            'module'    => 'account',
            'service'   => class_basename(self::class),
            'groupName' => $request->name,
        ]);

        // пример названия групп:
        // - managers\administrators
        // - real\brstone\bbook\platinum\usd
        // - real\brstone\bbook\elite\rub_mam_s
        // - real\dotbig\bbook\elite\level_z_usd
        // - real\brstone\regonly\eur
        $parts = explode('\\', $request->name);

        $demoType = Arr::getString($parts, 0);
        $brand    = Arr::getString($parts, 1);
        $type     = Arr::getString($parts, 3);
        $currency = Arr::getString($parts, 4);
        $brandId  = $this->brandAdapter->findIdByName($brand);

        if ($this->isTechnicalGroup($demoType) || ! $brandId) {
            $this->logger->info('technical group', [
                'demoType'  => $demoType,
                'brandName' => $brand,
            ]);

            return $this->output->serviceGroup($request->name);
        }

        $groupParams = new GroupParamsDto(
            isDemo: $demoType === 'demo',
            brandId: $brandId,
        );

        // В regonly группах упущен элемент (bbook/abook),
        // поэтому тип и валюта смещены на одну позицию
        if (Arr::getString($parts, 2) === Group::REGONLY_TYPE) {
            $type     = Group::REGONLY_TYPE;
            $currency = Arr::getString($parts, 3);
        }

        $parsedCurrency = $this->parseCurrencyName($currency);
        $groupParams->currencyId = (int) $this->currencyAdapter->findIdByName($parsedCurrency);

        if ( ! $groupParams->currencyId) {
            $this->logger->error('currency not found', [
                'currencyName'   => $currency,
                'parsedCurrency' => $parsedCurrency,
            ]);
        }

        // Группы кастомного типа создаются в существующем типе (например elite),
        // но такие группы отличаются форматом последней части пути, который предназначен для валюты.
        // Если длинна этой части не 3 символа и не содержит mam, значит считается, что это кастомный тип.
        $isCustomType = strlen($currency) !== 3 && ! str_contains($currency, 'mam');

        if ($isCustomType) {
            $groupParams->typeId = $this->groupTypeRepository->getCustomId();
            $specificType = SpecificGroupType::CUSTOM;
        } else {
            $groupParams->typeId = (int) $this->groupTypeRepository->findIdByName($type);
            $specificType = SpecificGroupType::REGULAR;
        }

        $groupParams->specificGroupType = str_contains($currency, 'mam')
            ? SpecificGroupType::MAM
            : $specificType;

        return $this->output->parsed($groupParams);
    }

    private function isTechnicalGroup(string $demoType) : bool
    {
        return ! in_array($demoType, ['real', 'demo'], true);
    }

    private function parseCurrencyName(string $currency) : string
    {
        // точное название валюты
        if (strlen($currency) === 3) {
            return $currency;
        }

        // есть дополнительное окончание, но валюта идет первая
        if (str_contains($currency, 'mam')) {
            return substr($currency, 0, 3);
        }

        // кастомное название, валюта в конце
        return substr($currency, -3);
    }
}
