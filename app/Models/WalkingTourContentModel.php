<?php
class WalkingTourContentModel implements JsonSerializable{
    private int $Id;
    private string $section_name, $title, $text, $button_text, $button_URL;
    private bool $isCreated;

    #[ReturnTypeWillChange]
    public function jsonSerialize(){
        $vars = get_object_vars($this);
        return $vars;
    }
    public function setId(int $id){
        $this->Id = $id;
    }
    public function setSection(string $section){
        $this->section_name = $section;
    }
    public function setTitle(string $title){
            $this->title = $title;
    }
    public function setText(string $text){
        $this->text = $text;
    }
    public function setButtonText(string $text){
        $this->button_text = $text;
    }
    public function setIsCreated(bool $isCreated){
        $this->isCreated = $isCreated;
    }
    public function setButtonURL(string $url){
        $this->button_URL = $url;
    }

    public function getId(): int{
        return $this->Id;
    }
    public function getSection(): string{
        return $this->section_name;
    }
    public function getTitle(): string{
        return $this->title;
    }
    public function getText(): string{
        return $this->text;
    }
    public function getButtonText() : string{
        return $this->button_text;
    }
    public function getIsCreated(){
        return $this->isCreated;
    }
    public function getButtonURL(){
        return $this->button_URL;
    }
}