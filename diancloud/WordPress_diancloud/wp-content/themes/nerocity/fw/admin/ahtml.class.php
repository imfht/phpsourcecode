<?php

class ahtml {
    /*
     * 
        DEFINE DEFAULT VALUE FOR FIELDS
     
        sett::$deff[ 'mytheme_social' ] = array();
        $deff = & sett::$deff[ 'page-slug' ];
        $def[ 'field-name' ] = 'defaultValue';


        ALL POSSIBLE SETTINGS FOR FIELD

        $sett = array(
            'pageSlug'      => 'general',
            'fieldName'     => 'twitter'
            'label'         => 'label',
            'type'          => array(
                'template'  => 'templateType',
                'input'     => 'inputType'
                'validator' => 'int'
            ),
            'btnType'       => 'primary|secondary',
            'hint'          => 'hint',
            'inputID'       => 'inputID',                         
            'templateID'    => 'templateID',
            'inputClass'    => 'inputClass',
            'templateClass' => 'templateClass',
            'action'        => 'javascriptAction',
            'value'         => 'inputValue',
            'values'        => 'selectValues',
            'defaultValue'  => 'defaultValue',  // will be use with meta
            'title'         => 'H3 Title',
            'description'   => 'paragraf',
            'content'       => 'HTML Code',
            'query'         => array( 'WP_Query' ),
        );
     */
    
    static $sett;
    static $content;
    
    /* CHECK ATTRIBUTES ( $sett ) */
    static function getInputType( $sett )
    {
        return isset( $sett[ 'type' ][ 'input' ] ) && method_exists( new ahtml() , $sett[ 'type' ][ 'input' ] ) ? $sett[ 'type' ][ 'input' ] : exit;
    }
    
    static function getInputTypeClass( $sett )
    {
        return 'my-field-' . self::getInputType( $sett );
    }
    
    static function getInputID( $sett , $attr = false )
    {
        /* SET INPUT NAME */
        $inputName = tools::getInputName( $sett );
        
        if( !empty( $inputName ) ){
            /* SET INPUT ID */
            $inputID = isset( $sett[ 'inputID' ] ) && !empty( $sett[ 'inputID' ] ) ? $sett[ 'inputID' ] : 'my-field-' . $inputName;
            if( !$attr ){
                return $inputID;
            }
            else{
                return !empty( $inputID ) ? 'id="' . $inputID . '"' : 'id="my-field-' . $inputName . '"';
            }
        }else{
            /* SET INPUT ID */
            $inputID = isset( $sett[ 'inputID' ] ) && !empty( $sett[ 'inputID' ] ) ? $sett[ 'inputID' ] : '';
            if( !$attr ){
                return $inputID;
            }
            else{
                return !empty( $inputID ) ? 'id="' . $inputID . '"' : '';
            }
        }
    }
    
    static function getInputClass( $sett , $attr = false , $additionalClass = '' )
    {
        /* SET INPUT CLASS */
        $inputClass = isset( $sett[ 'inputClass' ] ) && !empty( $sett[ 'inputClass' ] ) ? $sett[ 'inputClass' ] : $additionalClass;
        
        if( !$attr ){
            if( !empty( $additionalClass ) ){
                return $inputClass . ' ' . $additionalClass  ;
            }
            else{
                return $inputClass;
            }
        }
        else{
            return !empty( $inputClass ) ? 'class="my-field ' . $inputClass . ' ' . self::getInputTypeClass( $sett ). ' ' . $additionalClass . '"' : ' class="my-field ' . self::getInputTypeClass( $sett ). ' ' . $additionalClass . '"';
        }
    }
    
    static function getInputDisabled( $sett ){
        return isset( $sett[ 'disabled' ] ) && $sett[ 'disabled' ] ? 'disabled="disabled"' : '';
    }
    
    static function getButtonClass( $sett , $attr = false , $additionalClass = '' )
    {
        /* SET BUTTON CLASS */
        $buttonClass = isset( $sett[ 'btnType' ] ) ? 'button-' . $sett[ 'btnType' ] : 'button-primary';
        
        /* ADD ADDITIONAL CLASS */
        if( !empty( $additionalClass ) ){
            $result = $buttonClass . ' ' . $additionalClass;
        }
        else{
            $result = $buttonClass;
        }
            
        if( !$attr ){
            return $result;
        }
        else{
            return 'class="' . $result . '"';
        }
    }
    
    static function getInputLabel( $sett )
    {
        /* SET INPUT ID */
        $inputID = self::getInputID( $sett );
        
        /* SET INPUT LABEL */
        $label = isset( $sett[ 'label' ] ) && !empty( $sett[ 'label' ] ) ? $sett[ 'label' ] : '';
        
        /* SET LABEL ATTRIBUTE ID */
        $labelID = !empty( $inputID ) ? 'for="' . $inputID . '"' : '';
        
        if( !empty( $label ) ){
            return '<label ' . $labelID . '>' . $label . '</label>';
        }
    }
    
    static function getTemplateID( $sett , $attr = false )
    {
        /* SET INPUT NAME */
        $inputName = tools::getInputName( $sett );
        
        if( !empty( $inputName ) ){
            /* SET TEMPLATE ID */
            $templateID = isset( $sett[ 'templateID' ] ) && !empty( $sett[ 'templateID' ] ) ? $sett[ 'templateID' ] : 'my-template-' . $inputName;
            if( !$attr ){
                return $templateID;
            }
            else{
                return !empty( $templateID ) ? 'id="' . $templateID . '"' : 'id="my-template-' . $inputName . '"';
            }
        }else{
            /* SET TEMPLATE ID */
            $templateID = isset( $sett[ 'templateID' ] ) && !empty( $sett[ 'templateID' ] ) ? $sett[ 'templateID' ] : '';
            if( !$attr ){
                return $templateID;
            }
            else{
                return !empty( $templateID ) ? 'id="' . $templateID . '"' : '';
            }
        }
    }
    
    static function getTemplateClass( $sett , $additionalClass , $attr = false )
    {
        /* SET TEMPLATE CLASS */
        $templateClass = isset( $sett[ 'templateClass' ] ) && !empty( $sett[ 'templateClass' ] ) ? $sett[ 'templateClass' ] : '';

        if( isset( $sett[ 'disabled' ] ) && $sett[ 'disabled' ] ){
            $templateClass .= ' template-disabled';
        }
        
        if( !$attr ){
            return $templateClass;
        }
        else{
            if( strlen( $templateClass . $additionalClass ) ){
                return !empty( $templateClass ) ? 'class="' . $templateClass . ' ' . $additionalClass . '"' : 'class="' . $additionalClass . '"';
            }
        }
    }

