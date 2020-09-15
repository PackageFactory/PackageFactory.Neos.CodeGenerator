# PackageFactory.Neos.CodeGenerator

> (Opinionated yet extensible) generator for common code patterns in Neos and Neos.Flow projects

## !WARNING! Still under development

This package is still under development and is not ready for a stable release yet. 

## Authors

- Wilhelm Behncke (behncke@sitegeist.de)
- Bernhard Schmitt (schmitt@sitegeist.de)

## Why?

@TODO

## Usage

### CLI commands

#### `./flow pattern:list`

This command will give you an overview over all available code patterns.

**Example Output:**
```
List of all available code patterns
===================================

+--------------+--------------------------------------------------------+
| Pattern Key  | Short Description                                      |
+--------------+--------------------------------------------------------+
| po:enum      | Creates a pseudo-enum for use in presentation objects. |
| po:value     | Creates a presentation value object.                   |
| po:model     | Creates a presentation model.                          |
| po:component | Creates a presentation model and component.            |
+--------------+--------------------------------------------------------+

Usage:
./flow pattern:generate {Pattern Key} {Package Key} ...
```

#### `./flow pattern:describe {Pattern Key}`

This command will give you a detailed documentation for the given code pattern (similar to `./flow help {Command name}`).

**Example Output:**
```
 po:enum - Summary 

  Short description
    Creates a pseudo-enum for use in presentation objects.

  Description
    A pseudo-enum will be placed at
    Vendor\Site\Presentation\{Subnamespace}\{ClassName}. It will consist of the
    given values which then can be accessed by {ClassName}::{ValueName}().

  Arguments
    #0 - A package key or '.' to use the default package key or '..' to select
    a package key
    #1 - A Subnamespace (relative to Vendor\Site\Presentation)
    #2 - The enum class name
    #n - Comma-separated list of enum values

  Usage Example
    ./flow pattern:generate po:enum Vendor.Site Block/Button ButtonSize
    xs,s,m,l,xl
```

#### `./flow pattern:generate {Pattern Key} {...Arguments}`

This command will trigger the actual code generation as configured for `{Pattern Key}`. The available `{...Arguments}` can be viewed via `./flow pattern:describe {Pattern Key}`.

**Example Output:**
```
 Running po:component... 

Wrote file /var/www/html/Packages/Sites/Vendor.Site/Classes/Presentation/Block/Image/Image.php
Wrote file /var/www/html/Packages/Sites/Vendor.Site/Classes/Presentation/Block/Image/ImageInterface.php
Wrote file /var/www/html/Packages/Sites/Vendor.Site/Resources/Private/Fusion/Presentation/Block/Image/Image.fusion

Done!
```

### Built-in patterns

@TODO

### Writing custom patterns

@TODO

## Contribution & Philosophy

@TODO

## License

see [LICENSE](./LICENSE)
