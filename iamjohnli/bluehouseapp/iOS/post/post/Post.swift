//
//  Post.swift
//  post
//
//  Created by Derek Li on 14-10-8.
//  Copyright (c) 2014年 Derek Li. All rights reserved.
//

import Foundation

class Post{
    var id                  :Int = 0
    var title               :String! = ""
    var content             :String! = ""
    var created             :String! = ""
    var modified            :String! = ""
    var lastCommentTime     :String! = ""
    
    var nodeName            :String! = ""
    var memberName          :String! = ""
    var memberImageUrl      :String! = ""
    var commentCount        :Int = 0
    
    var links               :NSDictionary!
    
    
    
    init(){
        
    }
    init(dic:NSDictionary){
        
        self.reloadAttr(dic)
        
        
    }
    
    func reloadAttr(dic:NSDictionary){
        self.id = dic.objectForKey("id") as Int
        if(dic.objectForKey("title") != nil){
            self.title = dic.objectForKey("title") as? String
        }
        if(dic.objectForKey("content") != nil){
            self.content = dic.objectForKey("content") as? String
        }
        if(dic.objectForKey("created") != nil){
            self.created = dic.objectForKey("created") as? String
        }
        if(dic.objectForKey("modified") != nil){
            self.modified = dic.objectForKey("modified") as? String
        }
        if(dic.objectForKey("last_comment_time") != nil){
            self.lastCommentTime = dic.objectForKey("last_comment_time") as? String
        }
        if(dic.objectForKey("nodeName") != nil){
            self.nodeName = dic.objectForKey("nodeName") as? String
        }
        
        
        self.commentCount = dic.objectForKey("comment_count") as Int
        
        if(dic.objectForKey("memberimageurl") != nil){
            self.memberImageUrl = dic.objectForKey("memberimageurl") as? String
        }
        if(dic.objectForKey("memberName") != nil){
            self.memberName =  dic.objectForKey("memberName") as? String
        }
        self.links = dic.objectForKey("_links") as NSDictionary
    }
    
}