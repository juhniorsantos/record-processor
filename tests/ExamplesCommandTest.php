<?php

use RodrigoPedra\RecordProcessor\Examples\ExamplesCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;

beforeEach(function () {
    $this->command = new ExamplesCommand();
    $this->commandTester = new CommandTester($this->command);
});

test('command is properly configured', function () {
    expect($this->command->getName())->toBe('examples')
        ->and($this->command->getDescription())->toBe('Showcases converters usage');

    $definition = $this->command->getDefinition();

    expect($definition->hasArgument('reader'))->toBeTrue()
        ->and($definition->hasArgument('writer'))->toBeTrue()
        ->and($definition->hasOption('log'))->toBeTrue()
        ->and($definition->hasOption('aggregate'))->toBeTrue()
        ->and($definition->hasOption('invalid'))->toBeTrue();
});

test('command accepts valid reader types', function () {
    $validReaders = ['array', 'collection', 'csv', 'iterator', 'pdo', 'text'];

    foreach ($validReaders as $reader) {
        $this->commandTester->execute([
            'reader' => $reader,
            'writer' => 'csv',
        ]);

        expect($this->commandTester->getStatusCode())->toBe(0);
    }
});

test('command accepts valid writer types', function () {
    $validWriters = ['csv', 'echo', 'log', 'pdo', 'pdo-buffered', 'text'];

    foreach ($validWriters as $writer) {
        $this->commandTester->execute([
            'reader' => 'csv',
            'writer' => $writer,
        ]);

        expect($this->commandTester->getStatusCode())->toBe(0);
    }
});

test('command handles log option correctly', function () {
    $this->commandTester->execute([
        'reader' => 'array',
        'writer' => 'echo',
        '--log' => true,
    ]);

    $output = $this->commandTester->getDisplay();
    expect($output)->toContain('INPUT')
        ->and($output)->toContain('OUTPUT');
});

test('command handles aggregate option correctly', function () {
    $this->commandTester->execute([
        'reader' => 'array',
        'writer' => 'echo',
        '--aggregate' => true,
    ]);

    expect($this->commandTester->getStatusCode())->toBe(0);
});

test('command handles invalid reader option correctly', function () {
    $this->commandTester->execute([
        'reader' => 'array1',
        'writer' => 'echo',
        '--invalid' => true,
    ]);

    $output = $this->commandTester->getDisplay();

})->throws(InvalidArgumentException::class, 'Invalid reader');

test('command handles invalid writer option correctly', function () {
    $this->commandTester->execute([
        'reader' => 'csv',
        'writer' => 'echo1',
    ]);

    $output = $this->commandTester->getDisplay();

})->throws(InvalidArgumentException::class, 'Invalid writer');