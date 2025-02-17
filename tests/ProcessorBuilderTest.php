<?php

use RodrigoPedra\RecordProcessor\ProcessorBuilder;
use RodrigoPedra\RecordProcessor\Processor;
use RodrigoPedra\RecordProcessor\Contracts\ProcessorStage;
use RodrigoPedra\RecordProcessor\Records\Parser\ArrayRecordParser;

test('processor builder can be instantiated', function () {
    $builder = new ProcessorBuilder();
    expect($builder)->toBeInstanceOf(ProcessorBuilder::class);
});

test('processor builder can build a processor', function () {
    $builder = new ProcessorBuilder();
    $processor = $builder->build();

    expect($processor)->toBeInstanceOf(Processor::class);
});

test('processor builder can add stages', function () {
    $stage = Mockery::mock(ProcessorStage::class);
    
    $builder = new ProcessorBuilder();
    $builder->addStage($stage);
    
    $processor = $builder->build();
    expect($processor)->toBeInstanceOf(Processor::class);
});

test('processor builder can set logger', function () {
    $logger = Mockery::mock('Psr\\Log\\LoggerInterface');
    
    $builder = new ProcessorBuilder();
    $builder->setLogger($logger);
    
    expect($builder->getLogger())->toBe($logger);
});

test('processor builder can build with source', function () {
    $builder = new ProcessorBuilder();
    $builder->readFromArray(['test' => 'data']);
    
    $processor = $builder->build();
    expect($processor)->toBeInstanceOf(Processor::class);
});