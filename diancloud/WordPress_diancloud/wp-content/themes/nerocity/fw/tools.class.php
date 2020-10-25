<?php
class tools
{
    static function getPageSlug( $sett )
    {
        return isset( $sett[ 'pageSlug' ] ) && !empty( $sett[ 'pageSlug' ] ) ? $sett[ 'pageSlug' ] : '';
    }

    static function getFieldName( $sett )
    {
        return isset( $sett[ 'fieldName' ] ) && !empty( $sett[ 'fieldName' ] ) ? $sett[ 'fieldName' ] : '';
    }

    static function getInputName( $sett , $attr = false )
    {
        /* SET PAGE SLUG */
        $pageSlug = self::getPageSlug( $sett );

        /* SET FIELD NAME */
        $fieldName = self::getFieldName( $sett );

        if( !$attr ){
            if( isset( $sett[ 'type' ][ 'metabox' ] ) ) {
                return !empty( $fieldName ) ? 'mythemes-' . $fieldName : '';
            }
            else{
                return !empty( $fieldName ) ? 'mythemes-' . $fieldName : '';
            }
        }
        else{
            if( isset( $sett[ 'type' ][ 'multiple' ] ) ) {
                return !empty( $fieldName ) ? 'name="mythemes-' . $fieldName . '"' : '';
            }
            else{
                return !empty( $fieldName ) ? 'name="mythemes-' . $fieldName . '"' : '';
            }
        }
    }
}
?>