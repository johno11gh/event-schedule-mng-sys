#!/usr/bin/env python
# coding: utf-8
#test用スクリプト
import mysql.connector
from datetime import datetime
now = datetime.now()
print(now)
# コネクションの作成
conn = mysql.connector.connect(
    host='localhost',
    port='',
    user='',
    password='',
    database=''
    #use_pure = True #いらない模様
)

# コネクションが切れた時に再接続してくれるよう設定
conn.ping(reconnect=True)
# 接続できているかどうか確認
print(conn.is_connected())
cur = conn.cursor()

#SQL参考URL
#https://qiita.com/valzer0/items/2f27ba98397fa7ff0d74
#https://qiita.com/inetcpl/items/993cfc265e2aa742a264
#https://qiita.com/ponkio-o/items/ac7d9ca27215ca5d5bdd

#-----user情報認証--------------
twitter_user_id = '******'

sql = "SELECT count(*) FROM users WHERE twitter_user_id = %s"
records = [
    (twitter_user_id)
]
cur.execute(sql, records)
rowcount = cur.fetchall()[0][0] #twitter_user_idのデータの行数を数える
if rowcount == 1:
    print(rowcount)
    print('取得する')
    sql = "SELECT * FROM users WHERE twitter_user_id = %s"
    cur.execute(sql, (twitter_user_id,))
    rows = cur.fetchone() #select用
    u_id = rows[0]
    print(u_id)
    #for row in rows:
        #print(row) #別の書き方
    print('u_idの取得に成功しました')
elif rowcount == 0:
    print(rowcount)
    print('新規に入力する')
    sql = "INSERT INTO users(twitter_account, twitter_user_id, login_time, update_date, create_date) VALUES (%s, %s, %s, %s, %s)"
    twitter_account = result.user.screen_name
    twitter_user_id = result.user.id_str
    login_time = now
    update_date = now
    create_date = now
    records = [
        (twitter_account, twitter_user_id, login_time, update_date, create_date)
    ]
    cur.executemany(sql, records)
    u_id = cur.lastrowid
    print(u_id)

#----集会データ入力test用---------------------
start_time = '2021-7-1 00:54:00'
finish_time = '2021-7-1 01:56:00'
ship = 12
block = 905
gather_title = 'python_test3'
organizer = u_id
tag = 'python_test3'
others = None
tw_u_id_bot_input = None
organizer_confirm_flg = 0
tweet_url = 'https://twitter.com/*****/status/******'
update_date = now
create_date = now
records = [
    (start_time, finish_time, ship, block, gather_title, organizer, tag, others , tw_u_id_bot_input, organizer_confirm_flg, tweet_url, update_date, create_date)
]

#-----booking validation---------------------
sql = "SELECT count(*) FROM gatherData WHERE ((start_time BETWEEN %s AND %s) OR (finish_time BETWEEN %s AND %s) OR (%s BETWEEN start_time AND finish_time) OR (%s BETWEEN start_time AND finish_time)) AND ship = %s AND block = %s AND delete_flg = 0"
cur.execute(sql, (start_time, finish_time, start_time, finish_time, start_time, finish_time, ship, block,))
rowcount = cur.fetchall()[0][0]
print(rowcount)
sql = "SELECT count(*) FROM gatherData WHERE ((start_time BETWEEN %s AND %s) OR (finish_time BETWEEN %s AND %s) OR (%s BETWEEN start_time AND finish_time) OR (%s BETWEEN start_time AND finish_time)) AND organizer = %s AND delete_flg = 0"
cur.execute(sql, (start_time, finish_time, start_time, finish_time, start_time, finish_time, organizer))
rowcount2 = cur.fetchall()[0][0]
print(rowcount2)
#rows = cur.fetchone()
#for row in rows:
    #print(row)
if rowcount == 0 and rowcount2 == 0:
    #---gatherデータ入力----------------------------------
    #---INSERT-SQL--------------------------
    sql = "INSERT INTO gatherData(start_time, finish_time, ship, block, gather_title, organizer, tag, others, tw_u_id_bot_input, organizer_confirm_flg, tweet_url, update_date, create_date) VALUES ( %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s )"
    # ( %(start_time)s, %(finish_time)s, %(ship)s, %(block)s, %(gather_title)s, %(organizer)s, %(tag)s, %(others)s, %(tw_u_id_bot_input), %(organizer_confirm_flg)s, %(tweet_url)s, %(update_date)s, %(create_date)s )
    cur.executemany(sql, records) #sql実行
#----------------------------------------------

#例
#以下問題なく入るSQL------------------
#sql = "INSERT INTO `test_table`( name, price) VALUES ( %s, %s)"
#records = [
    #('JKL', 6000)
#]
#cur.executemany(sql, records)
#------------------------

#------INSERT未確認SQL------------------
#cur.execute("INSERT INTO test_table VALUES ( 'ETH', %s)", (5000 ,))
#sql="INSERT INTO `test_table`( name, price) VALUES ( 'JKL', 1000)"
#sql = "INSERT INTO `test_table`( name, price) VALUES ( %(name)s, %(price)s)"
#--------------------------

#DBの操作が終わったらカーソルとコネクターを閉じる
conn.commit()
cur.close()
conn.close()
