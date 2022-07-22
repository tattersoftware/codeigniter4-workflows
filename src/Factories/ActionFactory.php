<?php

namespace Tatter\Workflows\Factories;

use Tatter\Handlers\BaseFactory;
use Tatter\Workflows\BaseAction;

/**
 * Action Factory Class
 *
 * Used to discover and register Actions.
 *
 * @method static class-string<BaseAction>   find(string $id)
 * @method static class-string<BaseAction>[] findAll()
 */
class ActionFactory extends BaseFactory
{
    public const HANDLER_PATH = 'Actions';
    public const HANDLER_TYPE = BaseAction::class;

    /**
     * Gathers all attributes from Action handlers, ordered by name.
     *
     * @return array<string, array<string, scalar|null>> [HandlerId => Attributes]
     */
    public static function getAllAttributes(): array
    {
        $actions = [];

        foreach (self::findAll() as $handler) {
            $actions[$handler::HANDLER_ID] = $handler::getAttributes();
        }

        $names = array_column($actions, 'name');
        array_multisort($names, $actions);

        return $actions;
    }
}
