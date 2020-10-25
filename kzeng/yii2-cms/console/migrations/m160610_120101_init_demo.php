<?php

use yii\db\Migration;

class m160610_120101_init_demo extends Migration
{
    public function up()
    {
        $this->insert('{{%menu}}', ['id' => 'main-menu', 'created_by' => 1]);
        $this->insert('{{%menu_lang}}', ['menu_id' => 'main-menu', 'language' => 'en-US', 'title' => 'Main Menu']);

        $this->insert('{{%menu_link}}', ['id' => 'home', 'menu_id' => 'main-menu', 'link' => '/site/index', 'alwaysVisible' => 1, 'created_by' => 1, 'order' => 1]);
        $this->insert('{{%menu_link}}', ['id' => 'about', 'menu_id' => 'main-menu', 'link' => '/site/about', 'alwaysVisible' => 1, 'created_by' => 1, 'order' => 9]);
        $this->insert('{{%menu_link}}', ['id' => 'test-page', 'menu_id' => 'main-menu', 'link' => '/site/test', 'alwaysVisible' => 1, 'created_by' => 1, 'order' => 2]);
        $this->insert('{{%menu_link}}', ['id' => 'contact', 'menu_id' => 'main-menu', 'link' => '/site/contact', 'alwaysVisible' => 1, 'created_by' => 1, 'order' => 10]);

        $this->insert('{{%menu_link_lang}}', ['link_id' => 'home', 'label' => 'Home', 'language' => 'en-US']);
        $this->insert('{{%menu_link_lang}}', ['link_id' => 'about', 'label' => 'About', 'language' => 'en-US']);
        $this->insert('{{%menu_link_lang}}', ['link_id' => 'test-page', 'label' => 'Test Page', 'language' => 'en-US']);
        $this->insert('{{%menu_link_lang}}', ['link_id' => 'contact', 'label' => 'Contact', 'language' => 'en-US']);

        $this->insert('{{%page}}', ['id' => '1', 'slug' => 'test', 'created_by' => 1, 'updated_by' => 1, 'status' => 1, 'comment_status' => 0,
            'published_at' => '1440720000', 'created_at' => '1440763228', 'updated_at' => '1440771930']);

        $this->insert('{{%page_lang}}', ['page_id' => '1', 'title' => 'Test Page', 'language' => 'en-US',
            'content' => '<p style="text-align: justify;">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer id ullamcorper nibh, id blandit ante. Suspendisse non ante commodo, finibus nibh at, sollicitudin felis. Aliquam interdum eros eget tempor porta. Quisque viverra velit magna, ac eleifend mi vehicula nec. Curabitur sollicitudin metus eget odio posuere pulvinar. Nullam vestibulum massa ac dolor mattis pharetra. Vestibulum finibus non massa ut cursus.</p>' .
                '<p style="text-align: justify;">Proin eget ullamcorper elit, ac luctus ex. Pellentesque mattis nibh nec nunc fermentum lobortis. Cras malesuada ipsum eget massa pulvinar euismod. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam pellentesque, tortor in efficitur semper, tellus lorem blandit augue, sed euismod purus velit nec libero. Pellentesque dictum faucibus augue, ac rutrum velit. Quisque tristique neque sit amet turpis consectetur rutrum. Aliquam ac vulputate mauris.</p>']);

        $this->insert('{{%post_category}}', ['id' => '2', 'slug' => 'first-category', 'created_by' => 1, 'updated_by' => 1, 'visible' => 1, 'left_border' => 42107523, 'right_border' => 84215044, 'depth' => 1, 'created_at' => time(), 'updated_at' => time()]);
        $this->insert('{{%post_category_lang}}', ['post_category_id' => '1', 'title' => 'First Category', 'language' => 'en-US']);

        $this->insert('{{%post_tag}}', ['id' => '1', 'slug' => 'yee-cms', 'created_by' => 1, 'updated_by' => 1, 'created_at' => time(), 'updated_at' => time()]);
        $this->insert('{{%post_tag}}', ['id' => '2', 'slug' => 'yii2', 'created_by' => 1, 'updated_by' => 1, 'created_at' => time(), 'updated_at' => time()]);

        $this->insert('{{%post_tag_lang}}', ['post_tag_id' => '1', 'title' => 'YeeCMS', 'language' => 'en-US']);
        $this->insert('{{%post_tag_lang}}', ['post_tag_id' => '2', 'title' => 'Yii2', 'language' => 'en-US']);

        $this->insert('{{%post}}', ['id' => '1', 'slug' => 'integer-id-ullamcorper-nibh', 'category_id' => '1', 'created_by' => 1, 'updated_by' => 1, 'status' => 1, 'comment_status' => 1,
            'published_at' => '1440720000', 'created_at' => '1440763228', 'updated_at' => '1440771930']);

        $this->insert('{{%post_lang}}', ['post_id' => '1', 'title' => 'Integer id ullamcorper nibh', 'language' => 'en-US',
            'content' => '<p style="text-align: justify;">Integer id ullamcorper nibh, id blandit ante. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse non ante commodo, finibus nibh at, sollicitudin felis. Aliquam interdum eros eget tempor porta. Quisque viverra velit magna, ac eleifend mi vehicula nec. Curabitur sollicitudin metus eget odio posuere pulvinar. Nullam vestibulum massa ac dolor mattis pharetra. Vestibulum finibus non massa ut cursus.</p>' .
                '<p style="text-align: justify;">Proin eget ullamcorper elit, ac luctus ex. Pellentesque mattis nibh nec nunc fermentum lobortis. Cras malesuada ipsum eget massa pulvinar euismod. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam pellentesque, tortor in efficitur semper, tellus lorem blandit augue, sed euismod purus velit nec libero. Pellentesque dictum faucibus augue, ac rutrum velit. Quisque tristique neque sit amet turpis consectetur rutrum. Aliquam ac vulputate mauris.</p>']);

        $this->insert('{{%post}}', ['id' => '2', 'slug' => 'proin-eget-ullamcorper-elit', 'category_id' => '1', 'created_by' => 1, 'updated_by' => 1, 'status' => 1, 'comment_status' => 1,
            'published_at' => '1440720000', 'created_at' => '1440763228', 'updated_at' => '1440771930']);

        $this->insert('{{%post_lang}}', ['post_id' => '2', 'title' => 'Proin eget ullamcorper elit', 'language' => 'en-US',
            'content' => '<p style="text-align: justify;">Suspendisse non ante commodo, finibus nibh at, sollicitudin felis. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer id ullamcorper nibh, id blandit ante. Aliquam interdum eros eget tempor porta. Quisque viverra velit magna, ac eleifend mi vehicula nec. Curabitur sollicitudin metus eget odio posuere pulvinar. Nullam vestibulum massa ac dolor mattis pharetra. Vestibulum finibus non massa ut cursus.</p>' .
                '<p style="text-align: justify;">Proin eget ullamcorper elit, ac luctus ex. Pellentesque mattis nibh nec nunc fermentum lobortis. Cras malesuada ipsum eget massa pulvinar euismod. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam pellentesque, tortor in efficitur semper, tellus lorem blandit augue, sed euismod purus velit nec libero. Pellentesque dictum faucibus augue, ac rutrum velit. Quisque tristique neque sit amet turpis consectetur rutrum. Aliquam ac vulputate mauris.</p>']);

        $this->insert('{{%post_tag_post}}', ['post_id' => 1, 'tag_id' => 1]);
        $this->insert('{{%post_tag_post}}', ['post_id' => 1, 'tag_id' => 2]);
        $this->insert('{{%post_tag_post}}', ['post_id' => 2, 'tag_id' => 1]);
        
        $this->insert('{{%seo}}', ['url' => '/en', 'title' => 'Homepage', 'author' => 'Site Owner', 'keywords' => 'yii2, cms, yeecms', 'description' => 'Seo meta description', 'index' => 1, 'follow' => 1, 'created_by' => 1, 'updated_by' => 1, 'created_at' => '1452544164', 'updated_at' => '1452545049']);

    }

    public function down()
    {
        $this->delete('{{%post}}', ['slug' => 'integer-id-ullamcorper-nibh']);
        $this->delete('{{%post}}', ['slug' => 'proin-eget-ullamcorper-elit']);
        $this->delete('{{%page}}', ['slug' => 'test']);
        $this->delete('{{%menu_link}}', ['id' => 'home']);
        $this->delete('{{%menu_link}}', ['id' => 'about']);
        $this->delete('{{%menu_link}}', ['id' => 'contact']);
        $this->delete('{{%menu}}', ['id' => 'main-menu']);
    }
}
