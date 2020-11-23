<?php

namespace App\Classes;

use DOMDocument;

class NfeDOMDocument extends DOMDocument {
    private $chaveAcesso;
    private $valorTotal;
   
    /**
     * Constructor
     */
    function __construct() {
        parent::__construct ();
    }
   
    public function getChaveAcesso(){

        if(isset($this->getElementsByTagName('chNFe')->item(0)->nodeValue)){
            $this->chaveAcesso = $this->getElementsByTagName('chNFe')->item(0)->nodeValue; 
        }else{
            foreach ($this->getElementsByTagName('infNFe')->item(0)->attributes as $attr) {
                if($attr->nodeName == 'Id'){
                    $this->chaveAcesso = str_replace('NFe', '',$attr->nodeValue);
                }
            }
        }

        return $this->chaveAcesso;
    }

    public function getValorTotalNota(){

        $this->valorTotal = $this->getElementsByTagName('vNF')->item(0)->nodeValue; 

        return $this->valorTotal;
    }

}

?>