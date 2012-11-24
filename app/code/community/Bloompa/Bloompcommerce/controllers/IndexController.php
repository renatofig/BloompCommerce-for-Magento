<?php
require_once 'Mage/Checkout/controllers/CartController.php';
class Bloompa_Bloompcommerce_IndexController extends Mage_Core_Controller_Front_Action{
    public function IndexAction() {
      
	  // $this->loadLayout();   
	  // $this->getLayout()->getBlock("head")->setTitle($this->__("Bloomp Commerce - Redirect"));
	  //       $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
   //    $breadcrumbs->addCrumb("home", array(
   //              "label" => $this->__("Home Page"),
   //              "title" => $this->__("Home Page"),
   //              "link"  => Mage::getBaseUrl()
		 //   ));

   //    $breadcrumbs->addCrumb("bloomp commerce - redirect", array(
   //              "label" => $this->__("Bloomp Commerce - Redirect"),
   //              "title" => $this->__("Bloomp Commerce - Redirect")
		 //   ));

   //    $this->renderLayout(); 
	  
    //$this->_redirectUrl('http://www.bloompa.com.br');

      Mage::getSingleton('core/session', array('name' => 'adminhtml'));
      $adminsession = Mage::getSingleton('admin/session', array('name' => 'adminhtml'));

      if ($adminsession->isLoggedIn()) :
      //echo "data = {msg:'desconto atualizado', status:true}";
      else:
      //echo "data = {msg:'você não esta logado', status:false}";
      endif;
      $readConnection = Mage::getSingleton('core/resource')->getConnection('core_read');
      $writeConnection = Mage::getSingleton('core/resource')->getConnection('core_write');

      // @TODO: Colocar isto no arquivo de SQL
      if ($_REQUEST['set_token']):
          $strsql = "REPLACE INTO  `{$this->getTable('bloompa_settings')}` (`product`,`param`, `value`) VALUES ('BloompCommerce','token', '" . $_REQUEST['set_token'] . "');";
          $writeConnection->query($strsql);
      endif;
  

    }


    /**
    * Update Token - Request FROM Bloompa's server
    */
    public function UpdateTokenAction() {
      Mage::getSingleton('core/session', array('name' => 'adminhtml'));
      $adminsession = Mage::getSingleton('admin/session', array('name' => 'adminhtml'));      
      $writeConnection = Mage::getSingleton('core/resource')->getConnection('core_write');
      $strsql = "UPDATE bloompa_settings SET value ='" . $_REQUEST['token'] . "' WHERE product = 'BloompCommerce' AND param = 'token'";
      if($writeConnection->query($strsql)){
        $res = array('success'=>'true');
      }else{
        $res = array('error'=>'true');
      }
      exit(json_encode($res));      
    }


    /**
    * Update Token  - Request FROM Bloompa's Server
    */
    public function UpdateDiscountAction(){
        
        // $strsql = "SELECT * FROM  `{$this->getTable('salesrule')}` WHERE `name`='Bloompa-" . $_REQUEST['set_type'] . "'";
        // $bloompa = $readConnection->fetchAll($strsql);

        //   if (count($bloompa) == 0):
        //       $strsql = "INSERT INTO `{$this->getTable('salesrule')}` (`name`, `description`, `from_date`, `to_date`, `uses_per_customer`, `customer_group_ids`, `is_active`, `conditions_serialized`, `actions_serialized`, `stop_rules_processing`, `is_advanced`, `product_ids`, `sort_order`, `simple_action`, `discount_amount`, `discount_qty`, `discount_step`, `simple_free_shipping`, `apply_to_shipping`, `times_used`, `is_rss`, `website_ids`, `coupon_type`) VALUES ('Bloompa-" . $_REQUEST['set_type'] . "', 'Cupom de desconto fornecido pelo compartilhamento do site nas redes sociais.', '" . date('Y-m-d') . "', NULL, 0, '0,1,2,3,4', 1, 'a:6:{s:4:\"type\";s:32:\"salesrule/rule_condition_combine\";s:9:\"attribute\";N;s:8:\"operator\";N;s:5:\"value\";s:1:\"1\";s:18:\"is_value_processed\";N;s:10:\"aggregator\";s:3:\"all\";}', 'a:6:{s:4:\"type\";s:40:\"salesrule/rule_condition_product_combine\";s:9:\"attribute\";N;s:8:\"operator\";N;s:5:\"value\";s:1:\"1\";s:18:\"is_value_processed\";N;s:10:\"aggregator\";s:3:\"all\";}', 0, 1, '', 0, 'by_percent', '" . $_REQUEST['set_discount'] . "', NULL, 0, 0, 0, 0, 1, '1', 2)";
        //       $writeConnection->query($strsql);

        //       $strsql = "SELECT * FROM `{$this->getTable('salesrule')}` WHERE `name`='Bloompa-" . $_REQUEST['set_type'] . "'";
        //       $bloompa = $readConnection->fetchAll($strsql);

        //       $strsql = "INSERT INTO `{$this->getTable('salesrule_coupon')}` (`rule_id`, `code`, `usage_limit`, `usage_per_customer`, `times_used`, `expiration_date`, `is_primary`) VALUES (" . $bloompa[0]['rule_id'] . ", '" . md5("Bloompa-" . $_REQUEST['set_type']) . "', NULL, NULL, 0, NULL, 1)";
        //       $writeConnection->query($strsql);
        //   else:
        //       $strsql = "UPDATE `{$this->getTable('salesrule')}` SET discount_amount = '" . $_REQUEST['set_discount'] . "' WHERE name = 'Bloompa-" . $_REQUEST['set_type'] . "'";
        //       $writeConnection->query($strsql);
        //   endif;

    }
}