//
//  Comment.swift
//  post
//
//  Created by Derek Li on 14/12/26.
//  Copyright (c) 2014年 Derek Li. All rights reserved.
//

import Foundation

class Comment{
    var id                  :Int = 0
    var postTitle           :String! = ""
    var content             :String! = ""
    var modified            :String! = ""
    
    var memberName          :String! = ""
    var memberImageUrl      :String! = ""
    
    var links               :NSDictionary!
    
    
    
    init(){
        
    }
    init(dic:NSDictionary){
        
        
        self.id = dic.objectForKey("id") as Int
        
        if(dic.objectForKey("postTitle") != nil){
            self.postTitle = dic.objectForKey("postTitle") as? String
        }
        if(dic.objectForKey("content") != nil){
            self.content = dic.objectForKey("content") as? String
        }
        if(dic.objectForKey("modified") != nil){
            self.modified = dic.objectForKey("modified") as? String
        }
        
        if(dic.objectForKey("memberimageurl") != nil){
            self.memberImageUrl = dic.objectForKey("memberimageurl") as? String
        }
        if(dic.objectForKey("memberName") != nil){
            self.memberName =  dic.objectForKey("memberName") as? String
        }
        self.links = dic.objectForKey("_links") as NSDictionary
        
    }
    
}