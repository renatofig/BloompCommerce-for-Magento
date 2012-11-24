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
	  
    $this->_redirectUrl('http://www.bloompa.com.br');

    }


    /**
    * Update Token - Request FROM Bloompa's server
    */
    public function UpdateTokenAction() {
      header('P3P: CP="IDC DSP COR CURa ADMa OUR IND PHY ONL COM STA"');
      header("Access-Control-Allow-Methods: POST,GET,OPTIONS");
      header("Access-Control-Allow-Origin: *");
      header('Access-Control-Allow-Headers: X-Requested-With');
      
      $token = trim($_POST['token']);

      Mage::getSingleton('core/session', array('name' => 'adminhtml'));
      $adminsession = Mage::getSingleton('admin/session', array('name' => 'adminhtml'));      
      $writeConnection = Mage::getSingleton('core/resource')->getConnection('core_write');
      $strsql = "UPDATE bloompa_settings SET value ='" .$token. "' WHERE product = 'BloompCommerce' AND param = 'token'";
      if($writeConnection->query($strsql)){
        $res = array('status'=>'success');
      }else{
        $res = array('status'=>'fail');
      }
      exit(json_encode($res));      
    }


    /**
    * Update Discount  - Request FROM Bloompa's Server
    */
    public function UpdateDiscountAction(){

      header('P3P: CP="IDC DSP COR CURa ADMa OUR IND PHY ONL COM STA"');
      header("Access-Control-Allow-Methods: POST,GET,OPTIONS");
      header("Access-Control-Allow-Origin: *");
      header('Access-Control-Allow-Headers: X-Requested-With');

      $social_network = trim($_POST['social_network']);
      $discount_value = trim($_REQUEST['set_discount']);

      $strsql = "SELECT * FROM salesrule WHERE `name`='Bloompa-".$social_network. "'";
      $bloompa = $readConnection->fetchAll($strsql);

        if (count($bloompa) == 0):
            
            $strsql = "INSERT INTO salesrule(`name`, `description`, `from_date`, `to_date`, `uses_per_customer`, `customer_group_ids`, `is_active`, `conditions_serialized`, `actions_serialized`, `stop_rules_processing`, `is_advanced`, `product_ids`, `sort_order`, `simple_action`, `discount_amount`, `discount_qty`, `discount_step`, `simple_free_shipping`, `apply_to_shipping`, `times_used`, `is_rss`, `website_ids`, `coupon_type`) VALUES ('Bloompa-".$social_network."', 'Cupom de desconto fornecido pelo compartilhamento do site nas redes sociais.', '" . date('Y-m-d') . "', NULL, 0, '0,1,2,3,4', 1, 'a:6:{s:4:\"type\";s:32:\"salesrule/rule_condition_combine\";s:9:\"attribute\";N;s:8:\"operator\";N;s:5:\"value\";s:1:\"1\";s:18:\"is_value_processed\";N;s:10:\"aggregator\";s:3:\"all\";}', 'a:6:{s:4:\"type\";s:40:\"salesrule/rule_condition_product_combine\";s:9:\"attribute\";N;s:8:\"operator\";N;s:5:\"value\";s:1:\"1\";s:18:\"is_value_processed\";N;s:10:\"aggregator\";s:3:\"all\";}', 0, 1, '', 0, 'by_percent', '" .$discount_value. "', NULL, 0, 0, 0, 0, 1, '1', 2)";
            if(!$writeConnection->query($strsql))
              exit(json_encode(array('status'=>'fail', 'msg'=> 'Error to execute :'.$strsql)));              
            

            $strsql = "SELECT * FROM salesrule WHERE `name`='Bloompa-" .$social_network. "'";
            $bloompa = $readConnection->fetchAll($strsql);

            $strsql = "INSERT INTO salesrule (`rule_id`, `code`, `usage_limit`, `usage_per_customer`, `times_used`, `expiration_date`, `is_primary`) VALUES (" . $bloompa[0]['rule_id'] . ", '" . md5("Bloompa-" .$social_network. "', NULL, NULL, 0, NULL, 1)";
            if(!$writeConnection->query($strsql))              
              exit(json_encode(array('status'=>'fail', 'msg'=> 'Error to execute :'.$strsql)));              
            

            exit(json_encode(array('status'=>'success')));

        else:
            $strsql = "UPDATE salesrule SET discount_amount = '" .$discount_value. "' WHERE name = 'Bloompa-" .$social_network. "'";
            if($writeConnection->query($strsql)){
              $res = array('status'=>'success');
            }else{
              $res = array('status'=>'fail', 'msg'=> 'Error to execute :'.$strsql);                           
            }

            exit(json_encode($res)); 

        endif;

    }
}