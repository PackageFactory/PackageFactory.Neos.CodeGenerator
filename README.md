# PackageFactory.Neos.CodeGenerator

> (Opinionated yet extensible) generator for common code patterns in Neos and Neos.Flow projects

## Authors

## Why?

## Usage

### CLI commands

#### `./flow code:listpatterns`

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
./flow code:generate {Pattern Key} {Package Key} ...
```

#### `./flow code:describepattern {Pattern Key}`

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
    ./flow code:generate po:enum Vendor.Site Block/Button ButtonSize
    xs,s,m,l,xl
```

#### `./flow code:generate {Pattern Key} {...Arguments}`

This command will trigger the actual code generation as configured for `{Pattern Key}`. The available `{...Arguments}` can be viewed via `./flow code:describepattern {Pattern Key}`.

**Example Output:**
```
 Running po:component... 

Wrote file /var/www/html/Packages/Sites/Vendor.Site/Classes/Presentation/Block/Image/Image.php
Wrote file /var/www/html/Packages/Sites/Vendor.Site/Classes/Presentation/Block/Image/ImageInterface.php
Wrote file /var/www/html/Packages/Sites/Vendor.Site/Resources/Private/Fusion/Presentation/Block/Image/Image.fusion

Done!
```

### Built-in patterns

TBD.

### Writing custom patterns

TBD.

## Contribution & Philosophy

This package promises to always stay consistent with the official Neos Best-Practices (https://docs.neos.io/cms/manual/best-practices). Feel free to shout at us if it doesn't :)

It is noteworthy that the maintainers of this package all work at sitegeist neos solutions GmbH in Hamburg (https://sitegeist.de/). The built-in patterns provided here might be biased from that direction. However, if we observe wide-spread use of this package, we might reconsider its composition accordingly.

In the meantime, you can override pretty much everything in here, if you want to adjust the patterns to your needs or create some of your own.

Of course, we also gladly accept your contributions, be it PRs, Issues, Stars, Tweets, ... :)

Your feedback is very important to us, as it gives us opportunity to grow this package and learn from others in the community. Just keep in mind, that maintenance is spare-time business and PRs might take a while ;)

## License

see [LICENSE](./LICENSE)