    static function getSelectValues( $sett )
    {
        $result = '';

        if( !isset( $sett[ 'value' ] ) ){
            if( isset( $sett[ 'defaultValue' ] ) ){
                if( isset( $sett[ 'values' ] ) && !empty( $sett[ 'values' ] ) && is_array( $sett[ 'values' ] ) ){
                    foreach( $sett[ 'values' ] as $value => $label ){
                        $result .= '<option value="' . esc_attr( $value ) . '" ' . selected( $sett[ 'defaultValue' ] , esc_attr( $value ) , false ) . '>' . $label . '</option>';
                    }
                }

                return $result;
            }
            else{
                if( isset( $sett[ 'values' ] ) && !empty( $sett[ 'values' ] ) && is_array( $sett[ 'values' ] ) ){
                    foreach( $sett[ 'values' ] as $value => $label ){
                        $result .= '<option value="' . esc_attr( $value ) . '">' . $label . '</option>';
                    }
                }

                return $result;
            }
        }
        else{
            if( isset( $sett[ 'values' ] ) && !empty( $sett[ 'values' ] ) && is_array( $sett[ 'values' ] ) ){
                foreach( $sett[ 'values' ] as $value => $label ){
                    $result .= '<option value="' . esc_attr( $value ) . '" ' . selected( $sett[ 'value' ] , esc_attr( $value ) , false ) . '>' . $label . '</option>';
                }
            }

            return $result;
        }
    }
    
    static function getImageSelectValue( $sett )
    {
        if( isset( $sett[ 'coll' ] ) )
            $coll = $sett[ 'coll' ];
        else
            $coll = 1;
        
        if( isset( $sett[ 'position' ] ) )
            $options = '<div class="image-select-options ' . $sett[ 'position' ] . ' coll_' . $coll . '">';
        else
            $options = '<div class="image-select-options left coll_' . $coll . '">';
        
        if( isset( $sett[ 'bkg' ] ) ){
            $bkg_color = ' background-color: ' . $sett[ 'bkg' ] . '; ';
        }else{
            $bkg_color = '';
        }
        
        if( !isset( $sett[ 'size' ] ) ){
            $sett[ 'size' ] = 45;
        }
        
        $diff = (int)((45 - $sett[ 'size' ] ) / 2 );
        $margin = ' margin:' . $diff . 'px; ';
        $size = 'width="' . $sett[ 'size' ] . '" height="' . $sett[ 'size' ] . '"';
        
        $style = 'style="' . $margin . $bkg_color .'" ' . $size;
        
        $img = '<img ' . $style . ' src="" class="preview"/>';
        $bkg = '';

        if( !isset( $sett[ 'value' ] ) ){
            if( isset( $sett[ 'defaultValue' ] ) ){
                if( isset( $sett[ 'values' ] ) && !empty( $sett[ 'values' ] ) && is_array( $sett[ 'values' ] ) ){
                    foreach( $sett[ 'values' ] as $value => $label ){
                        $options .= '<span ref="' . $value . '"><img ' .$style . ' src="' . esc_url( $label ) . '"></span>';
                    }
                }
            }
            else{
                if( isset( $sett[ 'values' ] ) && !empty( $sett[ 'values' ] ) && is_array( $sett[ 'values' ] ) ){
                    
                    foreach( $sett[ 'values' ] as $value => $label ){
                        $options .= '<span ref="' . $value . '"><img ' .$style . ' src="' . esc_url( $label ) . '"></span>';
                    }
                }
            }
        }
        else{
            if( isset( $sett[ 'values' ] ) && !empty( $sett[ 'values' ] ) && is_array( $sett[ 'values' ] ) ){
                foreach( $sett[ 'values' ] as $value => $label ){
                    
                    if( $value == $sett[ 'value' ] ){
                        $options .= '<span ref="' . $value . '" class="current"><img ' . $style . ' src="' . esc_url( $label ) . '"></span>';
                        $img = '<img ' . $style . ' src="' . esc_url( $label ) .  '" class="preview"/>';
                    }else{
                        $options .= '<span ref="' . $value . '"><img ' . $style . ' src="' . esc_url( $label ) . '"></span>';
                    }
                }
            }
        }
        
        $options .= '</div>';
        
        $result  = '<span class="preview-value">';
        $result .= $img; 
        $result .= '</span>';
        $result .= $options;
        
        return $result;
    }

    static function getIconSelectValue( $sett )
    {
        if( isset( $sett[ 'coll' ] ) )
            $coll = $sett[ 'coll' ];
        else
            $coll = 1;


        $options  = '<div class="icon-select-wrapper">';
        $options .= '<div class="search-panel"><input type="text" class="search"> <span>' . __( 'Type Icon Name eg:' , 'myThemes' ) . ' <strong>paper-plane</strong></span></div>';

        if( isset( $sett[ 'position' ] ) )
            $options .= '<div class="icon-select-options ' . $sett[ 'position' ] . ' coll_' . $coll . '">';
        else
            $options .= '<div class="icon-select-options left coll_' . $coll . '">';
        
        if( isset( $sett[ 'bkg' ] ) ){
            $bkg_color = ' background-color: ' . $sett[ 'bkg' ] . '; ';
        }else{
            $bkg_color = '';
        }
        
        if( !isset( $sett[ 'size' ] ) ){
            $sett[ 'size' ] = 45;
        }
        
        $diff = (int)((45 - $sett[ 'size' ] ) / 2 );
        $margin = ' margin:' . $diff . 'px; ';
        $size = 'width="' . $sett[ 'size' ] . '" height="' . $sett[ 'size' ] . '"';
        
        $style = $size;
        
        $img = '<i ' . $style . '></i>';
        $bkg = '';

        if( !isset( $sett[ 'value' ] ) ){
            if( isset( $sett[ 'defaultValue' ] ) ){
                if( isset( $sett[ 'values' ] ) && !empty( $sett[ 'values' ] ) && is_array( $sett[ 'values' ] ) ){
                    foreach( $sett[ 'values' ] as $value ){
                        $options .= '<span ref="' . $value . '" class="no-preview"><i ' . $style . ' class="' . $value . '"></i></span>';
                    }
                }
            }
            else{
                if( isset( $sett[ 'values' ] ) && !empty( $sett[ 'values' ] ) && is_array( $sett[ 'values' ] ) ){
                    
                    foreach( $sett[ 'values' ] as $value ){
                        $options .= '<span ref="' . $value . '" class="no-preview"><i ' .$style . ' class="' . $value . '"></i></span>';
                    }
                }
            }
        }
        else{
            if( isset( $sett[ 'values' ] ) && !empty( $sett[ 'values' ] ) && is_array( $sett[ 'values' ] ) ){
                foreach( $sett[ 'values' ] as $value ){

                    if( $value == $sett[ 'value' ] ){
                        $options .= '<span ref="' . $value . '" class="current no-preview"><i ' . $style . ' class="' . $value . '"></i></span>';
                        $img = '<i ' . $style . ' class="' . $value . '"></i>';
                    }else{
                        $options .= '<span ref="' . $value . '" class="no-preview"><i ' .$style . ' class="' . $value . '"></i></span>';
                    }
                }
            }
        }
        
        $options .= '</div>';
        $options .= '</div>';
        
        $result  = '<span class="preview-value">';
        $result .= $img; 
        $result .= '</span>';
        $result .= $options;
        
        return $result;
    }
    
