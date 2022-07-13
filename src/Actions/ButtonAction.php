<?php

namespace Tatter\Workflows\Actions;

use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;
use Tatter\Workflows\BaseAction;

class ButtonAction extends BaseAction
{
    public const HANDLER_ID = 'button';
    public const ATTRIBUTES = [
        'category' => 'Core',
        'name'     => 'Button',
        'role'     => '',
        'icon'     => 'fas fa-dot-circle',
        'summary'  => 'Prompts the user to press a button.',
    ];

    /**
     * Form view to display
     */
    protected string $view = 'Tatter\Workflows\Views\actions\button';

    /**
     * Prompt text
     */
    protected string $prompt = 'By clicking below I agree to the terms.';

    /**
     * Job flag to set on success
     */
    protected string $flag = 'Accepted';

    /**
     * Display the button form.
     *
     * @return ResponseInterface The view
     */
    public function get(): ResponseInterface
    {
        return $this->render($this->view, [
            'prompt' => $this->prompt,
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

        $this->job->setFlag($this->flag);

        return null;
    }
}
