<?php


namespace Pada\Reflection\Scanner;


final class ClassInfo
{
    private string $filename;
    private \ReflectionClass $reflectionClass;
    // Class level annotations
    private \ArrayIterator $classAnnotations;
    // { methodName: [] }
    private \ArrayIterator $methodsAnnotations;

    public function __construct(string $filename, \ReflectionClass $reflectionClass, \ArrayIterator $classAnnotations, \ArrayIterator $methodsAnnotations)
    {
        $this->filename = $filename;
        $this->classAnnotations = $classAnnotations;
        $this->methodsAnnotations = $methodsAnnotations;
        $this->reflectionClass = $reflectionClass;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function getClassAnnotations(): \ArrayIterator
    {
        return $this->classAnnotations;
    }

    public function getMethodAnnotations(string $methodName): \ArrayIterator
    {
        if ($this->methodsAnnotations->offsetExists($methodName)) {
            return new \ArrayIterator($this->methodsAnnotations->offsetGet($methodName));
        }
        return new \ArrayIterator([]);
    }

    public function getReflection(): \ReflectionClass
    {
        return $this->reflectionClass;
    }

    /**
     * @return \ArrayIterator<string>
     */
    public function getMethodNames(): \ArrayIterator
    {
        return new \ArrayIterator(\array_keys(\iterator_to_array($this->methodsAnnotations)));
    }
}
