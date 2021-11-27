# Reflection Scanner
Scans a directory for class reflection info.
Supports DocBlock annotations (Doctrine annotation).

Example:
```php
$scanner = new \Pada\Reflection\Scanner\Scanner();

/** @var \Pada\Reflection\Scanner\ClassInfo $classInfo */
foreach ($scanner->in('../vendor') as $classInfo) {
    print "----------------------------\n";
    print "File:" . $classInfo->getFilename() . "\n";
    print "Class:" . $classInfo->getReflection()->getShortName() . "\n";
    foreach ($classInfo->getMethodNames() as $methodName) {
        print $classInfo->getReflection()->getShortName() . '.' . $methodName . "\n";
        print_r(\iterator_to_array($classInfo->getMethodAnnotations($methodName)));
    }
}
```
