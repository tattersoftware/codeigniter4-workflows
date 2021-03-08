# Tatter\Workflows
Job action control through dynamic workflows, for CodeIgniter 4

[![](https://github.com/tattersoftware/codeigniter4-workflows/workflows/PHPUnit/badge.svg)](https://github.com/tattersoftware/codeigniter4-workflows/actions?query=workflow%3A%22PHPUnit%22)
[![](https://github.com/tattersoftware/codeigniter4-workflows/workflows/PHPStan/badge.svg)](https://github.com/tattersoftware/codeigniter4-workflows/actions?query=workflow%3A%PHPStan%22)
[![Coverage Status](https://coveralls.io/repos/github/tattersoftware/codeigniter4-workflows/badge.svg?branch=develop)](https://coveralls.io/github/tattersoftware/codeigniter4-workflows?branch=develop)

## Quick Start

1. Install with Composer: `> composer require tatter/workflows`
2. Update the database: `> php spark migrate -all`
3. Register actions: `> php spark actions:register`
4. Start your first workflow: https://[yourdomain.com]/workflows

## Features

**Workflows** functions as a super-controller for CodeIgniter 4, allowing developers to
write their own actions as classes and then string them together for job flow controls.

## Installation

Install easily via Composer to take advantage of CodeIgniter 4's autoloading capabilities
and always be up-to-date:
* `> composer require tatter/workflows`

Or, install manually by downloading the source files and adding the directory to
`app/Config/Autoload.php`.

Once the files are downloaded and included in the autoload, run any library migrations
to ensure the database is setup correctly:
* `> php spark migrate -all`

## Configuration (optional)

The library's default behavior can be altered by extending its config file. Copy
**examples/Workflows.php** to **app/Config/** and follow the instructions
in the comments. If no config file is found in app/Config the library will use its own.

**Workflows** uses [Tatter\Users](https://github.com/tattersoftware/codeigniter4-users) to
work with user records. Follow the instructions to verify you have a compatible authentication
library with classes that implement the `UserEntity` and `HasPermission` interfaces.

## Usage

The CLI command `spark actions:register` will search all namespaces for valid action files
and register them. Action files are identified by:
* Located in the Actions subfolder within the root of a namespace
* Implementing **Tatter\Workflows\Interfaces\ActionInterface**

You may write your own actions or import them from existing packages. Once actions are
registered you can create workflows from a series of those actions by visiting the
`/workflows` route.

## Job control

**Runner.php** is the central controller that handles job flow. By default this intercepts
routes that match `/jobs/`, but this can be changed in the config file.

## Action permissions

You may limit access to individual Actions using the `role` attribute of its definition.
By default an empty `role` is available to everyone. Actions will use the `UserEntity`
with `HasPermission` interface to test for allowed users.

## Logging

Jobs track their activity through two supplemental database tables and their entities.

`Joblogs` are created automatically any time a job changes stages, and will record:
* The stage the job leaves (`null` for new jobs)
* The stage the job enters (`null` for completed jobs)
* The ID of the current user (if available)
* The timestamp of the activity

Since jobs may progress and regress through a stage multiple times, `Joblogs` are not
a good indicator of status. `Jobflags` are set by the developer and represent a definitive
job state. A flag is a string key and `CodeIgniter\I18n\Time` timestamp value. Flags are
managed from the `Job` entity methods:
* `getFlags(): array`
* `getFlag($name): Time`
* `setFlag($name)`
* `clearFlag($name)`
* `clearFlags()`

For example, an `Action` may require a user to accept the "Terms of Service" agreement
before proceeding. Its code may look like this:
```
public function get()
{
	if (! $this->job->getFlag('accepted'))
	{
		return service('response')->setBody(view('accept_form'));
	}

	// Null returns indicate "Action complete"
	return null;
}

public function accept_submit()
{
	$this->job->setFlag('accepted');

	return null;
}
```
