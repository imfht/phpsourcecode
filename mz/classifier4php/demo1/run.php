<?php
/**
 * User: djunny
 * Date: 2015/11/29
 * Time: 15:41
 */

error_reporting(E_ERROR);
// 定义 word2vec 路径
define('EXE_WORD2VEC', 'word2vec.exe');

// 加载类库
include '../include/phpanalysis.class.php';
include '../include/encoding.class.php';
include '../include/func.php';

// 开始从样本中取训练集
train();

/**
 * 训练完以后，我们需要把训练好的结果归类。
 *
 * 例如，我们训练了小说古代和现代的结果集：
 *
 * source/古代.txt
 * source/现代.txt
 *
 * 那么，我们 把"小说年代" 译为 age，打开 source_data 目录
 *
 * 建立一个 age 目录，
 *
 * 然后把古代.txt 和 现代.txt 两个 文件保存在 age 目录
 *
 * 此时，便可以开始测试识别结果
 */

// 开始从 source_data 中取训练结果， source_target 目录中取要识别的数据
analysis('source_target/', 'source_data/');

/**
 * 样本数据所在目录，每一个分类为一个子目录
 * 如果需要重新训练，请删除 source 根目录下 的 log 和 txt
 *
 * @param string $source_dir
 */
function train($source_dir = 'source/') {
    //
    $cmd_exe = EXE_WORD2VEC;

    // 加入一些没有用的词频
    $unuse_data = file('unuse.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $unuse_data = array_flip($unuse_data);

    // 载入目录下所有目录
    $dirs = array_diff(scandir($source_dir), array('..', '.'));
    foreach ($dirs as $dir) {
        $type_dir = $source_dir . $dir . '/';
        if (!is_dir($type_dir)) {
            continue;
        }
        // 搜索当前目录下所有文件
        $files = array_diff(scandir($type_dir), array('..', '.'));
        $source_file = $source_dir . '_' . $dir . '.log';
        // 如果训练 log 已经存在，那么退出
        if (!$files || is_file($source_file)) {
            l('跳过训练, Log文件存在', $dir);
            continue;
        }
        // 清空历史文件
        file_put_contents($source_file, '');
        foreach ($files as $file) {
            // 训练完后的结果保存文件
            $out_file = $source_dir . $dir . '.txt';
            $out = $return = '';
            if (is_file($out_file)) {
                l('跳过训练, 结果文件存在', $dir);
                continue;
            }
            // 打开当前文件
            $lines = file($type_dir . $file);
            // 过滤重复行
            $lines = array_flip(array_flip($lines));
            $count = count($lines);
            $content = '';
            // ********* 这里视具体业务而定 ********
            // 取前 1000行
            for ($i = 0; $i < min(1000, $count); $i++) {
                $content .= $lines[$i];
            }
            // 转 utf8 编码
            $content = encoding::iconv($content);
            // 分词，根据词性取回结果
            $tags = get_trans_data($content);
            // 过滤无用词
            foreach ($tags as $index => $tag) {
                if (isset($unuse_data[$tag])) {
                    unset($tags[$index]);
                }
            }
            // 保存
            if ($tags) {
                $fp = fopen($source_file, 'a+');
                fwrite($fp, implode(" ", $tags) . " ");
                fclose($fp);
            }
        }

        // 调用 word2vec 来分类
        $run_cmd = "%exe% -train %in% -output %out% -cbow 0 -size 200 -window 5 -negative 0 -hs 1 -sample 1e-3 -threads 16 -classes 500";
        $run_cmd = str_replace('%exe%', $cmd_exe, $run_cmd);
        $run_cmd = str_replace('%out%', $out_file, $run_cmd);
        $run_cmd = str_replace('%in%', $source_file, $run_cmd);

        exec($run_cmd, $out, $return);

        // 打开分类后的文件
        $content = file_get_contents($out_file);
        $content = explode("\n", $content);
        $arr = array();
        // 跳过第一行
        for ($i = 1, $l = count($content); $i < $l; $i++) {
            $line = explode(" ", trim($content[$i]));
            if (!$line[0]) {
                continue;
            }
            $arr[$line[0]] = $line[1];
        }
        // 排序结果 以及 简单计算每个词的分类
        $arr = array_keys($arr);
        $cnt = 10000;
        $num = 0;
        $new_arr = array();
        foreach ($arr as $v) {
            if (isset($tag_set[$v])) {
                continue;
            }
            $score = round(max($cnt - $num * 1.3, 5), 3);
            $new_arr[] = $v . ' ' . $score . ' ';
            $num++;
            //设置上限
            if ($num > $cnt) {
                break;
            }
        }

        // 将词和分数写入结果文件
        $fp = fopen($out_file, 'w+');
        fputs($fp, trim(implode("", $new_arr)));
        fclose($fp);
        unset($arr, $new_arr, $content);

    }
}

/**
 *
 * 分类过程
 *
 * @param string $source_target 目标要识别的内容
 * @param string $source_data   多个训练集保存目录(每个子目录一个分类,子目录代表要返回结果下标)
 */
function analysis($source_target = 'source_target/', $source_data = 'source_data/') {
    // 取得需要加载的训练集文件
    $cate_types = array_diff(scandir($source_data), array('..', '.'));
    $cate_datas = array();

    foreach ($cate_types as $cate_type) {
        // 按分类加载每一个训练集
        $source_path = $source_data . $cate_type . '/';
        $cate_files = array_diff(scandir($source_path), array('..', '.'));
        $cate_count = 0;
        foreach ($cate_files as $data_file) {
            $cate = str_replace('.txt', '', encoding::iconv($data_file));
            l('LoadSourceData', $cate_type, $cate);
            $datas = file_get_contents($source_path . $data_file);
            $tag_data = array();
            $tags = explode(' ', trim($datas));
            for ($j = 0, $k = count($tags); $j < $k; $j += 2) {
                $tag_data[$tags[$j]] = $tags[$j + 1];
            }
            $cate_datas[$cate_type][$cate] = $tag_data;
            $cate_count++;
        }
    }
    // 读取所有需要训练的数据
    $target_files = array_diff(scandir($source_target), array('..', '.'));
    foreach ($target_files as $target) {
        $lines = file($source_target . $target);
        $lines = array_flip(array_flip($lines));
        $count = count($lines);
        $content = '';

        // ********* 这里视具体业务而定，我们当前识别的是小说古现代，所以取前后500行来识别，够了 ********
        $min_len = 500;
        for ($i = 0; $i < min($min_len, $count); $i++) {
            $content .= encoding::iconv($lines[$i]);
        }

        if ($count - 1 > $i) {
            for ($i = $count - 1, $l = max($min_len, $count - $min_len); $i >= $l; $i--) {
                $content .= encoding::iconv($lines[$i]);
            }
        }

        // 过滤词性，得到骨干词
        $tags = get_trans_data($content);

        // 简单的计算分数
        $match_cates = array();
        foreach ($cate_datas as $cate_type => $trains_datas) {
            $max_score = 0;
            $max_cate = '';
            foreach ($trains_datas as $cate => $tag_data) {
                $score = 0;
                foreach ($tags as $tag) {
                    $score += $tag_data[$tag];
                }
                if ($score > $max_score) {
                    $max_score = $score;
                    $max_cate = $cate;
                }
                //l('Scan', $utf8_file, $cate, $score);
            }
            $match_cates[$cate_type] = $max_cate;
        }
        // 打印出结果
        l('Match', $match_cates, $target);
    }

}
