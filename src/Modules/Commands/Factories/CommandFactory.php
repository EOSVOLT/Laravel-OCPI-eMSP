<?php

namespace Ocpi\Modules\Commands\Factories;

use Ocpi\Models\Commands\Command;
use Ocpi\Modules\Credentials\Factories\PartyRoleFactory;

class CommandFactory
{
    public static function fromModel(Command $command): \Ocpi\Modules\Commands\Object\Command
    {
        $command->load('party_role');
        return new \Ocpi\Modules\Commands\Object\Command(
            $command->id,
            PartyRoleFactory::fromModel($command->party_role),
            $command->type,
            $command->payload,
            $command->response,
            $command->result,
        );
    }
}