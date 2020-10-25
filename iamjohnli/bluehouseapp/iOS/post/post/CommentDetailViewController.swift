//
//  CommentDetailViewController.swift
//  post
//
//  Created by Derek Li on 14-10-8.
//  Copyright (c) 2014年 Derek Li. All rights reserved.
//

import Foundation
import UIKit

class CommentDetailViewController:ViewController{
    var contentLabel:       UILabel!
    var contentInfoLabel:   UILabel!
    var createdLabel:       UILabel!
    var createdInfoLabel:   UILabel!
    var modifiedLabel:      UILabel!
    var modifiedInfoLabel:  UILabel!
    var comment:            Comment!
    
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
        self.navigationItem.title = "评论详情"
        
        self.initViewStyle()
        
        self.view.addSubview(contentLabel)
        self.view.addSubview(createdLabel)
        self.view.addSubview(modifiedLabel)
        
        self.view.addSubview(contentInfoLabel)
        self.view.addSubview(createdInfoLabel)
        self.view.addSubview(modifiedInfoLabel)
        
        self.getPostData()
        
    }
    
    func getPostData(){
        
        if comment.id == 0 {
            return
        }
        var dl=HttpClient()
        var url = "\(Constant().URL_COMMENT_DETAIL)\(comment.id)"
        println("\(url)")
        dl.downloadFromGetUrl(url, completionHandler: {(data: NSData?, error: NSError?) -> Void in
            if (error != nil){
                println("error=\(error!.localizedDescription)")
            }else{
                var dict=NSJSONSerialization.JSONObjectWithData(data!, options:.MutableContainers, error:nil) as? NSDictionary
                dict=NSJSONSerialization.JSONObjectWithData(data!, options:.MutableContainers, error:nil) as? NSDictionary
                println("\(dict)")
                var commentData:NSDictionary = dict?.objectForKey("data") as NSDictionary
                
                self.comment = Comment(dic: commentData)
                
                self.refreshView()
            }
        })
    }
    
    func refreshView(){
        self.contentInfoLabel.text  = self.comment.content
        //        self.createdInfoLabel.text  = self.comment.created
        self.modifiedInfoLabel.text = self.comment.modified
        self.tableView?.reloadData()
    }
    
    func initViewStyle(){
        
        var viewSize = self.view.frame.size
        var viewOrgin = self.view.frame.origin
        
        var labelWidth:CGFloat = 100.0
        var textFieldWidth:CGFloat = 250.0
        var btnWidth:CGFloat = 150.0
        
        var left:CGFloat = (viewSize.width)-btnWidth
        
        contentLabel = UILabel(frame:CGRect(origin: CGPointMake((viewSize.width - labelWidth - textFieldWidth)/2, 80.0), size: CGSizeMake(labelWidth,20)))
        contentLabel.text = "内容:"
        
        
        
        contentInfoLabel = UILabel(frame:CGRect(origin: CGPointMake((viewSize.width + labelWidth - textFieldWidth)/2, 80.0), size: CGSizeMake(textFieldWidth,20)))
        
        createdLabel = UILabel(frame:CGRect(origin: CGPointMake((viewSize.width - labelWidth - textFieldWidth)/2, 100.0), size: CGSizeMake(labelWidth,20)))
        createdLabel.text = "创建时间:"
        
        createdInfoLabel = UILabel(frame:CGRect(origin: CGPointMake((viewSize.width + labelWidth - textFieldWidth)/2, 100.0), size: CGSizeMake(textFieldWidth,20)))
        
        modifiedLabel = UILabel(frame:CGRect(origin: CGPointMake((viewSize.width - labelWidth - textFieldWidth)/2, 120.0), size: CGSizeMake(labelWidth,20)))
        modifiedLabel.text = "修改时间:"
        
        modifiedInfoLabel = UILabel(frame:CGRect(origin: CGPointMake((viewSize.width + labelWidth - textFieldWidth)/2, 120.0), size: CGSizeMake(textFieldWidth,20)))
    }
}