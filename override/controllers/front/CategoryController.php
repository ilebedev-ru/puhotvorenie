<?php

class CategoryController extends CategoryControllerCore {

    public function process()
    {
        $category_description = $this->category->description;
        parent::process();
        $this->category->description = $category_description;
    }

}