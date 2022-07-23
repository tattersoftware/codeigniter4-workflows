<?php

namespace Tatter\Workflows\Language\en;

return [
    // Workflows
    'noWorkflowAvailable'    => 'There are no available workflows.',
    'workflowNotFound'       => 'Could not find that workflow.',
    'workflowNotPermitted'   => 'You do not have permission to use that workflow.',
    'newWorkflowSuccess'     => 'New workflow created successfully.',
    'updateWorkflowSuccess'  => 'Workflow updated successfully.',
    'deletedWorkflowSuccess' => 'Workflow deleted successfully.',

    // Runner
    'actionNotFound'      => 'Could not find that action.',
    'jobNotFound'         => 'Could not find that job.',
    'stageNotFound'       => 'Could not find that job action.',
    'workflowNoStages'    => 'Cannot use a workflow with no assigned actions.',
    'skipRequiredStage'   => 'Cannot skip the required "{0}" action',
    'routeMissingJobId'   => 'Job ID missing for "{0}" action',
    'jobAlreadyComplete'  => 'That job is already complete.',
    'jobCannotRegress'    => 'That job cannot be regressed.',
    'useDeletedJob'       => 'That job is no longer available.',
    'newJobSuccess'       => 'New job created successfully.',
    'actionMissingMethod' => 'The "{0}" action does not support the "{1}" method.',
    'invalidActionReturn' => 'Unable to interpret action return.',
    'jobAwaitingInput'    => 'Your job "{0}" is currently waiting for staff input.',
    'jobComplete'         => 'Your job "{0}" completed successfully.',
    'jobDeleted'          => 'Your job "{0}" has been removed.',
    'jobNotAllowed'       => 'You do not have permission to do that.',
];
