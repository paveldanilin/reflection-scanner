<?php


namespace Pada\Reflection\Scanner;

interface ScannerInterface
{
    /**
     * Scans directory for annotation info
     *
     * @param string $dir
     * @return \Generator<ClassInfo>
     */
    public function in(string $dir): \Generator;
}
