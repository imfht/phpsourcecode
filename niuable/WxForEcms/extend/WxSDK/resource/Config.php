<?php
namespace WxSDK\resource;
class Config{
    public static $app_get_access_token = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=APPID&secret=APPSECRET';

    public static $app_get_ips = 'https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token=ACCESS_TOKEN';

    public static $app_network_check = 'https://api.weixin.qq.com/cgi-bin/callback/check?access_token=ACCESS_TOKEN';

    /**
     * 菜单-删除所有
     * @var string
     */
    public static $delete_menu = 'https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=ACCESS_TOKEN';
    public static $create_menu = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token=ACCESS_TOKEN';
    public static $delete_condition_menu = 'https://api.weixin.qq.com/cgi-bin/menu/delconditional?access_token=ACCESS_TOKEN';
    public static $create_condition_menu = 'https://api.weixin.qq.com/cgi-bin/menu/addconditional?access_token=ACCESS_TOKEN';
    public static $trymatch_condition_menu = 'https://api.weixin.qq.com/cgi-bin/menu/trymatch?access_token=ACCESS_TOKEN';
    public static $get_menu = 'https://api.weixin.qq.com/cgi-bin/menu/get?access_token=ACCESS_TOKEN';
    
    /**
     * 客服-新增
     * @var string
     */
    public static $add_kf='https://api.weixin.qq.com/customservice/kfaccount/add?access_token=ACCESS_TOKEN'; 
    public static $update_kf='https://api.weixin.qq.com/customservice/kfaccount/update?access_token=ACCESS_TOKEN'; 
    public static $delete_kf='https://api.weixin.qq.com/customservice/kfaccount/del?access_token=ACCESS_TOKEN'; 
    public static $kf_update_head_image='http://api.weixin.qq.com/customservice/kfaccount/uploadheadimg?access_token=ACCESS_TOKEN&kf_account=KFACCOUNT'; 
    public static $kf_get_list='https://api.weixin.qq.com/cgi-bin/customservice/getkflist?access_token=ACCESS_TOKEN'; 
    public static $kf_send_msg='https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=ACCESS_TOKEN'; 
    /**
     * 发送客服输入状态
     * @var string
     */
    public static $kf_send_input_state='https://api.weixin.qq.com/cgi-bin/message/custom/typing?access_token=ACCESS_TOKEN'; 
    public static $kf_creat_session='https://api.weixin.qq.com/customservice/kfsession/create?access_token=ACCESS_TOKEN'; 
    public static $kf_close_session='https: //api.weixin.qq.com/customservice/kfsession/close?access_token=ACCESS_TOKEN'; 
    public static $kf_get_session='https://api.weixin.qq.com/customservice/kfsession/getsession?access_token=ACCESS_TOKEN&openid=OPENID'; 
    public static $kf_get_session_list='https://api.weixin.qq.com/customservice/kfsession/getsessionlist?access_token=ACCESS_TOKEN&kf_account=KFACCOUNT'; 
    public static $kf_get_wait_case_list='https://api.weixin.qq.com/customservice/kfsession/getwaitcase?access_token=ACCESS_TOKEN'; 
    public static $kf_get_msg_list='https://api.weixin.qq.com/customservice/msgrecord/getmsglist?access_token=ACCESS_TOKEN'; 
    
    
    /**
     * 回复-自动回复规则
     * @var string
     */
    public static $reply_get_rules='https://api.weixin.qq.com/cgi-bin/get_current_autoreply_info?access_token=ACCESS_TOKEN'; 
    
