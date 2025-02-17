<?php

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use RodrigoPedra\RecordProcessor\Writers\LogWriter;

test('log writer can be instantiated with a logger', function () {
    $logger = Mockery::mock(LoggerInterface::class);
    $writer = new LogWriter($logger);

    expect($writer)->toBeInstanceOf(LogWriter::class);
});

test('log writer sets default log level to info', function () {
    $logger = Mockery::mock(LoggerInterface::class);
    $logger->shouldReceive('log')
        ->with(LogLevel::INFO, '', Mockery::any())
        ->once();

    $writer = new LogWriter($logger);
    $writer->append('test message');
});

test('log writer can change log level', function () {
    $logger = Mockery::mock('Psr\Log\LoggerInterface');
    $logger->shouldReceive('log')
        ->with(LogLevel::ERROR, '', Mockery::any())
        ->once();

    $writer = new LogWriter($logger);
    $writer->setLevel(LogLevel::ERROR);
    $writer->append('error message');
});

test('log writer throws exception for invalid log level', function () {
    $logger = Mockery::mock('Psr\Log\LoggerInterface');
    $writer = new LogWriter($logger);

    expect(fn () => $writer->setLevel('invalid_level'))
        ->toThrow(InvalidArgumentException::class);
});

test('log writer counts lines correctly', function () {
    $logger = Mockery::mock('Psr\Log\LoggerInterface');
    $logger->shouldReceive('log')->times(3);

    $writer = new LogWriter($logger);
    $writer->open();

    $writer->append('line 1');
    $writer->append('line 2');
    $writer->append('line 3');

    expect($writer->getLineCount())->toBe(3);
});

test('log writer resets line count on open', function () {
    $logger = Mockery::mock('Psr\Log\LoggerInterface');
    $logger->shouldReceive('log')->times(4);

    $writer = new LogWriter($logger);
    $writer->open();

    $writer->append('line 1');
    $writer->append('line 2');

    expect($writer->getLineCount())->toBe(2);

    $writer->open();
    expect($writer->getLineCount())->toBe(0);

    $writer->append('line 3');
    $writer->append('line 4');

    expect($writer->getLineCount())->toBe(2);
});