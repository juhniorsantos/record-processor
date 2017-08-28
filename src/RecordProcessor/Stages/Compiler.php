<?php

namespace RodrigoPedra\RecordProcessor\Stages;

use RodrigoPedra\RecordProcessor\Contracts\ProcessorStageFlusher;
use RodrigoPedra\RecordProcessor\Contracts\ProcessorStageHandler;
use RodrigoPedra\RecordProcessor\Contracts\Record;
use RodrigoPedra\RecordProcessor\Contracts\RecordFormatter;
use RodrigoPedra\RecordProcessor\Contracts\Writer;
use RodrigoPedra\RecordProcessor\Helpers\FileInfo;
use RodrigoPedra\RecordProcessor\Stages\TransferObjects\FlushPayload;
use RodrigoPedra\RecordProcessor\Traits\CountsRecords;
use RodrigoPedra\RecordProcessor\Traits\HasFileHeader;
use RodrigoPedra\RecordProcessor\Traits\HasFileTrailler;
use RodrigoPedra\RecordProcessor\Traits\WritesFileHeader;
use RodrigoPedra\RecordProcessor\Traits\WritesFileTrailler;
use SplFileInfo;

class Compiler implements ProcessorStageHandler, ProcessorStageFlusher
{
    use CountsRecords, HasFileHeader, WritesFileHeader, HasFileTrailler, WritesFileTrailler;

    /** @var Writer */
    protected $writer;

    /** @var RecordFormatter */
    protected $recordFormatter;

    /** @var bool */
    protected $isOpen;

    public function __construct( Writer $writer, RecordFormatter $recordFormatter )
    {
        $this->writer          = $writer;
        $this->recordFormatter = $recordFormatter;
    }

    /**
     * @param  Record $record
     *
     * @return  Record|null
     */
    public function handle( Record $record )
    {
        $this->open();

        if ($this->recordFormatter->formatRecord( $this->writer, $record )) {
            $this->incrementRecordCount();
        }

        return $record;
    }

    /**
     * @param  FlushPayload $payload
     *
     * @return FlushPayload
     */
    public function flush( FlushPayload $payload )
    {
        if ($payload->hasRecord()) {
            $record = $this->handle( $payload->getRecord() );

            $payload->setRecord( $record );
        }

        $this->close();

        $payload->setWriterClassName( get_class( $this->writer ) );
        $payload->setLineCount( $this->writer->getLineCount() );
        $payload->setRecordCount( $this->getRecordCount() );

        $output = $this->writer->output();

        $output = $output instanceof SplFileInfo
            ? $output->getFileInfo( FileInfo::class )
            : $output;

        $payload->setOutput( $output );

        return $payload;
    }

    /**
     * @return  void
     */
    protected function open()
    {
        if ($this->isOpen) {
            return;
        }

        $this->recordCount = 0;
        $this->isOpen      = true;

        $this->writer->open();
        $this->writeHeader();
    }

    protected function close()
    {
        if (!$this->isOpen) {
            return;
        }

        $this->isOpen = false;

        $this->writeTrailler();
        $this->writer->close();
    }
}
