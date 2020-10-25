//
//  PostDetailViewController.swift
//  post
//
//  Created by Derek Li on 14-10-8.
//  Copyright (c) 2014年 Derek Li. All rights reserved.
//

import Foundation
import UIKit

class PostDetailViewController:ViewController,UITableViewDelegate,UITableViewDataSource {
    var refreshControl = UIRefreshControl()
    var memberPhoto:            UIImageView!
    var titleLabel:             UILabel!
    var contentView:            UITextView!
    var createdLabel:           UILabel!
    var modifiedLabel:          UILabel!
    var lastCommentTimeLabel:   UILabel!
    var countLabel:             UILabel!
    var memberNameLabel:        UILabel!
    var nodeNameLabel:          UILabel!
    var post:                   Post!
    var member:                 Member!
    
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
        
        var title               :String?
        var content             :String?
        var created             :String?
        var modified            :String?
        var lastCommentTime     :String?
        
        var nodeName            :String?
        var memberName          :String?
        var memberImageUrl      :String?
        var commentCount        :Int = 0
        
        self.navigationItem.title = "详情"
        
        self.initViewStyle()
        
        self.view.addSubview(memberPhoto)
        self.view.addSubview(titleLabel)
        self.view.addSubview(contentView)
        self.view.addSubview(createdLabel)
        self.view.addSubview(modifiedLabel)
        self.view.addSubview(lastCommentTimeLabel)
        self.view.addSubview(memberNameLabel)
        self.view.addSubview(nodeNameLabel)
        self.view.addSubview(countLabel)
        
        self.view.addSubview(self.tableView!)
        
        
        
