<?php
namespace Zabba\Module\ZCartSave\Site\Helper;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\Exception;
use Joomla\Registry\Registry;
use VirtuemartCart;
use VirtuemartControllerConfig;
use OPCmini;
use OPCAddToCartAsLink;
use stdClass;

//use Joomla\Registry\Registry;
//use Joomla\CMS\Router\Route;

class ZCartSaveHelper
{
    private function _die($msg) 
    {
        echo $msg; 
	Factory::getApplication()->close(); 
    }

    private static function getParams() 
    {
        static $params; 
        if (!empty($params)) return $params; 
        $module_id = (int)Factory::getApplication()->getInput()->get('module_id', 0);
        if (!empty($module_id)) 
        {
            $db = Factory::getContainer()->get(DatabaseInterface::class);
            $query = $db->getQuery(true)
                ->select ($db->quoteName('params'))
                ->from ($db->quoteName('#__modules'))
                ->where ($db->quoteName('id'). ' = '.(int)$module_id); 
            $db->setQuery($query); 
            $params_txt = $db->loadResult();
            if (!empty($params_txt)) 
            {
                $params = new Registry($params_txt);  
                return $params; 
            }
        }
        else
        {
            $db = Factory::getContainer()->get(DatabaseInterface::class);
            $query = $db->getQuery(true)
                ->select ($db->quoteName('params'))
                ->from ($db->quoteName('#__modules'))
                ->where ($db->quoteName('module'). ' = '.'\'mod_virtuemart_zcartsave\'')
                ->where ($db->quoteName('published'). ' = '.(int)1);                            
            $db->setQuery($query); 
            $params_txt = $db->loadResult();
            if (!empty($params_txt)) 
            {
                $params = new Registry($params_txt); 
                return $params; 
            }
        }
        return new Registry(''); 
    }
    
    private static function getValidateCartName($my_id, $user_id, $cart_name='') {
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $q = $db->getQuery(true);
        if (empty($user_id)) return false; //only for logged in users
        if (!empty($my_id)) 
        {
            $q = 'select `hash` from #__mod_cartsave where `id` = '.(int)$my_id;
            $b2bshared = self::getParams()->get('b2bshared', 0); 
            if (!empty($b2bshared)) 
            {
                $users = self::getAuthorizedUsers($user_id); 
		$q .= ' and `user_id`IN ('.implode(',', $users).')';  
            }
            else
            {
                /*if (!$params->get('allowany', 0)) */
		$q .= ' and `user_id` = '.(int)$user_id;  
            }
            $db->setQuery($q); 
            $cart_name_loaded = $db->loadResult(); 
            if (empty($cart_name_loaded)) return false; 
            if (!empty($b2bshared)) 
            {
                return $cart_name_loaded; 
            }
            if ($cart_name !== $cart_name_loaded) 
            {
                return false; 
            }
            if ($cart_name === $cart_name_loaded) 
            {
                return $cart_name_loaded;
            }
        }
	return false; 
    }

    public static function loadid() 
    {
        $app = Factory::getApplication();
        $input = $app->getInput();
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $q = $db->getQuery(true);
        $my_id = $input->get('cart_name_id', 0,'int'); 
//	$my_id = JRequest::getInt('cart_name_id', 0); 
        $user = $app->getIdentity();
        $user_id = $user->id;
//	$user_id = JFactory::getUser()->get('id', 0); 
        $merge = $input->get('merge', false);
//	$merge = JRequest::getVar('merge', false); 
	if (empty($user_id)) return; //only for logged in users
	if (!empty($my_id)) 
        {
            $q = 'select `hash` from #__mod_cartsave where `id` = '.(int)$my_id;
            $b2bshared = self::getParams()->get('b2bshared', 0); 
            if (!empty($b2bshared)) 
            {
		$users = self::getAuthorizedUsers($user_id); 
		$q .= ' and `user_id`IN ('.implode(',', $users).')';  
            }
            else
            {
                /*if (!$params->get('allowany', 0)) */
		$q .= ' and `user_id` = '.(int)$user_id;  
            }
            $db->setQuery($q); 
            $cart_name = $db->loadResult(); 
            if (!empty($cart_name)) 
            {
		$n = self::loadCart($cart_name, $user_id, $merge); 
		$txt = Text::_('MOD_CARTSAVE_LOADEDN'); 
		$txt = str_replace('{n}', $n, $txt); 
        	return self::returnHandler($txt); 
            }
	}
    }

