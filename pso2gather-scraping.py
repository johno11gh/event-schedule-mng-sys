#!/usr/bin/env python
# coding: utf-8

# In[1]:


#ライブラリ
import json
import tweepy
from pytz import timezone
import datetime
from datetime import timedelta
from datetime import datetime as dt
from dateutil.relativedelta import relativedelta
import time
#get_ipython().run_line_magic('matplotlib', 'inline')
import matplotlib.pyplot as plt
from PIL import Image
import io
import requests
import termcolor
import re
import pprint
import mojimoji
#----spacy関連ライブラリ-------
import spacy
from spacy.matcher import Matcher
from spacy.tokens import Span
from spacy.lang.ja import Japanese
from spacy.tokens import Doc
#自動入力関係ライブラリ
import mysql.connector


# In[2]:


#TwitterAPI
#TwitterAPI
CK = '' #consumer_key
CS = '' #consumer_secret
AT = '-' #Access_token
AS = '' #Access_token_secret
auth = tweepy.OAuthHandler(CK, CS)
auth.set_access_token(AT, AS)

#インスタンス作成
twitter_api = tweepy.API(auth, wait_on_rate_limit = True, wait_on_rate_limit_notify = True)


# In[3]:


#収集カウント数　ワード　時刻 検索用定義
count = 100
search_word = '#PSO2集会告知 -filter:retweets'
now = datetime.datetime.now()
since_datetime = now - datetime.timedelta(minutes = 10) - datetime.timedelta(days = 7) #Since設定
since_dt = since_datetime.strftime('%Y-%m-%d')+'_'+since_datetime.strftime('%H:%M:%S')+'_JST'
until_datetime = now
until_dt = until_datetime.strftime('%Y-%m-%d')+'_'+until_datetime.strftime('%H:%M:%S')+'_JST'


# In[4]:


print(since_dt)
print(until_dt)


# In[5]:


#ハッシュタグ抽出時除外タグ
Exclusion_tags = (
    'PSO2', 'pso2', 'Pso2', 'PSO2NGS', 'PSO2集会告知', 'pso2集会告知', 'PSO2Global', 'PSO2global',
    'PSO2集会一覧', 'PSO2_SS','メンテの日なのでssを貼る', 'メンテの日じゃないけどssを貼る',
    'フォロワーのアークスがrtしてくれてまだ見ぬアークスと繋がりたい','拡散希望', 'PS4share', 'セッテ集会',
    'りざぶ郎版PSO2集会告知', 'PSO2集会告知版りざぶ郎'
)


# In[32]:


#各種関数
#ハッシュタグ関連の関数
def tag_func():
    #print(result.entities['hashtags'])
    hashtags_list = []
    global regist_tag
    regist_tag = None
    for hashtags in result.entities['hashtags']:
        tag_text = hashtags['text']
        #hashtags_list.append('#'+str(tag_text)) #後々本文からハッシュタグを削除するために利用する可能性あり
        #print(tag_text)
        #下のif文が全てまとめて簡潔に記したif文
        if tag_text in Exclusion_tags:
            #print('該当タグ')
            pass
        else:
            #print('非該当タグ')
            if regist_tag == None:
                regist_tag = tag_text
                print(termcolor.colored('登録タグが設定されました', 'red'))
            else:
                pass
    if not regist_tag == None:
        print(termcolor.colored('regist_tag:'+regist_tag, 'green'))
    else:
        print(termcolor.colored('入力タグなし', 'yellow'))
        regist_tag = 'PSO2集会告知'

#画像出力関数
def plt_func():
    #ターミナルに画像を出力
    try:
        media_count = 0
        plt.figure(figsize=(40,40))
        for media in result.extended_entities['media']:
            media_count += 1
            media_url = media['media_url_https']
            print(media_url)
            if media_count == 1:
                a_url = media_url
                a_img = Image.open(io.BytesIO(requests.get(a_url).content))
            elif media_count == 2:
                b_url = media_url
                b_img = Image.open(io.BytesIO(requests.get(b_url).content))
            elif media_count == 3:
                c_url = media_url
                c_img = Image.open(io.BytesIO(requests.get(c_url).content))
            elif media_count == 4:
                d_url = media_url
                d_img = Image.open(io.BytesIO(requests.get(d_url).content))
        if media_count == 1:
            plt.subplot(1,4,1)
            plt.imshow(a_img)
        if media_count == 2:
            plt.subplot(1,4,1)
            plt.imshow(a_img)
            plt.subplot(1,4,2)
            plt.imshow(b_img)
        if media_count == 3:
            plt.subplot(1,4,1)
            plt.imshow(a_img)
            plt.subplot(1,4,2)
            plt.imshow(b_img)
            plt.subplot(1,4,3)
            plt.imshow(c_img)
        if media_count == 4:
            plt.subplot(1,4,1)
            plt.imshow(a_img)
            plt.subplot(1,4,2)
            plt.imshow(b_img)
            plt.subplot(1,4,3)
            plt.imshow(c_img)
            plt.subplot(1,4,4)
            plt.imshow(d_img)
        plt.show()
    except:
        media_url = '画像なし'
        print(media_url)
        pass

