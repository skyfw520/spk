<?php

namespace SSpkS\Tests;

use PHPUnit\Framework\TestCase;
use SSPkS\Config;
use SSpkS\Package\PackageFinder;

class PackageFinderTest extends TestCase
{
    private $config;
    private $testFolder = __DIR__ . '/example_packageset';

    public function setUp(): void
    {
        $this->config = new Config(__DIR__, 'example_configs/sspks.yaml');
    }

    public function testNotExistFolder()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("/nonexistingfolder is not a folder!");
        $this->config->paths = array_merge(
            $this->config->paths,
            array('packages' => '/nonexistingfolder')
        );
        new PackageFinder($this->config);
    }

    public function testFileInsteadFolder()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessageMatches("/.+ is not a folder!/");
        $this->config->paths = array_merge(
            $this->config->paths,
            array('packages' => __FILE__)
        );
        new PackageFinder($this->config);
    }

    public function testFilelist()
    {
        $this->config->paths = array_merge(
            $this->config->paths,
            array('packages' => $this->testFolder)
        );
        $pf = new PackageFinder($this->config);
        $fl = $pf->getAllPackageFiles();
        $this->assertCount(5, $fl);
        foreach ($fl as $f) {
            $this->assertStringEndsWith('.spk', $f);
            $this->assertFileExists($f);
        }
    }

    public function testPackageList()
    {
        $this->config->paths = array_merge(
            $this->config->paths,
            array('packages' => $this->testFolder)
        );
        $pf = new PackageFinder($this->config);
        $pl = $pf->getAllPackages();
        $this->assertCount(5, $pl);
        foreach ($pl as $p) {
            $this->assertInstanceOf(\SSpkS\Package\Package::class, $p);
        }
    }
}
