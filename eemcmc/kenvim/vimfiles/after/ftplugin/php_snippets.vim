if !exists('loaded_snippet') || &cp
    finish
endif

let st = g:snip_start_tag
let et = g:snip_end_tag
let cd = g:snip_elem_delim

exec "Snippet _elseif elseif ( ".st."condition".et." )<CR>{<CR><Tab>".st.et."<CR>}<CR>".st.et
exec "Snippet _do do<CR>{<CR>".st.et."<CR><CR>} while (".st.et.");<CR>".st.et
exec "Snippet _reql require_once('".st."file".et."');<CR>".st.et
exec "Snippet _if? $".st."retVal".et." = (".st."condition".et.") ? ".st."a".et." : ".st."b".et." ;<CR>".st.et
exec "Snippet _phpp <?php<CR><CR>".st.et."<CR><CR>"
exec "Snippet _switch switch (".st."variable".et.")<CR>{<CR>case '".st."value".et."':<CR>".st.et."<CR>break;<CR><CR>".st.et."<CR><CR>default:<CR>".st.et."<CR>break;<CR>}<CR>".st.et
exec "Snippet _class #doc<CR>#classname:".st."ClassName".et."<CR>#scope:".st."PUBLIC".et."<CR>#<CR>#/doc<CR><CR>class ".st."ClassName".et." ".st."extendsAnotherClass".et."<CR>{<CR>#internal variables<CR><CR>#Constructor<CR>function __construct ( ".st."argument".et.")<CR>{<CR>".st.et."<CR>}<CR>###<CR><CR>}<CR>###".st.et
exec "Snippet _incll include_once( '".st."file".et."' );".st.et
exec "Snippet _incl include( '".st."file".et."' );".st.et
exec "Snippet _foreach foreach( $".st."variable".et." as $".st."key".et." => $".st."value".et." )<CR>{<CR>".st.et."<CR>}<CR>".st.et
exec "Snippet _ifelse if ( ".st."condition".et." )<CR>{<CR>".st.et."<CR>}<CR>else<CR>{<CR>".st.et."<CR>}<CR>".st.et
exec "Snippet $_REQ $_REQUEST['".st."variable".et."']".st.et
exec "Snippet $_POST $_POST['".st."variable".et."']".st.et
exec "Snippet $_GET $_GET['".st."variable".et."']".st.et
exec "Snippet _case case '".st."variable".et."':<CR>".st.et."<CR>break;<CR>".st.et
exec "Snippet _print print \"".st."string".et."\"".st.et.";".st.et."<CR>".st.et
exec "Snippet _function ".st."public".et."function ".st."FunctionName".et." (".st.et.")<CR>{<CR>".st.et."<CR>}<CR>".st.et
exec "Snippet _if if ( ".st."condition".et." )<CR>{<CR>".st.et."<CR>}<CR>".st.et
exec "Snippet _else else<CR>{<CR>".st.et."<CR>}<CR>".st.et
exec "Snippet _array $".st."arrayName".et." = array( '".st.et."',".st.et." );".st.et
exec "Snippet $G $GLOBALS['".st."variable".et."']".st.et
exec "Snippet _req require('".st."file".et."');<CR>".st.et
exec "Snippet _for for ($".st."i".et."=".st.et."; $".st."i".et." < ".st.et."; $".st."i".et."++)<CR>{<CR>".st.et."<CR>}<CR>".st.et
exec "Snippet _while while (".st."condtions".et.")<CR>{<CR>".st.et."<CR>}<CR>".st.et



exec "Snippet _getini Q::getIni('".st."option".et."')".st.et
exec "Snippet _find ".st."Model".et."::find(".st."conditions".et.")".st.et