#json作成関数
#辞書オブジェクト生成
def json_func():
    #各ツイート毎に辞書を作成していく
    data = dict()
    #data['full_text'] = result.full_text.splitlines() #"full_text":[""] の形になる
    #data = result.full_text.splitlines()
    #辞書オブジェクトをstr型で取得してprint
    #print(json.dumps(data, ensure_ascii=False, indent=2))
    #辞書オブジェクトをjsonファイルへ出力
    with open('pso2gatherin-spacy/tw_full_text.json', mode='a', encoding="utf-8") as f:
        #json.dump(data, f, ensure_ascii=False, indent=2)
        json.dump(result.full_text.splitlines(), f, ensure_ascii=False, indent=2)
        #f.write(str(result.full_text.splitlines()))
        f.write(',\n')

def json_alltext_func():
    #取得した全てのツイートを一まとめにし辞書を作る
    data = dict()
    with open('pso2gatherin-spacy/tw_full_text.json', mode='a', encoding="utf-8") as f:
        json.dump(all_full_text.splitlines(), f, ensure_ascii=False, indent=2)

def quoted_status_func():
    if(result.is_quote_status):
        print('引用RTあり')
        #print(result.is_quote_status)
        quoted_status = result.quoted_status
        #print(result.quoted_status)
        print('---引用RT本文---')
        print('引用元:'+quoted_status.user.name)
        print('引用元@'+quoted_status.user.screen_name)
        print('引用元u_id:'+quoted_status.user.id_str)
        print('引用元ステータスid:'+quoted_status.id_str)
        print('引用元URL:'+"https://twitter.com/{}/status/{}".format(quoted_status.user.id, quoted_status.id))
        print(quoted_status.full_text)
        #print(quoted_status.extended_entities['media'])
        try:
            print('引用元ツイートの添付画像')
            for quoted_media in result.quoted_status.extended_entities['media']:
                print(quoted_media['media_url_https'])
        except:
            print('引用元の画像なし')
            pass

#入力関数
def mysql_connector_func():
    #コネクター作成
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
    #user情報照会
    twitter_user_id = tw_u_id
    sql = "SELECT count(*) FROM users WHERE twitter_user_id = %s"
    records = [
        (twitter_user_id)
    ]
    cur.execute(sql, records)
    rowcount = cur.fetchall()[0][0]
    if rowcount == 1:
        sql = "SELECT * FROM users WHERE twitter_user_id = %s"
        cur.execute(sql, (twitter_user_id,))
        rows = cur.fetchone()
        u_id = rows[0]
    elif rowcount == 0:
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
    #集会情報入力用----------------------------
    start_time = holding_datetime_start
    finish_time = holding_datetime_finish
    ship = holding_ship
    block = holding_block
    gather_title = str(gatherTitle)
    organizer = u_id
    tag = regist_tag
    others = None
    tw_u_id_bot_input = None
    organizer_confirm_flg = 0
    tweet_url = t_status_id
    update_date = now
    create_date = now
    records = [
        (start_time, finish_time, ship, block, gather_title, organizer, tag, others , tw_u_id_bot_input, organizer_confirm_flg, tweet_url, update_date, create_date)
    ]
    #-----booking validation---------------------
    sql = "SELECT count(*) FROM gatherData WHERE ((start_time BETWEEN %s AND %s) OR (finish_time BETWEEN %s AND %s) OR (%s BETWEEN start_time AND finish_time) OR (%s BETWEEN start_time AND finish_time)) AND ship = %s AND block = %s AND delete_flg = 0"
    cur.execute(sql, (start_time, finish_time, start_time, finish_time, start_time, finish_time, ship, block,))
    rowcount = cur.fetchall()[0][0]
    sql = "SELECT count(*) FROM gatherData WHERE ((start_time BETWEEN %s AND %s) OR (finish_time BETWEEN %s AND %s) OR (%s BETWEEN start_time AND finish_time) OR (%s BETWEEN start_time AND finish_time)) AND organizer = %s AND delete_flg = 0"
    cur.execute(sql, (start_time, finish_time, start_time, finish_time, start_time, finish_time, organizer))
    rowcount2 = cur.fetchall()[0][0]
    if rowcount == 0 and rowcount2 == 0:
        #---gatherデータ入力----------------------------------
        #---INSERT-SQL--------------------------
        sql = "INSERT INTO gatherData(start_time, finish_time, ship, block, gather_title, organizer, tag, others, tw_u_id_bot_input, organizer_confirm_flg, tweet_url, update_date, create_date) VALUES ( %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s )"
        cur.executemany(sql, records) #sql実行
        print("入力しました")
    else:
        print("ブッキングを確認しました。入力を中止します")
    conn.commit()
    cur.close()
    conn.close()


