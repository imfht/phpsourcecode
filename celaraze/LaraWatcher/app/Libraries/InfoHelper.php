<?php


namespace App\Libraries;


use Illuminate\Support\Facades\File;

class InfoHelper
{
    /**
     * 更新ENV文件的键值
     * @param array $data
     */
    public static function setEnv(array $data)
    {
        $envPath = base_path() . DIRECTORY_SEPARATOR . '.env';
        $contentArray = collect(file($envPath, FILE_IGNORE_NEW_LINES));
        $contentArray->transform(function ($item) use ($data) {
            foreach ($data as $key => $value) {
                if (str_contains($item, $key)) {
                    return $key . '=' . $value;
                }
            }
            return $item;
        });
        $content = implode("\n", $contentArray->toArray());
        File::put($envPath, $content);
    }
}