    public static function drop_default() 
    {
        $app = Factory::getApplication();
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $q = $db->getQuery(true);
//	$db = JFactory::getDBO();
        $user = $app->getIdentity();
        $user_id = $user->id;
//	$user_id = JFactory::getUser()->get('id', 0);
	$cart_name_default = 'tmp'; 
	$q = 'select `id` from #__mod_cartsave where `hash` = "'.$cart_name_default.'"';
	$q .= ' and `user_id` = '.(int)$user_id;
	$db->setQuery($q);
	$drop_cart_id = $db->loadResult();
	if ((!empty($drop_cart_id)) || ($drop_cart_id === '')) 
        {
            $q = 'delete from #__mod_cartsave where `id` = '.$drop_cart_id;
            $db->setQuery($q);
            $db->execute();
            return self::returnHandler();
	}
    }		
    
    public static function dropid() 
    {
        $app = Factory::getApplication();
         $input = $app->getInput();
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $q = $db->getQuery(true);
        $my_id = $input->get('cart_name_id', 0,'int'); 
//	$my_id = JRequest::getInt('cart_name_id', 0); 
        $user = $app->getIdentity();
        $user_id = $user->id;
//	$user_id = JFactory::getUser()->get('id', 0); 
        $merge = $input->get('merge', false);		
	if (empty($user_id)) return; //only for logged in users
	if (!empty($my_id)) 
        {
            $q = 'select `hash` from #__mod_cartsave where `id` = '.(int)$my_id; 
            $b2bshared = self::getParams()->get('b2bshared', 0); 
            if (!empty($b2bshared)) 
            {
		$users = self::getAuthorizedUsers($user_id); 
		$q .= ' and `user_id`IN ('.implode(',', $users).')';  
            }
            else
            {
                /*if (!$params->get('allowany', 0)) */
		$q .= ' and `user_id` = '.(int)$user_id;  
            }
            $db->setQuery($q); 
            $cart_name = $db->loadResult(); 
            if ((!empty($cart_name)) || ($cart_name === '')) 
            {
		$q = 'delete from #__mod_cartsave where `id` = '.$my_id; 
		$db->setQuery($q); 
            	$db->execute(); 
		return self::returnHandler(); 
            }
	}
    }

    private static function getRealCart() 
    {
        self::loadVM(); 
	$cart = VirtuemartCart::getCart(); 
	return $cart->cartProductsData; 
    }
 
    public static function getCart($cart_name, $user_id, $cart_id=0) {
	if (empty($cart_name) && (empty($cart_id)))
	{
            return self::getRealCart(); 
	}
	self::checkCreateTable(); 
	$res = ''; 
	require_once(JPATH_ROOT.'/components/com_onepage/helpers/mini.php'); 
	require_once(JPATH_ROOT.'/components/com_onepage/helpers/addtocartaslink.php'); 
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $q = $db->getQuery(true);
	$q = "select `cart` from #__mod_cartsave where "; 
	if (!empty($cart_id)) 
        {
            $q .= ' `id` = '.(int)$cart_id; 
	}
	else 
        {
            $q .= " `hash` = '".$db->escape($cart_name)."' "; 
	}
	if (!empty($user_id)) 
        {
            $b2bshared = self::getParams()->get('b2bshared', 0); 
            if (!empty($b2bshared)) 
            {
                $users = self::getAuthorizedUsers($user_id); 
		$q .= ' and `user_id`IN ('.implode(',', $users).')';  
            }
            else
            {
		/*if (!$params->get('allowany', 0)) */
		$q .= ' and `user_id` = '.(int)$user_id;  
            }
	}
	$q .= " limit 1";
	$db->setQuery($q); 
	$res = $db->loadResult(); 
	if (empty($res)) 
        { 
            $params = self::getParams(); 
            if ($params->get('allowany', 0)) 
            {
                //check organization carts:
		$q = "select `cart` from #__mod_cartsave where `hash` = '".$db->escape($cart_name)."' "; 
		$b2bshared = self::getParams()->get('b2bshared', 0); 
		if (!empty($b2bshared)) 
                {
                    $users = self::getAuthorizedUsers($user_id); 
                    $q .= ' and `user_id`IN ('.implode(',', $users).')';  
		}
		else
		{
                    /*if (!$params->get('allowany', 0)) */
                    $q .= ' and `user_id` = '.(int)$user_id;  
		}
		$q .= " limit 1";
		$db->setQuery($q); 
		$res = $db->loadResult(); 
		if (empty($res)) 
                {
                    //get last cart by name of any user:
                    $q = "select `cart` from `#__mod_cartsave` where `hash` = '".$db->escape($cart_name)."' order by `id` desc limit 1"; 
                    $db->setQuery($q); 
                    $res = $db->loadResult(); 
		}
            }
	}
	if (empty($res)) return array(); 
	$cartProductsData = json_decode($res, true); 
	return $cartProductsData; 
    }
    
