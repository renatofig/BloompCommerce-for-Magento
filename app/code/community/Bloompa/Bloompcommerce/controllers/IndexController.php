<?php
require_once 'Mage/Checkout/controllers/CartController.php';
class Bloompa_Bloompcommerce_IndexController extends Mage_Core_Controller_Front_Action{
    
    public function IndexAction() {
      //$this->_redirectUrl('http://www.bloompa.com.br');
        

      $social_network = ucfirst(trim($this->getRequest()->getParam('social_network')));
      if(is_null($social_network)){
        $this->_redirect('/');
        exit();
      }
        

      $bc_discount_percent_model = Mage::getModel('salesrule/rule')->getCollection()->addFieldToFilter('name', array('eq'=>sprintf('Bloompa-'.$social_network)))->getFirstItem();
      $bc_discount_percent = $bc_discount_percent_model->getDiscountAmount();
      
      if(!is_null($bc_discount_percent)){
        
        $new_code = false;
        session_start();    
        if(isset($_SESSION['bc_customer_id']))
          $customer_id = $_SESSION['bc_customer_id'];
        else{
          $customer_id = Mage::getSingleton('customer/session')->getCustomerId();
          if(is_null($customer_id) OR empty($customer_id)){                    
            $customer_id = 'generic_'.strtoupper(Mage::helper('core')->getRandomString(5));            
            $_SESSION['bc_customer_id'] = $customer_id;
            $new_code = true;
          }

        }
                

        if($new_code==true){
          // data to create coupon
          $data = array(
              'product_ids' => null,
              'name' => sprintf('AUTO_GENERATION CUSTOMER_%s - '.intval($bc_discount_percent).'%% BloompCommerce-'.$social_network.' discount', $customer_id),
              'description' => 'Cupom de desconto fornecido pelo compartilhamento do site nas redes sociais (BloompCommerce).',
              'is_active' => 1,
              'website_ids' => array(1),
              'customer_group_ids' => array(1,2,3,4),
              'coupon_type' => 2,
              'coupon_code' => strtoupper('BC'.Mage::helper('core')->getRandomString(5)),
              'uses_per_coupon' => 1,
              'uses_per_customer' => 1,
              'from_date' => null,
              'to_date' => null,
              'sort_order' => null,
              'is_rss' => 1,
              'rule' => array(
                  'conditions' => array(
                      array(
                          'type' => 'salesrule/rule_condition_combine',
                          'aggregator' => 'all',
                          'value' => 1,
                          'new_child' => null
                      )
                  )
              ),
              'simple_action' => 'by_percent',
              'discount_amount' => $bc_discount_percent,
              'discount_qty' => 0,
              'discount_step' => null,
              'apply_to_shipping' => 0,
              'simple_free_shipping' => 0,
              'stop_rules_processing' => 0,
              'rule' => array(
                  'actions' => array(
                      array(
                          'type' => 'salesrule/rule_condition_product_combine',
                          'aggregator' => 'all',
                          'value' => 1,
                          'new_child' => null
                      )
                  )
              ),
              'store_labels' => array($bc_discount_percent.'% discount')
          );


            // create coupon        
            $model = Mage::getModel('salesrule/rule');
            $data = $this->_filterDates($data, array('from_date', 'to_date'));
            $validateResult = $model->validateData(new Varien_Object($data));
            if ($validateResult == true) {
                if (isset($data['simple_action']) && $data['simple_action'] == 'by_percent'
                        && isset($data['discount_amount'])) {
                    $data['discount_amount'] = min(100, $data['discount_amount']);
                }
                if (isset($data['rule']['conditions'])) {
                    $data['conditions'] = $data['rule']['conditions'];
                }
                if (isset($data['rule']['actions'])) {
                    $data['actions'] = $data['rule']['actions'];
                }
                unset($data['rule']);
                $model->loadPost($data);
                $model->save();
            }
        }
        
        Mage::getSingleton('checkout/cart')->getQuote()->getShippingAddress()->setCollectShippingRates(true);                  
        
        // find coupon            
        $model = Mage::getModel('salesrule/rule')
        ->getCollection()
        ->addFieldToFilter('name', array('eq'=>sprintf('AUTO_GENERATION CUSTOMER_'.$customer_id.' - '.intval($bc_discount_percent).'%% BloompCommerce-'.$social_network.' discount')))
        ->getFirstItem();
        $couponCode = $model->getCode();


        if(!is_null($couponCode) AND $couponCode!=''){
          
          //apply coupon
          Mage::getSingleton("checkout/session")->setData("coupon_code",$couponCode);
          Mage::getSingleton('checkout/cart')->getQuote()->setCouponCode($couponCode)->collectTotals()->save();
          Mage::getSingleton('core/session')->addSuccess($this->__('Coupon BloompCommerce applied.'));          
          
          $this->_redirect('checkout/cart');

          
        }else{
          exit('Coupon discount not found.');
        }
        
      
      }else{
        $this->_redirect('/');        
      }

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

            $strsql = "INSERT INTO salesrule (`rule_id`, `code`, `usage_limit`, `usage_per_customer`, `times_used`, `expiration_date`, `is_primary`) VALUES (".$bloompa[0]['rule_id'] . ", '" . md5("Bloompa-".$social_network)."', NULL, NULL, 0, NULL, 1)";

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