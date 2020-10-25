//
//  Member.swift
//  post
//
//  Created by Derek Li on 14/12/27.
//  Copyright (c) 2014年 Derek Li. All rights reserved.
//

import Foundation

class Member{
    var id                  :Int = 0
    
    var userimageurl        :String! = ""
    var username            :String! = ""
    var nickname            :String! = ""
    var city                :String! = ""
    var description         :String! = ""
    var oschina             :String! = ""
    var website             :String! = ""
    var weibo               :String! = ""
    
    
    
    var links               :NSDictionary!
    
    
    
    init(){
        
    }
    init(dic:NSDictionary){
        
        self.reloadAttr(dic)
        
        
    }
    
    func reloadAttr(dic:NSDictionary){
        self.id = dic.objectForKey("id") as Int
        
        self.username = dic.objectForKey("username") as? String
        
        if(dic.objectForKey("nickname") != nil){
            self.nickname = dic.objectForKey("nickname") as? String
        }
        if(dic.objectForKey("city") != nil){
            self.city = dic.objectForKey("city") as? String
        }
        if(dic.objectForKey("description") != nil){
            self.description = dic.objectForKey("description") as? String
        }
        if(dic.objectForKey("oschina") != nil){
            self.oschina = dic.objectForKey("oschina") as? String
        }
        if(dic.objectForKey("website") != nil){
            self.website = dic.objectForKey("website") as? String
        }
        if(dic.objectForKey("weibo") != nil){
            self.weibo = dic.objectForKey("weibo") as? String
        }
        if(dic.objectForKey("userimageurl") != nil){
            self.userimageurl = dic.objectForKey("userimageurl") as? String
        }
        self.links = dic.objectForKey("_links") as NSDictionary
    }

}