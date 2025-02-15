<?php 
// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use VirtuemartCart;
//use Zabba\Module\ZCartSave\Site\Helper\ZCartSaveHelper;

$document = $app->getDocument();
$wa = $document->getWebAssetManager();
$wa->getRegistry()->addExtensionRegistryFile('mod_virtuemart_zcartsave');
$wa->useScript('mod_virtuemart_zcartsave.askid2');
$wa->useScript('mod_virtuemart_zcartsave.mod_cartsave');
$wa->useStyle('mod_virtuemart_zcartsave.mod_cartsave');
$wa->useStyle('mod_virtuemart_zcartsave.opcard');
//$language = Factory::getApplication()->getLanguage();
//$language->load('mod_virtuemart_zcartsave', JPATH_BASE . '/modules/mod_virtuemart_zcartsave');
//var_dump($wa);
$document->addScriptOptions('mod_virtuemart_zcartsave.mod_cartsave', array('version' => 'auto', 'relative' => false), array('async' => 'async', 'defer'=>'defer')); 

//JHtml::_('script', $root.'/modules/mod_cartsave/assets/js/mod_cartsave.js', array('version' => 'auto', 'relative' => false), array('async' => 'async', 'defer'=>'defer')); 
if ($params->get('fontawesome', 0) == 0) {
   ?>
    <script defer src="https://use.fontawesome.com/releases/v5.0.8/js/all.js"></script>
<?php } 
?>
<script type="text/javascript"><!--//--><![CDATA[//><!--
  var MOD_CARTSAVE_QUESTION = <?php echo json_encode(Text::_('MOD_CARTSAVE_QUESTION')); ?>; 
  var MOD_CARTSAVE_WRONGFILEFORMAT = <?php echo json_encode(Text::_('MOD_CARTSAVE_WRONGFILEFORMAT')); ?>; 
  var MOD_CARTSAVE_ERROR_NAME_MISSING_LOAD = <?php echo json_encode(Text::_('MOD_CARTSAVE_ERROR_NAME_MISSING_LOAD')); ?>; 
  var MOD_CARTSAVE_ERROR_NAME_MISSING_SAVE = <?php echo json_encode(Text::_('MOD_CARTSAVE_ERROR_NAME_MISSING_SAVE')); ?>; 
  var MOD_CARTSAVE_MERGE_TRUE = <?php echo json_encode(Text::_('MOD_CARTSAVE_MERGE_TRUE')); ?>; 
  var MOD_CARTSAVE_MERGE_FALSE = <?php echo json_encode(Text::_('MOD_CARTSAVE_MERGE_FALSE')); ?>; 
  var MOD_CARTSAVE_DELETEACRT = <?php echo json_encode(Text::_('MOD_CARTSAVE_DELETEACRT')); ?>; 
//--><!]]>  
</script>
<div class="modulecartsaver <?php echo $params->get('header_class', ''); ?>">
<form action="<?php echo Route::_('index.php'); ?>" method="POST" id="cartsaverform_<?php echo $id; ?>" 
      class="is_empty" name="cartsaverform_<?php echo $id; ?>" <?php if ($params->get('showtoolboxlink', 0) != 0) { echo ' style="display:none;" '; } ?>>
