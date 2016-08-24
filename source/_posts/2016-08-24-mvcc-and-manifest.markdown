---
layout: post
title: "leveldb源码笔记之MVCC && Manifest"
date: 2016-08-24 15:51
comments: true
categories: 
    - distribute
    - system
    - leveldb
---
####leveldb中是利用Version和VersionEdit进行版本管理的：
![](/images/blog_images/leveldb/mvcc.png)

####VersionEdit代表了一次状态转移，每次状态转移都写入mainifest文件，以便重启时recover
![](/images/blog_images/leveldb/write_a_manifest.png)