    static function getLogicValue( $sett )
    {
        if( !isset( $sett[ 'value' ] ) ){
            if( isset( $sett[ 'defaultValue' ] ) ){
                return 'value="' . ( (int)$sett[ 'defaultValue' ] ) . '"';
            }
        }
        else{
            return 'value="' . ( (int) $sett[ 'value' ] ) . '"';
        }
    }
    
    static function getLogicCheckValue( $sett )
    {
        if( isset( $sett[ 'type' ][ 'input' ] ) && $sett[ 'type' ][ 'input' ] == 'logic' ){
            if( !isset( $sett[ 'value' ] ) ){
                if( isset( $sett[ 'defaultValue' ] ) ){
                    return checked( $sett[ 'defaultValue' ] , 1 , false );
                }
            }
            else{
                return checked( $sett[ 'value' ] , 1 , false );
            }
        }
    }
    
    static function getLogicButtonValue( $sett , $attr = false )
    {
        if( !isset( $sett[ 'value' ] ) ) {
            if( isset( $sett[ 'defaultValue' ] ) ) {
                if( $sett[ 'defaultValue' ] ) {
                    $result =  __( 'Disable', 'myThemes' );
                }else{
                    $result =  __( 'Enable', 'myThemes' );
                }
            }
            else{
                $result =  __( 'Enable', 'myThemes' );
            }
            
            if( !$attr ) {
                return $result;
            } else {
                return 'value="' . $result . '"';
            }
        }
        else {
            if( $sett[ 'value' ] ) {
                $result =  __( 'Disable', 'myThemes' );
            } else {
                $result =  __( 'Enable', 'myThemes' );
            }
            
            if( !$attr ) {
                return $result;
            }else{
                return 'value="' . $result . '"';
            }
        }
    }
    
    static function getTextareaValue( $sett )
    {   
        if( isset( $sett[ 'type' ][ 'input' ] ) && $sett[ 'type' ][ 'input' ] == 'textarea' ) {
            if( !isset( $sett[ 'value' ] ) ){
                if( isset( $sett[ 'defaultValue' ] ) ){
                    if( isset( $sett[ 'type' ][ 'validator' ] ) && $sett[ 'type' ][ 'validator' ] == 'noesc' )
                        return stripcslashes ( $sett[ 'defaultValue' ] );
                    else
                        return esc_attr( $sett[ 'defaultValue' ] );
                }
            }
            else{
                if( isset( $sett[ 'type' ][ 'validator' ] ) && $sett[ 'type' ][ 'validator' ] == 'noesc' )
                    return stripcslashes ( $sett[ 'value' ] );
                else
                    return esc_attr( $sett[ 'value' ] );
            }
        }
    }
    
    /* TEXT, SEARCH, UPLOAD, UPLOAD-ID, DIGIT */
    static function getValue( $sett , $attr = false )
    {
        if( !isset( $sett[ 'value' ] ) ){
            if( isset( $sett[ 'defaultValue' ] ) ){
                if( !$attr ){
                    return self::validator( $sett[ 'defaultValue' ] , self::getValidator( $sett ) );
                }
                else{                    
                    return 'value="' . self::validator( $sett[ 'defaultValue' ]  , self::getValidator( $sett ) ) . '"';
                }
            }
        }
        else{   
            if( !$attr ){
                return self::validator( $sett[ 'value' ] , self::getValidator( $sett ) ) ;
            }
            else{
                return 'value="' . self::validator( $sett[ 'value' ] , self::getValidator( $sett ) ) . '"';
            }
        }
    }
    
    /* AUTO COMPLETE RESULT ( AJAX REQUEST ) */
    static function getSearchValues()
    {
        $query = isset( $_GET[ 'params' ] ) ? (array)json_decode( stripslashes( $_GET[ 'params' ] )) : exit;
        $query[ 's' ] = isset( $_GET[ 'query' ] ) ? $_GET[ 'query' ] : exit;
        
        global $wp_query;
        $result = array();
        $result[ 'query' ] = $query[ 's' ];
        
        $wp_query = new WP_Query( $query );
        
        if( $wp_query -> have_posts() ){
            foreach( $wp_query -> posts as $post ){
                $result['suggestions'][] = $post -> post_title;
                $result['data'][] =  $post -> ID;
            }
        }
        
        echo json_encode( $result );
        exit();
    }
    
    static function parse_sett( $sett , $pageSlug )
    {
        $result = '';
        
        foreach( $sett as $fieldName => & $d ){
            if(  !isset( $d[ 'skip' ] ) || ( isset( $d[ 'skip' ] ) && !$d[ 'skip' ] ) ){
                $d[ 'fieldName' ] = $fieldName;
                $d[ 'pageSlug' ] = $pageSlug;
                $d[ 'value' ] = sett::get( $pageSlug . '-' . $fieldName );
                $result .= self::template( $d );
            }
        }
        
        return $result;
    }
    