    /**
     * 群发-预览
     * @var string
     */
    public static $mass_preview = 'https://api.weixin.qq.com/cgi-bin/message/mass/preview?access_token=ACCESS_TOKEN';
    public static $do_mass_by_tag = 'https://api.weixin.qq.com/cgi-bin/message/mass/sendall?access_token=ACCESS_TOKEN';
    public static $do_mass_by_ids = 'https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token=ACCESS_TOKEN';
    public static $delete_mass = 'https://api.weixin.qq.com/cgi-bin/message/mass/delete?access_token=ACCESS_TOKEN'; 
    public static $get_mass_status = 'https://api.weixin.qq.com/cgi-bin/message/mass/get?access_token=ACCESS_TOKEN';
    public static $get_mass_speed = 'https://api.weixin.qq.com/cgi-bin/message/mass/speed/get?access_token=ACCESS_TOKEN';
    public static $set_mass_speed = 'https://api.weixin.qq.com/cgi-bin/message/mass/speed/set?access_token=ACCESS_TOKEN';
    public static $mass_open_comment = 'https://api.weixin.qq.com/cgi-bin/comment/open?access_token=ACCESS_TOKEN';
    public static $mass_close_comment = 'https://api.weixin.qq.com/cgi-bin/comment/close?access_token=ACCESS_TOKEN';
    public static $mass_get_comment = 'https://api.weixin.qq.com/cgi-bin/comment/list?access_token=ACCESS_TOKEN';
    /**
     * 标记为精选评论
     * @var string
     */
    public static $mass_comment_markelect = 'https://api.weixin.qq.com/cgi-bin/comment/markelect?access_token=ACCESS_TOKEN';
    public static $mass_comment_unmarkelect = 'https://api.weixin.qq.com/cgi-bin/comment/unmarkelect?access_token=ACCESS_TOKEN';
    /**
     * 删除评论
     * @var string
     */
    public static $mass_delete_comment = 'https://api.weixin.qq.com/cgi-bin/comment/delete?access_token=ACCESS_TOKEN';
    public static $mass_reply_comment = 'https://api.weixin.qq.com/cgi-bin/comment/reply/add?access_token=ACCESS_TOKEN';
    /**
     * 删除回复
     * @var string
     */
    public static $mass_reply_comment_delete = 'https://api.weixin.qq.com/cgi-bin/comment/reply/delete?access_token=ACCESS_TOKEN';
    
    /**
     * 上传素材-群发图文的图片
     * @var string
     */
    public static $up_img_for_news_content='https://api.weixin.qq.com/cgi-bin/media/uploadimg?access_token=ACCESS_TOKEN';
    public static $up_news_for_mass = 'https://api.weixin.qq.com/cgi-bin/media/uploadnews?access_token=ACCESS_TOKEN'; 
    public static $update_news_forever = 'https://api.weixin.qq.com/cgi-bin/material/update_news?access_token=ACCESS_TOKEN';
    public static $up_media_short_time='https://api.weixin.qq.com/cgi-bin/media/upload?access_token=ACCESS_TOKEN&type=TYPE';
    public static $up_news_forever = 'https://api.weixin.qq.com/cgi-bin/material/add_news?access_token=ACCESS_TOKEN';
    public static $up_media_forever = 'https://api.weixin.qq.com/cgi-bin/material/add_material?access_token=ACCESS_TOKEN&type=TYPE';
    public static $up_video_for_mass = 'http://file.api.weixin.qq.com/cgi-bin/media/uploadvideo?access_token=ACCESS_TOKEN';

    /**
     * 图文-获取群发每日数据
     * @var string
     */
    public static $news_get_article_summary = 'https://api.weixin.qq.com/datacube/getarticlesummary?access_token=ACCESS_TOKEN';
    /**
     * 图文-获取群发总数据
     * @var string
     */
    public static $news_get_article_total = 'https://api.weixin.qq.com/datacube/getarticletotal?access_token=ACCESS_TOKEN';
    /**
     * 图文-获取统计数据
     * @var string
     */
    public static $news_get_user_read = 'https://api.weixin.qq.com/datacube/getuserread?access_token=ACCESS_TOKEN';
    /**
     * 图文-获取统计分时数据
     * @var string
     */
    public static $news_get_user_read_hour = 'https://api.weixin.qq.com/datacube/getuserreadhour?access_token=ACCESS_TOKEN';
    /**
     * 图文-获取分享转发数据
     * @var string
     */
    public static $news_get_user_share = 'https://api.weixin.qq.com/datacube/getusershare?access_token=ACCESS_TOKEN';
    /**
     * 图文-获取分享转发分时数据
     * @var string
     */
    public static $news_get_user_share_hour = 'https://api.weixin.qq.com/datacube/getusersharehour?access_token=ACCESS_TOKEN';

