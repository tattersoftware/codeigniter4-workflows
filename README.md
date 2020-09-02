# Tatter\Workflows
Job action control through dynamic workflows, for CodeIgniter 4

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
By default the "user" `role` is accessible by anyone. To restrict access, define a
`has_permission()` function which takes a single `string` parameter with the name of the role
(hint: or use **Myth:Auth**'s existing version).
