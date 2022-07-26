<?php

namespace Tatter\Workflows\Actions;

use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;
use Tatter\Workflows\BaseAction;
use Tatter\Workflows\Entities\Job;

class ButtonAction extends BaseAction
{
    public const HANDLER_ID = 'button';
    public const ATTRIBUTES = [
        'category' => 'Core',
        'name'     => 'Button',
        'role'     => '',
        'icon'     => 'fas fa-dot-circle',
        'summary'  => 'Prompts the user to press a button.',
        'view'     => 'Tatter\Workflows\Views\actions\button',
        'prompt'   => 'By clicking below I agree to the terms.',
        'flag'     => 'Accepted',
    ];

    public static function maySkip(Job $job): bool
    {
        return $job->getFlag(static::getAttributes()['flag']) !== null;
    }

    /**
     * Display the button form.
     *
     * @return ResponseInterface The view
     */
    public function get(): ResponseInterface
    {
        $attributes = static::getAttributes();

        return $this->render($attributes['view'], [
            'prompt' => $attributes['prompt'],
        ]);
    }

    /**
     * Validates that the button was pressed and
     * sets the inidicated job flag.
     *
     * @return RedirectResponse|null
     */
    public function post(): ?ResponseInterface
    {
        // Validate
        if (! $this->validate(['submit' => 'required'])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->job->setFlag(static::getAttributes()['flag']);

        return null;
    }
}
