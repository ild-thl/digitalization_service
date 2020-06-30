<?php

namespace App;

class XmlElement
{
    protected $parent_tag;

    protected $tag;

    protected $content;

    protected $elmoKeyId;

    protected $elmoKeyTitle;

    function __construct($_parent_tag = "", $_tag = "", $_content = "", $_elmoKeyId = "", $_elmoKeyTitle = "") {
        $this->parent_tag = $_parent_tag;
        $this->tag = $_tag;
        $this->content = $_content;
        $this->elmoKeyId = $_elmoKeyId;
        $this->elmoKeyTitle = $_elmoKeyTitle;
    }

    function getParentTag() {
        return $this->parent_tag;
    }

    function setParentTag($_parent_tag) {
        $this->parent_tag = $_parent_tag;
    }

    function getTag() {
        return $this->tag;
    }

    function setTag($_tag) {
        $this->tag = $_tag;
    }

    function getContent() {
        return $this->content;
    }

    function setContent($_content) {
        $this->content = $_content;
    }

    function getElmoKeyId() {
        return $this->elmoKeyId;
    }

    function setElmoKeyId($_elmoKeyId) {
        $this->elmoKeyId = $_elmoKeyId;
    }

    function getElmoKeyTitle() {
        return $this->elmoKeyTitle;
    }

    function setElmoKeyTitle($_elmoKeyTitle) {
        $this->elmoKeyTitle = $_elmoKeyTitle;
    }
}