    public static function loadCart($cart_name, $user_id, $merge=false) 
    {
	$cartProductsData = self::getCart($cart_name, $user_id); 
	return self::loadCartData($cartProductsData, $merge); 
    }
	//check own cart by name 
	//then check organization cart by name (sharedb2b)
	//then check anybodys cart by name (allowany)
    
    private static function loadCartData($cartProductsData, $merge=false) 
    {
        if (empty($cartProductsData)) 
        {
            self::returnHandler(Text::_('MOD_CARTSAVE_NOTFOUND')); 
            return; 
	}
	if (empty($cartProductsData)) return self::returnHandler(JText::_('MOD_CARTSAVE_NOTFOUND')); 
	self::loadVM(); 
	$cart = VirtuemartCart::getCart(); 
	$ign = array('virtuemart_product_id', 'quantity'); 
	if (empty($merge)) 
        {
            $cart->cartProductsData = array();
            $cart->products = array();
	}
	else 
        {
            /*
            foreach ($cartProductsData as $ind => $v) {
            $cart->cartProductsData[] = $v; 
            }
		
            $cartX = OPCmini::getCart(); 
            */
	}
	$obj = new stdClass(); 
	$obj->cart =& $cart; 
	foreach ($cartProductsData as $ind=>$p) 
        {
            $add_id = array(); 
            $qadd = array(); 
            $other = array(); 
            $add_id[$ind] = $p['virtuemart_product_id']; 
            $qadd[$ind] = $p['quantity']; 
            foreach($p as $key=>$val) 
            {
                if (in_array($key, $ign)) continue; 
                $other[$ind][$key] = $val; 
            }
            
	/*
	link_type: 
	0 -> feature disabled
	1 -> deletect cart and set link products
	2 -> do not increment quantity and do not delete cart
	3 -> increment quantity and do not delete cart
	*/
            require_once(JPATH_ROOT.'/components/com_onepage/helpers/mini.php'); 
            require_once(JPATH_ROOT.'/components/com_onepage/helpers/addtocartaslink.php'); 
            OPCAddToCartAsLink::addtocartaslink($obj,$add_id, $qadd, $other, false, 3); 
        }
	//$cart->cartProductsData = $cartProductsData; 
	$cartX = OPCmini::getCart(); 
	return count($cartProductsData); 
    }

    public static function getAuthorizedUsers($user_id) 
    {
	$users = array(); 
	$users[$user_id] = $user_id; 
	$b2bshared = self::getParams()->get('b2bshared', 0); 
	if (empty($b2bshared)) return $users; 
	$sgs = array(); 
	$pairbyemail = true; 
	$ignoreusers = array(); 
	$testusers = array(); 
	$ownsgs = self::getParams()->get('ownsgs', 0); 
	if (!empty($ownsgs)) 
        {
            $sgs = self::getCurrentSG(false); 
            $users = self::getUsersInSGS($sgs, $pairbyemail, $testusers, $ownsgs); 
	}
		require_once(JPATH_ROOT.'/components//com_onepage/helpers/mini.php'); 
		if (OPCmini::tableExists('usertabs')) 
                {
                    if (!empty($users)) 
                    {
                        $users[$user_id] = $user_id; 
                    }
                    else 
                    {
			$users = array(); 
                        $users[$user_id] = $user_id; 
                    }
                    $db = Factory::getContainer()->get(DatabaseInterface::class);
                    $q = $db->getQuery(true);
                    $q = 'select t2.`virtuemart_user_id` from #__usertabs as t1 ';
                    $q .= ' inner join #__usertabs as t2 on t1.`authorized_user_id` = t2.`authorized_user_id` ';
                    $q .= ' where t1.`virtuemart_user_id` IN ('.implode(',', $users).')'; 
                    $db->setQuery($q); 
                    $res = $db->loadAssocList(); 
                    if (!empty($res)) 
                    {
			foreach ($res as $row) 
                        {
                            $user_id = (int)$row['virtuemart_user_id']; 
                            if (empty($user_id)) continue; 
                            $users[$user_id] = $user_id; 
			}
                    }
                    return $users; 
		}
	return $users; 
    }