    /* TEMPLATES TYPE */
    static function template( $sett )
    {
        if( isset( $sett[ 'type' ][ 'template' ] ) && method_exists( new ahtml() , $sett[ 'type' ][ 'template' ] ) ) {
            return call_user_func_array( array( new ahtml() , $sett[ 'type' ][ 'template' ] ) , array( $sett ) );
        }
        else{
            ob_start();
            print_r( $sett );
            $data = ob_get_clean();
            
            $bt = debug_backtrace();
            $caller = array_shift( $bt );
            
            $result  = '<pre>' . $caller[ 'file' ] . ' : ' . $caller[ 'line' ];
            $result .= '<br>Template not exist : [ ' . tools::getPageSlug( $sett ) .' , ' . tools::getFieldName( $sett ) . ' ]';
            $result .= '<br>' . $data .'</pre>';
            return $result;
        }
    }
    
    /* TEMPLAE WITH ONLY INPUT */
    static function none( $sett )
    {   
        $hint = '';
        
        if( isset( $sett[ 'hint' ] ) && !empty( $sett[ 'hint' ] ) ){
            $hint  = '<div class="hint fl"><small>' . $sett[ 'hint' ] . '</small></div>';
            $hint .= '<div class="clear clearfix"></div>';
        }

        $result = '';

        if( isset( $sett[ 'content' ] ) ){
            $result = $sett[ 'content' ];
        }
        else{
            $result = call_user_func_array( array( new ahtml() , self::getInputType( $sett ) ) , array( $sett ) ) . $hint;    
        }
        
        return $result;
    }
    
    
    /* TEMPLATE TYPE INLINE */
    static function inline( $sett )
    {
        $result  = '<div ' . self::getTemplateID( $sett , true ) . ' ' . self::getTemplateClass( $sett , 'inline-type' , true ) . '>';
        
        /* ADD LABEL */
        $result .= '<div class="label">';
        $result .= self::getInputLabel( $sett );

        /* ADD HINT ( ADDITIONAL INFO ) */
        if( isset( $sett[ 'hint' ] ) && !empty( $sett[ 'hint' ] ) ){
            $result .= '<div class="hint"><small>' . $sett[ 'hint' ] . '</small></div>';
        }

        $result .= '</div>';
        
        /* ADD INPUT */
        $result .= '<div class="input">';
        $result .= call_user_func_array( array( new ahtml() , self::getInputType( $sett ) ) , array( $sett ) );
        if( isset( $sett[ 'submitValue' ] ) ){
            $result .= '<input type="submit" value="' . $sett[ 'submitValue' ] . '" class="button-primary my-multiple-submit">';
        }
        $result .= '</div>';
        
        $result .= '<div class="clear"></div>';
        
        $result .= '</div>';
        
        return $result;
    }
    
    /* TEMPLATE TYPE INLIST */
    static function inlist( $sett )
    {
        $result  = '<div ' . self::getTemplateID( $sett , true ) . ' ' . self::getTemplateClass( $sett , 'inlist-type' , true ) . '>';
        
        /* ADD LABEL */
        $result .= '<div class="label">';
        $result .= self::getInputLabel( $sett );

        /* ADD HINT ( ADDITIONAL INFO ) */
        if( isset( $sett[ 'hint' ] ) && !empty( $sett[ 'hint' ] ) ){
            $result .= '<div class="clear"></div>';
            $result .= '<div class="hint"><small>' . $sett[ 'hint' ] . '</small></div>';
        }
        
        $result .= '</div>';
        
        /* ADD INPUT */
        $result .= '<div class="input">';
        $result .= call_user_func_array( array( new ahtml() , self::getInputType( $sett ) ) , array( $sett ) );
        if( isset( $sett[ 'submitValue' ] ) ){
            $result .= '<input type="submit" value="' . $sett[ 'submitValue' ] . '" class="button-primary my-multiple-submit">';
        }
        $result .= '</div>';
        
        $result .= '</div>';
        
        return $result;
    }
    
	/* TEMPLATE TYPE CODE */
    static function code( $sett )
    {
        $result = '<div ' . self::getTemplateID( $sett , true ) . ' ' . self::getTemplateClass( $sett , 'code-type' , true ) . '>';
        
        if( isset( $sett[ 'title' ] ) ){
            $result .= '<h3 class="title">' . $sett[ 'title' ] . '</h3>';
        }
        
        if( isset( $sett[ 'description' ] ) ){
            $result .= '<p class="description">' . $sett[ 'description' ] . '</p>';
        }
        
        if( isset( $sett[ 'content' ] ) ){
            $result .= $sett[ 'content' ];
        }
        
        $result .= '</div>';
        
        return $result;
    }
    
    static function _popBox( $sett, $content )
	{
        $class = '';
        if( isset( $sett[ 'class'] ) )
            $class = $sett[ 'class' ];
        
        $rett  = '<div class="popup-box-shadow"></div>';
		$rett .= '<div class="' . $class . ' special-settings code-type popup-box">';
        
        if( isset( $sett[ 'boxID' ] ) )
            $rett .= '<span class="close-popup-box"><a href="javascript:tools.popBox2Hide( \'#' . $sett[ 'boxID' ] . '\' );"></a></span>';
		
		if( isset( $sett[ 'title' ] ) ){
            $rett .= '<h3 class="title">' . $sett[ 'title' ] . '</h3>';
        }
        
        if( isset( $sett[ 'description' ] ) ){
            $rett .= '<p class="description">' . $sett[ 'description' ] . '</p>';
        }
		
		$rett .= $content . '<div class="clearfix"></div></div>';
        
        return $rett;
	}
    
