//
//  MemberDetailViewController.swift
//  post
//
//  Created by Derek Li on 14/12/27.
//  Copyright (c) 2014年 Derek Li. All rights reserved.
//

//
//  PostDetailViewController.swift
//  post
//
//  Created by Derek Li on 14-10-8.
//  Copyright (c) 2014年 Derek Li. All rights reserved.
//

import Foundation
import UIKit

class MemberDetailViewController:ViewController,UITableViewDelegate,UITableViewDataSource {
    var refreshControl = UIRefreshControl()
    var memberPhoto:            UIImageView!
    var usernameLabel:          UILabel!
    var nicknameLabel:          UILabel!
    var cityLabel:              UILabel!
    var descriptionView:        UITextView!
    var oschinaLabel:           UILabel!
    var websiteLabel:            UILabel!
    var weiboLabel:             UILabel!
    
    
    var postLabel:              UILabel!
    var commentLabel:           UILabel!
    
    var commentTableView:       UITableView!
    
    var commentDataArray:       [AnyObject] = [AnyObject]()
    
    
    var member:                 Member!
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
        
        
        
        self.navigationItem.title = "详情"
        
        self.initViewStyle()
        
        self.view.addSubview(memberPhoto)
        self.view.addSubview(usernameLabel)
        self.view.addSubview(nicknameLabel)
        self.view.addSubview(cityLabel)
        self.view.addSubview(descriptionView)
        self.view.addSubview(websiteLabel)
        self.view.addSubview(weiboLabel)
        self.view.addSubview(oschinaLabel)
        
        self.view.addSubview(postLabel)
        self.view.addSubview(commentLabel)
        self.view.addSubview(commentTableView)
        
        self.view.addSubview(self.tableView!)
        
        self.refreshMember()
        
