<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Services\AddGroup;

use Platform\Account\Domain\Dto\GroupParamsDto;
use Platform\Package\Components\Collection\Arr;
use Platform\Account\Domain\Adapters\BrandAdapter;
use Platform\Account\Domain\Adapters\CurrencyAdapter;
use Platform\Package\Components\Log\Contracts\Logger;
use Platform\Account\Domain\Entities\SpecificGroupType;
use Platform\Account\Domain\Repositories\GroupRepository;
use Platform\Account\Domain\Repositories\GroupTypeRepository;
use Platform\Account\Domain\Services\ParseGroupName\ParseGroupNameInput;
use Platform\Account\Domain\Services\ParseGroupName\ParseGroupNameRequest;

class AddGroupService implements AddGroupInput
{
    public function __construct(
        private Logger              $logger,
        private AddGroupOutput      $output,
        private BrandAdapter        $brandAdapter,
        private CurrencyAdapter     $currencyAdapter,
        private GroupRepository     $groupRepository,
        private GroupTypeRepository $groupTypeRepository,
        private ParseGroupNameInput $parseGroupNameService,
    ) {
    }

    public function add(AddGroupRequest $request) : AddGroupResult
    {
        $this->logger->withContext([
            'module'    => 'account',
            'service'   => class_basename(self::class),
            'groupName' => $request->name,
        ]);

        $parsed = $this->parseRequest($request);

        try {
            $group = $this->groupRepository->add(
                $request->name,
                $parsed->isDemo,
                $parsed->brandId,
                $parsed->currencyId,
                $parsed->typeId,
                $parsed->specificGroupType,
            );

            if ( ! $group->brandId || ! $group->currencyId || ! $parsed->typeId) {
                $this->logger->alert('group without required fields', [
                    'groupId'    => $group->id,
                    'brandId'    => $group->brandId,
                    'currencyId' => $group->currencyId,
                    'typeId'     => $group->typeId,
                ]);
            }

            return $this->output->added($group);
        } catch (\Throwable $throwable) {
            $this->logger->critical('unexpected error', ['error' => Arr::formatThrowable($throwable)]);

            return $this->output->unableToAdd($throwable);
        }
    }

    private function parseRequest(AddGroupRequest $request) : GroupParamsDto
    {
        $groupParams = $this->parseGroupNameService->parse(new ParseGroupNameRequest($request->name))->groupParams;

        return new GroupParamsDto(
            isDemo: $this->getDemo($request, $groupParams),
            brandId: $this->getBrand($request, $groupParams),
            currencyId: $this->getCurrency($request, $groupParams),
            typeId: $this->getType($request, $groupParams),
            specificGroupType: $this->getSpecificType($request, $groupParams),
        );
    }

    private function getDemo(AddGroupRequest $request, ?GroupParamsDto $groupParams) : bool
    {
        return $request->isDemo ?? $groupParams->isDemo ?? false;
    }

    private function getBrand(AddGroupRequest $request, ?GroupParamsDto $groupParams) : int
    {
        if (is_null($request->brand)) {
            return $groupParams->brandId ?? 0;
        }

        if (is_int($request->brand)) {
            return $request->brand;
        }

        return $this->brandAdapter->findIdByName(strval($request->brand)) ?? 0;
    }

    private function getCurrency(AddGroupRequest $request, ?GroupParamsDto $groupParams) : int
    {
        if (is_null($request->currency)) {
            return $groupParams->currencyId ?? 0;
        }

        if (is_int($request->currency)) {
            return $request->currency;
        }

        return $this->currencyAdapter->findOrCreate(
            name: strval($request->currency),
            precision: intval($request->currencyPrecision),
        )->id;
    }

    private function getType(AddGroupRequest $request, ?GroupParamsDto $groupParams) : int
    {
        if (is_null($request->type)) {
            return $groupParams->typeId ?? 0;
        }

        if (is_int($request->type)) {
            return $request->type;
        }

        return $this->groupTypeRepository->findIdByName(strval($request->type)) ?? 0;
    }

    private function getSpecificType(AddGroupRequest $request, ?GroupParamsDto $groupParams) : SpecificGroupType
    {
        return $request->specificGroupType ?? $groupParams->specificGroupType ?? SpecificGroupType::REGULAR;
    }
}
