<?php
class Bloompa_Bloompcommerce_Block_Bc_Social_Expressions
    extends Mage_Core_Block_Abstract
    implements Mage_Widget_Block_Interface
{

    protected function _toHtml()
    {
		$html ='';
        $html .= 'bc_social_expressions parameter1 = '.$this->getData('parameter1').'<br/>';
        $html .= 'bc_social_expressions parameter2 = '.$this->getData('parameter2').'<br/>';
        $html .= 'bc_social_expressions parameter3 = '.$this->getData('parameter3').'<br/>';
        $html .= 'bc_social_expressions parameter4 = '.$this->getData('parameter4').'<br/>';
        $html .= 'bc_social_expressions parameter5 = '.$this->getData('parameter5').'<br/>';
        return $html;
    }
	
}