<?php if ($params->get('displaycartnameinput', 0) == 0) { ?>

    
<input class="form-control cart_name_input" 
       id="cart_name_<?php echo $id; ?>" 
       cart-id ="<?php echo $id; ?>"
       thisCart ="this"
       type="text" value="" 
       placeholder="<?php echo htmlentities(Text::_('MOD_CARTSAVE_YOUR_CART_NAME')); ?>" 
       form="cartsaverform_<?php echo $id; ?>" 
       name="cart_name">
 
<?php } ?>
<?php if (!empty($hasItems)) { 
$mergetype = (int)$params->get('mergetype', 0);
$do_not_show = array(2, 3, 4, 5); 
if (!in_array($mergetype, $do_not_show)) {
?>
<div>
 <div class=""><input class="mergecheckbox" id="merge_<?php echo $id; ?>" type="checkbox" <?php 
 if ($mergetype === 0) {
	echo ' checked="checked" '; 
 }
 
 ?> value="1" form="cartsaverform_<?php echo $id; ?>" name="merge">
     
 <label for="merge_<?php echo $id; ?>" id="label_merge"><?php echo Text::_('MOD_CARTSAVE_MERGE'); ?></label>
 </div>
</div>
<?php }
  else {
  if ($mergetype === 4) {
	  ?><input id="merge_<?php echo $id; ?>" type="hidden" value="0" form="cartsaverform_<?php echo $id; ?>" name="merge" /><?php
  }
  else if ($mergetype === 5) {
	  ?><input id="merge_<?php echo $id; ?>" type="hidden" value="1" form="cartsaverform_<?php echo $id; ?>" name="merge" /><?php
  }
  elseif ($mergetype === 2) {
	  ?><input data-value="1" value="1"  id="merge_<?php echo $id; ?>"
                 data-question="<?php echo htmlentities(json_encode(Text::_('MOD_CARTSAVE_MERGE_DIALOG'))); ?>" 
                 data-questionyes="<?php echo htmlentities(json_encode(Text::_(Text::_('MOD_CARTSAVE_MERGE_DIALOG_YES')))); ?>"
                 data-questioncancel="<?php echo htmlentities(json_encode(Text::_(Text::_('MOD_CARTSAVE_MERGE_DIALOG_CANCEL')))); ?>" 
                 data-questionno="<?php echo htmlentities(json_encode(Text::_(Text::_('MOD_CARTSAVE_MERGE_DIALOG_NO')))); ?>" 
                 type="hidden" form="cartsaverform_<?php echo $id; ?>" name="merge" /><?php
  }
  elseif ($mergetype === 3) {
	  ?><input data-value="0" value="0"  id="merge_<?php echo $id; ?>" data-question="
              <?php echo htmlentities(json_encode(Text::_('MOD_CARTSAVE_MERGE_DIALOG'))); ?>" 
              data-questionyes="<?php echo htmlentities(json_encode(Text::_(Text::_('MOD_CARTSAVE_MERGE_DIALOG_YES')))); ?>"
              data-questioncancel="<?php echo htmlentities(json_encode(Text::_(Text::_('MOD_CARTSAVE_MERGE_DIALOG_CANCEL')))); ?>" 
              data-questionno="<?php echo htmlentities(json_encode(Text::_(Text::_('MOD_CARTSAVE_MERGE_DIALOG_NO')))); ?>" 
              type="hidden" form="cartsaverform_<?php echo $id; ?>" name="merge" /><?php
  }
}	
}
else {
	?><input id="merge_<?php echo $id; ?>" type="hidden" value="1" form="cartsaverform_<?php echo $id; ?>" name="merge" /><?php
}
?>
<?php if ((!empty($hasItems)) && ($params->get('displaysavebutton', 0) == 0)) {
$onlylogged = $params->get('displaysavebuttonforlogged', 0);
if (($params->get('displaycartnameinput', 0) == 0) || (!empty($user_id)))
if (((!empty($onlylogged)) && (!empty($user_id))) || (empty($onlylogged))) {
	?>

        <button  type="button" 
                 id="save_button"
                 class="btn btn-primary show_on_input" 
                  value="<?php echo htmlentities(Text::_(Text::_('MOD_CARTSAVE_SAVE'))); ?>"
                  cart-action="save"
                  cart-id="<?php echo $id ?>">
        <i class="far fa-save"></i><?php echo Text::_('MOD_CARTSAVE_SAVE'); ?></button>

        
<?php } } ?>
<?php 
if ($params->get('displaycartnameinput', 0) == 0) 
        if ($params->get('displayloadbutton', 0) == 0) { ?>
 
        <button class="btn btn-primary show_on_input"
            id ="load_button"
            value="<?php echo htmlentities(Text::_(Text::_('MOD_CARTSAVE_LOAD'))); ?>" 
            cart-action="load"
            cart-id="<?php echo $id;?>">
            <i class="far fa-folder-open"></i>
            <?php echo Text::_('MOD_CARTSAVE_LOAD'); ?></button>	 

<?php } ?> 
        
