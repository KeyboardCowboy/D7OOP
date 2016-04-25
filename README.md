# D7OOP
Provides object oriented support for working with Drupal 7 modules.

The concept behind this module is to add object oriented support to module building on the basis that a Drupal module is a modular piece of functionality.

The `.module` file associated with a Drupal module is often a dumping ground for all of the code.  To help facilitate a cleaner codebase and to prep for D8 development, think about organizing your module this way:

**Awesome Sauce**
- `awesome_sauce.info`
- `awesome_sauce.module`: Contains all Drupal hook implementations in regular procedural format.
- `classes`
	- `AwesomeSauceModule.class.php`: Extends `Module.class.php` and contains all the custom functionality that would otherwise live in the `.module` file or in a separate `.inc` file.
- `js`
- `css`
- `templates`
- `includes`

This file structure is known by the module, making it simpler to include and attach files, as well as for other developers to read and follow.

Because procedural code is not encapsulated, the functions for working with modules are completely ambiguous of the state of and given module.  In reality, each module has unique properties that should not have to be calculated to looked up on each request.  For example, every module has a path.