    static function _popBox2( $sett, $content )
	{   
        $class = '';
        if( isset( $sett[ 'class'] ) )
            $class = $sett[ 'class' ];
        
        if( isset( $sett[ 'title' ] ) )
            $title = $sett[ 'title' ];
        else
            $title = '';
        
        if( isset( $sett[ 'boxID' ] ) ){
            $s_id = ' id="' . $sett[ 'boxID' ] . '-shadow" ';
            $b_id = ' id="' . $sett[ 'boxID' ] . '"';
        }else{
            $s_id = ' id="' . str_replace( array( ',' , ' ', '.' ) , '-' , $title ) . '-shadow"';
            $b_id = ' id="' . str_replace( array( ',' , ' ', '.' ) , '-' , $title ) . '"';
        }
            
        $rett  = '<div class="popup-box-shadow" ' . $s_id . '></div>';
		$rett .= '<div class="' . $class . ' special-settings code-type popup-box" ' . $b_id . '>';
        if( isset( $sett[ 'boxID' ] ) )
            $rett .= '<span class="close-popup-box"><a href="javascript:tools.popBox2Hide( \'#' . $sett[ 'boxID' ] . '\' );"></a></span>';
		
		if( $title ){
            $rett .= '<h3 class="title">' . $title . '</h3>';
        }
        
        if( isset( $sett[ 'description' ] ) ){
            $rett .= '<p class="description">' . $sett[ 'description' ] . '</p>';
        }
		
        $rett .= $content . '<div class="clearfix"></div></div>';
        
        return $rett;
    }
    
    static function _popBoxHook( $sett , $content )
    {
        self::$content .= self::_popBox2( $sett , $content );
    }
    
    static function my_hook()
    {
        echo self::$content;
    }
    
    static function _box( $sett, $content )
    {
        $class = '';
        if( isset( $sett[ 'class'] ) )
            $class = $sett[ 'class' ];
        
		$rett  = '<div class="' . $class . ' special-settings code-type">';
		
		if( isset( $sett[ 'title' ] ) ){
            $rett .= '<h3 class="title">' . $sett[ 'title' ] . '</h3>';
        }
        
        if( isset( $sett[ 'description' ] ) ){
            $rett .= '<p class="description">' . $sett[ 'description' ] . '</p>';
        }
		
        $rett .= $content . '<div class="clearfix"></div></div>';
        
        return $rett;
    }
	
    static function _formSubmit( $attr ) 
    {
        $_name = '';
        if( isset( $attr[ 'name' ] ) )
                $_name = 'name="'.$attr[ 'name' ].'"';

        $_value = '';
        if( isset( $attr[ 'value' ] ) )
                $_value = $attr[ 'value' ];
        
        $_onclick = '';
        if( isset( $attr[ 'onclick' ] ) )
            $_onclick = 'onclick="' . $attr[ 'onclick' ] . '"';
        
        $_type = 'submit';
        if( isset( $attr[ 'type' ] ) )
            $_type = $attr[ 'type' ];
        
        $_class = '';
        if( isset( $attr[ 'buttonClass' ] ) )
            $_class = $attr[ 'buttonClass' ];
        
        $_id = '';
        if( isset( $attr[ 'buttonID' ] ) )
            $_id = " id='" . $attr[ 'buttonID' ] . "' ";
		
        $rett = '<input type="' . $_type . '" ' . $_name . $_id . ' class="button-primary my-submit ' . $_class . '" value="' . $_value . '"  ' . $_onclick . ' >';
		
        if( !isset( $attr[ 'div' ] ) || $attr[ 'div' ] === true ) {
                $rett = '<div class="box-form-submit">' . $rett . '</div>';
        }

        return $rett;
    }
	
    static function _form( $content, $submit = 1 )
    {
        $_submit = '';
        if( $submit )
            $_submit = ahtml::_formSubmit( array( 'value'=>'Update' ) );
        
        return 	'<form method="post">' . 
                $content . 
                $_submit .
                '</form>';
    }
	
    static function box( $box , $pageSlug , $sett  )
    {
        $result = '<div>';
        
        foreach( $sett as $fieldName => & $d ){
            if( isset( $d[ 'type' ][ 'box' ] ) && isset( $d[ 'skip' ] ) && $d[ 'type' ][ 'box' ] == $box ){
                $d[ 'fieldName' ] = $fieldName;
                $d[ 'pageSlug' ] = $pageSlug;
                $d[ 'value' ] = sett::get( $pageSlug . '-' . $fieldName );
                $result .= ahtml::template( $d );
            }
        }
        
        $result .= '</div>';
        
        return $result;
    }
	
    static function boxForm( $box , $pageSlug , $sett , $key = null , $option  = null )
    {
        $result  = '<div>';
        $result .= '<form method="post">';
        
        foreach( $sett as $fieldName => & $d ){
            if( isset( $d[ 'type' ][ 'box' ] ) && isset( $d[ 'skip' ] ) && $d[ 'type' ][ 'box' ] == $box ){
                $d[ 'fieldName' ] = $fieldName;
                $d[ 'pageSlug' ] = $pageSlug;
                $d[ 'value' ] = sett::get( $pageSlug . '-' . $fieldName );
                $result .= ahtml::template( $d );
            }
        }
        
        if( !isset( $sett[ $box ][ 'submit' ] ) || ( isset( $sett[ $box ][ 'submit' ] ) &&  $sett[ $box ][ 'submit' ] ) ){
            $result .= '<div class="box-form-submit">';
            $result .= '<input type="submit" value="' . __( 'Update Settings' , 'myThemes' ) . '" class="button-primary my-submit">';
            if( !empty( $key ) || !empty( $option ) ){
                $result .= '<input type="button" value="' . __( 'Drop Settings' , 'myThemes' ) . '" class="button-secondary my-submit" onclick="javascript:fields.drop( ' . $key . ' , \''. $option . '\', this );">';
            }
            $result .= '</div>';
        }
        $result .= '</form>';
        $result .= '</div>';
        
        
        return $result;
    }
    
    static function boxMultipleForm( $box , $pageSlug , $sett  )
    {
        $result  = '<div>';
        foreach( $sett as $fieldName => & $d ){
            if( isset( $d[ 'type' ][ 'box' ] ) && isset( $d[ 'skip' ] ) && $d[ 'type' ][ 'box' ] == $box ){
                $d[ 'fieldName' ] = $fieldName;
                $d[ 'pageSlug' ] = $pageSlug;
                $d[ 'value' ] = sett::get( $pageSlug . '-' . $fieldName );
                $result .= '<form method="post">';
                $result .= ahtml::template( $d );
                $result .= '</form>';
            }
        }
        
        $result .= '</div>';
        
        
        return $result;
    }
    
    
    
