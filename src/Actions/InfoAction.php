<?php

namespace Tatter\Workflows\Actions;

use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;
use Tatter\Workflows\BaseAction;

class InfoAction extends BaseAction
{
    public const HANDLER_ID = 'info';
    public const ATTRIBUTES = [
        'category' => 'Core',
        'name'     => 'Info',
        'role'     => '',
        'icon'     => 'fas fa-info-circle',
        'summary'  => 'Set basic details of a job',
    ];

    protected string $view = 'Tatter\Workflows\Views\actions\info';
    protected array $rules = [
        'name'    => 'required|max_length[255]',
        'summary' => 'max_length[255]',
    ];

    /**
     * Display the edit form.
     *
     * @return ResponseInterface The view
     */
    public function get(): ResponseInterface
    {
        return $this->render($this->view);
    }

    /**
     * Validates and processes form submission.
     *
     * @return RedirectResponse|null
     */
    public function post(): ?ResponseInterface
    {
        // Validate
        if (! $this->validate($this->rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Try to update the job
        if (! $this->jobs->update($this->job->id, $this->request->getPost())) {
            return redirect()->back()->withInput()->with('errors', $this->jobs->errors());
        }

        return null;
    }
}
