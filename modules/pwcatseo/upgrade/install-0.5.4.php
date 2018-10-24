<?php

function upgrade_module_0_5_4($object)
{
    $object->registerHook('actionAdminCategoriesFormModifier');
    $object->registerHook('actionAdminCategoriesControllerSaveAfter');
    return true;
}
