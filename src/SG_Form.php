<?php 

namespace Scienceguard;

use Scienceguard\SG_Util;

class SG_Form{

	static function open($action){
		return '<form action="'.url($action).'" method="post">';
	}
	
	static function close(){
		return '</form>';
	}
	
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
			
			case 'label':
				$output .= '<label '.$field_attr.'>'.$value.'</label>';
			break;
			
			case 'text':
				$field_attr['value'] = $value;
				$param_attr = self::inlineAttr($field_attr);
				$param_attr .= ($event_attr) ? ' '.$event_attr : '';
				$output .= '<input type="text" '.trim($param_attr).'/> ';
			break;

			case 'hidden':
				$field_attr['value'] = $value;
				$param_attr = self::inlineAttr($field_attr);
				$param_attr .= ($event_attr) ? ' '.$event_attr : '';
				$output .= '<input type="hidden" '.trim($param_attr).'/> ';
			break;
			
			case 'textarea':
				$param_attr = self::inlineAttr($field_attr);
				$param_attr .= ($event_attr) ? ' '.$event_attr : '';
				$output .= '<textarea '.trim($param_attr).'>'.$value.'</textarea>';
			break;
			
			case 'select':  
				$param_attr = self::inlineAttr($field_attr);
				$param_attr .= ($event_attr) ? ' '.$event_attr : '';
				$output .= '<select '.trim($param_attr).'>';  
				foreach ($field_options as $option) { 
					$option_value = SG_Util::val($option, 'value');
					$option_label = SG_Util::val($option, 'label');

					$option_value = ($option_value!==null) ? $option_value : $option_label;
					$output .= '<option value="'.$option_value.'" '.self::checkedInput($value, $option_value, 'selected').'>'.$option_label.'</option>';  
				}  
				$output .= '</select> ';  
			break; 
			
			case 'select2':  
				$param_attr = self::inlineAttr($field_attr);
				$param_attr .= ($event_attr) ? ' '.$event_attr : '';
				$output .= '<select '.trim($param_attr).'>';  
				foreach ($field_options as $option) { 
					$option_value = SG_Util::val($option, 'value');
					$option_label = SG_Util::val($option, 'label');
					
					$option_value = ($option_value!==null) ? $option_value : $option_label;
					$output .= '<option value="'.$option_value.'" '.self::checkedInput($value, $option_value, 'selected').'>'.$option_label.'</option>';  
				}  
				$output .= '</select> ';  
			break; 
			
			case 'select_font':  
				$field_attr['value'] = $value;
				$param_attr = self::inlineAttr($field_attr);
				$param_attr .= ($event_attr) ? ' '.$event_attr : '';
				$output .= '<input type="hidden" '.trim($param_attr).'>';
			break;
			
			case 'select_images':  
				foreach ($field_options as $option) { 
					$this_id = $field_id.'-'.SG_Util::slug($option['value']);
					$this_checked = self::checkedInput($value, $option['value']);
					$option['value'] = (isset($option['value'])) ? $option['value'] : $option['label'];
					
					$field_attr['value'] = $option['value'];
					$field_attr = SG_Util::setNull($field_attr,'id');
					$param_attr = self::inlineAttr($field_attr);
					$param_attr .= ($event_attr) ? ' '.$event_attr : '';
					$param_attr .= ($this_checked) ? ' '.$this_checked : '';

					$output .= '<label class="select-image-item '.self::checkedClass($value, $option['value']).'" id="'.$this_id.'">';
					$output .= '<input type="radio" '.trim($param_attr).'/>';
					$output .= '<img src="http://placekitten.com/70/60" alt="'.$option['label'].'" />';
					$output .= '</label> ';  
				}  
			break; 
			
			case 'radio':  				
				foreach ($field_options as $option) { 
					$this_id = $field_id.'-'.SG_Util::slug($option['value']);
					$this_checked = self::checkedInput($value, $option['value']);
					$this_class = SG_Util::val($field_attr,'class');
					$option['value'] = (isset($option['value'])) ? $option['value'] : $option['label'];
					
					$field_attr['value'] = $option['value'];
					$param_attr = $field_attr;
					$param_attr = SG_Util::setNull($param_attr,'id');
					$param_attr = SG_Util::setNull($param_attr,'class');
					$param_attr = self::inlineAttr($field_attr);
					$param_attr .= ($event_attr) ? ' '.$event_attr : '';
					$param_attr .= ($this_checked) ? ' '.$this_checked : '';
					
					$output .= '<label class="'.$this_class.'">';
					$output .= '<input type="radio" '.trim($param_attr).'/> ';
					$output .= $option['label'];
					$output .= '</label> ';  
				}  
			break;
			
