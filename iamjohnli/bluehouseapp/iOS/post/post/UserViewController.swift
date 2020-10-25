//
//  UserViewController.swift
//  post
//
//  Created by Derek Li on 14-10-8.
//  Copyright (c) 2014年 Derek Li. All rights reserved.
//

import Foundation
import UIKit

class UserViewController:ViewController{
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
        self.navigationItem.title = "个人信息"
        
        let labelView = UILabel(frame:CGRect(origin: CGPointMake(10.0, 50.0), size: CGSizeMake(150,50)))//let 是Swift 表示常量的关键字
        labelView.text = "用户信息"
        self.view.addSubview(labelView)
        
        //var store:Store = Store.getInstance()
        
//        println("\(store.user.userId)")
        
        self.navigationController?.pushViewController(LoginViewController(), animated: true)
        
        
    }
    
    
    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }
    
}