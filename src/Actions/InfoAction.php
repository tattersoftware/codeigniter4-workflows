<?php

namespace Tatter\Workflows\Actions;

use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;
use Tatter\Workflows\BaseAction;

class InfoAction extends BaseAction
{
    public $attributes = [
        'category' => 'Core',
        'name'     => 'Info',
        'uid'      => 'info',
        'role'     => '',
        'icon'     => 'fas fa-info-circle',
        'summary'  => 'Set basic details of a job',
    ];

    /**
     * Display the edit form.
     *
     * @return ResponseInterface The view
     */
    public function get(): ResponseInterface
    {
        $this->response->setBody(view('Tatter\Workflows\Views\actions\info', [
            'layout' => $this->config->layouts['public'],
            'config' => $this->config,
            'job'    => $this->job,
        ]));

        return $this->response;
    }

    /**
     * Validates and processes form submission.
     *
     * @return RedirectResponse|null
     */
    public function post(): ?ResponseInterface
    {
        // Validate
        $validation = service('validation')->reset()->setRules([
            'name'    => 'required|max_length[255]',
            'summary' => 'max_length[255]',
        ]);

        if (! $validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Try to update the job
        if (! $this->jobs->update($this->job->id, $this->request->getPost())) {
            return redirect()->back()->withInput()->with('errors', $this->jobs->errors());
        }

        return null;
    }
}
