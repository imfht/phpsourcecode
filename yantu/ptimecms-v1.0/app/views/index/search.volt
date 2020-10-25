<div id="content_body">
    <div id="container" class="widthAuto">
        <div class="container_main">
            <h3>关于{{content}}的搜素结果：</h3>
            <!-- 相关文章 -->
            {% if articles |length %}
            <div class="model_header">
                <h3 class="model_name left">
                    <span class="model_text">相关文章</span>
                </h3>
            </div>
            <div class="model_body">
                {% if articles is defined %}
                    {% for one in articles %}
                        {% if loop.first %}
                            {% if one.img_dir is defined %}
                            <div id="hot_news"　class="model_list_picture_word">
                                <div class="left picture">
                                    <img src="{{ one.img_dir }}">
                                </div>
                                <div class="right word">
                                    <span class="article_info">
                                        <?= $this->tag->linkTo("article/".$article->id, $article->title) ?>
                                    </span>
                                    <span class="article_time">{{ article.created_at  }}</span>
                                    {% if one.description is defined %}
                                    <div class="clear"></div>
                                    <p class="word_text">{{ one.description }}</p>
                                    {% endif %}
                                </div>
                            </div>
                            {% endif %}
                        {% endif %}
                        {% break %}
                    {% endfor %}
                {% endif %}
                <div class="clear"></div>
                <div class="model_list_word" style="height:auto">
                    <ul id="page_more_article">
                        {% for one in articles %}
                            <li>
                                <span class="article_info">
                                <?= $this->tag->linkTo("article/".$one->id, $one->title) ?>
                                </span>
                                <span class="article_time">{{ one.created_at  }}</span>
                            </li>
                        {% endfor %}
                    </ul>
                </div>

                {% if articles |length == 15%}
                <div class="page_more" onclick = "getMore('article','{{content}}',0)">
                    下一页&nbsp{{ image("img/down_arrow.png") }}
                </div>
                <div class="img_loading">
                    {{ image("img/loading.gif")}}
                </div>
                {% endif %}
            </div>
            {% endif %}

            <!-- 相关专题 -->
            {% if topics|length %}
            <div class="model_header">
                <h3 class="model_name left">
                    <span class="model_text">相关专题</span>
                </h3>
            </div>
            <div class="model_body">
                <div class="model_list_word" style="height:auto">
                    <div class="l-box">
                        <ul  id="page_more_topic">
                            {% for result in topics  %}
                                <li>
                                    <a href="../topic/{{ result.id }}">{{ result.title }}</a>
                                </li>
                            {% endfor %}
                        </ul>
                    </div>
                </div>
                {% if topics |length == 15%}
                <div class="page_more" onclick = "getMore('topic','{{content}}',0)">
                    下一页&nbsp{{ image("img/down_arrow.png") }}
                </div>
                {% endif %}
            </div>
            {% endif %}

            <!-- 相关相册 -->
            {% if albums|length %}
            <div class="model_header">
                <h3 class="model_name left">
                    <span class="model_text">相关相册:</span>
                </h3>
            </div>
            <div class="pure-g-r" id="page_more_album">
                {% if albums is defined %}
                    {% for one in albums %}
                        <div class="pure-u-1-3"> 
                            <div class="album album_box">
                                <a href="../album/{{ one.id }}"><img src="{{ one.img_dir }}"></a>
                                <div class="album_info">
                                    <h4>{{ one.title }}</h4>
                                </div>
                            </div>
                        </div>
                    {% endfor %}
                {% endif %}
            </div> 
            {% if albums |length == 15%}
            <div class="page_more" onclick = "getMore('album','{{content}}',0)">
                下一页&nbsp{{ image("img/down_arrow.png") }}
            </div>
            {% endif %}
            {% endif %}

            <!-- 相关链接 -->
            {% if links|length %}
            <div class="model_header">
                <h3 class="model_name left">
                    <span class="model_text">相关链接:</span>
                </h3>
            </div>
            <div class="model_body">
                <div class="model_list_word" style="height:auto">
                    <div class="l-box">
                        <ul id="page_more_link">
                            {% for result in links  %}
                                <li>
                                    <a href="{{ result.url }}">{{ result.title }}</a>
                                </li>
                            {% endfor %}
                        </ul>
                    </div>
                </div>
            </div>
            {% if albums |length == 15%}
            <div class="page_more" onclick = "getMore('link','{{content}}',0)">
                下一页&nbsp{{ image("img/down_arrow.png") }}
            </div>
            {% endif %}
            {% endif %}

        </div>
    </div>
</div>