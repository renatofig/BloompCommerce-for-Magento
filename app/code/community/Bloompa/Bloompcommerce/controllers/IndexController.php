<?php
require_once 'Mage/Checkout/controllers/CartController.php';
class Bloompa_Bloompcommerce_IndexController extends Mage_Core_Controller_Front_Action{
    
    public function IndexAction() {
      //$this->_redirectUrl('http://www.bloompa.com.br');

      @session_start();        

      // $social_network = ucfirst(trim($this->getRequest()->getParam('social_network')));      
      // if(is_null($social_network)){
      //   // $this->_redirect('/');
      //   // exit();
      // }
      // //else
      // //   $_SESSION['bc_social_network'] = $social_network;      

      $social_network = 'Facebook';
      $_SESSION['bc_social_network'] = $social_network;

      $readConnection = Mage::getSingleton('core/resource')->getConnection('core_read');
      $bc_discount_percent = $readConnection->fetchOne("SELECT value FROM `bloompa_settings` WHERE `param`='discount_value_".strtolower($social_network)."' AND product='BloompCommerce' LIMIT 1");             
      if(!is_null($bc_discount_percent)){
        
        $new_code = false;                
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

          $stores_ids = Mage::getModel('core/store')->getCollection()->getAllIds();
          $websites_ids = Mage::getModel('core/website')->getCollection()->getAllIds();

          // data to create coupon
          $data = array(
              'product_ids' => null,
              'name' => sprintf('AUTO_GENERATION CUSTOMER_%s - '.intval($bc_discount_percent).'%% BloompCommerce-'.$social_network.' discount', $customer_id),
              'description' => 'Cupom de desconto fornecido pelo compartilhamento do site nas redes sociais (BloompCommerce).',
              'is_active' => 1,
              'website_ids' => $websites_ids,
              'customer_group_ids' => array(0,1,2,3,4),
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
              'store_labels' => array(intval($bc_discount_percent).'% off - Compartilhamento')
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
          $res1 = Mage::getSingleton("checkout/session")->setData("coupon_code",$couponCode);
          $res2 = Mage::getSingleton('checkout/cart')->getQuote()->setCouponCode($couponCode)->collectTotals()->save();
          
          if($res2 AND $res1){
            Mage::getSingleton('core/session')->addSuccess($this->__('Coupon BloompCommerce applied.'));                    
          }else{
            Mage::getSingleton('core/session')->addError($this->__('Coupon BloompCommerce NOT applied.'));
          }

        }else{
          Mage::getSingleton('core/session')->addError($this->__('Coupon BloompCommerce NOT FOUND.'));
        }
        
        $this->_redirect('checkout/cart');
        
      
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
      
      if($writeConnection->query("UPDATE bloompa_settings SET value ='" .$token. "' WHERE product = 'BloompCommerce' AND param = 'token'"))
        $res = array('status'=>'success');
      else
        $res = array('status'=>'fail');
      
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

      $social_network = strtolower(trim($_POST['social_network']));
      $discount_value = trim($_POST['set_discount']);
      
      $writeConnection = Mage::getSingleton('core/resource')->getConnection('core_write');

      if(!$writeConnection->query("UPDATE bloompa_settings SET value ='".$discount_value."' WHERE product='BloompCommerce' AND param='discount_value_".$social_network."'"))              
        exit(json_encode(array('status'=>'fail')));                   

      exit(json_encode(array('status'=>'success')));
    }


}