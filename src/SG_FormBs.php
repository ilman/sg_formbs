<?php 

namespace Scienceguard;

use Scienceguard\SG_Util;
use Scienceguard\SG_Form;

class SG_FormBs{
	
	static function field($field_type, $field_name, $value=null, $field_attr=array(), $default='', $field_options=array()){
		$output = '';
		
		//prepare field_id
		$field_id = SG_Util::val($field_attr,'id');
		$field_id = ($field_id) ? $field_id : SG_Util::slug($field_name);
		
		//prepare event_attr
		$event_attr = SG_Util::eventAttr($field_attr);
		
		//prepare select2 class
		if($field_type=='select2'){
			$field_attr['class'] = trim(SG_Util::val($field_attr,'class').' select-select2');	
		}
		
		//prepare select_font class
		if($field_type=='select_font'){
			$field_attr['class'] = trim(SG_Util::val($field_attr,'class').' select-font');	
		}
				
		//prepare field_attr
		$prefix = SG_Util::val($field_attr,'prefix');
		if(is_array($field_attr)){
			$field_attr['name'] = $field_name;
			if(!isset($field_attr['no_id'])){ 
				$field_attr['id'] = $field_id;
			}
			else{
				$field_attr['data-id'] = $field_id;
			}
		}

		//prepare field_class
		$field_class = SG_Util::val($field_attr,'class');

		if(is_array($value) || is_object($value)){ 
			$value = SG_Util::val($value, $field_name);
		}
		
		if($value===null){
			$value = $default;	
		}
		
		switch($field_type) {
			
			case 'select2':  
				$param_attr = SG_Form::inlineAttr($field_attr);
				$param_attr .= ($event_attr) ? ' '.$event_attr : '';
				$output .= '<select '.trim($param_attr).'>';  
				foreach ($field_options as $option) { 
					$option_value = SG_Util::val($option, 'value');
					$option_label = SG_Util::val($option, 'label');
					
					$option_value = ($option_value!==null) ? $option_value : $option_label;
					$output .= '<option value="'.$option_value.'" '.SG_Form::checkedInput($value, $option_value, 'selected').'>'.$option_label.'</option>';  
				}  
				$output .= '</select> ';  
			break; 
			
			case 'select_font':  
				$field_attr['value'] = $value;
				$param_attr = SG_Form::inlineAttr($field_attr);
				$param_attr .= ($event_attr) ? ' '.$event_attr : '';
				$output .= '<input type="hidden" '.trim($param_attr).'>';
			break;
			
			case 'select_images':  
				foreach ($field_options as $option) { 
					$this_id = $field_id.'-'.SG_Util::slug($option['value']);
					$this_checked = SG_Form::checkedInput($value, $option['value']);
					$option['value'] = (isset($option['value'])) ? $option['value'] : $option['label'];
					
					$field_attr['value'] = $option['value'];
					$field_attr = SG_Util::setNull($field_attr,'id');
					$param_attr = SG_Form::inlineAttr($field_attr);
					$param_attr .= ($event_attr) ? ' '.$event_attr : '';
					$param_attr .= ($this_checked) ? ' '.$this_checked : '';

					$output .= '<label class="select-image-item '.SG_Form::checkedClass($value, $option['value']).'" id="'.$this_id.'">';
					$output .= '<input type="radio" '.trim($param_attr).'/>';
					$output .= '<img src="http://placekitten.com/70/60" alt="'.$option['label'].'" />';
					$output .= '</label> ';  
				}  
			break; 
						
			case 'slider':				
				$value = ($value) ? $value : 0; 

				$field_attr['value'] = $value;
				$field_attr['class'] = $field_class.$prefix.' ui-slider-value';
				$field_attr['id'] = $field_id; //exception for slider
				$param_attr = SG_Form::inlineAttr($field_attr);
				$param_attr .= ($event_attr) ? ' '.$event_attr : '';

				$output .= '<input type="text" readonly="readonly" '.trim($param_attr).'/>';
				$output .= '<div id="'.$field_id.'-slider" class="'.$prefix.' ui-slider"></div> ';
			break;
			
			case 'upload':
				$thumb = ($value) ? '<img src="'.$value.'" />' : '';

				$field_attr['value'] = $value;
				$field_attr['class'] = $field_class.$prefix.' media-upload-url';
				$param_attr = SG_Form::inlineAttr($field_attr);
				$param_attr .= ($event_attr) ? ' '.$event_attr : '';

				$output .= '<input type="text" '.trim($param_attr).'/> ';
				$output .= '<button type="button" class="'.$prefix.' button media-upload-button" id="'.$field_id.'-upload">Upload</button>';
				$output .= '<div class="'.$prefix.' media-upload-preview">'.$thumb.'</div> ';
			break;
			
			case 'color':
				$field_attr['value'] = $value;
				$field_attr['class'] = $field_class.$prefix.' color-value';
				$param_attr = SG_Form::inlineAttr($field_attr);
				$param_attr .= ($event_attr) ? ' '.$event_attr : '';

				$output .= '<span class="'.$prefix.' input color-placeholder"><span class="'.$prefix.' color-preview" style="background-color:'.$value.'"></span></span> ';
				$output .= '<input type="text" '.trim($param_attr).'/> ';
				$output .= '<button type="button" class="button '.$prefix.' color-button">Select Color</button> ';
			break;

			default:
				$output .= SG_Form::field($field_type, $field_name, $value, $field_attr, $field_default, $field_options);
			break;
			
		}
		return $output;
	}

}