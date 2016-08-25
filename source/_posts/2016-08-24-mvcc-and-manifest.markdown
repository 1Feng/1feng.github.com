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
### Key/Value
k/v级别的MVCC是通过sequence number来实现的：

#### Sequence Number
- sequence number 是一个由VersionSet直接持有的全局的编号，每插入一条记录，就会递增


- 根据我们之前对写入操作的分析，我们可以看到，当插入一条key的时候，实际参与排序，存储的是key和sequence number以及type组成的
InternalKey


- 当我们进行Get操作时，我们只需要找到目标key，同时其sequence number <= specific sequence number
  - 普通的读取，sepcific sequence number == last sequence number
  - snapshot读取，sepcific sequenc number == snapshot sequence number

#### Snapshot
snapshot 其实就是一个sequence number，获取snapshot，即获取当前的last sequence number

例如：
``` cpp
  string key = 'a';
  string value = 'b';
  leveldb::Status s = db->Put(leveldb::WriteOptions(), key, value);
  assert(s.ok())
  leveldb::ReadOptions options;
  options.snapshot = db->GetSnapshot();
  string value = 'c';
  leveldb::Status s = db->Put(leveldb::WriteOptions(), key, value);
  assert(s.ok())
  // ...
  // ...
  value.clear();
  s = db->Get(leveldb::ReadOptions(), key, &value);   // value == 'c'
  assert(s.ok())
  s = db->Get(options, key, &value);   // value == 'b'
  assert(s.ok())
```

- 我们知道在sstable compact的时候，才会执行真正的删除或覆盖，而覆盖则是如果发现两条相同的记录
会丢弃旧的(sequence number较小)一条，但是这同时会破坏掉snapshot
- 那么 key = 'a', value = 'b'是如何避免compact时被丢弃掉的呢？
  - db在内存中记录了当前用户持有的所有snapshot
  - smallest snapshot = has snapshot ? oldest snapshot : last sequence number
  - 当进行compact时，如果发现两条相同的记录，只有当两条记录的sequence number都小于 smallest snapshot 时才丢弃掉其中sequence number较小的一条

### Sstable
sstable级别的MVCC是利用Version和VersionEdit实现的：

![](/images/blog_images/leveldb/mvcc.png)

### Mainifest
VersionEdit代表了一次状态转移，每次状态转移都写入mainifest文件，以便重启时recover

![](/images/blog_images/leveldb/write_a_manifest.png)
