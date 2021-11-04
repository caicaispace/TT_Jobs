<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\Http\Message;

class UploadFile
{
    private $stream;
    private $size;
    private $error;
    private $clientFileName;
    private $clientMediaType;

    public function __construct($tempName, $size, $errorStatus, $clientFilename = null, $clientMediaType = null)
    {
        $this->stream          = new Stream(fopen($tempName, 'r+'));
        $this->error           = $errorStatus;
        $this->size            = $size;
        $this->clientFileName  = $clientFilename;
        $this->clientMediaType = $clientMediaType;
    }

    public function getStream()
    {
        return $this->stream;
    }

    public function moveTo($targetPath)
    {
        return file_put_contents($targetPath, $this->stream) ? true : false;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function getError()
    {
        return $this->error;
    }

    public function getClientFilename()
    {
        return $this->clientFileName;
    }

    public function getClientMediaType()
    {
        return $this->clientMediaType;
    }
}