# In[7]:


#spacy処理
nlp = spacy.load("ja_core_news_sm")
#spacyパターン
matcher = Matcher(nlp.vocab)

#pattern = [{"LOWER": "pso"}, {"TEXT": "2"}] #pso 2
#matcher.add("PSO2", [pattern])
pattern2 = [{"TEXT": "り"}, {"TEXT": "ざぶ"}, {"TEXT": "郎"}] #リザぶろう
matcher.add("WEBSITE", [pattern2])
pattern3 = [{"IS_DIGIT": True, "OP": "?"},{"TEXT": "年", "OP": "?"},{"IS_DIGIT": True},{"TEXT": "月"}, {"IS_DIGIT": True}, {"TEXT": "日"}] #(2021)(年)6月2日
matcher.add("DATE", [pattern3])
pattern4 = [{"IS_DIGIT": True}, {"TEXT": "/"}, {"IS_DIGIT": True}, {"TEXT": "/", "OP": "?"}, {"IS_DIGIT": True, "OP": "?"}] #3/4(/3)
matcher.add("DATE", [pattern4])
pattern5 = [{"IS_DIGIT": True, "OP": "?"}, {"TEXT": "／", "OP": "?"}, {"IS_DIGIT": True}, {"TEXT": "／"} , {"IS_DIGIT": True}] #(2021)(/)6/9 全角／
matcher.add("DATE", [pattern5])
pattern6 = [{"IS_DIGIT": True}, {"TEXT": ":"}, {"IS_DIGIT": True}] #21:30　全角：
matcher.add("TIME", [pattern6])
pattern7 = [{"IS_DIGIT": True}, {"TEXT": "："}, {"IS_DIGIT": True}] #21:30 半角:
matcher.add("TIME", [pattern7])
pattern8 = [{"IS_DIGIT": True}, {"TEXT": "時"}, {"IS_DIGIT": True, "OP": "?"}, {"TEXT": "分", "OP": "?"}] #21時30分
matcher.add("TIME", [pattern8])
pattern9 = [{"IS_DIGIT": True}, {"TEXT": "時"}, {"IS_DIGIT": True, "OP": "?"}, {"TEXT": "半"}]
matcher.add("TIME", [pattern9])
pattern10 = [{"IS_DIGIT":  True}, {"TEXT": "鯖"}]
matcher.add("SHIP", [pattern10])
pattern11 = [{"TEXT": "シップ"}, {"IS_DIGIT": True}]
matcher.add("SHIP", [pattern11])
pattern14 = [{"LOWER": "ship"},{"IS_DIGIT": True}]
matcher.add("SHIP", [pattern14])
pattern15 = [{"TEXT": "共通"},{"TEXT": "シップ", "OP": "?"},{"TEXT": "チャレンジ"}]
matcher.add("SHIP", [pattern15])
pattern16 = [{"TEXT": "共通"},{"TEXT": "シップ", "OP": "?"},{"TEXT": "バトル"}]
matcher.add("SHIP", [pattern16])
pattern17 = [{"TEXT": "バトル"}, {"TEXT": "ロビー"}]
matcher.add("SHIP", [pattern17])
pattern18 = [{"TEXT": "チャレンジ"},{"TEXT": "ロビー"}]
matcher.add("SHIP", [pattern18])
pattern12 = [{"LOWER": "b"}, {"IS_PUNCT": True, "OP": "?"}, {"IS_DIGIT": True}]
matcher.add("BLOCK", [pattern12])
pattern13 = [{"LOWER": "b"}, {"TEXT": "ー", "OP": "?"}, {"IS_DIGIT": True}]
matcher.add("BLOCK", [pattern13])
pattern19 = [{"TEXT": "ブロック"},{"IS_DIGIT": True}]
matcher.add("BLOCK", [pattern19])
pattern20 = [{"IS_DIGIT": True}, {"TEXT": "ブロック"}]
matcher.add("BLOCK", [pattern20])
pattern21 = [{"IS_DIGIT": True}, {"TEXT": "ﾌﾞﾛｯｸ"}]
matcher.add("BLOCK", [pattern21])
pattern22 = [{"TEXT": "ロビー"}, {"IS_DIGIT": True}]
matcher.add("BLOCK", [pattern22])
pattern23 = [{"TEXT": "推奨"}, {"IS_DIGIT": True}]
matcher.add("BLOCK", [pattern23])
pattern24 = [{"IS_DIGIT": True}, {"LOWER":"h"}]
matcher.add("TIME", [pattern24])
pattern25 = [{"TEXT": "エアリオ"}, {"IS_DIGIT": True}]
matcher.add("BLOCK", [pattern25])