    public static function getCurrentSG($all=true) 
    {
        $db = Factory::getContainer()->get(DatabaseInterface::class);
//	$db = JFactory::getDBO(); 
        $app = Factory::getApplication();
        $user = $app->getIdentity();
        $user_id = $user->id;
//	$user = JFactory::getUser(); 
//	$user_id = $user->get('id'); 
	if (empty($user_id)) 
        {
            if ($all) 
            { 
                return array('1'); 
            }
            return array(); 
	}
	$ownsgs = self::getParams()->get('ownsgs', 0); 
        $qx = $db->getQuery(true);
	$qx = 'select `virtuemart_shoppergroup_id` from `#__virtuemart_vmuser_shoppergroups` where `virtuemart_user_id` = '.(int)$user_id; 
	$db->setQuery($qx); 
	$res = $db->loadAssocList(); 
	$ret = array(); 
	foreach ($res as $row) 
        {
            $sgid = (int)$row['virtuemart_shoppergroup_id']; 
            if ($all) 
            {
                $ret[$sgid] = $sgid; 
            }
            else
            {
                if (!empty($ownsgs) && ($sgid > $ownsgs)) 
                {
                    $ret[$sgid] = $sgid; 
                }
            }
	}
	if (empty($ret)) 
        {
            if ($all) 
            {
		$ret[2] = 2; 
		return $ret; 
            }
            else 
            {
		return array(); 
            }
	}
	return $ret; 
    }
    
    public static function getUsersInSGS($sgs, &$incm=false, &$testusers=array(), $ownsgs=11) {
	$manager = (int)self::getParams()->get('manager', 0); 
	$testuser = (int)self::getParams()->get('testuser', 0); 
        $db = Factory::getContainer()->get(DatabaseInterface::class);
	foreach ($sgs as $k => $sg) 
        {
            if ($sg <= $ownsgs) unset($sgs[$k]); 
	}
	if (empty($sgs)) return array(); 
	$qx = $db->getQuery(true);
        $qx = 'select s.`virtuemart_user_id` '; 
	if ($incm) 
        {
            $qx .= ', u.`email` '; 
	}
            $qx .= ' from `#__virtuemart_vmuser_shoppergroups` as s '; 
	if ($incm) 
        {
            $qx .= ' left join #__users as u on ((s.`virtuemart_user_id` > 0) and (u.`id` = s.`virtuemart_user_id`)) '; 
	}
            $qx .= ' where s.`virtuemart_shoppergroup_id` IN ('.implode(',', $sgs).')'; 
	try
        {
            $db->setQuery($qx); 
            $res = $db->loadAssocList(); 
	}
	catch (DatabaseExceptionExecuting $e) 
        {  
	}
	$users = array(); 
	$emails = array(); 
	$or = array(); 
	foreach ($res as $row) 
        {
            $user_id = (int)$row['virtuemart_user_id']; 
            $users[$user_id] = $user_id; 
            if (!empty($row['email'])) 
            {
                $emails[$row['email']] = $row['email']; 
                $or[] = '(u.`email` like \''.$db->escape($row['email']).'\')';
            }
	}
        /*
        $db = JFactory::getDBO(); 
	$q = 'select `group_id`, `user_id` from #__user_usergroup_map where user_id in ('.implode(',', $user_ids).') '; 
	$db->setQuery($q); 
	$gs = $db->loadAssocList(); 
	$users = array(); 
	foreach ($gs as $row) {
			$uid = (int)$row['user_id']; 
			$gid = (int)$row['group_id']; 
			if (empty($users[$uid])) $users[$uid] = array(); 
			$users[$uid][$gid] = $gid; 
			
	}
    */
        $db->setQuery($qx); 
	$q = 'select g.`user_id`, u.`email`, g.`group_id`, count(g.`group_id`) as `c` from `#__user_usergroup_map` as g right join #__users as u on (g.`user_id` = u.`id`) where (('.implode(' or ', $or).') or (u.`id` in ('.implode(',', $users).'))) group by g.`user_id` ';
	$db->setQuery($q); 
	$res = $db->loadAssocList(); 
        $user = Factory::getApplication()->getIdentity();
        $my_user_id = $user->id;
//	$my_user_id = JFactory::getUser()->get('id'); 
	foreach ($res as $row) 
        {
            $user_id = (int)$row['user_id']; 
            $c = (int)$row['c']; 
            $g = (int)$row['group_id']; 
            if ($g === $manager) 
            {
                $testusers[$user_id] = new stdClass(); 
		$testusers[$user_id]->email = $row['email']; 
		$testusers[$user_id]->user_id = $user_id; 
		unset($emails[$row['email']]); 
                unset($users[$user_id]); 
            }    
            if ($g === $testuser) 
            {
                $testusers[$user_id] = new stdClass(); 
		$testusers[$user_id]->email = $row['email']; 
		$testusers[$user_id]->user_id = $user_id; 
		unset($emails[$row['email']]); 
                unset($users[$user_id]); 
            }              
	//unset test users assigned to 2 and more groups:
            if (($user_id !== $my_user_id) && ($c > 1)) 
            {
                if (isset($emails[$row['email']])) 
                {
                    $testusers[$user_id] = new stdClass(); 
                    $testusers[$user_id]->email = $row['email']; 
                    $testusers[$user_id]->user_id = $user_id; 
                    unset($emails[$row['email']]); 
		}
		if (isset($users[$user_id])) 
                {
                    $testusers[$user_id] = new stdClass(); 
                    $testusers[$user_id]->email = $row['email']; 
                    $testusers[$user_id]->user_id = $user_id; 
                    unset($users[$user_id]); 
		}
            }
            elseif ($c > 1) 
            {
                $testusers[$user_id] = new stdClass(); 
		$testusers[$user_id]->email = $row['email']; 
		$testusers[$user_id]->user_id = $user_id; 
            }
	}
	if ($incm) 
        {
            $incm = $emails; 
	}
	return $users; 
    }
    
