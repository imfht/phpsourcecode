//
//  Stroe.swift
//  post
//
//  Created by Derek Li on 14-10-8.
//  Copyright (c) 2014年 Derek Li. All rights reserved.
//

import Foundation

var store:Store!

class Store{
    
    init(){
    }
    class func getInstance()->Store!{
        if let haveStore = store {
            store = Store()
        }
        return store
    }
}