        self.getPostData()
        self.getCommentData()
        self.getMemberData()
    }
    
    func getCommentData(){
        
        var dl=HttpClient()
        
        var postComments:NSDictionary = post.links.objectForKey("postComments") as NSDictionary
        var postCommentsURL:String = postComments.objectForKey("href") as String
        var url = "\(Constant().URL_BASE_PATH)\(postCommentsURL)"
        
        dl.downloadFromGetUrl(url, completionHandler: {(data: NSData?, error: NSError?) -> Void in
            if (error != nil){
                println("error=\(error!.localizedDescription)")
            }else{
                var dict=NSJSONSerialization.JSONObjectWithData(data!, options:.MutableContainers, error:nil) as? NSDictionary
                dict=NSJSONSerialization.JSONObjectWithData(data!, options:.MutableContainers, error:nil) as? NSDictionary
                
                var commentArrayDic = dict?.objectForKey("_embedded") as NSDictionary
                self.dataArray = commentArrayDic.objectForKey("items") as NSArray
                
                self.tableView?.reloadData()
                
            }
        })
    }
    
    func getMemberData(){
        
        var dl=HttpClient()
        
        var postDetail:NSDictionary = post.links.objectForKey("member") as NSDictionary
        var postDetailURL:String = postDetail.objectForKey("href") as String
        var url = "\(Constant().URL_BASE_PATH)\(postDetailURL)"
        
        dl.downloadFromGetUrl(url, completionHandler: {(data: NSData?, error: NSError?) -> Void in
            if (error != nil){
                println("error=\(error!.localizedDescription)")
            }else{
                var dict=NSJSONSerialization.JSONObjectWithData(data!, options:.MutableContainers, error:nil) as? NSDictionary
                dict=NSJSONSerialization.JSONObjectWithData(data!, options:.MutableContainers, error:nil) as? NSDictionary
                
                self.member = Member(dic:dict!)
                
            }
        })
        
        
    }
    
    func getPostData(){
        var dl=HttpClient()
        
        var postComments:NSDictionary = post.links.objectForKey("postComments") as NSDictionary
        var postCommentsURL:String = postComments.objectForKey("href") as String
        var url = "\(Constant().URL_BASE_PATH)\(postCommentsURL)"
        
        dl.downloadFromGetUrl(url, completionHandler: {(data: NSData?, error: NSError?) -> Void in
            if (error != nil){
                println("error=\(error!.localizedDescription)")
            }else{
                var dict=NSJSONSerialization.JSONObjectWithData(data!, options:.MutableContainers, error:nil) as? NSDictionary
                dict=NSJSONSerialization.JSONObjectWithData(data!, options:.MutableContainers, error:nil) as? NSDictionary
                
                var commentArrayDic = dict?.objectForKey("_embedded") as NSDictionary
                self.dataArray = commentArrayDic.objectForKey("items") as NSArray
                
                self.refreshView()
                
            }
        })
        
    }
    func refreshPost(){
        self.titleLabel.text = self.post.title
        self.contentView.text = self.post.content
        self.memberNameLabel.text = self.post.memberName
        self.nodeNameLabel.text = self.post.nodeName
        
        var count:Int = self.post.commentCount
        self.countLabel.text = "回复（\(count)）"
        
        var lastCommentTimeString:String = self.post.lastCommentTime!
        self.lastCommentTimeLabel.text = "最后回复：\(lastCommentTimeString)"
        var createdString:String = self.post.created!
        self.createdLabel.text = "创建：\(createdString)"
        var modifiedString:String = self.post.modified!
        self.modifiedLabel.text = "更改：\(modifiedString)"
    }
    
    func refreshView(){
        self.refreshPost()
        self.tableView?.reloadData()
    }
    
    func initViewStyle(){
        
        var viewSize = self.view.frame.size
        var viewOrgin = self.view.frame.origin
        
        var padding:CGFloat = 10
        var textFieldWidth:CGFloat = self.view.frame.size.width-padding*2
        var photoSize:CGFloat = 80
        
        var defaultFont :UIFont = UIFont(name: "Helvetica", size: 10)
        
        memberPhoto = UIImageView(frame: CGRect(origin: CGPointMake(padding, 80.0), size: CGSize(width: photoSize, height: photoSize)))
        var imageUrl:String = post.memberImageUrl!
        var url = "\(Constant().URL_BASE_PATH)\(imageUrl)"
        var nsd = NSData(contentsOfURL:NSURL(string:url))
        
        var img = UIImage(data: nsd);
        
        memberPhoto.image = img
        
        memberPhoto.userInteractionEnabled = true
        var singleTap:UITapGestureRecognizer = UITapGestureRecognizer(target: self, action:Selector("imageClick:"))
        memberPhoto.addGestureRecognizer(singleTap)
        
        
        titleLabel = UILabel(frame:CGRect(origin: CGPointMake(padding*2+photoSize, 80.0), size: CGSizeMake(textFieldWidth-photoSize-padding,20)))
        
        
        
        contentView = UITextView(frame:CGRect(origin: CGPointMake(padding*2+photoSize, 100.0), size: CGSizeMake(textFieldWidth-photoSize-padding,60)))
        contentView.font = defaultFont
        contentView.editable = false
        
        memberNameLabel = UILabel(frame:CGRect(origin: CGPointMake(padding, 160.0), size: CGSizeMake(textFieldWidth/6,20)))
        memberNameLabel.font = defaultFont
        
        countLabel = UILabel(frame:CGRect(origin: CGPointMake(padding+textFieldWidth/6, 160.0), size: CGSizeMake(textFieldWidth/6,20)))
        countLabel.font = defaultFont
        
        nodeNameLabel = UILabel(frame:CGRect(origin: CGPointMake(padding+textFieldWidth/3, 160.0), size: CGSizeMake(textFieldWidth/6,20)))
        nodeNameLabel.font = UIFont(name: "Helvetica", size: 12)
        
        
        createdLabel = UILabel(frame:CGRect(origin: CGPointMake(padding+textFieldWidth/2, 160.0), size: CGSizeMake(textFieldWidth/2,20)))
        createdLabel.font = defaultFont
        
        
        
        lastCommentTimeLabel = UILabel(frame:CGRect(origin: CGPointMake(padding, 180.0), size: CGSizeMake(textFieldWidth/2,20)))
        lastCommentTimeLabel.font = defaultFont
        
        
        modifiedLabel = UILabel(frame:CGRect(origin: CGPointMake(padding+textFieldWidth/2, 180.0), size: CGSizeMake(textFieldWidth/2,20)))
        modifiedLabel.font = defaultFont
        
        
        
        var frame = self.view.frame
        self.tableView = UITableView(frame: CGRect(origin: CGPointMake(0, 210.0), size: CGSizeMake(frame.size.width,frame.size.height-210)), style:UITableViewStyle.Plain)
        self.tableView!.delegate = self
        self.tableView!.dataSource = self
        self.tableView!.registerClass(UITableViewCell.self, forCellReuseIdentifier: "MyTestCell")
        
        
    }
    
    @IBAction func imageClick(recognizer: UITapGestureRecognizer) {
        var memberDetailVC = MemberDetailViewController()
        memberDetailVC.member = self.member
        self.navigationController?.pushViewController(memberDetailVC , animated: true)
    }
    
    // MARK: - UITableViewDataSource
    func tableView(tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        return dataArray.count;
    }
    
    func tableView(tableView: UITableView, cellForRowAtIndexPath indexPath: NSIndexPath) -> UITableViewCell {
        
        let cell = UITableViewCell(style: UITableViewCellStyle.Subtitle, reuseIdentifier: "newsCell")
        
        //let obj: New = dataArray[indexPath.row] as New
        let obj:NSDictionary = dataArray[indexPath.row] as NSDictionary
        
        var comment = Comment(dic: obj)
        cell.textLabel?.text = comment.content
        
        
        //        let dateFormatter = NSDateFormatter()
        //        dateFormatter.dateFormat = "yyyy年 MM月 dd日"
        //
        //        let str = dateFormatter.stringFromDate(obj.title)
        
        var membername:String = comment.memberName!
        var modifiedDate:String = comment.modified!
        
        var detailString:String = "\(membername)   回复时间：\(modifiedDate)"
        
        cell.detailTextLabel?.text = detailString
        
        var imgURL:String = obj.objectForKey("memberimageurl") as String
        var url = "\(Constant().URL_BASE_PATH)\(imgURL)"
        var nsd = NSData(contentsOfURL:NSURL(string:url))
        
        var img = UIImage(data: nsd);
        
        cell.imageView?.image = img
        
        return cell;
    }
    
    func tableView(tableView: UITableView, didSelectRowAtIndexPath indexPath: NSIndexPath){
        //        下面是取消点击后的选中
        //        tableView.deselectRowAtIndexPath(indexPath, animated: true)
        
    }
    
}