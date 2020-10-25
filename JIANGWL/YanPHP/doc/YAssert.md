## YAssert
YAssert is powered by [beberlei/assert](https://github.com/beberlei/assert).

## YAssert Example usages

``` php
<?php
use Assert\Assertion;

function duplicateFile($file, $times)
{
    Assertion::file($file);
    Assertion::digit($times);

    for ($i = 0; $i < $times; $i++) {
        copy($file, $file . $i);
    }
}
```

Real time usage with [Azure Blob Storage](https://github.com/beberlei/azure-blob-storage/blob/master/lib/Beberlei/AzureBlobStorage/BlobClient.php#L571):

``` php
<?php
public function putBlob($containerName = '', $blobName = '', $localFileName = '', $metadata = array(), $leaseId = null, $additionalHeaders = array())
{
    YAssert::notEmpty($containerName, 'Container name is not specified');
    self::assertValidContainerName($containerName);
    YAssert::notEmpty($blobName, 'Blob name is not specified.');
    YAssert::notEmpty($localFileName, 'Local file name is not specified.');
    YAssert::file($localFileName, 'Local file name is not specified.');
    self::assertValidRootContainerBlobName($containerName, $blobName);

    // Check file size
    if (filesize($localFileName) >= self::MAX_BLOB_SIZE) {
        return $this->putLargeBlob($containerName, $blobName, $localFileName, $metadata, $leaseId, $additionalHeaders);
    }

    // Put the data to Windows Azure Storage
    return $this->putBlobData($containerName, $blobName, file_get_contents($localFileName), $metadata, $leaseId, $additionalHeaders);
}
```

### NullOr helper

A helper method (`YAssert::nullOr*`) is provided to check if a value is null OR holds for the assertion:

``` php
<?php
YAssert::nullOrMax(null, 42); // success
YAssert::nullOrMax(1, 42);    // success
YAssert::nullOrMax(1337, 42); // exception
```

### All helper

The `Assertion::all*` method checks if all provided values hold for the
assertion. It will throw an exception of the assertion does not hold for one of
the values:

``` php
<?php
YAssert::allIsInstanceOf(array(new \stdClass, new \stdClass), 'stdClass'); // success
YAssert::allIsInstanceOf(array(new \stdClass, new \stdClass), 'PDO');      // exception
```

## List of assertions

``` php
<?php
use Assert\Assertion;

YAssert::alnum(mixed $value);
YAssert::between(mixed $value, mixed $lowerLimit, mixed $upperLimit);
YAssert::betweenExclusive(mixed $value, mixed $lowerLimit, mixed $upperLimit);
YAssert::betweenLength(mixed $value, int $minLength, int $maxLength);
YAssert::boolean(mixed $value);
YAssert::choice(mixed $value, array $choices);
YAssert::choicesNotEmpty(array $values, array $choices);
YAssert::classExists(mixed $value);
YAssert::contains(mixed $string, string $needle);
YAssert::count(array|\Countable $countable, array|\Countable $count);
YAssert::date(string $value, string $format);
YAssert::defined(mixed $constant);
YAssert::digit(mixed $value);
YAssert::directory(string $value);
YAssert::e164(string $value);
YAssert::email(mixed $value);
YAssert::endsWith(mixed $string, string $needle);
YAssert::eq(mixed $value, mixed $value2);
YAssert::extensionLoaded(mixed $value);
YAssert::extensionVersion(string $extension, string $operator, mixed $version);
YAssert::false(mixed $value);
YAssert::file(string $value);
YAssert::float(mixed $value);
YAssert::greaterOrEqualThan(mixed $value, mixed $limit);
YAssert::greaterThan(mixed $value, mixed $limit);
YAssert::implementsInterface(mixed $class, string $interfaceName);
YAssert::inArray(mixed $value, array $choices);
YAssert::integer(mixed $value);
YAssert::integerish(mixed $value);
YAssert::interfaceExists(mixed $value);
YAssert::ip(string $value, int $flag = null);
YAssert::ipv4(string $value, int $flag = null);
YAssert::ipv6(string $value, int $flag = null);
YAssert::isArray(mixed $value);
YAssert::isArrayAccessible(mixed $value);
YAssert::isCallable(mixed $value);
YAssert::isInstanceOf(mixed $value, string $className);
YAssert::isJsonString(mixed $value);
YAssert::isObject(mixed $value);
YAssert::isResource(mixed $value);
YAssert::isTraversable(mixed $value);
YAssert::keyExists(mixed $value, string|int $key);
YAssert::keyIsset(mixed $value, string|int $key);
YAssert::keyNotExists(mixed $value, string|int $key);
YAssert::length(mixed $value, int $length);
YAssert::lessOrEqualThan(mixed $value, mixed $limit);
YAssert::lessThan(mixed $value, mixed $limit);
YAssert::max(mixed $value, mixed $maxValue);
YAssert::maxLength(mixed $value, int $maxLength);
YAssert::methodExists(string $value, mixed $object);
YAssert::min(mixed $value, mixed $minValue);
YAssert::minLength(mixed $value, int $minLength);
YAssert::noContent(mixed $value);
YAssert::notBlank(mixed $value);
YAssert::notEmpty(mixed $value);
YAssert::notEmptyKey(mixed $value, string|int $key);
YAssert::notEq(mixed $value1, mixed $value2);
YAssert::notInArray(mixed $value, array $choices);
YAssert::notIsInstanceOf(mixed $value, string $className);
YAssert::notNull(mixed $value);
YAssert::notSame(mixed $value1, mixed $value2);
YAssert::null(mixed $value);
YAssert::numeric(mixed $value);
YAssert::objectOrClass(mixed $value);
YAssert::phpVersion(string $operator, mixed $version);
YAssert::propertiesExist(mixed $value, array $properties);
YAssert::propertyExists(mixed $value, string $property);
YAssert::range(mixed $value, mixed $minValue, mixed $maxValue);
YAssert::readable(string $value);
YAssert::regex(mixed $value, string $pattern);
YAssert::same(mixed $value, mixed $value2);
YAssert::satisfy(mixed $value, callable $callback);
YAssert::scalar(mixed $value);
YAssert::startsWith(mixed $string, string $needle);
YAssert::string(mixed $value);
YAssert::subclassOf(mixed $value, string $className);
YAssert::true(mixed $value);
YAssert::url(mixed $value);
YAssert::uuid(string $value);
YAssert::version(string $version1, string $operator, string $version2);
YAssert::writeable(string $value);

```

Remember: When a configuration parameter is necessary, it is always passed AFTER the value. The value is always the first parameter.

## Exception & Error Handling

If any of the assertions fails a `Assert\AssertionFailedException` is thrown.
You can pass an argument called ```$message``` to any assertion to control the
exception message. Every exception contains a default message and unique message code
by default.

``` php
<?php
use Yan\Core\YAssert;
use Yan\Core\Exception\YAssertionFailedException;

try {
    YAssert::integer($value, "The pressure of gas is measured in integers.");
} catch(YAssertionFailedException $e) {
    // error handling
    $e->getValue(); // the value that caused the failure
    $e->getConstraints(); // the additional constraints of the assertion.
}
```

### Customised exception messages

You can pass a callback as the message parameter, allowing you to construct your own
message only if an assertion fails, rather than every time you run the test.

The callback will be supplied with an array of parameters that are for the assertion.

As some assertions call other assertions, your callback will need to example the array
to determine what assertion failed.

The array will contain a key called `::assertion` that indicates which assertion
failed.

The callback should return the string that will be used as the exception
message.

