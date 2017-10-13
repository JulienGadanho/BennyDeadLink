<?php

class Check
{
    public $id;
    public $url;
    public $cible;
    
    function SetUrl($url)
    {
        $this->url = $url;
    }
    
    function SetCible($cible)
    {
        $this->cible = $cible;
    }
    
    function SetId($id)
    {
        $this->id = $id;
    }
    
    function UpdateStatut()
    {
        global $bdd;
        $html = file_get_contents($this->cible);
        
        if(strstr($html,$this->url) != false)
        {
            $bdd->query("UPDATE actions SET state = '1', last_crawl ='".date('d-m-Y')."' WHERE id = '".$this->id."'");
        }
        else
        {
            $bdd->query("UPDATE actions SET state = '0', last_crawl ='".date('d-m-Y')."' WHERE id = '".$this->id."'");
        }
    }
}