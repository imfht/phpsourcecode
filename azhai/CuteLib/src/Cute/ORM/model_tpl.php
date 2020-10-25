<?php
if ($ns):
    echo 'namespace ' . trim($ns, '\\') . ";\n";
endif;
?>

use \Cute\ORM\Model;


/**
* <?= $name ?> 模型
*/
class <?= $name ?> extends Model
{
<?php if ($mixin):
    echo '    use \\' . $mixin . ';' . "\n";
endif; ?>
<?php
foreach ($fields as $field => $default):
    if (in_array($field, $pkeys)):
?>
    protected $<?= $field ?> = NULL;
<?php else: ?>
    public $<?= $field ?> = <?= var_export($default, true) ?>;
<?php
    endif;
endforeach;
?>

    public static function getTable()
    {
        return '<?= $table ?>';
    }

    public static function getPKeys()
    {
        return ['<?= implode("', '", $pkeys) ?>'];
    }

<?php if (!$mixin || !$behaviors_in_mixin): ?>
    public function getBehaviors()
    {
        return [];
    }
<?php endif; ?>
}
