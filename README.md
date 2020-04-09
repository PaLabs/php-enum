# PHP Enum implementation (Java-like) for php7.4+

[![Build Status](https://travis-ci.com/PaLabs/php-enum.svg?branch=master)](https://travis-ci.com/PaLabs/php-enum.svg?branch=master)
[![Latest Stable Version](https://poser.pugx.org/palabs/php-enum/v/stable)](https://packagist.org/packages/palabs/php-enum)
[![License](https://poser.pugx.org/palabs/php-enum/license)](https://packagist.org/packages/palabs/php-enum)

## Benefits
- Type-hint: `function someAction(Action $action) {`
- No magick methods or phpdoc comments, only raw enum values
- List of all the possible values
- Own fields or methods in enums

## Installation
```
composer require palabs/php-enum
```

## Create your first enum

```php
use PaLabs\Enum\Enum;

class Action extends Enum
{
    public static Action $VIEW, $EDIT;
}
Action::init();
```

That's all!

## Examples

 ```php
function someAction(Action $action) {
    switch($action) {
        case Action::$VIEW:
            // some code
        break;
        case Action::$EDIT:
            // another code
        break;
        default:
            // ...
        break;
    }
}

$viewAction = Action::$VIEW;
if($viewAction->equals(ACTION::$EDIT)) {
 // ...
}

$allActions = Action::values();
```

## Custom fields in enum
 ```php
use PaLabs\Enum\Enum;

class Planet extends Enum
{
    public static Planet 
        $MERCURY,
        $VENUS,
        $EARTH,
        $MARS,
        $JUPITER,
        $SATURN,
        $URANUS,
        $NEPTUNE;

    private float $mass;   // in kilograms
    private float $radius; // in meters
    
    public function __construct(float $mass, float $radius) {
        $this->mass = $mass;
        $this->radius = $radius;
    }

    private const G = 6.67300E-11;

    public function surfaceGravity(): float {
        return self::G * $this->mass / ($this->radius * $this->radius);
    }
    public function surfaceWeight(float $otherMass): float {
        return $otherMass * $this->surfaceGravity();
    }

}

Planet::$MERCURY = new Planet(3.303e+23, 2.4397e6);
Planet::$VENUS = new Planet(4.869e+24, 6.0518e6);
Planet::$EARTH = new Planet(5.976e+24, 6.37814e6);
Planet::$MARS = new Planet(6.421e+23, 3.3972e6);
Planet::$JUPITER = new Planet(1.9e+27,   7.1492e7);
Planet::$SATURN = new Planet(5.688e+26, 6.0268e7);
Planet::$URANUS = new Planet(8.686e+25, 2.5559e7);
Planet::$NEPTUNE = new Planet(1.024e+26, 2.4746e7);
Planet::init();

$yourEarthWeight = 65.0;
$mass = $yourEarthWeight / Planet::$EARTH->surfaceGravity();
foreach (Planet::values() as $planet) {
    sprintf("Your weight on %s is %f%n", $planet->name(), $planet->surfaceWeight($mass));
}
```

## Methods
- `name()` Returns name of the current enum instance (e.g. 'VIEW' for Action::$VIEW)
- `ordinal()` Returns ordinal number of enum instance in all enum instance start with 0. E.g. 0 for Action::$VIEW, 1 for Action::$EDIT
- `equals(Enum $other)` - Tests whether enum instances are equal or not

## Static methods
- `values()` Returns all enum instances
- `valueOf(string $name)` Return enum instance for given name or throwing exception if no enum instance found
- `init()` - Initialize enum (filling enum instances). Need to be called after enum declaration 