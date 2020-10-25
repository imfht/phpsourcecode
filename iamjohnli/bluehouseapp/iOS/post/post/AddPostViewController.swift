//
//  AddPostViewController.swift
//  post
//
//  Created by Derek Li on 14-10-8.
//  Copyright (c) 2014年 Derek Li. All rights reserved.
//

import Foundation
import UIKit

class AddPostViewController:ViewController,UITextFieldDelegate{
    
    var submitButton:         UIButton!
    var titleLabel:       UILabel!
    var titleTextField:   UITextField!
    var contentLabel:            UILabel!
    var contentTextField:        UITextField!
    var backgrondButton:      UIButton!
    
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
        self.navigationItem.title = "发表帖子"
        
        self.initViewStyle()
        
        titleTextField.delegate = self
        titleTextField.becomeFirstResponder()
        
        self.view.addSubview(backgrondButton)
        self.view.addSubview(submitButton)
        self.view.addSubview(titleLabel)
        self.view.addSubview(contentLabel)
        self.view.addSubview(titleTextField)
        self.view.addSubview(contentTextField)
        
        
    }
    
    override func canBecomeFirstResponder()->Bool{
        return true
    }
    
    func initViewStyle(){
        
        var viewSize = self.view.frame.size
        var viewOrgin = self.view.frame.origin
        
        var labelWidth:CGFloat = 60.0
        var textFieldWidth:CGFloat = 200.0
        var btnWidth:CGFloat = 150.0
        
        var left:CGFloat = (viewSize.width)-btnWidth
        
        submitButton = UIButton(frame:CGRect(origin: CGPointMake(left/2 , 260.0), size: CGSizeMake(btnWidth,30)))
        submitButton.setTitle("提交帖子内容", forState: UIControlState.Normal)
        submitButton.backgroundColor = UIColor.redColor()
        submitButton.addTarget(self, action: "buttonClick:", forControlEvents: UIControlEvents.TouchUpInside)
        submitButton.tag = 1
        
        titleLabel = UILabel(frame:CGRect(origin: CGPointMake((viewSize.width - labelWidth - textFieldWidth)/2, 160.0), size: CGSizeMake(labelWidth,30)))
        titleLabel.text = "标题:"
        
        contentLabel = UILabel(frame:CGRect(origin: CGPointMake((viewSize.width - labelWidth - textFieldWidth)/2, 210.0), size: CGSizeMake(labelWidth,30)))
        contentLabel.text = "内容:"
        
        
        titleTextField = UITextField(frame:CGRect(origin: CGPointMake((viewSize.width + labelWidth - textFieldWidth)/2, 160.0), size: CGSizeMake(textFieldWidth,30)))
        titleTextField.placeholder = "请输入标题"
        titleTextField.borderStyle = UITextBorderStyle.RoundedRect
        
        
        contentTextField = UITextField(frame:CGRect(origin: CGPointMake((viewSize.width + labelWidth - textFieldWidth)/2, 210.0), size: CGSizeMake(textFieldWidth,30)))
        contentTextField.borderStyle = UITextBorderStyle.RoundedRect
        contentTextField.placeholder = "请输入内容"
        //        pwdTextField.secureTextEntry = true
        
        
        
        backgrondButton = UIButton(frame:CGRect(origin: CGPointMake(0.0, 0.0), size: CGSizeMake(viewSize.width,viewSize.height)))
        backgrondButton.addTarget(self, action: "hideInput:", forControlEvents: UIControlEvents.TouchUpInside)
        
        
    }
    
    @IBAction func buttonClick(sender: UIButton!){
        switch(sender.tag){
        case    1:  self.addPost()
            break
        default:    break
        }
    }
    
    @IBAction func hideInput(sender: UIButton!) {
        titleTextField.resignFirstResponder()
        contentTextField.resignFirstResponder()
        
    }
    
    func addPost(){
        var title = self.titleTextField.text
        var content = self.contentTextField.text
        
        var dl=HttpClient()
        
        var url = "\(Constant().URL_POST_CREATE)"
        var dic=["title":title,"content":content]
        
        dl.downloadFromPostUrl(url, dic: dic, completionHandler: {(data: NSData?, error: NSError?) -> Void
            in
            if (error != nil){
                println("error=\(error!.localizedDescription)")
            }else{
                
                //                var dict=NSJSONSerialization.JSONObjectWithData(data!, options:.MutableContainers, error:nil) as? NSDictionary
                
                //                var str1 = dict?.objectForKey("title")
                
                println("发表成功！")
                
            }
            
        })
    }
    
}