<?php

namespace RodrigoPedra\RecordProcessor\Stages;

use Illuminate\Support\Str;
use League\Csv\Exception;
use League\Csv\Reader as RawCSVReader;
use RodrigoPedra\RecordProcessor\Contracts\ProcessorStageFlusher;
use RodrigoPedra\RecordProcessor\Helpers\FileInfo;
use RodrigoPedra\RecordProcessor\Stages\TransferObjects\FlushPayload;
use RodrigoPedra\RecordProcessor\Writers\CSVFileWriter;
use RodrigoPedra\RecordProcessor\Writers\TextFileWriter;
use RuntimeException;
use SplFileInfo;
use SplFileObject;

use function RodrigoPedra\RecordProcessor\value_or_null;

class DownloadFileOutput implements ProcessorStageFlusher
{
    public const bool DELETE_FILE_AFTER_DOWNLOAD = true;

    public const bool KEEP_AFTER_DOWNLOAD = false;

    protected ?SplFileObject $inputFile;

    protected FileInfo $inputFileInfo;

    protected mixed $outputFileInfo;

    protected bool $deleteAfterDownload;

    public function __construct($outputFileName = '', $deleteFileAfterDownload = false)
    {
        $this->outputFileInfo = value_or_null($outputFileName);
        $this->deleteAfterDownload = $deleteFileAfterDownload === true;
    }

    /**
     * @throws Exception
     */
    public function flush(FlushPayload $payload): mixed
    {
        $this->inputFile = $this->getInputFile($payload);
        $this->inputFileInfo = $this->inputFile->getFileInfo(FileInfo::class);

        $this->buildOutputFileInfo($payload->getWriterClassName());

        $this->downloadFile();

        return $this->outputFileInfo;

        //        $payload->setOutput($output);

        //        return $payload;
    }

    protected function getInputFile(FlushPayload $payload): SplFileObject
    {
        $inputFile = $payload->getOutput();

        if (! $inputFile instanceof SplFileObject) {
            throw new RuntimeException('Process output should be a file to be downloadable');
        }

        return $inputFile;
    }

    /**
     * @throws Exception
     */
    protected function downloadFile(): void
    {
        if ($this->inputFileInfo->isCSV()) {
            $this->outputFileWithLeagueCSV($this->inputFile);
        } else {
            $this->sendHeaders();

            $this->inputFile->rewind();
            $this->inputFile->fpassthru();
        }

        $this->unlinkInputFile();

        exit;
    }

    protected function sendHeaders(): void
    {
        $mimeType = $this->inputFileInfo->isTempFile()
            ? $this->outputFileInfo->guessMimeType()
            : $this->inputFileInfo->guessMimeType();

        header('Content-Type: '.$mimeType.'; charset=utf-8');
        header('Content-Transfer-Encoding: binary');
        header('Content-Description: File Transfer');

        $filename = rawurlencode($this->outputFileInfo->getBasename());
        header('Content-Disposition: attachment; filename="'.$filename.'"');
    }

    /**
     * @throws Exception
     */
    protected function outputFileWithLeagueCSV(SplFileObject $file): void
    {
        $reader = RawCSVReader::createFromFileObject($file);
        $reader->download($this->outputFileInfo->getBasename());
    }

    protected function buildOutputFileInfo($writerClassName): void
    {
        if (is_string($this->outputFileInfo)) {
            $this->outputFileInfo = new FileInfo($this->outputFileInfo);

            return;
        }

        if ($this->outputFileInfo instanceof SplFileInfo) {
            $this->outputFileInfo = $this->outputFileInfo->getFileInfo(FileInfo::class);

            return;
        }

        // invalid outputFileInfo, tries to guess from inputFile

        if ($this->inputFileInfo->isTempFile()) {
            $this->outputFileInfo = $this->buildTempOutputFileInfo($writerClassName);

            return;
        }

        $this->outputFileInfo = $this->inputFileInfo;
    }

    protected function unlinkInputFile(): void
    {
        if (! $this->deleteAfterDownload) {
            return;
        }

        $this->inputFile = null;

        $realPath = $this->inputFileInfo->getRealPath();

        if ($realPath === false) {
            // file does not exists
            return;
        }

        unlink($realPath);
    }

    protected function buildTempOutputFileInfo($writerClassName): FileInfo
    {
        $fileName = implode('_', ['temp', date('YmdHis'), Str::random(8)]);

        $extension = null;

        switch ($writerClassName) {
            case CSVFileWriter::class:
                $extension = 'csv';
                break;
            case TextFileWriter::class:
                $extension = 'txt';
                break;
        }

        $fileName = implode('.', array_filter([$fileName, $extension]));

        return new FileInfo($fileName);
    }
}
