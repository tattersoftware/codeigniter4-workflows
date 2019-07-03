# Tatter\Workflows
Job task control through dynamic workflows, for CodeIgniter 4

## Quick Start

1. Install with Composer: `> composer require tatter/workflows`
2. Update the database: `> php spark migrate:latest -all`
3. Register tasks: `> php spark tasks:register`
4. Start your first workflow: https://[yourdomain.com]/workflows

## Features

Workflows functions as a super-controller for CodeIgniter 4, allowing developers to write
their own tasks as REST-ish modules and then string them together for job flow controls.

## Installation

Install easily via Composer to take advantage of CodeIgniter 4's autoloading capabilities
and always be up-to-date:
* `> composer require tatter/workflows`

Or, install manually by downloading the source files and adding the directory to
`app/Config/Autoload.php`.

Once the files are downloaded and included in the autoload, run any library migrations
to ensure the database is setup correctly:
* `> php spark migrate:latest -all`

**Pro Tip:** You can add the spark command to your composer.json to ensure your database is
always current with the latest release:
```
{
	...
    "scripts": {
        "post-update-cmd": [
            "composer dump-autoload",
            "php spark migrate:latest -all"
        ]
    },
	...
```

## Configuration (optional)

The library's default behavior can be altered by extending its config file. Copy
**bin/Workflows.php** to **app/Config/** and follow the instructions
in the comments. If no config file is found in app/Config the library will use its own.

## Usage

The CLI command `spark tasks:register` will search all namespaces for valid task files
(i.e. in the Tasks subfolder and namespace, implementing Tatter\Workflows\Interfaces\TaskInterface)
and register them. You may write your own tasks or import them from existing packages.
Once tasks are registered you can create workflows from a series of those tasks by visiting
the `/workflows` route.

## Job control

**Runner.php** is the central controller that handles job flow. By default this intercepts
routes that match `/jobs/`, but this can be changed with the config file.
