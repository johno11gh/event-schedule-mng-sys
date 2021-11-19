#!/usr/bin/env python
# coding: utf-8

# In[1]:

#ライブラリ参照
import tweepy #import pandas as pd
from pytz import timezone
from dateutil import parser
import datetime
from datetime import datetime as dt
from datetime import timedelta
#import timedelta #wrong import code
import time
#get_ipython().run_line_magic('matplotlib', 'inline')
#from IPython.display import Image
import json

# In[2]:


#時刻設定

#def change_time(created_at):
#    st = time.strptime(created_at, ) #



# In[3]:


#TwitterAPI
CK = '' #consumer_key
CS = '' #consumer_secret
AT = '-' #Access_token
AS = '' #Access_token_secret
auth = tweepy.OAuthHandler(CK, CS)
auth.set_access_token(AT, AS)

#インスタンス作成
twitter_api = tweepy.API(auth, wait_on_rate_limit = True, wait_on_rate_limit_notify = True)


# In[4]:


#収集ツイート数＆ワード＆収集時刻設定
count = 1
search_word = '#PSO2集会告知 -filter:retweets'
#search_word = '(from:clear031) -filter:replies'


# In[5]:


#ツイート検索結果を収集
n = 0
#data = []
for result in tweepy.Cursor(twitter_api.search, q = search_word, result_type = 'mixed', tweet_mode = 'extended', include_rts = False, since='2021-4-1').items(count):
    n += 1
    user = result.user.name
    screen_name = '@'+ result.user.screen_name
    tw_u_id = result.user.id_str
    created_at = result.created_at
    created_at_9 = created_at +  datetime.timedelta(hours = 9) #JSTに補正，＋９時間
    #ツイートID
    t_id = result.id
    print('----{}----'.format(n))
    print("https://twitter.com/{}/status/{}".format(tw_u_id, t_id))
    print(user)
    print(screen_name)
    print(tw_u_id)
    print(result.full_text)
    #print(result.rts)
    #画像URL
    #i = 0
    #if i in result.extended_entities['media']:
        #print('画像あり')
        #print(i)
    try:
        for media in result.extended_entities['media']:
            media_url = media['media_url_https']
            print(media_url)
            #Image(url = media_url)
    except:
        media_url = '画像なし'
        print(media_url)
        pass
    print(t_id)
    #print(created_at) #not need
    #print(type(created_at)) #not need
    print(created_at_9)
    print('--------')
    #result._json.replace("'", '"')
    print(result._json)
    print('--------')
    #print(Status._json) #ダメっぽい
    #JST = datetime.timezone(timedelta(hours=+9)) #bad code
    #created_at_jst = created_at.astimezone(JST) #bad code
    #print(created_at_jst)  #not need
    #created_at_jst = datetime.datetime.created_at + datetime.timedelta(hours = 9)　#created_atの前にdatetime.datetimeはいらない
    #time.sleep(0.5)
    #最新ツイートの中身を見る
    #result_n = '\n'.join(result)
    data = []
    data.append(result)
    print('--------')
    print(data)
    print('-------')
print('---------')
#print(data)
#print('---------')
#data_n = '\n'.join(data)
#print(data_n)


# In[ ]:
