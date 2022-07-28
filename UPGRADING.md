# Upgrade Guide

## Version 4 to 5
***

* `Workflows` is now officially a Tatter Module, so relies on the opinionated frontend tech provided by `Tatter\Frontend`
* Related, all layouts are now routed through `Tatter\Layouts`: Actions and their views should be updated to use the same
* `Tatter\Handlers` version 3 improves class discovery and attribute access - all Actions should be updated (compare to `InfoAction`)
* Actions are now proper Controllers; this shouldn't affect most implementations, but make sure there are not method collisions
* A number of view files have been removed or consolidated; check `Config\Workflows::$views` to ensure the views you need still exist
* Almost all classes have had breaking changes, so if you extended these for some reason make sure your extensions are compatible

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
