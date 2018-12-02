
Jmeter tests can be used for load/functional testing

PHPUnit tests are suited for unit testing. Can used for functional tests when we need to simulate the behavior of a browser.

## How to run jmeter tests

Install jmeter and run it
```
sudo apt install jmeter
jmeter
```

Run test directly from the terminal
```
jmeter -n -t tests/jmeter/test.jmx results.csv
```

Tutorials for jmeter
http://jmeter.apache.org/usermanual/test_plan.html


## How to run phpunit tests

Install composer
```
sudo apt-get install composer
```

In the root directory of gila cms add the composer.json
```
{
    "require": {
        "phpunit/phpunit": "^7"
    }
}
```

Run
```
composer install
```

Now you must have a new directory vendor that contains phpunit package. You can run the tests from root directory like this.
```
./vendor/phpunit/phpunit/phpunit tests/phpunit/class-gila.php
```

More informations for the phpunit
https://phpunit.de
https://phpunit.readthedocs.io