			case 'checkbox':
				if(!is_array($field_options)){
					$field_options = array(
						array('label'=>'true', 'value'=>'true')
					);
				}
				foreach ($field_options as $option) { 
					$this_id = $field_id.'-'.SG_Util::slug($option['value']);
					$this_checked = self::checkedInput($value, $option['value']);
					$this_class = SG_Util::val($field_attr,'class');
					$option['value'] = (isset($option['value'])) ? $option['value'] : $option['label'];
					
					$field_attr['value'] = $option['value'];
					$field_attr = SG_Util::setNull($field_attr,'id');
					$field_attr = SG_Util::setNull($field_attr,'class');
					$param_attr = self::inlineAttr($field_attr);
					$param_attr .= ($event_attr) ? ' '.$event_attr : '';
					$param_attr .= ($this_checked) ? ' '.$this_checked : '';
					
					$output .= '<label class="'.$this_class.'">';
					$output .= '<input type="checkbox" '.trim($param_attr).'/> ';
					$output .= $option['label'];
					$output .= '</label> ';  
				}  
			break;
			
			case 'multicheckbox':
				foreach ($field_options as $option) { 
					$this_id = $field_id.'-'.SG_Util::slug($option['value']);
					$this_class = SG_Util::val($field_attr,'class');
					
					$option['value'] = (isset($option['value'])) ? $option['value'] : $option['label'];
					
					$output .= '<div class="'.$this_class.'"><label><input type="checkbox" name="'.$field_name.'[]" id="'.$this_id.'" value="'.$option['value'].'" '.self::checkedInput($value, $option['value']).' /> '.$option['label'].'</label></div>';  
				}  
			break;
						
			case 'slider':				
				$value = ($value) ? $value : 0; 

				$field_attr['value'] = $value;
				$field_attr['class'] = $field_class.$prefix.' ui-slider-value';
				$field_attr['id'] = $field_id; //exception for slider
				$param_attr = self::inlineAttr($field_attr);
				$param_attr .= ($event_attr) ? ' '.$event_attr : '';

				$output .= '<input type="text" readonly="readonly" '.trim($param_attr).'/>';
				$output .= '<div id="'.$field_id.'-slider" class="'.$prefix.' ui-slider"></div> ';
			break;
			
			case 'upload':
				$thumb = ($value) ? '<img src="'.$value.'" />' : '';

				$field_attr['value'] = $value;
				$field_attr['class'] = $field_class.$prefix.' media-upload-url';
				$param_attr = self::inlineAttr($field_attr);
				$param_attr .= ($event_attr) ? ' '.$event_attr : '';

				$output .= '<input type="text" '.trim($param_attr).'/> ';
				$output .= '<button type="button" class="'.$prefix.' button media-upload-button" id="'.$field_id.'-upload">Upload</button>';
				$output .= '<div class="'.$prefix.' media-upload-preview">'.$thumb.'</div> ';
			break;
			
			case 'color':
				$field_attr['value'] = $value;
				$field_attr['class'] = $field_class.$prefix.' color-value';
				$param_attr = self::inlineAttr($field_attr);
				$param_attr .= ($event_attr) ? ' '.$event_attr : '';

				$output .= '<span class="'.$prefix.' input color-placeholder"><span class="'.$prefix.' color-preview" style="background-color:'.$value.'"></span></span> ';
				$output .= '<input type="text" '.trim($param_attr).'/> ';
				$output .= '<button type="button" class="button '.$prefix.' color-button">Select Color</button> ';
			break;
			
		}
		return $output;
	}

	static function inlineAttr($attrs){
		if(is_array($attrs) || is_object($attrs)){
			return SG_Util::inlineAttr($attrs);
		}
		else{
			return $attrs;
		}
	}
	
	static function checkedInput($value, $option='', $text='checked'){
		
		if(self::checkedClass($value, $option)){
			return $text.'="'.$text.'"';	
		}
			
	} 
	
	static function checkedClass($value, $option=false, $text='selected'){
		
		$return = $text;
			
		if(is_array($value)){
			if(in_array($option, $value)){ return $return; }
		}
		
		if($option!==false){
			if($value===0){ $value='0'; }
			if($option===0){ $option='0'; }

			if($value==$option){ return $return; }
		}
	} 

	static function generate($fields, $values=array()){
		$output = '';
		foreach($fields as $fld){
			$row = array();
			foreach($fld as $key=>$val){
				$row[strtolower($key)] = $val;
			}

			$row = (object) $row;

			$column_type_parse = explode('(', str_replace(')', '', $row->type));
			$column_type = trim($column_type_parse[0]);
			
			$field_label   = ucwords(str_replace('_', ' ', $row->field));
			$field_name    = $row->field;
			$field_key     = $row->key;
			$field_default = $row->default;
			$field_options = array();
			$field_attr    = array('class'=>'form-control');

			if($field_key){
				$field_attr['disabled'] = 'disabled';
			}

			if($column_type=='enum'){
				$field_type = 'select';
				$options = str_replace("'", '', $column_type_parse[1]);
				$field_options = explode(',', $options);
			}
			elseif($column_type=='text'){
				$field_type = 'textarea';
			}
			else{
				$field_type = 'text';
			}

			$output .= '<div class="form-group">
				<label>'.$field_label.'</label>
				'.self::field($field_type, $field_name, $values, $field_attr, $field_default, $field_options).'
			</div>';
		}

		return $output;
	}
}