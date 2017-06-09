<?php

namespace Graze\BufferedConsole\Test\Unit;

use Graze\BufferedConsole\BufferedConsoleOutput;
use Graze\BufferedConsole\Terminal\Terminal;
use Graze\BufferedConsole\Test\TestCase;
use Graze\BufferedConsole\Wrap\Wrapper;
use Mockery;
use Symfony\Component\Console\Output\ConsoleOutput;

class BufferedConsoleOutputTest extends TestCase
{
    /** @var BufferedConsoleOutput */
    private $console;
    /** @var mixed */
    private $output;
    /** @var Terminal */
    private $terminal;
    /** @var mixed */
    private $wrapper;
    /** @var mixed */
    private $symfonyTerminal;

    public function setUp()
    {
        $this->output = Mockery::mock(ConsoleOutput::class);
        $this->wrapper = Mockery::mock(Wrapper::class)->makePartial();
        $this->symfonyTerminal = Mockery::mock(\Symfony\Component\Console\Terminal::class);
        $this->symfonyTerminal->shouldReceive('getWidth')
                              ->andReturn(80);
        $this->symfonyTerminal->shouldReceive('getHeight')
                              ->andReturn(50);
        $this->terminal = new Terminal(null, $this->symfonyTerminal);
        $this->console = new BufferedConsoleOutput($this->output, $this->terminal, $this->wrapper);
    }

    public function testSingleWrite()
    {
        $this->output->shouldReceive('write')
                     ->with('sample text', false, 0)
                     ->once();

        $this->console->write('sample text');

        $this->assertTrue(true);
    }

    public function testMultipleWrite()
    {
        $this->output->shouldReceive('write')
                     ->with(['first', 'second'], true, 0)
                     ->once();

        $this->console->writeln(['first', 'second']);

        $this->assertTrue(true);
    }

    public function testUpdate()
    {
        $this->output->shouldReceive('write')
                     ->with(['first', 'second'], false, 0)
                     ->once();
        $this->console->reWrite(['first', 'second']);

        $this->assertTrue(true);
    }

    public function testUpdateOverwrite()
    {
        $this->output->shouldReceive('write')
                     ->with(['first', 'second'], false, 0)
                     ->once();
        $this->console->reWrite(['first', 'second']);

        $this->output->shouldReceive('write')
                     ->with("\e[1A\r\e[5C\e[K thing\n", false, 0)
                     ->once();
        $this->console->reWrite(['first thing', 'second']);

        $this->assertTrue(true);
    }

    public function testUpdateWithStyling()
    {
        $this->output->shouldReceive('write')
                     ->with(['<info>first</info>', '<error>second</error>'], false, 0)
                     ->once();
        $this->console->reWrite(['<info>first</info>', '<error>second</error>']);

        $this->output->shouldReceive('write')
                     ->with("\e[1A\r\e[5C\e[K thing\n", false, 0)
                     ->once();
        $this->console->reWrite(['<info>first</info> thing', '<error>second</error>']);

        $this->output->shouldReceive('write')
                     ->with("\e[1A\r\e[5C\e[K<info> thing</info>\n", false, 0)
                     ->once();
        $this->console->reWrite(['<info>first thing</info>', '<error>second</error>']);

        $this->assertTrue(true);
    }

    public function testUpdateWithStyleReplacement()
    {
        $this->output->shouldReceive('write')
                     ->with(['<info>first</info>', '<error>second</error>'], false, 0)
                     ->once();
        $this->console->reWrite(['<info>first</info>', '<error>second</error>']);

        $this->output->shouldReceive('write')
                     ->with("\e[1A\r\e[K<info>new</info> thing\n\e[K<error>fish</error>", false, 0)
                     ->once();
        $this->console->reWrite(['<info>new</info> thing', '<error>fish</error>']);

        $this->assertTrue(true);
    }

    public function testUpdateWithNewLine()
    {
        $this->output->shouldReceive('write')
                     ->with(['first', 'second'], true, 0)
                     ->once();
        $this->console->reWrite(['first', 'second'], true);

        $this->output->shouldReceive('write')
                     ->with("\e[2A\r\e[5C\e[K thing\n", true, 0)
                     ->once();
        $this->console->reWrite(['first thing', 'second'], true);

        $this->assertTrue(true);
    }

    public function testBlankLines()
    {
        $this->output->shouldReceive('write')
                     ->with(['first', 'second', 'third', 'fourth'], false, 0)
                     ->once();
        $this->console->reWrite(['first', 'second', 'third', 'fourth']);

        $this->output->shouldReceive('write')
                     ->with("\e[3A\r\e[Knew\n\n\n", false, 0)
                     ->once();
        $this->console->reWrite(['new', 'second', 'third', 'fourth']);

        $this->assertTrue(true);
    }

    public function testWrappedLines()
    {
        $this->wrapper->shouldReceive('wrap')
                      ->with(['123456789012345'])
                      ->once()
                      ->andReturn(['1234567890', '12345']);

        $this->output->shouldReceive('write')
                     ->with(['1234567890', '12345'], false, 0)
                     ->once();
        $this->console->reWrite(['123456789012345']);

        $this->wrapper->shouldReceive('wrap')
                      ->with(['123cake   12345'])
                      ->once()
                      ->andReturn(['123cake   ', '12345']);

        $this->output->shouldReceive('write')
                     ->with("\e[1A\r\e[3C\e[Kcake   \n", false, 0)
                     ->once();
        $this->console->reWrite(['123cake   12345']);
    }

    public function testNewlyWrappingLines()
    {
        $this->wrapper->shouldReceive('wrap')
                      ->with(['1234567890','1234567890'])
                      ->once()
                      ->andReturn(['1234567890','1234567890']);
        $this->output->shouldReceive('write')
            ->with(['1234567890','1234567890'], false, 0)
            ->once();
        $this->console->reWrite(['1234567890','1234567890']);

        $this->wrapper->shouldReceive('wrap')
                      ->with(['123456789012345','123456789012345'])
                      ->once()
                      ->andReturn(['1234567890','12345','1234567890','12345']);
        $this->output->shouldReceive('write')
                     ->with("\e[1A\r\n\e[5C\e[K\n\e[K1234567890\n\e[K12345", false, 0)
                     ->once();
        $this->console->reWrite(['123456789012345','123456789012345']);
    }

    public function testTrimmedLines()
    {
        $this->console->setTrim(true);

        $this->wrapper->shouldReceive('trim')
                      ->with(['123456789012345'])
                      ->once()
                      ->andReturn(['1234567890']);

        $this->output->shouldReceive('write')
                     ->with(['1234567890'], false, 0)
                     ->once();
        $this->console->reWrite(['123456789012345']);

        $this->wrapper->shouldReceive('trim')
                      ->with(['123cake   12345'])
                      ->once()
                      ->andReturn(['123cake   ']);

        $this->output->shouldReceive('write')
                     ->with("\r\e[3C\e[Kcake   ", false, 0)
                     ->once();
        $this->console->reWrite(['123cake   12345']);
    }
}
