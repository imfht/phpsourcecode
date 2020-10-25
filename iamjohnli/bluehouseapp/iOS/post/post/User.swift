//
//  User.swift
//  post
//
//  Created by Derek Li on 14-10-8.
//  Copyright (c) 2014年 Derek Li. All rights reserved.
//

import Foundation

class User{
    var userId:Int = 0
    var userName:String?
    init(){
    }
    init(userId:Int,userName:String){
        self.userId = userId
        self.userName = userName
    }
}