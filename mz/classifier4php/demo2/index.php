<?php
/**
 * User: djunny
 * Date: 2019-11-23
 * Time: 00:27
 * Desc: 本演示用于归类常见的新闻（支持二级）
 * 训练数据集以及过滤无用词文件已经在 train 目录
 *
 */

error_reporting(E_ERROR);

// 加载类库txt
include '../include/phpanalysis.class.php';
include '../include/encoding.class.php';
include '../include/func.php';

class auto_category
{
    const TRAIN_DIR = './train/';

    public static function run($text) {
        return self::start_train($text);
    }

    /**
     * start train
     *
     * @param $text
     * @param $callback
     */
    protected static function start_train($text, $dir = '') {
        $score_list = [];
        $tags       = self::get_tags($text);
        $files      = glob(($dir ?: self::TRAIN_DIR) . '*.txt');
        foreach ($files as $file) {
            $train_data                      = self::load_train_file($file);
            $score_set                       = self::train_score($tags, $train_data);
            $score_list[$train_data['cate']] = [
                'score' => $score_set['total'],
                'dir'   => $train_data['child_dir'],
            ];
        }

        uasort($score_list, function ($a, $b) {
            return $a['score'] >= $b['score'] ? -1 : 1;
        });

        // 取前三名子类
        foreach ($score_list as &$score_set) {
            if (is_dir($score_set['dir'])) {
                $score_set['child'] = self::start_train($text, $score_set['dir']);
            }
        }
        return $score_list;
    }

    /**
     * get train score
     *
     * @param $tags
     * @param $train_data
     *
     * @return array
     */
    protected static function train_score(&$tags, &$train_data) {
        $score       = 0;
        $score_count = 0;
        $exists_tags = &$train_data['tags'];
        $bad_words   = $train_data['bad_words'];
        foreach ($tags as $tag => $num) {
            if (isset($exists_tags[$tag]) && !isset($bad_words[$tag])) {
                $tmp_score = $exists_tags[$tag];
                $score     += $tmp_score;
                $score_count++;
            }
        }
        return ['total' => $score, 'count' => $score_count];
    }

    /**
     * load train file
     *
     * @param $file
     *
     * @return array
     */
    protected static function load_train_file($file) {
        $fp         = fopen($file, 'r+');
        $tag_name   = trim(fgets($fp));
        $train_data = trim(fgets($fp, 51200));
        fclose($fp);
        $train_data = explode(" ", $train_data);
        $tags       = [];
        for ($i = 0; $i < count($train_data); $i += 2) {
            $tags[$train_data[$i]] = $train_data[$i + 1];
        }
        // load .set file
        $bad_words = [];
        if (is_file($file . '.set')) {
            $bad_words = json_decode(file_get_contents($file . '.set'), 1);
        }

        $child_dir = strtr(basename($file), ['.txt' => '']);
        $child_dir = dirname($file) . '/' . $child_dir . '/';


        return [
            'cate'      => $tag_name,
            'tags'      => $tags,
            'bad_words' => $bad_words,
            'child_dir' => $child_dir
        ];
    }

    /**
     * get tags
     *
     * @param $content
     *
     * @return array
     */
    protected static function get_tags($content) {
        //初始化类
        static $kwAnalysis = NULL;
        if ($kwAnalysis == NULL) {
            $kwAnalysis = new PhpAnalysis('utf-8', 'utf-8', TRUE);
        }
        //执行分词
        $kwAnalysis->SetSource($content);
        $kwAnalysis->StartAnalysis(TRUE);
        $keys = preg_replace('/,[\x{4e00}-\x{9fa5}],|^[\x{4e00}-\x{9fa5}],|,[\x{4e00}-\x{9fa5}]$/u', '', $kwAnalysis->GetFinallyResult(','));
        //delete specialchars
        $keys = str_replace(
            array('!', '"', '#', '$', '%', '&', '\'', '(', ')', '*',
                  '+', ', ', '-', '.', '/', ':', ';', '<', '=', '>',
                  '?', '@', '[', '\\', ']', '^', '_', '`', '{', '|',
                  '}', '~', '；', '﹔', '︰', '﹕', '：', '，', '﹐', '、',
                  '【', '】', '—',
                  '．', '﹒', '˙', '·', '。', '？', '！', '～', '‥', '‧',
                  '′', '〃', '〝', '〞', '‵', '‘', '’', '『', '』', '「',
                  '」', '“', '”', '…', '❞', '❝', '﹁', '﹂', '﹃', '﹄',
                  '《', '》'
            ),
            '',
            $keys);
        while (strpos($keys, ',,') !== FALSE) {
            $keys = str_replace(',,', ',', $keys);
        }
        if (substr($keys, 0, 1) == ',') {
            $keys = substr($keys, 1);
        }

        $keys = explode(',', $keys);
        //sort keyword
        $arrs = array();
        foreach ($keys as $key) {
            isset($arrs[$key]) ? $arrs[$key]++ : $arrs[$key] = 1;
        }
        arsort($arrs);
        return $arrs;
    }
}

if ($_POST['text']) {
    $result = auto_category::run($_POST['text']);
}

$colors = [
    'red',
    'purple',
    'pink'
];

?>
<!DOCTYPE HTML>
<html lang="zh" class="wap">
<head>
    <meta charset="utf-8">
    <title>自动新闻归类测试 - classifier4php</title>
    <style>
        table {
            border: 1px solid #DDD
        }

        td {
            padding: 10px;
            border-bottom: 1px solid #DDD;
        }
    </style>
</head>
<body>
<div style="max-width:1200px;margin:0 auto;">
    <h1>自动新闻归类测试 - classifier4php</h1>
    <div style="display: flex;">
        <form style="width:30%" method="post">
            <textarea placeholder="请输入要归类的新闻内容" name="text"
                      style="width:100%;height:350px;border:1px solid #DDD;"><?= htmlspecialchars($_POST['text']) ?></textarea>
            <button type="submit"
                    style="background:#000;color:#FFF;border:0;display:block;margin:10px 0;padding:10px 15px;float:right">
                提交
            </button>
        </form>
        <table style="width:60%;margin-left:20px">
            <TR>
                <Td>分类名</Td>
                <Td>打分(得分越高，越可能是该分类)</Td>
            </TR>

            <?php
            $parent_index = 0;
            foreach ($result as $cate => $data) {
                ?>
                <Tr style="<?php if (isset($colors[$parent_index++])) {
                    echo 'color:' . $colors[$parent_index - 1];
                } else {
                    echo 'color:gray';
                } ?>">
                    <td><?= $cate ?></td>
                    <td><?= $data['score'] ?></td>
                </Tr>
                <?php $child_index = 0;
                foreach ($data['child'] as $child_cate => $child_data) { ?>
                    <Tr style="<?php if ($parent_index < 3 && isset($colors[$child_index++])) {
                        echo 'color:' . $colors[$child_index - 1];
                    } else {
                        echo 'color:gray';
                    } ?>">
                        <Td>&nbsp;&nbsp;|-<?= $child_cate ?></Td>
                        <Td>&nbsp;&nbsp;|-<?= $child_data['score'] ?></Td>
                    </Tr>
                <? } ?>
            <? } ?>
        </table>
    </div>
</div>