<?php if ((!empty($cart_names)) && ($params->get('displaycartlist', 0) == 0)) { ?>
<div class="cartname_list hide_on_input">
<?php if ($params->get('displayfulllist', 0) == 0) { ?>


<a href="#" id="toggleLink_<?php echo key($cart_names); ?>" class="listtoggler listtoggler_<?php key($cart_names); ?>" 
   data-id="<?php echo key($cart_names); ?>"> <!-- Přidáme datový atribut -->
(<?php echo count($cart_names); ?>) 
   <?php echo Text::_('MOD_CARTSAVE_LISTCARTS'); ?>
   <span class="list_state"></span>
</a>
    
<?php } ?>
<div class="cart_list cart_list_<?php echo key($cart_names); ?>" <?php
if ($params->get('displayfulllist', 0) == 0) {
 echo ' style="display:none;" '; 
}
?>>
 
<?php
foreach ($cart_names as $cart_name_id=>$name) {
	/* stAn note -> this line is compatible ONLY with latest joomla 3.8+ */
	$cart_link = Route::_('index.php?option=com_ajax&module=cartsave&cart_name_id='.(int)$cart_name_id.'&format=raw&module_id='.(int)$module_id.'&myaction=load&cart_name='.urlencode($name), true, true, true);
    
	?><div class="namedcart_row">
            
    <a class="load_named_cart hasTooltip" href="<?php echo $cart_link; ?>"
        cartName-Id = "<?php echo (int)$cart_name_id; ?>"
        cart-id = "<?php echo (int)$id; ?>"
        title="<?php echo htmlentities(Text::_(Text::_('MOD_CARTSAVE_LOAD')).': '.$name); ?>">
    		<?php if ($params->get('displayloadicon', 0) == 0) { ?>
		<i class="fas fa-arrow-alt-circle-up"></i>
		<?php } ?>
		<?php if ($params->get('displaycartname', 0) == 0) { ?>
		<span class="cart_name" ><?php echo $name; ?></span>
		<?php } ?>
    </a>
	<?php if ($params->get('displayremoveicon', 0) == 0) { ?>

            <a class="drop_named_cart hasTooltip menuicon" href="#" 
            cartName-Id = "<?php echo (int)$cart_name_id; ?>"
            cart-id = "<?php echo (int)$id; ?>"
            cart-name ="<?php echo htmlentities($name); ?>"
           title="<?php echo htmlentities(Text::_(Text::_('MOD_CARTSAVE_DROP')).': '.$name); ?>" >
            <i class="fas fa-trash-alt"></i></a>
	<?php } ?>

	</div>

<?php
  }
?>

</div>
</div>
<?php } ?>

 <input type="hidden" form="cartsaverform_<?php echo $id; ?>" name="option" value="com_ajax" />
 <input type="hidden" form="cartsaverform_<?php echo $id; ?>" name="module" value="virtuemart_zcartsave" />
 <input type="hidden" form="cartsaverform_<?php echo $id; ?>" name="cart_name_id" value="" id="cart_name_id_<?php echo $id; ?>" />
 <input type="hidden" form="cartsaverform_<?php echo $id; ?>" name="format" value="raw" />
 <input type="hidden" form="cartsaverform_<?php echo $id; ?>" name="myaction" value="" id="myaction_<?php echo $id; ?>" />
 <input type="hidden" form="cartsaverform_<?php echo $id; ?>" name="return" value="<?php echo $return; ?>" />
 
 <input type="hidden" form="cartsaverform_<?php echo $id; ?>" name="module_id" value="<?php echo (int)$module_id; ?>" />

<input type="hidden" id="cart_name_default_<?php echo $id; ?>" form="cartsaverform_<?php echo $id; ?>"  name="cart_name_default" value="tmp"/> 
 <?php 

 $Itemid = $input->get('Itemid', 0,'int'); 
 //$Itemid = JRequest::getInt('Itemid', 0); 
 $lang = $input->get('lang', '');
 //$lang = JRequest::getVar('lang', ''); 
 if (!empty($lang)) {
	 ?><input type="hidden" form="cartsaverform_<?php echo key($cart_names); ?>" name="lang" value="<?php echo htmlentities($lang); ?>" /><?php
 }
 
 if (!empty($Itemid)) { ?>
  <input type="hidden" form="cartsaverform_<?php echo key($cart_names); ?>" name="Itemid" value="<?php echo (int)$Itemid; ?>" />
 <?php }
 
 if ($params->get('clearcart', 0)) {
	 $cart = VirtuemartCart::getCart(); 
	 if (!empty($cart->cartProductsData)) {
	 Factory::getApplication()->getLanguage()->load('com_onepage'); 
	 ?> 

  <!-- moje -->
  
         <a class="btn btn-primary hide_on_input"
            id ="save_default_button"
            cart-action="save_default"
            cart-id="<?php echo $id ?>">
            <i class="far fa-save"></i><?php echo Text::_('MOD_CARTSAVE_SAVE_DEFAULT'); ?></a>
  
         <a class="btn btn-danger hide_on_input" style="color: white"
            href="<?php echo Route::_('index.php/?option=com_onepage&view=opc&task=clearcart'); ?>"><i class="fas fa-trash-alt"></i>
             <?php echo Text::_('COM_ONEPAGE_CLEAR_CART'); ?></a>
 
	 <?php
	 }
         
if ($count_default_cart > 0) {
	 ?>
            <button class="btn btn-primary hide_on_input"
            id ="load_default_button"
            value="<?php echo htmlentities(Text::_(Text::_('MOD_CARTSAVE_LOAD_DEFAULT'))); ?>" 
            cart-action="load_default"
            cart-id="<?php echo $id;?>">
            <i class="far fa-folder-open"></i>
            <?php echo Text::_('MOD_CARTSAVE_LOAD_DEFAULT'); ?></button>	 


<!-- moje -->

<?php
}

 }
  
 
 ?>
 

</form>
</div> 
