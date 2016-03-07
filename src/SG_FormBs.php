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
		

		//prepare slider data
		$slider_max = SG_Util::val($field_attr,'slider_max', 100);
				
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
				$field_attr['class'] = trim(SG_Util::val($field_attr,'class').' input-select2 form-control');
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
			

			case 'select2_tags':  
			case 'select2_add': 
				if($field_type=='select2_tags'){
					//prepare select2 class
					$field_attr['class'] = trim(SG_Util::val($field_attr,'class').' input-select2-tags form-control');	
				}
				elseif($field_type=='select2_add'){
					//prepare select2 class
					$field_attr['class'] = trim(SG_Util::val($field_attr,'class').' input-select2-post-add form-control');	
				}

				if(is_array($value)){
					$field_attr['value'] = '';
					foreach($value as $val){
						$field_attr['value'] .= $val.', ';
					}
					$field_attr['value'] = trim($field_attr['value'], ', ');
				}
				else{
					$field_attr['value'] = $value;
				}

				$param_attr = SG_Form::inlineAttr($field_attr);
				$param_attr .= ($event_attr) ? ' '.$event_attr : '';
				$output .= '<input type="text" '.trim($param_attr).'/> ';  
			break; 

			
			case 'image_radio':  
			case 'image_checkbox':  
				foreach ($field_options as $option) { 
					$this_id = $field_id.'-'.SG_Util::slug($option['value']);
					$this_checked = SG_Form::checkedInput($value, $option['value']);
					$option['value'] = (isset($option['value'])) ? $option['value'] : $option['label'];
					
					$field_attr['value'] = $option['value'];
					$field_attr = SG_Util::setNull($field_attr,'id');
					$param_attr = SG_Form::inlineAttr($field_attr);
					$param_attr .= ($event_attr) ? ' '.$event_attr : '';
					$param_attr .= ($this_checked) ? ' '.$this_checked : '';

					$type = ($field_type=='image_checkbox') ? 'checkbox' : 'radio';

					$output .= '<label class="sgtb-select-image '.SG_Form::checkedClass($value, $option['value']).'" id="'.$this_id.'">';
					$output .= '<input type="'.$type.'" '.trim($param_attr).'/>';
					$output .= '<img src="'.$option['value'].'" alt="'.$option['label'].'" />';
					$output .= '</label> ';  
				}  
			break; 

			
			case 'upload':
				$thumb = ($value) ? '<img src="'.$value.'" />' : '';

				$field_attr['value'] = $value;
				$field_attr['class'] = $field_class.$prefix.' media-upload-url';
				$param_attr = SG_Form::inlineAttr($field_attr);
				$param_attr .= ($event_attr) ? ' '.$event_attr : '';

				$output .= '<div class="sgtb-input-group input-file-upload">';
				$output .= '<input type="text" '.trim($param_attr).'>';
				$output .= '<span class="sgtb-input-group-btn">';
				$output .= '<button class="sgtb-btn sgtb-btn-default file-upload-btn" id="'.$field_attr['id']

