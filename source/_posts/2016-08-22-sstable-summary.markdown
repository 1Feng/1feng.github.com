---
layout: post
title: "leveldb源码笔记之sstable"
date: 2016-08-22 11:19
comments: true
categories:
    - distribute
    - system
    - leveldb
---
###整体看下sstable的组要组成，如下：
![](/images/blog_images/leveldb/sstable.png)

### sstalbe 生成细节

>sstable 生成时机:
>>1. immutable-memtable 中的key/value dump到磁盘，生成sstable

>>2. level-n的sstable compact（多路归并）生成level-n+1的sstable

####首先是写入data block:
![](/images/blog_images/leveldb/write_a_data_block.png)

####data block都写入完成后，接下来是meta block:
![](/images/blog_images/leveldb/write_a_meta_block.png)

####然后是data/meta block索引信息data/meta index block写入:
![](/images/blog_images/leveldb/write_a_index_block.png)

####最后将index block的索引信息写入Footer
![](/images/blog_images/leveldb/write_a_footer.png)

####一个完整的sstable形成!