        self.getPostData()
        self.getCommentData()
        
        
    }
    
    func getCommentData(){
        
        var dl=HttpClient()
        
        var postComments:NSDictionary = member.links.objectForKey("postComments") as NSDictionary
        var postCommentsURL:String = postComments.objectForKey("href") as String
        var url = "\(Constant().URL_BASE_PATH)\(postCommentsURL)"
        
        dl.downloadFromGetUrl(url, completionHandler: {(data: NSData?, error: NSError?) -> Void in
            if (error != nil){
                println("error=\(error!.localizedDescription)")
            }else{
                var dict=NSJSONSerialization.JSONObjectWithData(data!, options:.MutableContainers, error:nil) as? NSDictionary
                dict=NSJSONSerialization.JSONObjectWithData(data!, options:.MutableContainers, error:nil) as? NSDictionary
                
                var commentArrayDic = dict?.objectForKey("_embedded") as NSDictionary
                self.commentDataArray = commentArrayDic.objectForKey("items") as NSArray
                
                self.commentTableView?.reloadData()
                
            }
        })
    }
    
    func getPostData(){
        
        var dl=HttpClient()
        
        var postList:NSDictionary = member.links.objectForKey("posts") as NSDictionary
        var postListURL:String = postList.objectForKey("href") as String
        var url = "\(Constant().URL_BASE_PATH)\(postListURL)"
        
        dl.downloadFromGetUrl(url, completionHandler: {(data: NSData?, error: NSError?) -> Void in
            if (error != nil){
                println("error=\(error!.localizedDescription)")
            }else{
                var dict=NSJSONSerialization.JSONObjectWithData(data!, options:.MutableContainers, error:nil) as? NSDictionary
                dict=NSJSONSerialization.JSONObjectWithData(data!, options:.MutableContainers, error:nil) as? NSDictionary
                
                var postArrayDic = dict?.objectForKey("_embedded") as NSDictionary
                self.dataArray = postArrayDic.objectForKey("items") as NSArray
                self.tableView?.reloadData()
                
            }
        })
    }
    func refreshMember(){
        
        self.usernameLabel.text = self.member.username
        
        self.nicknameLabel.text = "昵称："+self.member.nickname
        
        self.cityLabel.text = "城市：" + self.member.city
        
        self.descriptionView.text = "简介：" + self.member.description
        
        self.oschinaLabel.text = "开源中国：" + self.member.oschina
        
        self.websiteLabel.text = "个人网站：" + self.member.website
        
        self.weiboLabel.text = "个人微博：" + self.member.weibo
        
        
        var imageUrl:String = self.member.userimageurl!
        var url = "\(Constant().URL_BASE_PATH)\(imageUrl)"
        var nsd = NSData(contentsOfURL:NSURL(string:url))
        
        var img = UIImage(data: nsd);
        
        self.memberPhoto.image = img
        
    }
    
    func refreshView(){
        self.refreshMember()
        self.commentTableView.reloadData()
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
        
        
        
        usernameLabel = UILabel(frame:CGRect(origin: CGPointMake(padding*2+photoSize,80.0), size: CGSizeMake(textFieldWidth-photoSize-padding,20)))
        usernameLabel.font = UIFont(name: "Helvetica", size: 18)
        
        
        nicknameLabel = UILabel(frame:CGRect(origin: CGPointMake(padding*2+photoSize,100.0), size: CGSizeMake(textFieldWidth/2-photoSize/2-padding/2,10)))
        nicknameLabel.font = defaultFont
        
        cityLabel = UILabel(frame:CGRect(origin: CGPointMake(padding*2+photoSize+textFieldWidth/2-photoSize/2-padding/2,100.0), size: CGSizeMake(textFieldWidth/2-photoSize/2-padding/2,10)))
        cityLabel.font = defaultFont
        
        
        descriptionView = UITextView(frame:CGRect(origin: CGPointMake(padding*2+photoSize,110.0), size: CGSizeMake(textFieldWidth-photoSize-padding,20)))
        descriptionView.editable = false
        descriptionView.font = defaultFont
        
        websiteLabel = UILabel(frame:CGRect(origin: CGPointMake(padding*2+photoSize,130.0), size: CGSizeMake(textFieldWidth-photoSize-padding,10)))
        websiteLabel.font = defaultFont
        
        
        oschinaLabel = UILabel(frame:CGRect(origin: CGPointMake(padding*2+photoSize,140.0), size: CGSizeMake(textFieldWidth-photoSize-padding,10)))
        oschinaLabel.font = defaultFont
        
        weiboLabel = UILabel(frame:CGRect(origin: CGPointMake(padding*2+photoSize,150.0), size: CGSizeMake(textFieldWidth-photoSize-padding,10)))
        weiboLabel.font = defaultFont
        
        
        
        
        postLabel = UILabel(frame:CGRect(origin: CGPointMake(padding,160), size: CGSizeMake(textFieldWidth,20)))
        postLabel.text = "发表过的帖子："
        postLabel.font = UIFont(name: "Helvetica", size: 14)
        
        
        var y:CGFloat = 80+photoSize
        
        var frame = self.view.frame
        self.tableView = UITableView(frame: CGRect(origin: CGPointMake(0, y+20), size: CGSizeMake(frame.size.width,frame.size.height/2-y/2 - 20 - 25)), style:UITableViewStyle.Plain)
        self.tableView!.delegate = self
        self.tableView!.dataSource = self
        self.tableView!.registerClass(UITableViewCell.self, forCellReuseIdentifier: "MyTestCell")
        
        
        commentLabel = UILabel(frame:CGRect(origin: CGPointMake(padding,y+frame.size.height/2-y/2 - 25), size: CGSizeMake(textFieldWidth,20)))
        commentLabel.text = "回复过的帖子："
        commentLabel.font = UIFont(name: "Helvetica", size: 14)
        
        self.commentTableView = UITableView(frame: CGRect(origin: CGPointMake(0, y+frame.size.height/2-y/2 - 25 + 20), size: CGSizeMake(frame.size.width,frame.size.height/2-y/2-20 - 25)), style:UITableViewStyle.Plain)
        self.commentTableView!.delegate = self
        self.commentTableView!.dataSource = self
        self.commentTableView!.registerClass(UITableViewCell.self, forCellReuseIdentifier: "MyTestCell")
        
    }
    
    @IBAction func imageClick(recognizer: UITapGestureRecognizer) {
        
    }
    
    // MARK: - UITableViewDataSource
    func tableView(tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        
        self.resizeTableView(tableView)
        
        if(tableView != self.tableView){
            return self.commentDataArray.count;
        }
        return dataArray.count;
    }
    
    func resizeTableView(tableView:UITableView){
        if(tableView == self.tableView){
            //            var frame = self.view.frame
            //            var count:CGFloat = CGFloat(self.dataArray.count)
            //            self.tableView?.frame.size.height =  count * 44.0
            //
            //            self.commentLabel.frame.origin.y = 160.0 + count * 44.0
            //            self.commentTableView.frame.origin.y = 160.0 + count * 44.0 + 20
            //            self.commentTableView.frame.size.height = frame.height - (160.0 + count * 44.0 + 40)
            //
        }
    }
    
    func tableView(tableView: UITableView, cellForRowAtIndexPath indexPath: NSIndexPath) -> UITableViewCell {
        
        let cell = UITableViewCell(style: UITableViewCellStyle.Subtitle, reuseIdentifier: "newsCell")
        
        //let obj: New = dataArray[indexPath.row] as New
        
        var obj:NSDictionary = NSDictionary()
        if(tableView == self.tableView){
            obj = dataArray[indexPath.row] as NSDictionary
        }else{
            obj = commentDataArray[indexPath.row] as NSDictionary
        }
        
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