    /**
     * 消息-获取发送概况数据
     * @var string
     */
    public static $msg_get_upstream_msg = 'https://api.weixin.qq.com/datacube/getupstreammsg?access_token=ACCESS_TOKEN';
    public static $msg_get_upstream_msg_hour = 'https://api.weixin.qq.com/datacube/getupstreammsghour?access_token=ACCESS_TOKEN';
    public static $msg_get_upstream_msg_week = 'https://api.weixin.qq.com/datacube/getupstreammsgweek?access_token=ACCESS_TOKEN';
    public static $msg_get_upstream_msg_month = 'https://api.weixin.qq.com/datacube/getupstreammsgmonth?access_token=ACCESS_TOKEN';
    public static $msg_get_upstream_msg_dist = 'https://api.weixin.qq.com/datacube/getupstreammsgdist?access_token=ACCESS_TOKEN';
    public static $msg_get_upstream_msg_dist_week = 'https://api.weixin.qq.com/datacube/getupstreammsgdistweek?access_token=ACCESS_TOKEN';
    public static $msg_get_upstream_msg_dist_month = 'https://api.weixin.qq.com/datacube/getupstreammsgdistmonth?access_token=ACCESS_TOKEN';

    /**
     * 接口-获取接口分析数据
     * @var string
     */
    public static $interface_get_summary = 'https://api.weixin.qq.com/datacube/getinterfacesummary?access_token=ACCESS_TOKEN';
    /**
     * 接口-获取接口分析分时数据
     * @var string
     */
    public static $interface_get_summary_hour = 'https://api.weixin.qq.com/datacube/getinterfacesummaryhour?access_token=ACCESS_TOKEN';

    /**
     * 下载素材-临时
     * @var string
     */
    public static $get_media_short_time='https://api.weixin.qq.com/cgi-bin/media/get?access_token=ACCESS_TOKEN&media_id=MEDIA_ID';
    public static $get_media_forever='https://api.weixin.qq.com/cgi-bin/material/get_material?access_token=ACCESS_TOKEN';
    public static $del_media_forever='https://api.weixin.qq.com/cgi-bin/material/del_material?access_token=ACCESS_TOKEN'; 
    
    /**
     * 统计-获取永久素材总数
     * @var string
     */
    public static $get_media_total_forever='https://api.weixin.qq.com/cgi-bin/material/get_materialcount?access_token=ACCESS_TOKEN'; 
    public static $get_media_list_forever='https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token=ACCESS_TOKEN'; 
    
    /**
     * 模板-设置行业
     * @var string
     */
    public static $tpl_set_industry='https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token=ACCESS_TOKEN'; 
    public static $tpl_get_industry='https://api.weixin.qq.com/cgi-bin/template/get_industry?access_token=ACCESS_TOKEN'; 
    /**
     * 模板-从模板库中引入模板，以添加到正在使用的模板
     * @var string
     */
    public static $tpl_add_import='https://api.weixin.qq.com/cgi-bin/template/api_add_template?access_token=ACCESS_TOKEN'; 
    public static $tpl_get_list='https://api.weixin.qq.com/cgi-bin/template/get_all_private_template?access_token=ACCESS_TOKEN'; 
    public static $tpl_delete='https://api.weixin.qq.com/cgi-bin/template/del_private_template?access_token=ACCESS_TOKEN'; 
    public static $tpl_send_msg='https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=ACCESS_TOKEN'; 
    
