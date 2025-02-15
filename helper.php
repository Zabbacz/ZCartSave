<?php

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use VirtuemartCart;
use Joomla\CMS\Language\Text;
use Zabba\Module\ZCartSave\Site\Helper\ZCartSaveHelper;

class ModVirtuemartZCartSaveHelper
{
    public static function getAjax() 
    {
        $app = Factory::getApplication();
        $input = $app->getInput();
//	$my_action = JRequest::getVar('myaction', ''); 
        $my_action = $input->get('myaction', '', 'string');
            if ($my_action === 'save')
            {
                if (ZCartSaveHelper::hasItems()) 
                {
                    ZCartSaveHelper::loadVM(); 
                    $cart = VirtuemartCart::getCart(); 
                    $data = $cart->cartProductsData; 
                    if (empty($data)) return ZCartSaveHelper::returnHandler();
                    $data = json_encode($data); 
                    $user = $app->getIdentity();
                    $user_id = $user->id;
//                    $user_id = JFactory::getUser()->get('id', 0); 
                    $cart_name = $input->get('cart_name', '','string'); 
//                    $cart_name = JRequest::getVar('cart_name', ''); 
                    if (empty($cart_name)) 
                    {
                        $user = $app->getIdentity();
                        $user_id = $user->id;
			if (!empty($user_id)) 
                        {
                            $default_cart_name="posledni";
                            $date = new DateTime();
                            $created_on = strftime("%e-%b-%Y_%H:%M", $date->getTimestamp());
                            $cart_name = $created_on.'-'.substr(uniqid(), 0, 4);
			}
			else
                        {
                            return ZCartSaveHelper::returnHandler(); 
			}
                    }
                    if (empty($data)) return ZCartSaveHelper::returnHandler(); 
                    ZCartSaveHelper::store($cart_name, $data, $user_id); 
		}
            }
// moje
            if ($my_action === 'save_default') 
            {
                if (ZCartSaveHelper::hasItems()) 
                {
                    ZCartSaveHelper::loadVM(); 
                    $cart = VirtuemartCart::getCart(); 
                    $data = $cart->cartProductsData; 
                    if (empty($data)) return ZCartSaveHelper::returnHandler();
                    $data = json_encode($data); 
                    $user = $app->getIdentity();
                    $user_id = $user->id;
//                    $user_id = JFactory::getUser()->get('id', 0); 
                    $cart_name = $input->get('cart_name_default', '','string'); 
//                    $cart_name = JRequest::getVar('cart_name_default', ''); 
                    if (empty($cart_name)) 
                    {
                        $user = $app->getIdentity();
                        $user_id = $user->id;
//                        $user_id = JFactory::getUser()->get('id'); 
			if (!empty($user_id)) 
                        {
                            $default_cart_name="tmp";
                            $cart_name = $default_cart_name;  
			}
			else 
                        {
                            return ZCartSaveHelper::returnHandler(); 
			}
                    }
                    if (empty($data)) return ZCartSaveHelper::returnHandler(); 
                    ZCartSaveHelper::store($cart_name, $data, $user_id); 
				
		}
            }	
            if ($my_action === 'load_default') 
            {
                $cart_name = $input->get('cart_name_default', '', 'string');
//                $cart_name = JRequest::getVar('cart_name_default', ''); 
                $user = $app->getIdentity();
                $user_id = $user->id;
//			$user_id = JFactory::getUser()->get('id', 0); 
//			$merge = JRequest::getVar('merge', true); 
//			$merge = 0; 
                        $merge = $input->get('merge', false);
			$n = ZCartSaveHelper::loadCart($cart_name, $user_id, $merge); 
			$txt = Text::_('MOD_CARTSAVE_LOADEDN'); 
			$txt = str_replace('{n}', $n, $txt); 
			ZCartSaveHelper::drop_default(); 
			return ZCartSaveHelper::returnHandler($txt);
            }

// moje		
            if ($my_action === 'load') 
            {
//                $cart_name = JRequest::getVar('cart_name', ''); 
                $cart_name = $input->get('cart_name', '', 'string');
                $user = $app->getIdentity();
                $user_id = $user->id;
//                $user_id = JFactory::getUser()->get('id', 0); 
                $merge = $input->get('merge', false);
                
                                        ?>
                    <script>
                        alert(<?= $merge ?>;);
    </script><?php

                
//		$merge = JRequest::getVar('merge', false); 
		$n = ZCartSaveHelper::loadCart($cart_name, $user_id, $merge); 
		$txt = Text::_('MOD_CARTSAVE_LOADEDN'); 
		$txt = str_replace('{n}', $n, $txt); 
		return ZCartSaveHelper::returnHandler($txt);
            }
            if ($my_action === 'loadid') 
            {
        	ZCartSaveHelper::loadid(); 
			
            }
            if ($my_action === 'dropid') 
            {
                ZCartSaveHelper::dropid(); 
			
            }
            return ZCartSaveHelper::returnHandler();
        
    }
}    