    public function getDefaultCart() 
    {
        $user = Factory::getApplication()->getIdentity();
        $user_id = $user->id;
//        $user_id = JFactory::getUser()->get('id', 0);
	$default_cart_name = 'tmp'; 
        $db = Factory::getContainer()->get(DatabaseInterface::class);
            $q = $db->getQuery(true);
            $q = 'select count(*) from `#__mod_cartsave` where '; 
            $q .= ' `user_id` = '.(int)$user_id;
            $q .= ' AND `hash` LIKE "'.$default_cart_name.'"';
	$db->setQuery($q);
	$count = $db->loadResult();
	return $count;
    }

    public function getNames() 
    {
        $user = Factory::getApplication()->getIdentity();
        $user_id = $user->id;
//	$user_id = JFactory::getUser()->get('id', 0); 
	if (empty($user_id)) return array(); 
	$params = self::getParams(); 
	$b2bshared = $params->get('b2bshared', 0); 
	if (!empty($b2bshared)) 
        {
            self::getAuthorizedUsers($user_id); 
	}
	self::checkCreateTable();
            $db = Factory::getContainer()->get(DatabaseInterface::class);
                $q = $db->getQuery(true);
		$q = 'select `id`, `hash` from `#__mod_cartsave` where '; 
		if (!empty($b2bshared)) 
                {
                    $users = self::getAuthorizedUsers($user_id); 
                    $q .= ' `user_id`IN ('.implode(',', $users).')';  
		}
		else
		{
                    /*if (!$params->get('allowany', 0)) */
                    $q .= ' `user_id` = '.(int)$user_id;  
		}
		$q .= ' order by `id` desc '; 
		$db->setQuery($q); 
		$res = $db->loadAssocList(); 
		if (!empty($res)) 
                {
                    $names = array(); 
                    foreach ($res as $row) 
                    {
                        $names[(int)$row['id']] = $row['hash']; 
                    }
                    return $names; 
		}
		else
                {
                    return array(); 
		}
    }

    private static function checkValidateQuantities($cartData) 
    {
		
    }

    public static function returnHandler($txt='') 
    {
        $return = Factory::getApplication()->getInput()->get('return', '');
        $redirecttocart = (int)self::getParams()->get('redirecttocart', 0); 
	if ($redirecttocart === 0) 
        {
            $url = Route::_('index.php?option=com_virtuemart&view=cart');
            if (empty($txt)) 
            {
		Factory::getApplication()->redirect($url); 
            }
            else
            {
        	Factory::getapplication()->enqueueMessage($txt, 'notice'); 
		Factory::getApplication()->redirect($url); 
            }
	}
	if (!empty($return)) 
        {
            $url = base64_decode($return); 
            if (empty($txt)) 
            {
                Factory::getApplication()->redirect($url); 
            }
            else 
            {
		Factory::getapplication()->enqueueMessage($txt, 'notice'); 
		Factory::getApplication()->redirect($url); 
            }
	}
    }

