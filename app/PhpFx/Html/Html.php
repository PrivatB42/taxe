<?php 

namespace App\PhpFx\Html;

class Html {

    private string $html = '';

    public function __construct(){
        
    }

    public function add($html): self {
        $this->html .= $html;
        return $this;
    }

    public function __toString(): string {
        return $this->html;
    }

    public function getHtml(): string {
        return $this->html;
    }

    public function show(): void {
        echo $this->html;
    }
}