    /* INPUTS TYPE */
    /* INPUT TYPE HIDDEN */
    static function hidden( $sett )
    {
        $result  = '<input type="hidden" ';
        $result .= self::getInputID( $sett , true ) . ' ';
        $result .= self::getInputClass( $sett , true ) . ' ';
        $result .= tools::getInputName( $sett , true ) . ' ';
        $result .= self::getValue( $sett , true ) . '/>';       
        return $result;
    }
    /* INPUT TYPE TEXT */
    static function text( $sett )
    {
        $result  = '<input type="text" ';
        $result .= self::getInputID( $sett , true ) . ' ';
        $result .= self::getInputClass( $sett , true ) . ' ';
        $result .= tools::getInputName( $sett , true ) . ' ';
        $result .= self::getValue( $sett , true ) . '/>';       
        return $result;
    }
    
    /* INPUT TYPE LIMITED TEXT */
    static function limitedText( $sett )
    {
        $result  = '<input type="text" ';
        $result .= self::getInputID( $sett , true ) . ' ';
        $result .= self::getInputClass( $sett , true ) . ' ';
        $result .= tools::getInputName( $sett , true ) . ' ';
        
        if( !isset( $sett[ 'limit' ] ) ){
            $limit = 50;
        }
        else{
            $limit = (int)$sett[ 'limit' ];
        }
        
        $result .= self::getLimitStringAction( 'this' , $limit );
        $result .= self::getValue( $sett , true ) . '/>';

        return $result;
    }
    
    /* INPUT TYPE SEARCH ( AUTO COMPLETE ) */
    static function search( $sett )
    {
        /* SET INPUT VALUE */
        $value  = self::getValue( $sett );
        $title  = '';
        $postID = '';
        
        if( !empty( $value ) && (int)$value > 0 ){
            $p = get_post( $value );
            if( !is_wp_error( $p ) && is_object( $p ) ){
                $title = $p -> post_title;
                $postID = $p -> ID;
            }
        }
        
        /* POST TITLE */
        $result  = '<input type="text" ' . self::getInputClass( $sett , true ) . ' value="' . esc_attr( $title ) . '" ' . self::getSearchAction( $sett ) . '/>';
        
        /* DEFAULT VALIDATOR */
        if( !isset( $sett[ 'type' ][ 'validator' ] ) ){
            $sett[ 'type' ][ 'validator' ] = 'int';
        }
        
        /* POST ID */
        $result .= '<input type="hidden" class="my-field-search-postID"';
        $result .= self::getInputID( $sett , true ) . ' ';
        $result .= tools::getInputName( $sett , true ) . ' ';
        $result .= self::getValue( $sett , true ) . '/>';
        
        /* POSTS FROM QUERY */
        $result .= '<input type="hidden" class="my-field-params" value="' . urlencode( json_encode( $sett[ 'query' ] ) ) . '" />';
        $result .= '<a class="search-clean" href="javascript:fields.clean( \'#' . self::getTemplateID( $sett ) . '\'  )" ';
        $result .= 'title="' . esc_attr__( 'Remove data from this field' , "myThemes" ) . '">';
        $result .= '<img src="' . get_template_directory_uri() . '/media/admin/images/clear-hover.png" height="0" width="0"/></a>';
        
        return $result;
    }
    
    /* INPUT TYPE DIGIT ( ACCEPT ONLY DIGITS ) */
    static function digit( $sett )
    {
        /* DEFAULT VALIDATOR */
        if( !isset( $sett[ 'type' ][ 'validator' ] ) ){
            $sett[ 'type' ][ 'validator' ] = 'int';
        }
        
        $result  = '<input type="text" ';
        $result .= self::getInputID( $sett , true ) . ' ';
        $result .= self::getInputClass( $sett , true ) . ' ';
        $result .= tools::getInputName( $sett , true ) . ' ';
        $result .= self::getValue( $sett , true ) . '/>';
        
        return $result;
    }
    
    /* INPUT TYPE UPLOAD ( URL OR UPLOADED FILE PATH ) */
    static function upload( $sett )
    {
        /* DEFAULT VALIDATOR */
        if( !isset( $sett[ 'type' ][ 'validator' ] ) ){
            $sett[ 'type' ][ 'validator' ] = 'url';
        }
        
        /* UPLOAD URL / FILE PATH */
        $result  = '<input type="text" ';
        $result .= self::getInputID( $sett , true ) . ' ';
        $result .= self::getInputClass( $sett , true ) . ' ';
        $result .= tools::getInputName( $sett , true ) . ' ';
        $result .= self::getValue( $sett , true ) . '/>';
        
        /* UPLOAD BUTTON */
        $result .= '<input type="button" ';
        $result .= self::getButtonClass( $sett , true , 'button-upload' ) . ' ';
        $result .= ' value="' . __( 'Choose File' , "myThemes" ) . '" ';
        $result .= ' onclick="javascript:my_uploader( jQuery( this ).parent().children(\'input#' . self::getInputID( $sett ) . '\') )"/>';
            
        return $result;
    }
    
    /* INPUT TYPE UPLOAD ID ( SAVE ID OF ATTACHED FILE ) */
    static function uploadID( $sett )
    {
        /* SET UPLOAD ID VALUE */
        $value = '';
        
        if( (int)self::getValue( $sett ) > 0 ){
            $src = wp_get_attachment_image_src( self::getValue( $sett ) , 'full' );
            if( isset( $src[ 0 ] ) && !empty( $src[ 0 ] ) ){
                $value = $src[ 0 ];
            }
        }
        
        /* DEFAULT VALIDATOR */
        if( !isset( $sett[ 'type' ][ 'validator' ] ) ){
            $sett[ 'type' ][ 'validator' ] = 'int';
        }
        
        /* UPLOAD URL */
        $result  = '<input type="text" ';
        $result .= 'id="' . self::getInputID( $sett ) . '" ';
        $result .= self::getInputClass( $sett , true ) . ' ';
        $result .= 'value="' . esc_url( $value ). '"/>';
        
        /* UPLOAD BUTTON */
        $result .= '<input type="button" ';
        $result .= self::getButtonClass( $sett , true , 'button-upload' ) . ' ';
        $result .= ' value="' . __( 'Choose File' , "myThemes" ) . '" ';
        $result .= ' onclick="javascript:mytheme_fl_uploadID( \'input#' . self::getInputID( $sett ) . '\' , \'#my-uploader-box\'  )"/>';
        
        /* UPLOAD ID */
        $result .= '<input type="hidden" ';
        $result .= ' id="' . self::getInputID( $sett ) . '-ID" ';
        $result .= tools::getInputName( $sett , true ) . ' ';
        $result .= self::getValue( $sett , true ) . '"/>';
        
        my_fl_upload::run( );
        
        return $result;
    }
    