    /**
     * 用户标签-新增
     * 
     * @var string
     */
    public static $user_tag_create = "https://api.weixin.qq.com/cgi-bin/tags/create?access_token=ACCESS_TOKEN";
    public static $user_tag_get = "https://api.weixin.qq.com/cgi-bin/tags/get?access_token=ACCESS_TOKEN";
    public static $user_tag_update = "https://api.weixin.qq.com/cgi-bin/tags/update?access_token=ACCESS_TOKEN";
    public static $user_tag_delete = "https://api.weixin.qq.com/cgi-bin/tags/delete?access_token=ACCESS_TOKEN";
    public static $user_get_by_tag = "https://api.weixin.qq.com/cgi-bin/user/tag/get?access_token=ACCESS_TOKEN";
    /**
     * 给用户打标签
     * @var string
     */
    public static $user_set_tag = "https://api.weixin.qq.com/cgi-bin/tags/members/batchtagging?access_token=ACCESS_TOKEN";
    /**
     * 取消标签
     * @var string
     */
    public static $user_set_untag = "https://api.weixin.qq.com/cgi-bin/tags/members/batchuntagging?access_token=ACCESS_TOKEN";
    public static $user_get_tag_by_openid = "https://api.weixin.qq.com/cgi-bin/tags/getidlist?access_token=ACCESS_TOKEN";
    public static $user_update_remark = "https://api.weixin.qq.com/cgi-bin/user/info/updateremark?access_token=ACCESS_TOKEN";
    public static $user_get_info = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=ACCESS_TOKEN&openid=OPENID&lang=LANG";
    public static $user_get_info_list = "https://api.weixin.qq.com/cgi-bin/user/info/batchget?access_token=ACCESS_TOKEN";
    public static $user_get_list = 'https://api.weixin.qq.com/cgi-bin/user/get?access_token=ACCESS_TOKEN&next_openid=NEXT_OPENID';
    public static $user_get_blacklist = 'https://api.weixin.qq.com/cgi-bin/tags/members/getblacklist?access_token=ACCESS_TOKEN';
    public static $user_add_blacklist = 'https://api.weixin.qq.com/cgi-bin/tags/members/batchblacklist?access_token=ACCESS_TOKEN';
    public static $user_delete_blacklist = 'https://api.weixin.qq.com/cgi-bin/tags/members/batchunblacklist?access_token=ACCESS_TOKEN';
    public static $user_getusersummary = 'https://api.weixin.qq.com/datacube/getusersummary?access_token=ACCESS_TOKEN';
    public static $user_getusercumulate = 'https://api.weixin.qq.com/datacube/getusercumulate?access_token=ACCESS_TOKEN';

    /**
     * 二维码
     * @var string
     */
    public static $qrcode_create = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=ACCESS_TOKEN';
    public static $qrcode_create_by_ticket = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=TICKET';
    
    /**
     * 链接-长连接转短链接
     * @var string
     */
    public static $url_get_short_from_long = 'https://api.weixin.qq.com/cgi-bin/shorturl?access_token=ACCESS_TOKEN';
    
    /**
     * 二维码请求链接
     * @var string
     */
    public static $url_get_qcode_moment = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=TOKEN POST";

    /**
     * ocr识别-身份证
     * @var string
     */
    public static $ocr_recognize_idcard = 'http://api.weixin.qq.com/cv/ocr/idcard?type=MODE&img_url=ENCODE_URL&access_token=ACCESS_TOCKEN';

    /**
     * 卡券-新建
     * @var string
     */
    public static $card_create = 'https://api.weixin.qq.com/card/create?access_token=ACCESS_TOKEN';

    public static $card_qrcode_create = 'https://api.weixin.qq.com/card/qrcode/create?access_token=ACCESS_TOKEN';
    public static $card_landingpage_create = 'https://api.weixin.qq.com/card/landingpage/create?access_token=ACCESS_TOKEN';
    /**
     * @var string 导入自定义code
     */
    public static $card_code_import = 'http://api.weixin.qq.com/card/code/deposit?access_token=ACCESS_TOKEN';
    /**
     * @var string 导入自定义code核查
     */
    public static $card_code_import_check = 'http://api.weixin.qq.com/card/code/checkcode?access_token=ACCESS_TOKEN';
    /**
     * @var string 查询导入code数目接口
     */
    public static $card_code_get_count = 'http://api.weixin.qq.com/card/code/getdepositcount?access_token=ACCESS_TOKEN';
    /**
     * @var string 获取可插入微信图文的卡券html代码
     */
    public static $card_code_get_html_in_news = 'https://api.weixin.qq.com/card/mpnews/gethtml?access_token=ACCESS_TOKEN';
    /**
     * @var string 设置卡券设置测试白名单
     */
    public static $card_code_create_test_white_list = 'https://api.weixin.qq.com/card/testwhitelist/set?access_token=ACCESS_TOKEN';
    /**
     * @var string 获取门店小程序配置的卡券
     */
    public static $card_get_4_store = 'https://api.weixin.qq.com/card/storewxa/get?access_token=ACCESS_TOKEN';
    public static $card_set_4_store = 'https://api.weixin.qq.com/card/storewxa/set?access_token=ACCESS_TOKEN';

