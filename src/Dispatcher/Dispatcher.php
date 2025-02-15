<?php

namespace Zabba\Module\ZCartSave\Site\Dispatcher;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Dispatcher\AbstractModuleDispatcher;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\Input\Input;
use Joomla\Registry\Registry;
use Joomla\CMS\Helper\HelperFactoryAwareInterface;
use Joomla\CMS\Helper\HelperFactoryAwareTrait;

class Dispatcher extends AbstractModuleDispatcher implements HelperFactoryAwareInterface
{
    use HelperFactoryAwareTrait;

    protected function getLayoutData(): array
    {
        $data = parent::getLayoutData();
        
        $data['hasItems'] = $this->getHelperFactory()->getHelper('ZCartSaveHelper')->hasItems(); 
        $data['cart_names'] = $this->getHelperFactory()->getHelper('ZCartSaveHelper')->getNames();
        $data['count_default_cart'] = $this->getHelperFactory()->getHelper('ZCartSaveHelper')->getDefaultCart();
        $data['return'] = base64_encode(Uri::getInstance()->toString());
        
        if ((isset($module)) && (!empty($module->id))){
        $data['id'] = (int)$module->id; 
        }
        else{
            $data['id'] = 0; 
        }
        $data['module_id'] = $id; 
        $data['user_id'] = Factory::getApplication()->getIdentity()->id;
        
        $data['root'] = Uri::root();  
       if (substr($data['root'], -1) === '/') {
           $data['root'] = substr($data['root'], 0, -1); 
       }
        
    return $data;

    }
}