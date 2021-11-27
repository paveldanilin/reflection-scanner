<?php


namespace Pada\Reflection\Scanner;


use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;
use Symfony\Component\Finder\Finder;

final class Scanner implements ScannerInterface
{
    private Reader $reader;

    public function __construct(?Reader $reader = null)
    {
        $this->reader = $reader ?? new AnnotationReader();
    }

    /**
     * @param string $dir
     * @return \Generator<ClassInfo>
     */
    public function in(string $dir): \Generator
    {
        $finder = (new Finder())->name('*.php');

        foreach ($finder->in($dir) as $fileInfo) {
            [$namespace, $class] = $this->getNamespaceAndClass($fileInfo->getPathname());

            if (empty($class)) {
                continue;
            }

            if (!empty($namespace)) {
                $namespace .= '\\';
            }

            try {
                $classReflection = new \ReflectionClass($namespace . $class);

                // { methodName: [...annotations,] }
                $methodAnnotations = [];
                foreach ($classReflection->getMethods() as $methodReflection) {
                    $methodAnnotations[$methodReflection->getName()] = $this->reader->getMethodAnnotations($methodReflection);
                }

                yield new ClassInfo(
                    $fileInfo->getFilename(),
                    $classReflection,
                    new \ArrayIterator($this->reader->getClassAnnotations($classReflection)),
                    new \ArrayIterator($methodAnnotations),
                );
            } catch (\Throwable $e) {
                continue;
            }
        }
    }

    private function getNamespaceAndClass(string $filename): array
    {
        $fp = \fopen($filename, 'rb');
        $class = $namespace = $buffer = '';
        $i = 0;

        while (!$class) {
            if (\feof($fp)) {
                break;
            }

            $buffer .= \fread($fp, 2048);
            $tokens = @\token_get_all($buffer);

            if (\strpos($buffer, '{') === false) {
                continue;
            }

            for ($iMax = \count($tokens); $i< $iMax; $i++) {
                if ($tokens[$i][0] === T_NAMESPACE) {
                    for ($j=$i+1, $jMax = \count($tokens); $j< $jMax; $j++) {
                        if ($tokens[$j][0] === T_STRING) {
                            $namespace .= '\\'.$tokens[$j][1];
                        } else if ($tokens[$j] === '{' || $tokens[$j] === ';') {
                            break;
                        }
                    }
                }

                if ($tokens[$i][0] === T_CLASS) {
                    for ($j=$i+1, $jMax = \count($tokens); $j< $jMax; $j++) {
                        if ($tokens[$j] === '{') {
                            $class = $tokens[$i+2][1] ?? '';
                        }
                    }
                }
            }
        }

        return [\trim($namespace), \trim($class)];
    }
}