    /**
     * 门店
     * @var string
     */
    public static $poi_add = "http://api.weixin.qq.com/cgi-bin/poi/addpoi?access_token=ACCESS_TOKEN";
    public static $poi_search = "http://api.weixin.qq.com/cgi-bin/poi/getpoi?access_token=ACCESS_TOKEN";
    public static $poi_get_list = "https://api.weixin.qq.com/cgi-bin/poi/getpoilist?access_token=ACCESS_TOKEN";
    public static $poi_update = "https://api.weixin.qq.com/cgi-bin/poi/updatepoi?access_token=ACCESS_TOKEN";
    public static $poi_delete = "https://api.weixin.qq.com/cgi-bin/poi/delpoi?access_token=ACCESS_TOKEN";
    public static $poi_getwxcategory = "https://api.weixin.qq.com/cgi-bin/poi/getwxcategory?access_token=ACCESS_TOKEN";
    
    /**
     * 门店小程序
     * @var string
     */
    public static $poi_wxa_getcategory = "https://api.weixin.qq.com/wxa/get_merchant_category?access_token=ACCESS_TOKEN";
    public static $poi_wxa_add = "https://api.weixin.qq.com/wxa/apply_merchant?access_token=ACCESS_TOKEN";
    public static $poi_wxa_get_merchant_audit_info = "https://api.weixin.qq.com/wxa/get_merchant_audit_info?access_token=ACCESS_TOKEN";
    public static $poi_wxa_update_info = "https://api.weixin.qq.com/wxa/modify_merchant?access_token=ACCESS_TOKEN";
    public static $poi_search_in_map = "https://api.weixin.qq.com/wxa/search_map_poi?access_token=ACCESS_TOKEN";
    public static $poi_add_in_map = "https://api.weixin.qq.com/wxa/create_map_poi?access_token=ACCESS_TOKEN";
    public static $poi_add_store = "https://api.weixin.qq.com/wxa/add_store?access_token=ACCESS_TOKEN";
    public static $poi_update_store = "https://api.weixin.qq.com/wxa/update_store?access_token=ACCESS_TOKEN";
    public static $poi_get_store_info = "https://api.weixin.qq.com/wxa/get_store_info?access_token=ACCESS_TOKEN";
    public static $poi_get_store_list = "https://api.weixin.qq.com/wxa/get_store_list?access_token=ACCESS_TOKEN";
    public static $poi_delete_store = "https://api.weixin.qq.com/wxa/del_store?access_token=ACCESS_TOKEN";
    
    /**
     * 拉取省市区信息
     */
    public static $get_area_info="https://api.weixin.qq.com/wxa/get_district?access_token=ACCESS_TOKEN";
    
    /**
     * 
     * @var string 微信智能接口-语义理解
     */
    public static $smart_guess_meaning = "https://api.weixin.qq.com/semantic/semproxy/search?access_token=ACCESS_TOKEN";
    public static $smart_send_voice = "https://api.weixin.qq.com/cgi-bin/media/voice/addvoicetorecofortext?access_token=ACCESS_TOKEN&format=mp3&voice_id=VOICE_ID&lang=LANG";
    public static $smart_get_voice_meaning = "https://api.weixin.qq.com/cgi-bin/media/voice/queryrecoresultfortext?access_token=ACCESS_TOKEN&voice_id=VOICE_ID&lang=LANG";
    public static $smart_translate_content = "https://api.weixin.qq.com/cgi-bin/media/voice/translatecontent?access_token=ACCESS_TOKEN&lfrom=LFROM&lto=LTO";
}