.'-upload">Upload</button>';
				$output .= '</span>';
				$output .= '</div>';

				$output .= '<div class="'.$prefix.' sgtb-input-preview media-upload-preview">'.$thumb.'</div> ';
			break;

			
			case 'color':
				$field_attr['value'] = $value;
				$field_attr['class'] = $field_class.$prefix.' color-picker-input';
				$param_attr = SG_Form::inlineAttr($field_attr);
				$param_attr .= ($event_attr) ? ' '.$event_attr : '';

				$output .= '<div class="'.$prefix.' sgtb-input-group input-color-picker">';
				$output .= '<input type="text" '.trim($param_attr).' />';
				$output .= '<span class="sgtb-input-group-btn">';
				$output .= '<input type="hidden" class="color-picker-btn" data-show-alpha="true" value="'.$value.'" />';
				$output .= '</span>';
				$output .= '</div>';

				// $output .= '<span class="'.$prefix.' input color-placeholder"><span class="'.$prefix.' color-preview" style="background-color:'.$value.'"></span></span> ';
				// $output .= '<input type="text" '.trim($param_attr).'/> ';
				// $output .= '<button type="button" class="button '.$prefix.' color-button">Select Color</button> ';
			break;


			case 'font':  
				$field_attr['value'] = $value;
				$field_attr['class'] = $field_class.$prefix.' form-control font-select input-select2';
				$field_attr['data-data'] = 'google_fonts';
				$field_attr['placeholder'] = '- Select Font -';
				$param_attr = SG_Form::inlineAttr($field_attr);
				$param_attr .= ($event_attr) ? ' '.$event_attr : '';

				$output .= '<div class="input-select-font">';
				$output .= '<input type="text" '.trim($param_attr).' />';
				$output .= '<div class="preview font-preview"></div>';
				$output .= '</div>';
			break;


			case 'icon':  
				$field_attr['value'] = $value;
				$field_attr['class'] = $field_class.$prefix.' icon-picker-input';
				$param_attr = SG_Form::inlineAttr($field_attr);
				$param_attr .= ($event_attr) ? ' '.$event_attr : '';

				$output .= '<div class="sgtb-input-group input-icon-picker">';
				$output .= '<input type="text" '.trim($param_attr).' />';
				$output .= '<span class="sgtb-input-group-btn">';
				$output .= '<button class="sgtb-btn sgtb-btn-default icon-picker-btn" data-icon="glyphicon-home"></button>';
				$output .= '</span>';
				$output .= '</div>';
			break;


			case 'slider_min':  
			case 'slider_max': 
				$field_attr['value'] = $value;
				$field_attr['class'] = trim(SG_Util::val($field_attr,'class').' slider-value');	
				$param_attr = SG_Form::inlineAttr($field_attr);
				$param_attr .= ($event_attr) ? ' '.$event_attr : '';

				$output .= '<div class="input-slider">';
				if($field_type=='slider_max'){
					$output .= '<div class="slider-ui" data-range="max" data-max="'.$slider_max.'"></div>';
				}
				else{
					$output .= '<div class="slider-ui"></div>';
				}
				$output .= '<input type="text" '.trim($param_attr).'/>';
				$output .= '</div>';
			break;


			case 'slider_range': 
				$field_attr['value'] = $value;
				$field_attr['class'] = trim(SG_Util::val($field_attr,'class').' slider-value');	
				$param_attr = SG_Form::inlineAttr($field_attr);
				$param_attr .= ($event_attr) ? ' '.$event_attr : '';
				$output .= '<input type="hidden" '.trim($param_attr).'>';

				$value_min = SG_Util::val($value, 'min');
				$value_max = SG_Util::val($value, 'max');

				if(!$value_min){ $value_min = 0; }
				if(!$value_max){ $value_max = 20; }

				$output .= '<div class="input-slider">';
				$output .= '	<div class="slider-ui" data-range="range" data-max="'.$slider_max.'"></div>';
				$output .= '	<div class="sgtb-row">';

				$output .= '		<div class="sgtb-col-sm-6">';
				$output .= '			<input type="text" class="'.$field_attr['class'].' min" name="'.$field_attr['name'].'[min]" value="'.$value_min.'" />';

				$output .= '		</div>';
				$output .= '		<div class="sgtb-col-sm-6">';
				$output .= '			<input type="text" class="'.$field_attr['class'].' max" name="'.$field_attr['class'].'[max]" value="'.$value_max.'" />';
				$output .= '		</div>';

				$output .= '	</div>';
				$output .= '</div>';
			break;

			case 'spinner':  
				$field_attr['value'] = $value;
				$field_attr['class'] = trim(SG_Util::val($field_attr,'class').' input-spinner');	
				$param_attr = SG_Form::inlineAttr($field_attr);
				$param_attr .= ($event_attr) ? ' '.$event_attr : '';

				$output .= '<input type="text" '.trim($param_attr).' />';
			break;

			default:
				$output .= SG_Form::field($field_type, $field_name, $value, $field_attr, $default, $field_options);
			break;
			
		}
		return $output;
	}

}