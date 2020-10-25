---
layout: post
title: 篮球才-测试
category: [软件使用]
description: vim拥有大量的语法文件，这当中当然包括了markdown的语法高亮文件。但是，随着时间的推移，基本的markdown语法高亮已经不太适合，因为 markdown的语法进行了扩充，有用很多其他的特性(包括支持Latex、输入表格等)。其实 Vim语法检测很好使用在开始写语法文件之前，有必要先了解一下vim是如何对文件类型进行识别的，有助于更好的理解vim的启动过程，本文是一个初级使用。
tags: [vim, markdown]
keywords: Vim,语法检测
---

强大的 @vim@ 拥有大量的语法文件，这当中当然包括了 @md@ 的语法高亮文件。但是，随着时间的推移，

基本的markdown语法高亮已经不太适合，因为 @md@ 的语法进行了扩充，有用很多其他的特性(包括支持Latex、输入表格等)。
其实 Vim语法检测很好使用在开始写语法文件之前，有必要先了解一下vim是如何对文件类型进行识别的，

有助于更好的理解vim的启动过程，本文是一个初级使用。分析了**Vim语法高亮**的原理，

阐述了如何定制一个自己的文件类型，并进行语法高亮



## 为什么要建立一个博客
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse fermentum lobortis porta. Mauris faucibus hendrerit pulvinar. Pellentesque sagittis, enim eu ornare dapibus, ligula nibh fermentum mauris, nec interdum lorem ante vel nunc. Nam vestibulum eleifend lorem, ullamcorper fermentum nulla ultrices in. Vivamus luctus iaculis quam. Nulla arcu eros, iaculis at elementum dignissim, rhoncus rutrum felis. Etiam convallis felis at dui laoreet malesuada. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec et nunc erat. In hac habitasse platea dictumst. Suspendisse et ante eget arcu pellentesque fermentum nec eu est. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Curabitur sit amet risus sed magna varius commodo ac eget sapien. In lectus ante, pretium sed ultrices ut, bibendum nec eros.


