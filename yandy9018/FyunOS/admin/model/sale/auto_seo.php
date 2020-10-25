<?php
class ModelSaleAutoSEO extends Model {
    public function generateAll () {
        $seo= new SEO($this->registry);
        $seo->generateSEOURL();
    }
   
}