    /* INPUT TYPE PICK COLOR */
    static function pickColor( $sett )
    {
        /* SET INPUT NAME */
        $inputName = tools::getInputName( $sett );
        
        /* COLOR */
        $result  = '<input type="text" ';
        
        if( isset( $sett[ 'action' ] ) ){
            $result .= ' rel="' . $sett[ 'action' ] . '" ';
        }
        
        $result .= self::getInputID( $sett , true ) . ' ';
        $result .= self::getInputClass( $sett , true ) . ' ';
        $result .= tools::getInputName( $sett , true ) . ' ';
        $result .= 'op_name="' . $inputName . '" ';
        $result .= self::getValue( $sett , true ) . '/>';

        /* PICK ICON */
        $result .= '<a href="javascript:void(0);" class="pickcolor hide-if-no-js" id="link-pick-' . $inputName . '"></a>';
        
        /* COLOR PANEL */
        $result .= '<div id="color-panel-' . $inputName . '" class="color-panel"></div>';
        
        return $result;
    }
    
    /* INPUT TYPE TEXTAREA */
    static function textarea( $sett )
    {
        $result  = '<textarea ';
        $result .= self::getInputID( $sett , true ) . ' ';
        $result .= self::getInputClass( $sett , true ) . ' ';
        $result .= tools::getInputName( $sett , true ) . ' ';
        $result .= '>' . self::getTextareaValue( $sett ) . '</textarea>';
        
        return $result;
    }
    
    /* INPUT TYPE LIMITED TEXTAREA */
    static function limitedTextarea( $sett )
    {
        $result  = '<textarea ';
        $result .= self::getInputID( $sett , true ) . ' ';
        $result .= self::getInputClass( $sett , true ) . ' ';
        $result .= tools::getInputName( $sett , true ) . ' ';
        
        if( !isset( $sett[ 'limit' ] ) ){
            $limit = 150;
        }
        else{
            $limit = (int)$sett[ 'limit' ];
        }
        
        $result .= self::getLimitStringAction( 'this' , $limit );
        $result .= '>' . self::getTextareaValue( $sett ) . '</textarea>';
        
        return $result;
    }
    
    /* INPUT TYPE LIMITED NUMBER OF WORDS */
    static function limitedWords( $sett )
    {
        $result  = '<textarea ';
        $result .= self::getInputID( $sett , true ) . ' ';
        $result .= self::getInputClass( $sett , true ) . ' ';
        $result .= tools::getInputName( $sett , true ) . ' ';
        
        if( !isset( $sett[ 'limit' ] ) ){
            $limit = 10;
        }
        else{
            $limit = (int)$sett[ 'limit' ];
        }
        
        $result .= self::getLimitWordsAction( 'this' , $limit );
        $result .= '>' . self::getTextareaValue( $sett ) . '</textarea>';
        
        return $result;
    }
    
    /* INPUT TYPE SELECT */
    static function select( $sett )
    {
        if( isset( $sett[ 'type' ][ 'multiple' ] ) ) {
            $result  = '<select multiple="multiple" ';
        }
        else{
            $result  = '<select ';
        }
        $result .= self::getInputID( $sett , true ) . ' ';
        $result .= self::getInputClass( $sett , true ) . ' ';
        $result .= tools::getInputName( $sett , true ) . ' ';
        $result .= self::getSelectAction( $sett ) . ' ';
        $result .= self::getInputDisabled( $sett );
        $result .= '>' . self::getSelectValues( $sett ) . '</select>';
        
        return $result;
    }
    
    /* INPUT TYPE LOGIC */
    static function logic( $sett )
    {
        /* DEFAULT VALIDATOR */
        if( !isset( $sett[ 'type' ][ 'validator' ] ) ){
            $sett[ 'type' ][ 'validator' ] = 'int';
        }
        
        $result  = '<input type="checkbox"';
        $result .= self::getInputID( $sett , true ) . ' ';
        $result .= self::getInputClass( $sett , true ) . ' ';
        $result .= self::getLogicAction( $sett );
        $result .= self::getLogicCheckValue( $sett ) . '/>';
        $result .= '<input type="hidden"';
        $result .= tools::getInputName( $sett , true ) . ' ';
        $result .= self::getLogicValue( $sett ) . '/>';
        
        return $result;
    }

    static function logicButton( $sett )
    {
        /* INPUT BUTTON */
        $result  = '<input type="button" ';
        $result .= self::getButtonClass( $sett , true , 'my-submit button-logic' ) . ' ';
        $result .= self::getLogicButtonValue( $sett , true ) . ' ';
        $result .= self::getLogicButtonAction( $sett ) . ' />';
        
        /* INPUT ID */
        $result .= '<input type="hidden" ';
        $result .= self::getInputID( $sett , true ) . ' ';
        $result .= tools::getInputName( $sett , true ) . ' ';
        $result .= self::getLogicValue( $sett ) . '/>';
        
        /* MESSAGE */
        $result .= '<span class="message logic-button hidden">';
        $result .= '</span>';
        
        return $result;
    }
    
    static function imageSelect( $sett )
    {
        $result  = '<div ';
        $result .= self::getInputID( $sett , true ) . ' ';
        $result .= self::getInputClass( $sett , true ) . ' ';
        $result .= self::getImageSelectAction( $sett ) . ' >';
        
        $result .= '<input type="hidden"';
        $result .= tools::getInputName( $sett , true ) . ' ';
        $result .= self::getValue( $sett , true ) . '>';
        
        $result .= self::getImageSelectValue( $sett );
        $result .= '</div>';
        
        return $result;
    }

    static function iconSelect( $sett )
    {
        $result  = '<div ';
        $result .= self::getInputID( $sett , true ) . ' ';
        $result .= self::getInputClass( $sett , true ) . '  >';
        
        $result .= '<input type="hidden"';
        $result .= tools::getInputName( $sett , true ) . ' ';
        $result .= self::getValue( $sett , true ) . '>';
        
        $result .= self::getIconSelectValue( $sett );
        $result .= '</div>';
        
        return $result;
    }
    