    public static function store($hash, $json_data, $user_id) 
    {
        self::checkCreateTable(); 
	if (empty($hash)) return; 
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $q = $db->getQuery(true);
	$b2bshared = self::getParams()->get('b2bshared', 0); 
	$q = "select `id` from #__mod_cartsave where `hash` = '".$db->escape($hash)."' "; 
	if (!empty($user_id)) 
        {
            if (!empty($b2bshared)) 
            {
		$users = self::getAuthorizedUsers($user_id); 
		$q .= ' and `user_id`IN ('.implode(',', $users).')';  
            }
            else
            {
		/*if (!$params->get('allowany', 0)) */
		$q .= ' and `user_id` = '.(int)$user_id;  
            }
	}
	$q .= " limit 1";
	$db->setQuery($q); 
	$rx = $db->loadResult(); 
	if (empty($user_id))
        {
            if (!empty($rx)) 
            {
		return self::returnHandler(__LINE__); 
            }
	}
	if (!empty($rx)) 
        { 
            $q = "update `#__mod_cartsave` set `cart` = '".$db->escape($json_data)."', `modified` = NOW() where `id` = ".(int)$rx;
            $db->setQuery($q); 
            $db->execute(); 
            return self::returnHandler(__LINE__); 
	}
		
	$q = "insert into `#__mod_cartsave`  (`id`, `cart`, `hash`, `user_id`, `created`, `created_by`, `modified`, `modified_by`) ";
	$q .= " values (NULL, '".$db->escape($json_data)."', '".$db->escape($hash)."', ".(int)$user_id.", NOW(), ".(int)$user_id.", NOW(), ".(int)$user_id.")"; 
	$db->setQuery($q); 
	$db->execute(); 
	return self::returnHandler(__LINE__); 
    }
    
    private static function checkCreateTable() {
        if (self::tableExists('mod_cartsave')) return; 
            $inno = 'CREATE TABLE IF NOT EXISTS `#__mod_cartsave` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`cart` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
		`hash` varchar(160) CHARACTER SET utf8 NOT NULL COLLATE utf8_general_ci NOT NULL,
		`user_id` int(11) NOT NULL,
		`extra` varchar(255) NOT NULL DEFAULT \'\',
		`created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
		`created_by` int(11) NOT NULL DEFAULT \'0\',
		`modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
		`modified_by` int(11) NOT NULL DEFAULT \'0\',
		
		PRIMARY KEY (`id`),
		KEY `hash` (`hash`),
		KEY `user_id` (`user_id`)
	
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=380'; 
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $inno = $db->getQuery(true);
        $db->setQuery($inno); 
        $db->execute(); 
    }
    
    private static function tableExists($table)
    {
    $db = Factory::getContainer()->get(DatabaseInterface::class);
    $prefix = $db->getPrefix();
    $table = str_replace('#__', '', $table); 
    $table = str_replace($prefix, '', $table); 
    $q = $db->getQuery(true);
    $q = "SHOW TABLES LIKE '".$db->getPrefix().$table."'";
    $db->setQuery($q);
    $r = $db->loadResult();
    if (!empty($r)) 
    {
        return true;
    }
    return false;
    }

    public static function hasItems() 
    {
        $session = Factory::getApplication()->getSession(); 
	$cart = $session->get('vmcart', 0, 'vm');
	if (empty($cart)) 
        {
            $c2 = $session->get('vmcart', 0);
            if (!empty($c2)) $cart = $c2; 
	}
	if (empty($cart)) 
        {
            return false; 
	}
	$sessionCart = (object)json_decode( $cart ,true);
	if (!empty($sessionCart) && (!empty($sessionCart->cartProductsData))) 
        {
            return true; 
	}
	self::loadVM(); 
        $cart = VirtueMartCart::getCart(false); 
	if (!empty($cart->cartProductsData)) return true; 
        return false; 
    }

    public static function loadVM() 
    {
        if (!class_exists('VmConfig'))	  
	{
            require (JPATH_ADMINISTRATOR.'/components/com_virtuemart/helpers/config.php');
	    
	   }
	   \VmConfig::loadConfig(); 
         if (!class_exists('VirtueMartCart'))
         {
            require(JPATH_SITE.'/components/com_virtuemart/helpers/cart.php');
         }
	 Factory::getLanguage()->load('com_virtuemart');
    }
    
}