# In[8]:


#日付正規表現
pattern_date1 = r'[12]\d{3}[/\-年](0?[1-9]|1[0-2])[/\-月](0?[1-9]|[12][0-9]|3[01])日?$'
prog_date1 = re.compile(pattern_date1)
pattern_date2 = r'(0?[1-9]|1[0-2])[/\-月](0?[1-9]|[12][0-9]|3[01])日?$'
prog_date2 = re.compile(pattern_date2)
#時間正規表現
pattern_time1 = r'([0-9]?[0-9])[:][0-9]'
prog_time1 = re.compile(pattern_time1)
pattern_time2 = r'([0-9]?[0-9])[:]'
prog_time2 = re.compile(pattern_time2)


# In[31]:


#ツイート検索結果
n = 0
data = []
all_full_text = ''
for result in tweepy.Cursor(twitter_api.search, q = search_word, result_type = 'recent', tweet_mode = 'extended', include_rts = False, is_quote_status = False, since = since_dt, until = until_dt ).items(count):
    n += 1
    user = result.user.name
    screen_name = '@' + result.user.screen_name
    tw_u_id = result.user.id
    created_at = result.created_at
    created_at_JST = created_at + datetime.timedelta(hours = 9)
    created_at_date = created_at_JST.date()
    t_status_id = result.id
    all_full_text += result.full_text
    #print(result)
    print('-----{}-----'.format(n))
    print("https://twitter.com/{}/status/{}".format(str(tw_u_id), str(t_status_id)))
    print(result.lang)
    print(user) #ユーザー名
    print(screen_name) #スクリーン名　@******
    print('u_id:'+str(tw_u_id)) #ユーザーID：ツイッター付与の数字ID
    print('status_id:'+str(t_status_id)) #ツイートのステータスID
    print(created_at_JST) #ツイート日時(TZ:JST)
    print('-----タグ-----')
    tag_func()
    print("タグ:", "regist_tag", regist_tag)
    print('-----本文-----')
    print(result.full_text)

    #------画像出力----------------
    #plt_func()
    #------個別の本文json保存-------
    #json_func()
    #------引用RT読み込み関数-------
    #quoted_status_func()

    #print('--本文抽出情報--')
    #ツイート本文NLP処理
    doc = nlp(result.full_text)
    matches = matcher(doc)
    holding_date = None
    holding_time_start = None
    holding_time_end = None
    holding_block = None
    holding_ship = None
    holding_datetime_start = None
    holding_datetime_finish = None
    print('----matched_span----')
    ######## DATE ############
    for match_id, start, end in matches:
        matched_span = doc[start: end]
        #print(matched_span.text) #6/19
        print(doc.vocab.strings[match_id], doc[start: end].text) #DATE 6/19
        if doc.vocab.strings[match_id] == "DATE":
            #print("matched_span_date",doc[start:end].text)
            temporary_date = doc[start:end].text
            temporary_date = mojimoji.zen_to_han(temporary_date, kana = False)
            temporary_date = temporary_date.replace("年", '/').replace('月', '/').replace('日', '').replace(' ', '').replace('　', '')
            #正規表現確認し、入力する開催日時を設定する
            if prog_date1.match(temporary_date) or prog_date2.match(temporary_date):
                if prog_date2.match(temporary_date):
                    temporary_date = str(datetime.datetime.today().year) + '/' + temporary_date
                temporary_date = temporary_date.replace('/', '-')
                temporary_date = dt.strptime(temporary_date, '%Y-%m-%d').date()
                print("temporary_date",type(temporary_date), temporary_date)
                if temporary_date < created_at_date:
                    temporary_date += relativedelta(years = 1)
                if holding_date == None:
                    holding_date = temporary_date
        if doc.vocab.strings[match_id] == "TIME":
            if holding_time_start == None:
                holding_time_start = doc[start:end].text
                holding_time_start = mojimoji.zen_to_han(holding_time_start, kana=False)
            elif not holding_time_start == None and holding_time_end == None:
                holding_time_end = doc[start:end].text
                holding_time_end = mojimoji.zen_to_han(holding_time_end, kana=False)
        if doc.vocab.strings[match_id] == "BLOCK":
            if holding_block == None:
                temporary_block = doc[start:end].text
                if "推奨" in temporary_block:
                    temporary_block1 = re.sub("\\D", "", temporary_block)
                    temporary_block2 = int(temporary_block1) + 10000
                    temporary_block = str(temporary_block2)
                    #print("推奨ブロックが選択されています")
                #print("temporary_block:"+temporary_block)
                temporary_block = re.sub("\\D", "", temporary_block)
                #print("temporary_block:"+temporary_block)
                holding_block = int(temporary_block)
        if doc.vocab.strings[match_id] == "SHIP":
            if holding_ship == None:
                temporary_ship = doc[start:end].text
                if "バトル" in temporary_ship:
                    temporary_ship = 12
                elif "チャレンジ" in temporary_ship:
                    temporary_ship = 11
                else:
                    temporary_ship = re.sub("\\D", "", temporary_ship)
                holding_ship = int(temporary_ship)
    if holding_ship == None and not holding_block == None:
        if holding_block >= 800 and holding_block <= 899:
            holding_ship = 11
        elif holding_block >= 900 and holding_block <= 999:
            holding_ship = 12
        #spans = [doc[start: end] for match_id, start, end in matcher(doc)]
        #print(spans) #[6/19, １鯖]
    print('------got_data-------')
    if not holding_date == None:
        print("DATE:", holding_date)
    else:
        print("DATE:抽出できず")
    ####### TIME ##########
    if not holding_time_start == None:
        #print("TIME:",holding_time_start) #
        holding_time_start = holding_time_start.replace('時', ':').replace('半', '30').replace('分', '').replace('h', ':')
        #print(type(holding_time_start), holding_time_start) #
        if prog_time2.match(holding_time_start) and not prog_time1.match(holding_time_start):
            #print('分表記なし')#
            holding_time_start += "00"
            #print(type(holding_time_start), holding_time_start)#
        holding_time_start = holding_time_start.replace(":", ",")
        #print(type(holding_time_start), holding_time_start)#
        holding_time_start = holding_time_start.split(',')
        #print(type(holding_time_start), holding_time_start)#
        holding_time_start_hours = int(holding_time_start[0])
        if holding_time_start[1] == '':
            holding_time_start[1] = 0
        holding_time_start_minutes = int(holding_time_start[1])
        if holding_time_start_hours >= 24:
            holding_time_start_hours -= 24
        holding_time_start = datetime.time(holding_time_start_hours, holding_time_start_minutes)
        print("START-TIME:", type(holding_time_start), holding_time_start)
    else:
        print("START-TIME:開始時間抽出できず")
    if not holding_time_end == None:
        #print("TIME:", holding_time_end)
        #print(type(holding_time_end), holding_time_end)
        holding_time_end = holding_time_end.replace('時', ':').replace('半', '30').replace('分', '').replace('h', ':')
        #print(holding_time_end)
        if prog_time2.match(holding_time_end) and not prog_time1.match(holding_time_end):
            holding_time_end += "00"
        holding_time_end = holding_time_end.replace(":", ',')
        #print(holding_time_end)
        holding_time_end = holding_time_end.split(',')
        #print(holding_time_end)
        holding_time_end_hours = int(holding_time_end[0])
        if holding_time_end[1] == '':
            holding_time_end[1] = 0
        holding_time_end_minutes = int(holding_time_end[1])
        if holding_time_end_hours >= 24:
            holding_time_end_hours -= 24
        holding_time_end = datetime.time(holding_time_end_hours, holding_time_end_minutes)
        print("FINISH-TIME:", type(holding_time_end), holding_time_end)
    else:
        print("FINISH-TIME:終了時間抽出できず")
    ########### BLOCK ###############
    if not holding_block == None:
        print("BLOCK:", holding_block)
    else:
        print("BLOCK:抽出できず")
    ######### SHIP #############
    if not holding_ship == None:
        print("SHIP:", holding_ship)
    else:
        print("SHIP:抽出できず")
    ####### START_DATETIME & FINISH_DATETIME ############
    if not holding_date == None and not holding_time_start == None:
        holding_datetime_start = datetime.datetime.combine(holding_date, holding_time_start)
    if not holding_date == None and not holding_time_end == None:
        holding_datetime_finish = datetime.datetime.combine(holding_date, holding_time_end)
        if not holding_datetime_start == None:
            if holding_datetime_finish < holding_datetime_start:
                holding_datetime_finish += relativedelta(days = 1)
    if not holding_datetime_start == None:
        print("入力開始日時:", holding_datetime_start)
    else:
        print('入力開始日時確認不可')
    if not holding_datetime_finish == None:
        print("入力終了日時:", holding_datetime_finish)
    else:
        if not holding_datetime_start == None:
            holding_datetime_finish = holding_datetime_start + datetime.timedelta(hours = 3)
            print('入力終了日時自動生成:', holding_datetime_finish)
        else:
            print('入力終了日時確認不可')

    print('----token----')
    title_token_start = None
    title_token_end = None
    print(doc.ents)
    for token in doc:
        token_i = token.i
        token_text = token.text
        token_pos = token.pos_
        token_dep = token.dep_
        #print(f"{token_i:<4}{token_text:<12}{token_pos:<10}{token_dep}")
        if token_i < len(doc) - 3:
            next_token = token.i+1
            doc_next_token = doc[token.i+1]
            next_token2 = token.i+2
            doc_next_token2 = doc[token.i+2]
            next_token3 = token.i+3
            doc_next_token3 = doc[token.i+3]
            if token.text == "集会" or token.text == "名称":
                if doc_next_token.text =="名" or doc_next_token.text == "名称":
                    #print(termcolor.colored('条件パターン発見', "green"))
                    if title_token_start == None:
                        doc_title_token_start = doc_next_token3
                        title_token_start = next_token3
                    for j in range(20):
                        if doc[token.i+j].pos_ == "SPACE":
                            if title_token_end == None:
                                #print('SPACE発見', token.i+j)
                                title_token_end = token.i+j
                                doc_title_token_end = doc[token.i+j]
                elif doc_next_token.text ==":":
                    print(termcolor.colored('条件パターン発見', "green"))
                    if title_token_start == None:
                        doc_title_token_start = doc_next_token2
                        title_token_start = next_token2
                        for j in range(20):
                            if doc[token.i+j].pos_ == "SPACE":
                                if title_token_end == None:
                                    #print('SPACE発見', token.i+j)
                                    title_token_end = token.i+j
                                    doc_title_token_end = doc[token.i+j]
                elif doc_next_token.pos_ == "SPACE":
                    #print('次はスペース')
                    if title_token_end == None:
                        title_token_end = token.i+1
                        doc_title_token_end = doc[title_token_end]
                     #print(title_token_end, 'タイトル終のトークン番号')
                    for j in range(10):
                        if doc[token.i - j].pos_ == "SPACE":
                            if token.i - j <= 0:
                                break
                            if title_token_start == None:
                                title_token_start = token.i-j+1
                                doc_title_token_start = doc[token.i-j+1]
                            #print(title_token_start, doc_title_token_start, '集会名スタートトークン')
    #print(title_token_start, doc_title_token_start, title_token_end, doc_title_token_end)
    if not title_token_start == None and not title_token_end == None:
        span = doc[title_token_start: title_token_end]
        gatherTitle = span
        print("集会タイトル：",gatherTitle)
    else:
        print('集会名取得できず')
        gatherTitle = '解析不可'
    #print('----ent----')
    #for ent in doc.ents:
        #print(ent.text, ent.label_)
    #print("\n")
    if not holding_ship == None and not holding_block == None and not holding_datetime_start == None:
        #データベース入力実行
        mysql_connector_func()
        print(termcolor.colored('入力プロセス完了しました', 'red'))
    else:
        print(termcolor.colored('入力しませんでした', 'red'))
    #time.sleep(1)
    print("\n")
#json_alltext_func() #全本文のjsonファイル作成実行