    static function view( $sett )
    {
        $result  = '<div class="view-list">';
        if( isset( $sett[ 'values' ] ) && !empty( $sett[ 'values' ] ) ){
            foreach( $sett[  'values' ] as $key => $value ){
                $result .= '<div class="item">';
                $result .= '<p>';
                $result .= '<span class="item-label"><strong>' . $value . '</strong></span>';
                $result .= '<span class="btn"><input type="button" class="button my-submit" value="' . __( 'Drop item' , 'myThemes' ) . '" onclick="javascript:fields.drop( ' . $key . ' , \'' . $sett[ 'option' ] . '\' , this );"></span>';
                $result .= '<span class="clear"></span>';
                $result .= '</div>';
            }
        }
        else{
            echo '<p>' . __( 'Not fount items' , 'myThemes' ) . '</p>';
        }
        $result .= '</div>';
        
        return $result;
    }
    
    
    /* FIELDS ACTIONS */
    /* UPLOAD ACTION */
    static function getLimitStringAction( $obj , $nr ){
        return 'onkeyup="javascript:fields.limitString( ' . $obj . ' , ' . $nr . ' );"';
    }
    
    static function getLimitWordsAction( $obj , $nr ){
        return 'onkeyup="javascript:fields.limitWords( ' . $obj . ' , ' . $nr . ' );"';
    }
    
    static function getUploadAction( $id )
    {
        return 'onclick="javascript:fields.upload(\'input#' . $id . '\' );"';
    }
    
    static function getUploadIDAction( $id )
    {
        return 'onclick="javascript:fields.uploadID(\'input#' . $id . '\' , \'\' );"';
    }
    
    static function _getUploadIDAction( $id )
    {
        return 'onclick="javascript:fields._uploadID(\'input#' . $id . '\' );"';
    }
    
    static function getSelectAction( $sett )
    {
        if( isset( $sett[ 'action' ] ) && !empty( $sett[ 'action' ] ) ){
            return 'onchange="javascript:' . $sett[ 'action' ] . ';"';
        }
    }
    
    static function getLogicAction( $sett )
    {
        if( isset( $sett[ 'action' ] ) && !empty( $sett[ 'action' ] ) ){
            return 'onclick="javascript:fields.check( this , ' . $sett[ 'action' ] . ' );" ';
        }else{
            return 'onclick="javascript:fields.check( this , { \'t\' : \'-\' , \'f\' : \'-\' } );" ';
        }
    }
    
    static function getSearchAction( $sett )
    {
        if( isset( $sett[ 'action' ] ) && !empty( $sett[ 'action' ] ) ){
            return 'rel="function(){' . $sett[ 'action' ] . '}" ';
        }
    }
    
    static function getImageSelectAction( $sett )
    {
        if( isset( $sett[ 'action' ] ) && !empty( $sett[ 'action' ] ) ){
            return 'rel="' . $sett[ 'action' ] . '"';
        }
    }
        
    static function getLogicButtonAction( $sett ){
        if( isset( $sett[ 'action' ] ) && !empty( $sett[ 'action' ] ) ){
            
            $action = '';
 
            /* SIMPLE CHECK ACTION */
            if( isset( $sett[ 'action' ][ 'check' ] ) ){
                $json = str_replace( 
                    '"', 
                    "'", 
                    json_encode( 
                        array( 
                            'labels' => array( 
                                self::getLogicButtonValue( array( 'value' => 0 ) ),
                                self::getLogicButtonValue( array( 'value' => 1 ) )
                            )
                        ) 
                    )
                );
                $action .= 'fields.checkButton( this , ' . $json . ' );';
            }
            
            /* AJAX ACTION */
            if( isset( $sett[ 'action' ][ 'ajax' ] ) ){
                $sett[ 'action' ][ 'ajax' ][ 'option' ] = tools::getInputName( $sett );
                
                /* JAVASCRIPT ACTION */
                if( isset( $sett[ 'action' ][ 'js' ] ) ){
                    $function = '(function( obj ){' . $sett[ 'action' ][ 'js' ] . '})';
                }else{
                    $function = '(function( obj ){})';
                }
                
                $action .= 'fields.logicButton( ' . str_replace( '"' , "'" , json_encode( $sett[ 'action' ][ 'ajax' ] ) ) . ' , ' . $function . ' , this );';
            }else{
                /* JAVASCRIPT ACTION */
                if( isset( $sett[ 'action' ][ 'js' ] ) ){
                    $action .= $sett[ 'action' ][ 'js' ];
                }
            }
            
            if( !empty( $action  ) ){
                return 'onclick="javascript:' . $action . '"';
            }
        }
        
    }
    
    static function getValidator( $sett )
    {
        if( !isset( $sett[ 'type' ][ 'validator' ] ) ){ /* DEFAULT VALIDATOR TYPE */
            switch( $sett[ 'type' ][ 'input' ] ){
                case 'digit' : {
                    return 'int';
                    break;
                }
                case 'logic' : {
                    return 'int';
                    break;
                }
                case 'textarea' : {
                    return 'no_html';
                    break;
                }
                case 'uploadID' : {
                    return 'int';
                    break;
                }
                case 'upload' : {
                    return 'url';
                    break;
                }
                case 'search' : {
                    return 'int';
                    break;
                }
            }
        }
        else{
            return $sett[ 'type' ][ 'validator' ];
        }
    }
    
    static function validator( $value , $type )
    {   
        switch( $type ){
            case 'int' : {
                if( empty( $value ) ){
                    return '';
                }
                if( is_array( $value ) ){
                    return '';
                }else{
                    return (int) $value;
                }
                break;
            }
            
            case 'url' : {
                return esc_url( $value );
                break;
            }
            
            case 'email' : {
                if( is_email( $value ) ){
                    return $value;
                }
                else{
                    return '';
                }
                break;
            }

            case 'no_html' : {
                return esc_textarea( $value );
                break;
            }
            
            case 'noesc' : {
                return $value;
                break;
            }
            
            default : {
                return esc_attr( $value );
                break;
            }
        }
    }
};

    add_action( 'admin_footer', array( 'ahtml' , 'my_hook' ) );
    add_action( 'wp_ajax_search' , array( 'ahtml' , 'getSearchValues' ) );
?>