//
//  RegistViewController.swift
//  post
//
//  Created by Derek Li on 14-10-8.
//  Copyright (c) 2014年 Derek Li. All rights reserved.
//

import Foundation
import UIKit

class RegistViewController:ViewController,UITextFieldDelegate{
    
    var loginButton:         UIButton!
    var registButton:        UIButton!
    var usernameLabel:       UILabel!
    var usernameTextField:   UITextField!
    var pwdLabel:            UILabel!
    var pwdTextField:        UITextField!
    var backgrondButton:      UIButton!
    
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
        self.navigationItem.title = "注册"
        
        self.initViewStyle()
        
        usernameTextField.delegate = self
        usernameTextField.becomeFirstResponder()
        
        self.view.addSubview(backgrondButton)
        self.view.addSubview(loginButton)
        self.view.addSubview(registButton)
        self.view.addSubview(usernameLabel)
        self.view.addSubview(pwdLabel)
        self.view.addSubview(usernameTextField)
        self.view.addSubview(pwdTextField)
        
        
    }
    
    override func canBecomeFirstResponder()->Bool{
        return true
    }
    
    func initViewStyle(){
        
        var viewSize = self.view.frame.size
        var viewOrgin = self.view.frame.origin
        
        var labelWidth:CGFloat = 60.0
        var textFieldWidth:CGFloat = 200.0
        var btnWidth:CGFloat = 75.0
        
        loginButton = UIButton(frame:CGRect(origin: CGPointMake(viewSize.width/2 - 5.0-btnWidth, 260.0), size: CGSizeMake(btnWidth,30)))
        loginButton.setTitle("登录", forState: UIControlState.Normal)
        loginButton.backgroundColor = UIColor.redColor()
        loginButton.addTarget(self, action: "buttonClick:", forControlEvents: UIControlEvents.TouchUpInside)
        loginButton.tag = 1
        
        registButton = UIButton(frame:CGRect(origin: CGPointMake(viewSize.width/2 + 5.0, 260.0), size: CGSizeMake(btnWidth,30)))
        registButton.setTitle("注册", forState: UIControlState.Normal)
        registButton.backgroundColor = UIColor.redColor()
        registButton.addTarget(self, action: "buttonClick:", forControlEvents: UIControlEvents.TouchUpInside)
        registButton.tag = 2
        
        
        usernameLabel = UILabel(frame:CGRect(origin: CGPointMake((viewSize.width - labelWidth - textFieldWidth)/2, 160.0), size: CGSizeMake(labelWidth,30)))
        usernameLabel.text = "用户名:"
        
        pwdLabel = UILabel(frame:CGRect(origin: CGPointMake((viewSize.width - labelWidth - textFieldWidth)/2, 210.0), size: CGSizeMake(labelWidth,30)))
        pwdLabel.text = "密码:"
        
        
        usernameTextField = UITextField(frame:CGRect(origin: CGPointMake((viewSize.width + labelWidth - textFieldWidth)/2, 160.0), size: CGSizeMake(textFieldWidth,30)))
        usernameTextField.placeholder = "请输入邮箱地址"
        usernameTextField.borderStyle = UITextBorderStyle.RoundedRect
        
        
        pwdTextField = UITextField(frame:CGRect(origin: CGPointMake((viewSize.width + labelWidth - textFieldWidth)/2, 210.0), size: CGSizeMake(textFieldWidth,30)))
        pwdTextField.borderStyle = UITextBorderStyle.RoundedRect
        pwdTextField.placeholder = "请输入密码"
        pwdTextField.secureTextEntry = true
        
        
        
        backgrondButton = UIButton(frame:CGRect(origin: CGPointMake(0.0, 0.0), size: CGSizeMake(viewSize.width,viewSize.height)))
        backgrondButton.addTarget(self, action: "hideInput:", forControlEvents: UIControlEvents.TouchUpInside)
        
        
    }
    
    @IBAction func buttonClick(sender: UIButton!){
        switch(sender.tag){
        case    1:  self.gotoLogin()
            break
        case    2:  self.gotoRegist()
            break
        default:    break
        }
    }
    
    @IBAction func hideInput(sender: UIButton!) {
        usernameTextField.resignFirstResponder()
        pwdTextField.resignFirstResponder()
        
    }
    
    func gotoLogin(){
        var username = self.usernameTextField.text
        var password = self.pwdTextField.text
        
        var dl=HttpClient()
        //        var url="http://192.168.1.100:8000/hi/hi"
        
        var url = Constant().URL_USER_LOGIN
        var dic=["username":username,"password":password]
        
        //        dl.downloadFromGetUrl(url, completionHandler: {(data: NSData?, error: NSError?) -> Void in
        //            if (error != nil){
        //                println("error=\(error!.localizedDescription)")
        //            }else{
        //                var dict=NSJSONSerialization.JSONObjectWithData(data!, options:.MutableContainers, error:nil) as? NSDictionary
        //                dict=NSJSONSerialization.JSONObjectWithData(data!, options:.MutableContainers, error:nil) as? NSDictionary
        //
        //                //                var str1 = dict?.objectForKey("data") as NSArray
        //                var str1: AnyObject? = dict?.objectForKey("agent")
        //                println("get_dict=\(dict)")
        //            }
        //        })
        println(url)
        dl.downloadFromPostUrl(url, dic: dic, completionHandler: {(data: NSData?, error: NSError?) -> Void in
            if (error != nil){
                println("error=\(error!.localizedDescription)")
            }else{
                
                var dict=NSJSONSerialization.JSONObjectWithData(data!, options:.MutableContainers, error:nil) as? NSDictionary
                
                //                var str1 = dict?.objectForKey("title")
                println("登录成功！")
                println("post_dict=\(dict)")
            }
        })
    }
    
    func gotoRegist(){
        self.navigationController?.pushViewController(RegistViewController(), animated: true)
    }
    
}