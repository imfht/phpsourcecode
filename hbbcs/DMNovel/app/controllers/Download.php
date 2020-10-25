<?php

/**
 * Created by PhpStorm.
 * User: joe
 * Date: 17-2-27
 * Time: 下午5:08
 */
class Download extends CI_Controller
{
    private $filePath;
    private $story;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('story_model');
        $this->load->model('chapter_model', 'chapter');
    }

    public function index()
    {
        $id=$this->input->post('id');
        $type=$this->input->post('type');
        if (!$id) {
            show_json(['error'=>'未找到要下载的小说。']);
            return;
        }

        $this->filePath = 'books/download/' . (substr($id, 0, -4) ? : 0);
        $this->story = $this->story_model->get($id);

        if (!$this->story) {
            show_json(['error'=>'未找到小说内容']);
            exit();
        }

        if (!file_exists($this->filePath)) {
            mkdirs($this->filePath);
        }

        $file=$this->$type($id);
        show_json(['url' => site_url($file)]);
    }

    public function txt($id)
    {
    }

    public function epub($id)
    {
        $fileName = $this->filePath . '/' . $id . '.epub';
        $file     = fopen($this->filePath . '/' . $id . '_id.txt', 'w+');
        $sort     = fgets($file);
        $this->db->where('order >', $sort);
        $chapters = $this->db->where('story_id', $id)->order_by('order', 'asc')->get('chapter')->result_array();
        if ($chapters) {
            fputs($file, $chapters[count($chapters) - 1]['order']);
            $this->load->library('epub');
            $this->epub->temp_folder = 'books/download/';
            $this->epub->epub_file   = $fileName;
            $this->epub->title       = $this->story['title'];
            $this->epub->AddImage($this->story['image']?:'books/default.jpg', 'image/jpeg', true);
            foreach ($chapters as $chapter) {
                $content='<h2>'.$chapter['title'].'</h2><br />'.$chapter['content'];
                $this->epub->AddPage($content, false, $chapter['title']);
            }

            if ($this->epub->error) {
                show_json(['error'=>$this->epub->error]);
                exit();
            }
            $this->epub->CreateEPUB();

            if ($this->epub->error) {
                show_json(['error'=>$this->epub->error]);
                exit();
            }
        }
        fclose($file);

        return $fileName;
    }
}
