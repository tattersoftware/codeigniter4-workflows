# Upgrade Guide

## Version 3 to 4
***

* Instead of a session key provide an implementation of `codeigniter4/authentication-implementation` for determining the current user (see [User Guide](https://codeigniter4.github.io/CodeIgniter4/extending/authentication.html)).
* Corollary, the library assumes the required function is pre-loaded (e.g. call `helper('auth')` before using `Workflows`)
* Entity stored relations are now private members and will not be available in `$attributes` - extending classes should use the relation getters
* With `Tatter\Users` the "user role" concept is now obsolete; `role` remains for creating restrictions but unrestricted Actions should use `''` instead of `'user'`
* `BaseAction` now defines all available methods with a consolidated return type: `ResponseInterface|null`. Actions should be updated for the following behavior:
	- Return a `ResponseInterface` for interaction
	- Return `null` to indicate the Stage is complete
	- Throw a `WorkflowsException` for any errors to display to the user
* Corollary, the `BaseAction` constructor has been updated with new parameters. Any extensions of `__construct()` will need adjusting to match.

Note: A number of database fields were incorrect (missing defaults, incorrect `null`).
The migrations [have been updated](https://github.com/tattersoftware/codeigniter4-workflows/commit/5101e8deb005f6da24ab92357b25793616d78252)
to reflect their proper state. This only really affects testing so new migrations were not
generated, but current production instances should be aware of the nuanced differences.
