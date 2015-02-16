# Laravel Fixture

Basic light-weight fixtures management for Laravel 4.

## How to use

Setup the tearDown() in your test cases

```php
public function tearDown() {
	// Your code
    // ...
	Fixture::tearDown();
}
```

### Usage of Fixture::need()

```php
class FooTest {
	public function testBar() {
    	// Create your fixture by passing the namespaced class name
        // you want to fixture and an array of attributes.
        // If an User instance matching the given attributes is found,
        // then it will be returned. Otherwise a new User instance will be
        // saved to the testing database.
        // Note that User must be an Eloquent implementation.
		$user = Fixture::need('User', array('email' => 'email1'));

        // Example, $user now exists with the given attributes.
        $this->assertEquals($user->email, 'email1');

        // ...
        // more tests.
	}
}
```
You'll want to use the *need* method when your test requires a given instance.


### Usage of Fixture::needNot()

```php
class FooTest {
	public function testBar() {
    	// Ensure that no instances of Some\Object matching the given
        // attributes are stored in the testing database.
		Fixture::needNot('Some\Object', array('attribute' => 'value'));
	}
}
```
You'll use this when your test requires that your database is clean before running.

This may save you some time since you can have a fine data setup instead of running migrations that slower down test execution time.

Note that Fixture is **not** a Facade, so you don't need to configure any service provider.


