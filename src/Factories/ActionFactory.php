<?php

namespace Tatter\Workflows\Factories;

use RuntimeException;
use Tatter\Handlers\BaseFactory;
use Tatter\Workflows\BaseAction;
use Tatter\Workflows\Models\ActionModel;

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
     * Gathers all Actions and registers their attributes in the database.
     * Updates existing records as needed.
     *
     * @throws RuntimeException for insert failures
     */
    public static function register(): void
    {
        $actions = model(ActionModel::class);
        
        foreach (self::findAll() as $handler) {
            $data = $handler::getAttributes();

            // Check for an existing entry
            if ($action = $actions->where('uid', $data['uid'])->first()) {
                $result = $actions->update($action->id, $data);
            }
            else {
                $result = $actions->insert($data, false);
            }

            if ($result === false) {
                $message = "Unable to register {$handler}: " . implode(' ', $actions->errors());
                throw new RuntimeException($message);
            }
        }
    }
}
