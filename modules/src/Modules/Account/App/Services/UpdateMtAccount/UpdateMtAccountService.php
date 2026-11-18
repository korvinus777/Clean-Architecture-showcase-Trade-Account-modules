<?php

declare(strict_types=1);

namespace Platform\Account\App\Services\UpdateMtAccount;

use Platform\Package\Components\Log\Contracts\Logger;
use Platform\Account\Domain\Services\Update\UpdateInput;
use Platform\Account\Domain\Repositories\GroupRepository;
use Platform\Account\Domain\Services\Update\UpdateRequest;
use Platform\Account\Domain\Repositories\AccountRepository;

class UpdateMtAccountService implements UpdateMtAccountInput
{
    public function __construct(
        private Logger                $logger,
        private UpdateInput           $updateService,
        private GroupRepository       $groupRepository,
        private AccountRepository     $accountRepository,
        private UpdateMtAccountOutput $output,
    ) {
    }

    public function update(UpdateMtAccountRequest $request) : UpdateMtAccountResult
    {
        $this->logger->withContext([
            'module'  => 'account',
            'service' => class_basename(self::class),
            'login'   => $request->login,
        ]);

        $account = $this->accountRepository->findByLogin($request->login);

        if ( ! $account) {
            return $this->output->accountNotFound();
        }

        $group = $request->groupName
            ? $this->groupRepository->findByName($request->groupName, true)
            : null;

        if ($request->groupName && ! $group) {
            $this->logger->critical('group not found', [
                'groupName' => $request->groupName,
            ]);

            return $this->output->groupNotFound();
        }

        $result = $this->updateService->update(new UpdateRequest(
            accountId: $account->id,
            type: $group ? (string) $group->typeName : null,
            specificGroupType: $group?->specificType,
            leverage: $request->leverage,
            enabled: $request->isEnabled,
            tradable: $request->isTradable,
            margin: $request->margin,
            isFromMt: true,
        ));

        return $result->isOk()
            ? $this->output->success()
            : $this->output->canNotUpdate();
    }
}
