<?php

namespace App;

use SimpleXMLElement;

class ElmoElement
{
    public $xml;

    private array $xmlErrors;

    private array $xmlSchemaErrors;

    private $filename_unsigned;

    private $filename_signed;

    private bool $signatureValid;

    function __construct($_filename_unsigned = "", $_filename_signed = "") {
        $this->xml = null;
        $this->xmlErrors = array();
        $this->xmlSchemaErrors = array();
        $this->filename_unsigned = $_filename_unsigned;
        $this->filename_signed = $_filename_signed;
        $this->signatureValid = false;
    }

    public function getXmlErrors() {
        return $this->xmlErrors;
    }

    public function setXmlErrors(Array $_xmlErrors = array()) {
        $this->xmlErrors = $_xmlErrors;
    }

    public function isXmlValid() {
        return sizeof($this->xmlErrors) === 0;
    }

    public function getXmlSchemaErrors() {
        return $this->xmlSchemaErrors;
    }

    public function setXmlSchemaErrors(Array $_xmlSchemaErrors = array()) {
        $this->xmlSchemaErrors = $_xmlSchemaErrors;
    }

    public function isXmlSchemaValid() {
        return sizeof($this->xmlSchemaErrors) === 0;
    }

    public function getFilenameUnsigned() {
        return $this->filename_unsigned;
    }

    public function getBasenameUnsigned() {
        $strsplit = explode("/", $this->filename_unsigned);
        return $strsplit[sizeof($strsplit)-1];
    }

    public function getFilenameSigned() {
        return $this->filename_signed;
    }

    public function getBasenameSigned() {
        $strsplit = explode("/", $this->filename_signed);
        return $strsplit[sizeof($strsplit)-1];
    }

    public function isSignatureValid() {
        return $this->signatureValid;
    }

    public function setSignatureValid(bool $_signatureValid = false) {
        $this->signatureValid = $_signatureValid;
    }
}