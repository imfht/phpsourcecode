<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-04-24 18:42
 */
namespace Notadd\Mall\Handlers\Administration\Upload;

use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;

/**
 * Class UploadHandler.
 */
class UploadHandler extends Handler
{
    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * UploadHandler constructor.
     *
     * @param \Illuminate\Container\Container   $container
     * @param \Illuminate\Filesystem\Filesystem $filesystem
     */
    public function __construct(Container $container, Filesystem $filesystem)
    {
        parent::__construct($container);
        $this->messages->push($this->translator->trans('上传图片成功！'));
        $this->files = $filesystem;
    }

    /**
     * Execute Handler.
     */
    public function execute()
    {
        $this->validate($this->request, [
            'file' => [
                Rule::image(),
                Rule::required(),
            ],
        ], [
            'file.image'    => '上传文件格式必须为图片格式！',
            'file.required' => '必须上传一个文件！',
        ]);
        $avatar = $this->request->file('file');
        $hash = hash_file('md5', $avatar->getPathname(), false);
        $dictionary = $this->pathSplit($hash, '12', Collection::make([
            static_path(),
            'uploads',
        ]))->implode(DIRECTORY_SEPARATOR);
        $file = Str::substr($hash, 12, 20) . '.' . $avatar->getClientOriginalExtension();
        if (!$this->files->exists($dictionary . DIRECTORY_SEPARATOR . $file)) {
            $avatar->move($dictionary, $file);
        }
        $this->data['path'] = $this->pathSplit($hash, '12,20', Collection::make([
                'uploads',
            ]))->implode('/') . '.' . $avatar->getClientOriginalExtension();

        return true;
    }

    /**
     * String split handler.
     *
     * @param string $path
     * @param string $dots
     * @param null   $data
     *
     * @return \Illuminate\Support\Collection|null
     */
    protected function pathSplit($path, $dots, $data = null)
    {
        $dots = explode(',', $dots);
        $data = $data ? $data : new Collection();
        $offset = 0;
        foreach ($dots as $dot) {
            $data->push(Str::substr($path, $offset, $dot));
            $offset += $dot;
        }

        return $data;
    }
}
